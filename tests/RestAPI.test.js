import ResourceClient from './RestClient/ResourceClient';
import { default as StatelessClient, getResourcePath } from './RestClient/StatelessClient';
import { clearCacheFiles } from './RestClient/cache';
import { hostUrl, JWT_TTL, LOCAL_BAD_THUMB_PATH, LOCAL_THUMB_PATH } from './RestClient/constants';
import { IMAGE, MEDIA, MEDIA_SUBTYPES_VIDEO, MEDIA_TYPES, TOKEN, USER } from './RestClient/resources';
import { images } from './data/images';
import { invalidPhoto, validPhoto } from './data/media';
import { aggregateAlbum, editorUserStaticAlbum, plainUserStaticAlbum } from './data/albums';
import {
  adminUser,
  bannedUser,
  createdUser,
  editorUser,
  otherUser,
  pendingUser,
  plainUser,
  writerUser,
} from './data/users';
import { img3000, img640, imgHeavy, textFileAsJPEG, textFileAsTxt } from './data/files';
import { cleanupTestDatabase } from './RestClient/database';
import { getDateForBackend, getSortedKeysAsString, looksLikeUUID } from './RestClient/utils';

const { PHOTO, VIDEO } = MEDIA_TYPES;
const { DAILYMOTION, FACEBOOK, INSTAGRAM, VIMEO, YOUTUBE } = MEDIA_SUBTYPES_VIDEO;
const plainUserPath = getResourcePath(USER, plainUser.id);
const tokenPath = getResourcePath(TOKEN);

const client = new StatelessClient(hostUrl);

beforeAll(async (done) => {
  await cleanupTestDatabase();
  await client.clearPublicFiles();
  done();
});

beforeEach(() => {
  clearCacheFiles();
  return client.clearCache();
});

describe('Token tests', () => {
  test('logged-out request results in 200', async () => {
    const path = plainUserPath;
    const response = await client.get({ path });

    expect(response.statusCode).toBe(200);
    expect(Object.keys(response.body).length > 0).toBeTruthy();
  });

  test('banned user login request results in 403', async () => {
    const { username, password } = bannedUser;
    const loginResponse = await client.post({
      path: tokenPath,
      data: { userP: password, username: username },
    });
    expect(loginResponse.statusCode).toBe(403);
  });

  test('invalid token results in 403', async () => {
    const path = plainUserPath;
    const token = 'no-way-this-is-gonna-work';

    const response = await client.get({ path, token });
    expect(response.statusCode).toBe(403);
  });

  test('token management is working properly', async () => {
    /*
     - login request as user 1 => fine
     - logged-in request to user 1 in the future so token has expired => error (expired)
     - logged-in request to user 1 now: => fine
     - log-out request => fine
     - request with deleted token to user 1 => error
    */
    let user1Token;

    const { username, password } = plainUser;

    // Login request as user 1
    const loginResponse = await client.post({
      path: tokenPath,
      data: { userP: password, username: username },
    });
    expect(loginResponse.statusCode).toBe(200);
    const loginResponseBody = loginResponse.body;
    expect(loginResponseBody.token).toBeDefined();
    user1Token = loginResponseBody.token;

    // Tweak server time to be seconds after the expiration date
    await client.setDate(getDateForBackend(JWT_TTL + 2));

    // Request user 1 with user1Token while server thinks it's the future
    const futureResponse = await client.get({
      path: plainUserPath,
      token: user1Token,
    });
    expect(futureResponse.statusCode).toBe(403);

    // Sets time back
    await client.setDate();

    // Request user 1 with user1Token while server thinks it's the present again
    const loginNowResponse = await client.get({ path: plainUserPath, token: user1Token });
    expect(loginNowResponse.statusCode).toBe(200);

    // Delete token as self (ie, logout)
    const deleteResponse = await client.delete({
      path: getResourcePath(TOKEN),
      token: user1Token,
    });
    expect(deleteResponse.statusCode).toBe(200);

    // Request user 1 with blacklisted user1Token
    const loginAgainResponse = await client.get({ path: plainUserPath, token: user1Token });
    expect(loginAgainResponse.statusCode).toBe(403);
  });
});

describe('User tests', () => {
  const userClient = new ResourceClient(client, USER);

  describe('Error cases', () => {
    test('Missing user request should return a 404', async () => {
      const { statusCode } = await userClient.get(2500);
      expect(statusCode).toEqual(404);
    });

    test('Banned user request should return a 403', async () => {
      const { statusCode } = await userClient.get(bannedUser.id);
      expect(statusCode).toEqual(403);
    });

    test('Pending user request should return a 403', async () => {
      const { statusCode } = await userClient.get(pendingUser.id);
      expect(statusCode).toEqual(403);
    });
  });

  describe('User GET', () => {
    const plainUserPublicInfo =
      '["avatar","city","date","firstName","lang","lastName","site","userId","username"]';

    const plainUserSelfInfo =
      '["avatar","city","date","email","firstName","lang","lastName",' +
      '"site","status","userId","username"]';

    const plainUserAdminInfo =
      '["avatar","city","date","email","firstName","lang","lastLogin","lastName",' +
      '"site","status","userId","username"]';

    test("Retrieve plainuser's data as guest", async () => {
      userClient.setToken(null);
      const { body } = await userClient.get(plainUser.id);
      expect(getSortedKeysAsString(body)).toEqual(plainUserPublicInfo);
    });

    test("Retrieve plainuser's data as other user", async () => {
      await userClient.setUser(otherUser);
      const { body } = await userClient.get(plainUser.id);
      expect(getSortedKeysAsString(body)).toEqual(plainUserPublicInfo);
    });

    test("Retrieve plainuser's data as writer user", async () => {
      await userClient.setUser(writerUser);
      const { body } = await userClient.get(plainUser.id);
      expect(getSortedKeysAsString(body)).toEqual(plainUserPublicInfo);
    });

    test("Retrieve plainuser's data as editor", async () => {
      await userClient.setUser(editorUser);
      const { body } = await userClient.get(plainUser.id);
      expect(getSortedKeysAsString(body)).toEqual(plainUserPublicInfo);
    });

    test("Retrieve plainuser's data as admin", async () => {
      await userClient.setUser(adminUser);
      const { body } = await userClient.get(plainUser.id);
      expect(getSortedKeysAsString(body)).toEqual(plainUserAdminInfo);
    });

    test("Retrieve plainuser's data as self", async () => {
      await userClient.setUser(plainUser);
      const { body } = await userClient.get(plainUser.id);
      expect(getSortedKeysAsString(body)).toEqual(plainUserSelfInfo);
    });
  });

  describe('User list GET', () => {
    test('Retrieve all valid users as guest', async () => {
      userClient.setToken(null);
      const { body } = await userClient.list();
      const userIds = body.map((u) => u.userId);
      const expectedUserIds = [
        plainUser.id,
        adminUser.id,
        editorUser.id,
        writerUser.id,
        otherUser.id,
      ];
      expect(userIds).toEqual(expectedUserIds);
    });

    test('Retrieve all users in the database as admin', async () => {
      await userClient.setUser(adminUser);
      const { body } = await userClient.list();
      const userIds = body.map((u) => u.userId);
      const expectedUserIds = [
        plainUser.id,
        bannedUser.id,
        adminUser.id,
        editorUser.id,
        writerUser.id,
        otherUser.id,
        pendingUser.id,
      ];
      expect(userIds).toEqual(expectedUserIds);
    });

    test('Retrieve 3rd and 4th valid users as guest', async () => {
      userClient.setToken(null);
      const { body } = await userClient.list({ start: 2, count: 2 });
      const userIds = body.map((u) => u.userId);
      expect(userIds).toEqual([editorUser.id, writerUser.id]);
    });

    test('Retrieve 2nd and 3rd valid users sorted by username ascending as guest', async () => {
      userClient.setToken(null);
      const { body } = await userClient.list({ start: 1, count: 2, sort: 'username' });
      const userIds = body.map((u) => u.userId);
      expect(userIds).toEqual([editorUser.id, otherUser.id]);
    });

    test('Retrieve 2nd and 3rd valid users sorted by username descending as guest', async () => {
      userClient.setToken(null);
      const { body } = await userClient.list({ start: 1, count: 2, sort: 'username', dir: 'desc' });
      const userIds = body.map((u) => u.userId);
      expect(userIds).toEqual([plainUser.id, otherUser.id]);
    });
  });

  describe('User POST', () => {
    const createdUserKeys =
      '["avatar","city","date","email","firstName","lang","lastName",' +
      '"site","status","userId","username"]';

    test('Logged-in user cannot create a new user', async () => {
      await userClient.setUser(plainUser);
      const { statusCode } = await userClient.post({});
      expect(statusCode).toEqual(403);
    });

    test('Guest user cannot create a new user with invalid data', async () => {
      await userClient.setToken(null);
      const { statusCode } = await userClient.post({});
      expect(statusCode).toEqual(400);
    });

    test('Successful user creation should return an id', async () => {
      await userClient.setToken(null);
      userClient.setUUIDs([createdUser.id]);
      const { statusCode, body } = await userClient.post({
        username: createdUser.username,
        userP: createdUser.password,
        userPC: createdUser.password,
        email: createdUser.email,
      });
      expect(statusCode).toEqual(200);
      expect(getSortedKeysAsString(body)).toEqual(createdUserKeys);
      expect(looksLikeUUID(body.userId)).toBeTruthy();
      expect(body.userId).toEqual(createdUser.id);
    });
  });

  describe('User PUT', () => {
    test('Admin can change user status', async () => {
      await userClient.setUser(adminUser);
      const { statusCode, body } = await userClient.put(createdUser.id, { status: 'member' });
      expect(statusCode).toEqual(200);
      expect(body.status).toEqual('member');
    });

    test('Plain user cannot update new user', async () => {
      await userClient.setUser(plainUser);
      const { statusCode } = await userClient.put(createdUser.id, { firstName: 'nope' });
      expect(statusCode).toEqual(403);
    });

    test('Plain user can update their account', async () => {
      await userClient.setUser({ id: createdUser.id });
      const { statusCode, body } = await userClient.put(createdUser.id, { firstName: 'yes' });
      expect(statusCode).toEqual(200);
      expect(body.firstName).toEqual('yes');
    });

    test('Requests with password mismatch are rejected', async () => {
      await userClient.setUser({ id: createdUser.id });
      const { statusCode, body } = await userClient.put(createdUser.id, {
        userP: '123',
        userPC: '345',
      });
      expect(statusCode).toEqual(400);
      expect(body).toEqual({ errors: { userPC: ['notSame'] } });
    });

    test('Requests with matching passwords are successful, and old password is made invalid', async () => {
      const newPassword = '345';

      await userClient.setUser({ id: createdUser.id });
      const { statusCode } = await userClient.put(createdUser.id, {
        userP: newPassword,
        userPC: newPassword,
      });
      expect(statusCode).toEqual(200);

      userClient.setToken(null);
      try {
        await userClient.setUser({ id: createdUser.id });
      } catch (e) {
        expect(e.message).toEqual(`Login as '${createdUser.username}' failed`);
      }

      userClient.setToken(null);
      await userClient.setUser({ username: createdUser.username, password: newPassword });
    });
  });

  describe('User DELETE', () => {
    let userIdToDelete;
    const userToDelete = { username: 'userToDelete', password: '123456789' };

    beforeAll(async () => {
      await userClient.setToken(null);
      const { body } = await userClient.post({
        username: userToDelete.username,
        userP: userToDelete.password,
        userPC: userToDelete.password,
        email: 'deleteme@gmail.com',
      });
      userIdToDelete = body.userId;

      await userClient.setUser(adminUser);
      await userClient.put(userIdToDelete, { status: 'member' });
    });

    test('Guest cannot delete a user', async () => {
      userClient.setToken(null);
      const { statusCode } = await userClient.delete(userIdToDelete);
      expect(statusCode).toEqual(403);
    });

    test('User cannot delete their account', async () => {
      await userClient.setUser(userToDelete);
      const { statusCode } = await userClient.delete(userIdToDelete);
      expect(statusCode).toEqual(403);
    });

    test('Admin can delete an account', async () => {
      await userClient.setUser(adminUser);
      const { statusCode } = await userClient.delete(userIdToDelete);
      expect(statusCode).toEqual(200);

      userClient.setToken(null);
      const { statusCode: notFoundStatusCode } = await userClient.get(userIdToDelete);
      expect(notFoundStatusCode).toEqual(404);
    });
  });
});

describe('Image tests', () => {
  const imageClient = new ResourceClient(client, IMAGE);
  const defaultImageId = images[0].id;
  const type = 0;
  const MAX_WIDTH = 1920;
  const MAX_HEIGHT = 1920;

  describe('POST error cases', () => {
    test('Plain user POST fails when no file is sent', async () => {
      await imageClient.setUser(plainUser);
      const { statusCode, body } = await imageClient.postFormData({ type }, []);
      expect(statusCode).toEqual(400);
      expect(body.errors.topLevelError.code).toEqual(10010);
    });

    test('Plain user POST fails because file is not actually an image', async () => {
      await imageClient.setUser(plainUser);
      const { statusCode, body } = await imageClient.postFormData({ type }, [textFileAsJPEG]);
      expect(statusCode).toEqual(400);
      expect(body.errors.topLevelError.code).toEqual(10011);
    });

    test('Plain user POST fails because file is a text file', async () => {
      await imageClient.setUser(plainUser);
      const { statusCode, body } = await imageClient.postFormData({ type }, [textFileAsTxt]);
      expect(statusCode).toEqual(400);
      expect(body.errors.topLevelError.code).toEqual(10011);
    });

    test('Plain user POST fails because image file size is too big', async () => {
      await imageClient.setUser(plainUser);
      const { statusCode, body } = await imageClient.postFormData({ type }, [imgHeavy]);
      expect(statusCode).toEqual(400);
      expect(body.errors.topLevelError.code).toEqual(10002);
    });

    test('Plain user POST fails because storage type is not supported', async () => {
      await imageClient.setUser(plainUser);
      const { statusCode, body } = await imageClient.postFormData({ type: 1 }, [img640]);
      expect(statusCode).toEqual(400);
      expect(body.errors.topLevelError.code).toEqual(10001);
    });

    test('Plain user POST fails because uuid already exists', async () => {
      await imageClient.setUser(plainUser);
      imageClient.setUUIDs([images[0].id]);
      const { statusCode, body } = await imageClient.postFormData({ type }, [img640]);
      expect(statusCode).toEqual(400);
      expect(body.errors.topLevelError.code).toEqual(10008);
    });
  });

  describe('POST success cases', () => {
    test('Plain user POST succeeds and returns the right dimensions', async () => {
      await imageClient.setUser(plainUser);
      const {
        statusCode,
        body: [{ key, width, height }],
      } = await imageClient.postFormData({ type }, [img640]);
      expect(statusCode).toEqual(200);
      expect(looksLikeUUID(key)).toBeTruthy();
      expect(parseInt(width, 10)).toEqual(img640.width);
      expect(parseInt(height, 10)).toEqual(img640.height);
    });

    test('Plain user POST succeeds even when image dimensions are brought down', async () => {
      await imageClient.setUser(plainUser);
      const {
        statusCode,
        body: [{ key, width, height }],
      } = await imageClient.postFormData({ type }, [img3000]);
      expect(statusCode).toEqual(200);
      expect(looksLikeUUID(key)).toBeTruthy();
      expect(parseInt(width, 10)).toEqual(MAX_WIDTH);
      expect(parseInt(height, 10)).toEqual(MAX_HEIGHT);
    });
  });

  describe('POST ACLS: all and only bad users get a 403', () => {
    test('Guest cannot POST', async () => {
      await imageClient.setToken(null);
      const { statusCode } = await imageClient.postFormData({});
      expect(statusCode).toEqual(403);
    });

    test('Pending user cannot POST', async () => {
      await imageClient.setUser(pendingUser);
      const { statusCode } = await imageClient.postFormData({});
      expect(statusCode).toEqual(403);
    });

    test('Banned user cannot POST', async () => {
      await imageClient.setUser(bannedUser);
      const { statusCode } = await imageClient.postFormData({});
      expect(statusCode).toEqual(403);
    });

    test('Plain user can POST', async () => {
      await imageClient.setUser(plainUser);
      const { statusCode } = await imageClient.postFormData({ type: 0 }, [img640]);
      expect(statusCode).toEqual(200);
    });

    test('Writer user can POST', async () => {
      await imageClient.setUser(writerUser);
      const { statusCode } = await imageClient.postFormData({ type: 0 }, [img640]);
      expect(statusCode).toEqual(200);
    });

    test('Editor user can POST', async () => {
      await imageClient.setUser(editorUser);
      const { statusCode } = await imageClient.postFormData({ type: 0 }, [img640]);
      expect(statusCode).toEqual(200);
    });

    test('Admin can POST', async () => {
      await imageClient.setUser(adminUser);
      const { statusCode } = await imageClient.postFormData({ type: 0 }, [img640]);
      expect(statusCode).toEqual(200);
    });
  });

  describe('GET/PUT ACLS: everyone gets a 403', () => {
    test('Guest cannot GET/PUT', async () => {
      imageClient.setToken(null);

      const { statusCode: listStatus } = await imageClient.get();
      expect(listStatus).toEqual(403);

      const { statusCode: getStatus } = await imageClient.get(defaultImageId);
      expect(getStatus).toEqual(403);

      const { statusCode: putStatus } = await imageClient.put(defaultImageId, {});
      expect(putStatus).toEqual(403);
    });

    test('Plain user cannot GET/PUT', async () => {
      await imageClient.setUser(plainUser);

      const { statusCode: listStatus } = await imageClient.get();
      expect(listStatus).toEqual(403);

      const { statusCode: getStatus } = await imageClient.get(defaultImageId);
      expect(getStatus).toEqual(403);

      const { statusCode: putStatus } = await imageClient.put(defaultImageId, {});
      expect(putStatus).toEqual(403);
    });

    test('Admin user cannot GET/PUT', async () => {
      await imageClient.setUser(adminUser);

      const { statusCode: listStatus } = await imageClient.get();
      expect(listStatus).toEqual(403);

      const { statusCode: getStatus } = await imageClient.get(defaultImageId);
      expect(getStatus).toEqual(403);

      const { statusCode: putStatus } = await imageClient.put(defaultImageId, {});
      expect(putStatus).toEqual(403);
    });
  });

  describe('DELETE ACLS: owner and editor/admins can delete', () => {
    test('Guest cannot DELETE', async () => {
      imageClient.setToken(null);
      const { statusCode } = await imageClient.delete(defaultImageId);
      expect(statusCode).toEqual(403);
    });

    test('Plain user cannot DELETE', async () => {
      await imageClient.setUser(plainUser);
      const { statusCode } = await imageClient.delete(defaultImageId);
      expect(statusCode).toEqual(403);
    });

    test('Editor user can DELETE', async () => {
      await imageClient.setUser(editorUser);
      const { statusCode } = await imageClient.delete(images[0].id);
      expect(statusCode).toEqual(200);
    });

    test('Admin user can DELETE', async () => {
      await imageClient.setUser(adminUser);
      const { statusCode } = await imageClient.delete(images[1].id);
      expect(statusCode).toEqual(200);
    });
  });
});

describe('Media tests', () => {
  const mediaClient = new ResourceClient(client, MEDIA);

  describe('Media GET ACLs', () => {
    const media0PublicInfo =
      '["album","date","description","height","id","imageId","lastEditionDate","lastEditor",' +
      '"mediaSubType","mediaType","status","submitter","title","vendorKey","width"]';
    // TODO: rajouter author

    describe('Valid photo', () => {
      test('Guest can see public info', async () => {
        await mediaClient.setToken(null);
        const { statusCode, body } = await mediaClient.get(validPhoto.id);
        expect(statusCode).toEqual(200);
        expect(getSortedKeysAsString(body)).toEqual(media0PublicInfo);
      });

      test('Owner can see public info', async () => {
        await mediaClient.setUser(writerUser);
        const { statusCode, body } = await mediaClient.get(validPhoto.id);
        expect(statusCode).toEqual(200);
        expect(getSortedKeysAsString(body)).toEqual(media0PublicInfo);
      });

      test('Editor can see', async () => {
        await mediaClient.setUser(editorUser);
        const { statusCode, body } = await mediaClient.get(validPhoto.id);
        expect(statusCode).toEqual(200);
        expect(getSortedKeysAsString(body)).toEqual(media0PublicInfo);
      });

      test('Admin can see', async () => {
        await mediaClient.setUser(adminUser);
        const { statusCode, body } = await mediaClient.get(validPhoto.id);
        expect(statusCode).toEqual(200);
        expect(getSortedKeysAsString(body)).toEqual(media0PublicInfo);
      });
    });

    describe('Invalid photo', () => {
      test('Guest cannot see', async () => {
        await mediaClient.setToken(null);
        const { statusCode } = await mediaClient.get(invalidPhoto.id);
        expect(statusCode).toEqual(403);
      });

      test('Owner can see', async () => {
        await mediaClient.setUser(plainUser);
        const { statusCode, body } = await mediaClient.get(invalidPhoto.id);
        expect(statusCode).toEqual(200);
        expect(getSortedKeysAsString(body)).toEqual(media0PublicInfo);
      });

      test('Writer cannot see', async () => {
        await mediaClient.setUser(writerUser);
        const { statusCode } = await mediaClient.get(invalidPhoto.id);
        expect(statusCode).toEqual(403);
      });

      test('Editor can see', async () => {
        await mediaClient.setUser(editorUser);
        const { statusCode, body } = await mediaClient.get(invalidPhoto.id);
        expect(statusCode).toEqual(200);
        expect(getSortedKeysAsString(body)).toEqual(media0PublicInfo);
      });

      test('Admin can see', async () => {
        await mediaClient.setUser(adminUser);
        const { statusCode, body } = await mediaClient.get(invalidPhoto.id);
        expect(statusCode).toEqual(200);
        expect(getSortedKeysAsString(body)).toEqual(media0PublicInfo);
      });
    });
  });

  describe('Photo tests', () => {
    const existingImageId = images[2].id;

    describe('POST ACLs', () => {
      test('Guest cannot POST', async () => {
        await mediaClient.setToken(null);
        const { statusCode } = await mediaClient.post({
          mediaType: PHOTO,
          albumId: plainUser.albumId,
          title: 'A new photo title',
          description: 'A new photo description',
          imageId: existingImageId,
          storageType: 0,
        });
        expect(statusCode).toEqual(403);
      });
    });

    describe('Successful POST', () => {
      test('Plain user can POST', async () => {
        await mediaClient.setUser(plainUser);
        const { statusCode, body } = await mediaClient.post({
          mediaType: PHOTO,
          albumId: plainUser.albumId,
          title: 'A new photo title',
          description: 'A new photo description',
          imageId: existingImageId,
          storageType: 0,
        });
        expect(statusCode).toEqual(200);
        expect(looksLikeUUID(body.id)).toBeTruthy();
      });
    });

    describe('Failing POST', () => {
      // Missing/bad imageId
      // Bad albumId (aggregate album, someone else's album),
      // Bad storageType
    });
  });

  describe('Video tests', () => {
    const createdVideoKeys =
      '["album","date","description","height","id","imageId","lastEditionDate","lastEditor",' +
      '"mediaSubType","mediaType","status","submitter","title","vendorKey","width"]';
    // TODO: rajouter author

    describe('POST ACLs', () => {});

    describe('Successful POST', () => {
      test('YouTube video', async () => {
        mediaClient.setLocalVideoThumb(LOCAL_THUMB_PATH);
        await mediaClient.setUser(plainUser);
        const { statusCode, body } = await mediaClient.post({
          mediaType: VIDEO,
          mediaSubType: YOUTUBE,
          vendorKey: '1PcGJIjhQjg',
          albumId: plainUser.albumId,
          title: 'A new YouTube video title',
          description: 'A new YouTube video description',
          storageType: 0,
        });
        expect(statusCode).toEqual(200);
        expect(getSortedKeysAsString(body)).toEqual(createdVideoKeys);
        expect(looksLikeUUID(body.id)).toBeTruthy();
      });

      test('Vimeo video', async () => {
        await mediaClient.setUser(plainUser);
        mediaClient.setLocalVideoThumb(LOCAL_THUMB_PATH);
        const { statusCode, body } = await mediaClient.post({
          mediaType: VIDEO,
          mediaSubType: VIMEO,
          vendorKey: '16567910',
          albumId: plainUser.albumId,
          title: 'A new Vimeo video title',
          description: 'A new Vimeo video description',
          storageType: 0,
        });
        expect(statusCode).toEqual(200);
        expect(getSortedKeysAsString(body)).toEqual(createdVideoKeys);
        expect(looksLikeUUID(body.id)).toBeTruthy();
      });

      test('Facebook video', async () => {
        await mediaClient.setUser(plainUser);
        mediaClient.setLocalVideoThumb(LOCAL_THUMB_PATH);
        const { statusCode, body } = await mediaClient.post({
          mediaType: VIDEO,
          mediaSubType: FACEBOOK,
          vendorKey: 'showhey.miyata/videos/1854604844577137',
          albumId: plainUser.albumId,
          title: 'A new Facebook video title',
          description: 'A new Facebook video description',
          storageType: 0,
        });
        expect(statusCode).toEqual(200);
        expect(getSortedKeysAsString(body)).toEqual(createdVideoKeys);
        expect(looksLikeUUID(body.id)).toBeTruthy();
      });

      test('Dailymotion video', async () => {
        await mediaClient.setUser(plainUser);
        mediaClient.setLocalVideoThumb(LOCAL_THUMB_PATH);
        const { statusCode, body } = await mediaClient.post({
          mediaType: VIDEO,
          mediaSubType: DAILYMOTION,
          vendorKey: 'x1buew',
          albumId: plainUser.albumId,
          title: 'A new Dailymotion video title',
          description: 'A new Dailymotion video description',
          storageType: 0,
        });
        expect(statusCode).toEqual(200);
        expect(getSortedKeysAsString(body)).toEqual(createdVideoKeys);
        expect(looksLikeUUID(body.id)).toBeTruthy();
      });

      test('Instagram video', async () => {
        await mediaClient.setUser(plainUser);
        mediaClient.setLocalVideoThumb(LOCAL_THUMB_PATH);
        const { statusCode, body } = await mediaClient.post({
          mediaType: VIDEO,
          mediaSubType: INSTAGRAM,
          vendorKey: 'Bks-3LhgiDQ',
          albumId: plainUser.albumId,
          title: 'A new Instagram video title',
          description: 'A new Instagram video description',
          storageType: 0,
        });
        expect(statusCode).toEqual(200);
        expect(getSortedKeysAsString(body)).toEqual(createdVideoKeys);
        expect(looksLikeUUID(body.id)).toBeTruthy();
      });

      test('Plain user can post to their own static album', async () => {
        await mediaClient.setUser(plainUser);
        mediaClient.setLocalVideoThumb(LOCAL_THUMB_PATH);
        const { statusCode, body } = await mediaClient.post({
          mediaType: VIDEO,
          mediaSubType: YOUTUBE,
          vendorKey: 'RoAV-FSDGlA',
          albumId: plainUserStaticAlbum.id,
          title: 'A failing YouTube video title',
          description: 'A failing YouTube video description',
          storageType: 0,
        });
        expect(statusCode).toEqual(200);
        expect(statusCode).toEqual(200);
        expect(getSortedKeysAsString(body)).toEqual(createdVideoKeys);
        expect(looksLikeUUID(body.id)).toBeTruthy();
        expect(body.album.id).toEqual(plainUserStaticAlbum.id);
      });

    });

    describe('Failing POST', () => {
      // Missing/bad vendorKey
      // Bad albumId (aggregate album, someone else's album) Api_ErrorCodes::MEDIA_BAD_ALBUM_FOR_POST
      // Bad storageType

      test('Invalid mediaSubType fails', async () => {
        await mediaClient.setUser(plainUser);
        mediaClient.setLocalVideoThumb(LOCAL_THUMB_PATH);
        const { statusCode, body } = await mediaClient.post({
          mediaType: VIDEO,
          mediaSubType: 'sds',
          albumId: plainUser.albumId,
          title: 'A new video title',
          description: 'A new video description',
          vendorKey: '1PcGJIjhQjg',
          storageType: 0,
        });
        expect(statusCode).toEqual(400);
        expect(body.errors).toEqual({"mediaSubType": ["invalidType"]});
      });

      test('Invalid vendorKey fails because thumbnail is invalid', async () => {
        mediaClient.setLocalVideoThumb(LOCAL_BAD_THUMB_PATH);
        await mediaClient.setUser(plainUser);
        const { statusCode, body } = await mediaClient.post({
          mediaType: VIDEO,
          mediaSubType: YOUTUBE,
          vendorKey: 'badKey',
          albumId: plainUser.albumId,
          title: 'A failing YouTube video title',
          description: 'A failing YouTube video description',
          storageType: 0,
        });
        expect(statusCode).toEqual(400);
        expect(body.errors.topLevelError.code).toEqual(10011);
      });

      test('Invalid storageType fails', async () => {
        await mediaClient.setUser(plainUser);
        mediaClient.setLocalVideoThumb(LOCAL_THUMB_PATH);
        const { statusCode, body } = await mediaClient.post({
          mediaType: VIDEO,
          mediaSubType: YOUTUBE,
          vendorKey: '1PcGJIjhQjg',
          albumId: plainUser.albumId,
          title: 'A failing YouTube video title',
          description: 'A failing YouTube video description',
          storageType: 27,
        });
        expect(statusCode).toEqual(400);
        expect(body.errors).toEqual({'storageType': ["invalidType"]});
      });

      test('Aggregate albumId fails', async () => {
        await mediaClient.setUser(plainUser);
        mediaClient.setLocalVideoThumb(LOCAL_THUMB_PATH);
        const { statusCode, body } = await mediaClient.post({
          mediaType: VIDEO,
          mediaSubType: YOUTUBE,
          vendorKey: '1PcGJIjhQjg',
          albumId: aggregateAlbum.id,
          title: 'A failing YouTube video title',
          description: 'A failing YouTube video description',
          storageType: 0,
        });
        expect(statusCode).toEqual(400);
        expect(body.errors).toEqual({'albumId': ["albumTypeNotAllowed"]});
      });

      test('Plain user cannot post to another user\'s album', async () => {
        await mediaClient.setUser(plainUser);
        mediaClient.setLocalVideoThumb(LOCAL_THUMB_PATH);
        const { statusCode, body } = await mediaClient.post({
          mediaType: VIDEO,
          mediaSubType: YOUTUBE,
          vendorKey: '1PcGJIjhQjg',
          albumId: editorUserStaticAlbum.id,
          title: 'A failing YouTube video title',
          description: 'A failing YouTube video description',
          storageType: 0,
        });
        expect(statusCode).toEqual(400);
        expect(body.errors).toEqual({'albumId': ["albumNotWritable"]});
      });

      test('Plain user cannot post to non-existing album', async () => {
        await mediaClient.setUser(plainUser);
        mediaClient.setLocalVideoThumb(LOCAL_THUMB_PATH);
        const { statusCode, body } = await mediaClient.post({
          mediaType: VIDEO,
          mediaSubType: YOUTUBE,
          vendorKey: '1PcGJIjhQjg',
          albumId: 'nachoalbum',
          title: 'A failing YouTube video title',
          description: 'A failing YouTube video description',
          storageType: 0,
        });
        expect(statusCode).toEqual(400);
        expect(body.errors).toEqual({'albumId': ["doesNotExist"]});
      });
    });
  });
});

describe.skip('Album tests', () => {
  /*
   * Tous les utilisateurs ont un album public associe
   */
});
