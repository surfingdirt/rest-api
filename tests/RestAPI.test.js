import ResourceClient from './RestClient/ResourceClient';
import { default as StatelessClient, getResourcePath } from './RestClient/StatelessClient';
import { clearCacheFiles } from './RestClient/cache';
import { hostUrl, JWT_TTL, LOCAL_BAD_THUMB_PATH, LOCAL_THUMB_PATH } from './RestClient/constants';
import {
  ALBUM,
  COMMENT,
  IMAGE,
  MEDIA,
  MEDIA_SUBTYPES_VIDEO,
  MEDIA_TYPES,
  REACTION,
  TOKEN,
  USER,
} from './RestClient/resources';
import { images } from './data/images';
import { invalidPhoto, validPhoto } from './data/media';
import {
  aggregateAlbum,
  editorUserStaticPrivateAlbum,
  editorUserStaticPublicAlbum,
  plainUserStaticAlbum,
} from './data/albums';
import {
  photoIdForComments,
  singleComment,
  translatedComment,
  XSSComment,
  invalidComment,
  commentsForUpdate,
  commentsForDelete,
  commentsForBatch,
  commentsForReactionDelete,
} from './data/comments';
import {
  laughingReaction,
  scaredReaction,
  angryReaction,
  scaredReactionForDelete,
  albumForReactions,
} from './data/reactions';
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
import {
  getDateForBackend,
  getSortedKeysAsString,
  uuidv4,
  looksLikeUUID,
  checkSuccess,
  checkBadRequest,
  checkUnauthorised,
  checkNotFound,
} from './RestClient/utils';

const { PHOTO, VIDEO } = MEDIA_TYPES;
const { DAILYMOTION, FACEBOOK, INSTAGRAM, VIMEO, YOUTUBE } = MEDIA_SUBTYPES_VIDEO;
const plainUserPath = getResourcePath(USER, plainUser.id);
const tokenPath = getResourcePath(TOKEN);

const oldP = 'abc123def';
const newPassword = 'thisisthenewpassword';

const ME = 'me';

const client = new StatelessClient(hostUrl);

beforeAll(async (done) => {
  await cleanupTestDatabase();
  // await client.clearPublicFiles();
  done();
});

beforeEach(() => {
  clearCacheFiles();
  return client.clearCache();
});

describe('Token tests', () => {
  test('logged-out request results in 200', async () => {
    const path = plainUserPath;
    const body = checkSuccess(await client.get({ path }));
    expect(Object.keys(body).length > 0).toBeTruthy();
  });

  test('banned user login request results in 403', async () => {
    const { username, password } = bannedUser;
    checkUnauthorised(
      await client.post({
        path: tokenPath,
        data: { userP: password, username: username },
      }),
    );
  });

  test('invalid token results in 403', async () => {
    const path = plainUserPath;
    const token = 'no-way-this-is-gonna-work';
    checkUnauthorised(await client.get({ path, token }));
  });

  describe('End to end', () => {
    test('token management is working properly', async () => {
      let user1Token;

      const { username, password } = plainUser;

      // Login request as user 1
      const loginResponseBody = checkSuccess(
        await client.post({
          path: tokenPath,
          data: { userP: password, username: username },
        }),
      );
      expect(loginResponseBody.token).toBeDefined();
      user1Token = loginResponseBody.token;

      // Tweak server time to be seconds after the expiration date
      const afterExpirationDate = getDateForBackend(JWT_TTL + 2);
      await client.setDate(afterExpirationDate);

      // Request user 1 with user1Token while server thinks it's the future
      checkUnauthorised(
        await client.get({
          path: plainUserPath,
          token: user1Token,
        })
      );
      // Sets time back
      await client.setDate(null);

      // Request user 1 with user1Token while server thinks it's the present again
      checkSuccess(await client.get({ path: plainUserPath, token: user1Token }));

      // Delete token as self (ie, logout)
      checkSuccess(
        await client.delete({
          path: getResourcePath(TOKEN),
          token: user1Token,
        }),
      );

      // Request user 1 with blacklisted user1Token
      checkUnauthorised(await client.get({ path: plainUserPath, token: user1Token }), 403);
    });
  });
});

describe('User tests', () => {
  const userClient = new ResourceClient(client, USER);

  const createNewMember = async (username, email, userId = null) => {
    await userClient.clearToken();
    if (userId) {
      userClient.setUUIDs([userId]);
    }
    const localNewUser = checkSuccess(
      await userClient.post({
        username,
        userP: oldP,
        userPC: oldP,
        email,
      }),
    );

    await userClient.setUser(adminUser);
    checkSuccess(await userClient.put(localNewUser.userId, { status: 'member' }));

    return localNewUser;
  };

  describe('Error cases', () => {
    test('Missing user request should return a 404', async () => {
      checkNotFound(await userClient.get(2500), 404);
    });

    test('Banned user request should return a 403', async () => {
      checkUnauthorised(await userClient.get(bannedUser.id), 403);
    });

    test('Pending user request should return a 403', async () => {
      checkUnauthorised(await userClient.get(pendingUser.id));
    });
  });

  describe('User GET', () => {
    const plainUserPublicInfo =
      '["actions","album","avatar","bio","city","cover","date","firstName","lastName","locale","site","timezone","userId","username"]';

    const plainUserSelfInfo =
      '["actions","album","avatar","bio","city","cover","date","email","firstName","lastName","locale","site","status","timezone","userId","username"]';

    const plainUserAdminInfo =
      '["actions","album","avatar","bio","city","cover","date","email","firstName","lastLogin","lastName","locale","site","status","timezone","userId","username"]';

    test("Retrieve plainuser's data as guest", async () => {
      userClient.clearToken();
      const body = checkSuccess(await userClient.get(plainUser.id));
      expect(getSortedKeysAsString(body)).toEqual(plainUserPublicInfo);
    });

    test("Retrieve plainuser's data as other user", async () => {
      await userClient.setUser(otherUser);
      const body = checkSuccess(await userClient.get(plainUser.id));
      expect(getSortedKeysAsString(body)).toEqual(plainUserPublicInfo);
    });

    test("Retrieve plainuser's data as writer user", async () => {
      await userClient.setUser(writerUser);
      const body = checkSuccess(await userClient.get(plainUser.id));
      expect(getSortedKeysAsString(body)).toEqual(plainUserPublicInfo);
    });

    test("Retrieve plainuser's data as editor", async () => {
      await userClient.setUser(editorUser);
      const body = checkSuccess(await userClient.get(plainUser.id));
      expect(getSortedKeysAsString(body)).toEqual(plainUserPublicInfo);
    });

    test("Retrieve plainuser's data as admin", async () => {
      await userClient.setUser(adminUser);
      const body = checkSuccess(await userClient.get(plainUser.id));
      expect(getSortedKeysAsString(body)).toEqual(plainUserAdminInfo);
    });

    test("Retrieve plainuser's data as self", async () => {
      await userClient.setUser(plainUser);
      const body = checkSuccess(await userClient.get(plainUser.id));
      expect(getSortedKeysAsString(body)).toEqual(plainUserSelfInfo);
    });
  });

  describe('User GET /me', () => {
    const meKeys =
      '["actions","album","avatar","bio","city","cover","date","email","firstName","lastName","locale","site","status","timezone","userId","username"]';

    test('Retrieve /user/me as guest', async () => {
      userClient.clearToken();
      const body = checkSuccess(await userClient.get(ME));
      expect(getSortedKeysAsString(body)).toEqual(meKeys);
      expect(body.album).toEqual(null);
      expect(body.status).toEqual('guest');
    });

    test('Retrieve /user/me as plainuser', async () => {
      await userClient.setUser(plainUser);
      const body = checkSuccess(await userClient.get(ME));
      expect(getSortedKeysAsString(body)).toEqual(meKeys);
      expect(body.status).toEqual('member');
    });
  });

  describe('User list GET', () => {
    test('Retrieve all valid users as guest', async () => {
      userClient.clearToken();
      const body = checkSuccess(await userClient.list());
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
      const body = checkSuccess(await userClient.list());
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
      userClient.clearToken();
      const body = checkSuccess(await userClient.list({ start: 2, count: 2 }));
      const userIds = body.map((u) => u.userId);
      expect(userIds).toEqual([editorUser.id, writerUser.id]);
    });

    test('Retrieve 2nd and 3rd valid users sorted by username ascending as guest', async () => {
      userClient.clearToken();
      const body = checkSuccess(await userClient.list({ start: 1, count: 2, sort: 'username' }));
      const userIds = body.map((u) => u.userId);
      expect(userIds).toEqual([editorUser.id, otherUser.id]);
    });

    test('Retrieve 2nd and 3rd valid users sorted by username descending as guest', async () => {
      userClient.clearToken();
      const body = checkSuccess(
        await userClient.list({ start: 1, count: 2, sort: 'username', dir: 'desc' }),
      );
      const userIds = body.map((u) => u.userId);
      expect(userIds).toEqual([plainUser.id, otherUser.id]);
    });
  });

  describe('User POST', () => {
    const createdUserKeys =
      '["actions","album","avatar","bio","city","cover","date","email","firstName","lastName","locale","site","status","timezone","userId","username"]';

    test('Logged-in user cannot create a new user', async () => {
      await userClient.setUser(plainUser);
      checkUnauthorised(await userClient.post({}));
    });

    test('Guest user cannot create a new user with invalid data', async () => {
      await userClient.clearToken();
      checkBadRequest(await userClient.post({}));
    });

    test('Successful user creation should return an id', async () => {
      await userClient.clearToken();
      userClient.setUUIDs([createdUser.id]);
      const body = checkSuccess(
        await userClient.post({
          username: createdUser.username,
          userP: createdUser.password,
          userPC: createdUser.password,
          email: createdUser.email,
        }),
      );
      expect(getSortedKeysAsString(body)).toEqual(createdUserKeys);
      expect(looksLikeUUID(body.userId)).toBeTruthy();
      expect(body.userId).toEqual(createdUser.id);
    });
  });

  describe('User PUT', () => {
    test('Admin can change user status', async () => {
      await userClient.setUser(adminUser);
      const body = checkSuccess(await userClient.put(createdUser.id, { status: 'member' }));
      expect(body.status).toEqual('member');
    });

    test('Plain user cannot update new user', async () => {
      await userClient.setUser(plainUser);
      checkUnauthorised(await userClient.put(createdUser.id, { firstName: 'nope' }));
    });

    test('Plain user can update their account', async () => {
      await userClient.setUser({ id: createdUser.id });
      const body = checkSuccess(await userClient.put(createdUser.id, { firstName: 'yes' }));
      expect(body.firstName).toEqual('yes');
    });

    test('Plain user can update their bio', async () => {
      await userClient.setUser({ id: createdUser.id });
      const body = checkSuccess(await userClient.put(createdUser.id, { bio: [{locale: 'en-US', text: 'modified bio' }]}));
      expect(body.bio).toEqual([{ locale: 'en-US', text: 'modified bio', original: true}]);
    });

    test('Requests with password mismatch are rejected', async () => {
      await userClient.setUser({ id: createdUser.id });
      const body = checkBadRequest(
        await userClient.put(createdUser.id, {
          userP: '12345678',
          userPC: '34567890',
          userPO: 'irrelevant',
        }),
      );
      expect(body).toEqual({ code: 16001, errors: { userPC: ['notSame'] } });

      const body2 = checkBadRequest(
        await userClient.put(createdUser.id, {
          userP: '12345678',
          userPO: 'irrelevant',
        }),
      );
      expect(body2).toEqual({ code: 16001, errors: { userPC: ['notSame'] } });

      const body3 = checkBadRequest(
        await userClient.put(createdUser.id, {
          userPC: '12345678',
          userPO: 'irrelevant',
        }),
      );
      expect(body3).toEqual({
        code: 16001,
        errors: { userP: ['notEmptyInvalid'], userPC: ['missingReference'] },
      });
    });

    test('Requests with matching passwords are successful, and old password is made invalid', async () => {
      const username = 'userForPasswordUpdate';
      const email = 'update@here.com';
      const localNewUser = await createNewMember(username, email);

      await userClient.setUser({ id: localNewUser.userId });
      checkSuccess(
        await userClient.put(localNewUser.userId, {
          userPO: oldP,
          userP: newPassword,
          userPC: newPassword,
        }),
      );

      userClient.clearToken();
      let errorMessageAfterUpdate = null;
      try {
        await userClient.setUser({
          username: username,
          password: oldP,
        });
      } catch (e) {
        errorMessageAfterUpdate = e.message;
      }
      expect(errorMessageAfterUpdate).toEqual(`Login as '${username}' failed`);

      userClient.clearToken();
      expect(async () => {
        await userClient.setUser({ username: username, password: newPassword });
      }).not.toThrow();
    });

    test.skip('Requests with matching passwords but inadequate new password are rejected', async () => {
      const username = 'userForPasswordUpdate2';
      const email = 'update2@here.com';
      const localNewUser = await createNewMember(username, email);

      await userClient.setUser({ id: localNewUser.userId });
      const resp = await userClient.put(localNewUser.userId, {
        userP: '1',
        userPC: '1',
        userPO: oldP,
      });

      const body = checkBadRequest(resp);
      expect(body).toEqual({ code: 16001, errors: { userP: ['tooShort'] } });
    });
  });

  describe('User DELETE', () => {
    let userIdToDelete;
    const userToDelete = { username: 'userToDelete', password: '123456789' };

    beforeAll(async () => {
      await userClient.clearToken();
      const body = checkSuccess(
        await userClient.post({
          username: userToDelete.username,
          userP: userToDelete.password,
          userPC: userToDelete.password,
          email: 'deleteme@gmail.com',
        }),
      );
      userIdToDelete = body.userId;

      await userClient.setUser(adminUser);
      checkSuccess(await userClient.put(userIdToDelete, { status: 'member' }));
    });

    test('Guest cannot delete a user', async () => {
      userClient.clearToken();
      checkUnauthorised(await userClient.delete(userIdToDelete));
    });

    test('User cannot delete their account', async () => {
      await userClient.setUser(userToDelete);
      checkUnauthorised(await userClient.delete(userIdToDelete));
    });

    test('Admin can delete an account', async () => {
      await userClient.setUser(adminUser);
      checkSuccess(await userClient.delete(userIdToDelete));

      userClient.clearToken();
      checkNotFound(await userClient.delete(userIdToDelete));
    });
  });

  describe('User account management', () => {
    test('Complete lost password workflow', async () => {
      const username = 'forgetter';
      const email = 'forgetter@gmail.com';
      const activationKey = 'randomkeyfortest';
      // Create a new member
      const userId = uuidv4();
      const newUser = await createNewMember(username, email, userId);

      const lostPasswordBody = checkSuccess(
        await client.post({ path: '/lost-password/', data: { username }}),
      );
      // Note: this is not the expected response in production mode.
      // This test is here to exercise the backend code and make sure it does not crash before
      // generating a key.
      expect(lostPasswordBody).toEqual({
        activationKey,
        email,
        userId,
        username,
        newPassword: 'randomkeyfortest',
      });

      checkNotFound(
        await client.post({
          path: `/user/${userId}/activate-new-password/`,
          data: { aK: 'badkey' },
        }),
      );
      checkNotFound(
        await client.post({
          path: `/user/bad-user-id/activate-new-password/`,
          data: { aK: activationKey },
        }),
      );
      const body = checkSuccess(
        await client.post({
          path: `/user/${userId}/activate-new-password/`,
          data: { aK: activationKey },
        }),
      );
      expect(body).toEqual({ 'status': true});

      try {
        await client.login(username, oldP);
        throw new Error('Should have failed to login with old password');
      } catch(e) {
        expect(e.message).toEqual(`Login as '${username}' failed`);
      }

      try {
        await client.login(username, 'randomkeyfortest');
      } catch(e) {
        throw new Error('Failed to login with new password');
      }
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
      const body = checkBadRequest(await imageClient.postFormData({ type }, []));
      expect(body.errors.topLevelError.code).toEqual(10010);
    });

    test('Plain user POST fails because file is not actually an image', async () => {
      await imageClient.setUser(plainUser);
      const body = checkBadRequest(await imageClient.postFormData({ type }, [textFileAsJPEG]));
      expect(body.errors.topLevelError.code).toEqual(10011);
    });

    test('Plain user POST fails because file is a text file', async () => {
      await imageClient.setUser(plainUser);
      const body = checkBadRequest(await imageClient.postFormData({ type }, [textFileAsTxt]));
      expect(body.errors.topLevelError.code).toEqual(10011);
    });

    test('Plain user POST fails because image file size is too big', async () => {
      await imageClient.setUser(plainUser);
      const body = checkBadRequest(await imageClient.postFormData({ type }, [imgHeavy]));
      expect(body.errors.topLevelError.code).toEqual(10002);
    });

    test('Plain user POST fails because storage type is not supported', async () => {
      await imageClient.setUser(plainUser);
      const body = checkBadRequest(await imageClient.postFormData({ type: 1 }, [img640]));
      expect(body.errors.topLevelError.code).toEqual(10001);
    });

    test('Plain user POST fails because uuid already exists', async () => {
      await imageClient.setUser(plainUser);
      imageClient.setUUIDs([images[0].id]);
      const body = checkBadRequest(await imageClient.postFormData({ type }, [img640]));
      expect(body.errors.topLevelError.code).toEqual(10008);
    });
  });

  describe('POST success cases', () => {
    test('Plain user POST succeeds and returns the right dimensions', async () => {
      await imageClient.setUser(plainUser);
      const [{ key, width, height }] = checkSuccess(
        await imageClient.postFormData({ type }, [img640]),
      );
      expect(looksLikeUUID(key)).toBeTruthy();
      expect(parseInt(width, 10)).toEqual(img640.width);
      expect(parseInt(height, 10)).toEqual(img640.height);
    });

    test('Plain user POST succeeds even when image dimensions are brought down', async () => {
      await imageClient.setUser(plainUser);
      const [{ key, width, height }] = checkSuccess(
        await imageClient.postFormData({ type }, [img3000]),
      );
      expect(looksLikeUUID(key)).toBeTruthy();
      expect(parseInt(width, 10)).toEqual(MAX_WIDTH);
      expect(parseInt(height, 10)).toEqual(MAX_HEIGHT);
    });
  });

  describe('POST ACLS: all and only bad users get a 403', () => {
    test('Guest cannot POST', async () => {
      await imageClient.clearToken();
      checkUnauthorised(await imageClient.postFormData({}));
    });

    test('Pending user cannot POST', async () => {
      await imageClient.setUser(pendingUser);
      checkUnauthorised(await imageClient.postFormData({}));
    });

    test('Banned user cannot POST', async () => {
      await imageClient.setUser(bannedUser);
      checkUnauthorised(await imageClient.postFormData({}));
    });

    test('Plain user can POST', async () => {
      await imageClient.setUser(plainUser);
      checkSuccess(await imageClient.postFormData({ type: 0 }, [img640]));
    });

    test('Writer user can POST', async () => {
      await imageClient.setUser(writerUser);
      checkSuccess(await imageClient.postFormData({ type: 0 }, [img640]));
    });

    test('Editor user can POST', async () => {
      await imageClient.setUser(editorUser);
      checkSuccess(await imageClient.postFormData({ type: 0 }, [img640]));
    });

    test('Admin can POST', async () => {
      await imageClient.setUser(adminUser);
      checkSuccess(await imageClient.postFormData({ type: 0 }, [img640]));
    });
  });

  describe('GET ACLS: listing is forbidden but individual requests are ok', () => {
    const defaultImageInfo = '["height","imageId","storageType","type","width"]';

    test('Guest cannot list but can see individual images', async () => {
      imageClient.clearToken();

      checkUnauthorised(await imageClient.get());

      const body = checkSuccess(await imageClient.get(defaultImageId));
      expect(getSortedKeysAsString(body)).toEqual(defaultImageInfo);
    });

    test('Plain user cannot list but can see individual images', async () => {
      imageClient.clearToken();

      checkUnauthorised(await imageClient.get());

      const body = checkSuccess(await imageClient.get(defaultImageId));
      expect(getSortedKeysAsString(body)).toEqual(defaultImageInfo);
    });

    test('Admin user cannot list but can see individual images', async () => {
      imageClient.clearToken();

      checkUnauthorised(await imageClient.get());

      const body = checkSuccess(await imageClient.get(defaultImageId));
      expect(getSortedKeysAsString(body)).toEqual(defaultImageInfo);
    });
  });

  describe('PUT ACLS: everyone gets a 403', () => {
    test('Guest cannot PUT', async () => {
      imageClient.clearToken();

      checkUnauthorised(await imageClient.put(defaultImageId, {}));
    });

    test('Plain user cannot PUT', async () => {
      await imageClient.setUser(plainUser);

      checkUnauthorised(await imageClient.get());

      checkUnauthorised(await imageClient.put(defaultImageId, {}));
    });

    test('Admin user cannot PUT', async () => {
      await imageClient.setUser(adminUser);

      checkUnauthorised(await imageClient.put(defaultImageId, {}));
    });
  });

  describe('DELETE ACLS: owner and editor/admins can delete writer image', () => {
    test('Guest cannot DELETE', async () => {
      imageClient.clearToken();
      checkUnauthorised(await imageClient.delete(defaultImageId));
    });

    test('Plain user cannot DELETE', async () => {
      await imageClient.setUser(plainUser);
      checkUnauthorised(await imageClient.delete(defaultImageId));
    });

    test('Editor user can DELETE', async () => {
      await imageClient.setUser(editorUser);
      checkSuccess(await imageClient.delete(images[0].id));
    });

    test('Admin user can DELETE', async () => {
      await imageClient.setUser(adminUser);
      checkSuccess(await imageClient.delete(images[1].id));
    });
  });
});

describe('Media tests', () => {
  const mediaClient = new ResourceClient(client, MEDIA);

  const postImageAs = async (user) => {
    const imageClient = new ResourceClient(client, IMAGE);
    await imageClient.setUser(user);
    const body = checkSuccess(await imageClient.postFormData({ type: 0 }, [img640]));
    return body[0].key;
  };

  const postPhotoAs = async (user, imageId) => {
    await mediaClient.setUser(user);
    const body = checkSuccess(
      await mediaClient.post({
        mediaType: PHOTO,
        albumId: user.albumId,
        title: [{ locale: 'en-US', text: 'A new video title'}],
        description: [{ locale: 'en-US', text: 'A new video description'}],
        imageId: imageId,
        storageType: 0,
      }),
    );
    return body;
  };

  const postVideoAs = async (user, vendorKey) => {
    await mediaClient.setUser(user);
    mediaClient.setLocalVideoThumb(LOCAL_THUMB_PATH);
    const body = checkSuccess(
      await mediaClient.post({
        mediaType: VIDEO,
        mediaSubType: YOUTUBE,
        albumId: user.albumId,
        title: [{ locale: 'en-US', text: 'A new video title'}],
        description: [{ locale: 'en-US', text: 'A new video description'}],
        vendorKey,
        storageType: 0,
      }),
    );
    return body;
  };

  describe('GET', () => {
    const media0PublicInfo =
      '["actions","album","date","description","height","id","imageId","lastEditionDate","lastEditor",' +
      '"mediaSubType","mediaType","status","storageType","submitter","thumbHeight","thumbWidth","title","users",' +
      '"vendorKey","width"]';
    // TODO: rajouter author

    describe('Valid photo', () => {
      test('Guest can see public info', async () => {
        await mediaClient.clearToken();
        const body = checkSuccess(await mediaClient.get(validPhoto.id));
        expect(getSortedKeysAsString(body)).toEqual(media0PublicInfo);
        expect(body.actions.edit).toEqual(false);
        expect(body.actions.delete).toEqual(false);
      });

      test('Owner can see public info', async () => {
        await mediaClient.setUser(writerUser);
        const body = checkSuccess(await mediaClient.get(validPhoto.id));
        expect(getSortedKeysAsString(body)).toEqual(media0PublicInfo);
        expect(body.actions.edit).toEqual(true);
        expect(body.actions.delete).toEqual(true);
      });

      test('Editor can see public info', async () => {
        await mediaClient.setUser(editorUser);
        const body = checkSuccess(await mediaClient.get(validPhoto.id));
        expect(getSortedKeysAsString(body)).toEqual(media0PublicInfo);
        expect(body.actions.edit).toEqual(true);
        expect(body.actions.delete).toEqual(true);
      });

      test('Admin can see public info', async () => {
        await mediaClient.setUser(adminUser);
        const body = checkSuccess(await mediaClient.get(validPhoto.id));
        expect(getSortedKeysAsString(body)).toEqual(media0PublicInfo);
        expect(body.actions.edit).toEqual(true);
        expect(body.actions.delete).toEqual(true);
      });
    });

    describe('Invalid photo', () => {
      test('Guest cannot see invalid photo', async () => {
        await mediaClient.clearToken();
        checkUnauthorised(await mediaClient.get(invalidPhoto.id));
      });

      test('Owner can see invalid photo', async () => {
        await mediaClient.setUser(plainUser);
        const body = checkSuccess(await mediaClient.get(invalidPhoto.id));
        expect(getSortedKeysAsString(body)).toEqual(media0PublicInfo);
        expect(body.actions.edit).toEqual(true);
        expect(body.actions.delete).toEqual(true);
      });

      test('Writer cannot see invalid photo', async () => {
        await mediaClient.setUser(writerUser);
        checkUnauthorised(await mediaClient.get(invalidPhoto.id));
      });

      test('Editor can see invalid photo', async () => {
        await mediaClient.setUser(editorUser);
        const body = checkSuccess(await mediaClient.get(invalidPhoto.id));
        expect(getSortedKeysAsString(body)).toEqual(media0PublicInfo);
        expect(body.actions.edit).toEqual(true);
        expect(body.actions.delete).toEqual(true);
      });

      test('Admin can see invalid photo', async () => {
        await mediaClient.setUser(adminUser);
        const body = checkSuccess(await mediaClient.get(invalidPhoto.id));
        expect(getSortedKeysAsString(body)).toEqual(media0PublicInfo);
        expect(body.actions.edit).toEqual(true);
        expect(body.actions.delete).toEqual(true);
      });
    });

    describe('Batch GET', () => {
      test('Owner can get 2 valid photos', async() => {
        await mediaClient.setUser(plainUser);
        const body = checkSuccess(await mediaClient.batchGet([validPhoto.id, invalidPhoto.id]));
        expect(body.length).toEqual(2);
      })

      test('Invalid photos are not part of batch responses for guest ', async() => {
        await mediaClient.clearToken();
        const body = checkSuccess(await mediaClient.batchGet([validPhoto.id, invalidPhoto.id]));
        expect(body.length).toEqual(1);
      })
    });
  });

  describe('PUT ACLs', () => {
    test("Guest can't PUT", async () => {
      await mediaClient.clearToken();
      checkUnauthorised(
        await mediaClient.put(invalidPhoto.id, {
          title: [{ locale: 'en-US', text: 'Modified title'}],
        }),
      );
    });

    test("Writer can't PUT", async () => {
      await mediaClient.setUser(writerUser);
      checkUnauthorised(
        await mediaClient.put(invalidPhoto.id, {
          title: [{ locale: 'en-US', text: 'Modified title'}],
        }),
      );
    });

    test('Owner can PUT', async () => {
      await mediaClient.setUser(plainUser);
      await mediaClient.get(invalidPhoto.id);
      const body = checkSuccess(
        await mediaClient.put(invalidPhoto.id, {
          title: [{ locale: 'en-US', text: 'Modified title'}],
        }),
      );
      expect(body.title).toEqual([{ locale: 'en-US', text: 'Modified title', original: true}]);
    });

    test('Editor can PUT', async () => {
      await mediaClient.setUser(editorUser);
      const body = checkSuccess(
        await mediaClient.put(invalidPhoto.id, {
          title: [{ locale: 'en-US', text: 'Modified title2'}],
        }),
      );
      expect(body.title).toEqual([{ locale: 'en-US', text: 'Modified title2', original: true}]);
    });

    test('Admin can PUT', async () => {
      await mediaClient.setUser(adminUser);
      const body = checkSuccess(
        await mediaClient.put(invalidPhoto.id, {
          title: [{ locale: 'en-US', text: 'Modified title3'}],
        }),
      );
      expect(body.title).toEqual([{ locale: 'en-US', text: 'Modified title3', original: true}]);
    });
  });

  describe('Photo tests', () => {
    const existingImageId = images[2].id;
    const secondExistingImageId = images[3].id;
    const thirdExistingImageId = images[4].id;
    const fourthExistingImageId = images[6].id;

    describe('POST', () => {
      describe('ACLs', () => {
        test('Guest cannot POST', async () => {
          await mediaClient.clearToken();
          checkUnauthorised(
            await mediaClient.post({
              mediaType: PHOTO,
              albumId: plainUser.albumId,
              title: [{ locale: 'en-US', text: 'A new video title'}],
              description: [{ locale: 'en-US', text: 'A new video description'}],
              imageId: existingImageId,
              storageType: 0,
            }),
          );
        });
      });

      describe('Successes', () => {
        test('Plain user can POST', async () => {
          await mediaClient.setUser(plainUser);
          const body = checkSuccess(
            await mediaClient.post({
              mediaType: PHOTO,
              albumId: plainUser.albumId,
              title: [{ locale: 'en-US', text: 'A new video title'}],
              description: [{ locale: 'en-US', text: 'A new video description'}],
              imageId: existingImageId,
              storageType: 0,
            }),
          );
          expect(looksLikeUUID(body.id)).toBeTruthy();

          // Can't post the same image twice
          const bodyDupe = checkBadRequest(
            await mediaClient.post({
              mediaType: PHOTO,
              albumId: plainUser.albumId,
              title: [{ locale: 'en-US', text: 'A new video title'}],
              description: [{ locale: 'en-US', text: 'A new video description'}],
              imageId: existingImageId,
              storageType: 0,
            }),
          );
          expect(bodyDupe.errors).toEqual({ imageId: ['duplicatedImageId'] });
        });

        test('Title and description are not mandatory', async () => {
          await mediaClient.setUser(plainUser);
          const body = checkSuccess(
            await mediaClient.post({
              mediaType: PHOTO,
              albumId: plainUserStaticAlbum.id,
              imageId: thirdExistingImageId,
              storageType: 0,
            }),
          );
          expect(looksLikeUUID(body.id)).toBeTruthy();
          expect(body.album.id).toEqual(plainUserStaticAlbum.id);
        });

        test('Plain user can post to their own static album', async () => {
          await mediaClient.setUser(plainUser);
          const body = checkSuccess(
            await mediaClient.post({
              mediaType: PHOTO,
              albumId: plainUserStaticAlbum.id,
              title: [{ locale: 'en-US', text: 'A new video title'}],
              description: [{ locale: 'en-US', text: 'A new video description'}],
              imageId: secondExistingImageId,
              storageType: 0,
            }),
          );
          expect(looksLikeUUID(body.id)).toBeTruthy();
          expect(body.album.id).toEqual(plainUserStaticAlbum.id);
        });

        test('Photo post results in album lastEditionDate update', async () => {
          const date = getDateForBackend();
          await mediaClient.setDate(date);
          await mediaClient.setUser(plainUser);

          checkSuccess(
            await mediaClient.post({
              mediaType: PHOTO,
              albumId: plainUserStaticAlbum.id,
              title: [{ locale: 'en-US', text: 'A new video title'}],
              description: [{ locale: 'en-US', text: 'A new video description'}],
              imageId: fourthExistingImageId,
              storageType: 0,
            }),
          );

          const albumClient = new ResourceClient(client, ALBUM);
          const { lastEditionDate } = checkSuccess(await albumClient.get(plainUserStaticAlbum.id));
          // expect(lastEditionDate).toEqual(date);

          // Sets time back
          await mediaClient.setDate();
        });
      });

      describe('Failures', () => {
        const notPostedImageId = images[5].id;

        test('Invalid imageId fails', async () => {
          await mediaClient.setUser(plainUser);
          const body = checkBadRequest(
            await mediaClient.post({
              mediaType: PHOTO,
              imageId: 'badImageId',
              albumId: plainUser.albumId,
              title: [{ locale: 'en-US', text: 'A failing photo title'}],
              description: [{ locale: 'en-US', text: 'A failing photo description'}],
              storageType: 0,
            }),
          );
          expect(body.errors).toEqual({ imageId: ['doesNotExist'] });
        });

        test('Aggregate albumId fails', async () => {
          await mediaClient.setUser(plainUser);
          const body = checkBadRequest(
            await mediaClient.post({
              mediaType: PHOTO,
              imageId: notPostedImageId,
              albumId: aggregateAlbum.id,
              title: [{ locale: 'en-US', text: 'A failing photo title'}],
              description: [{ locale: 'en-US', text: 'A failing photo description'}],
              storageType: 0,
            }),
          );
          expect(body.errors).toEqual({ albumId: ['albumTypeNotAllowed'] });
        });

        test("Plain user cannot post to another user's album", async () => {
          await mediaClient.setUser(plainUser);
          const body = checkBadRequest(
            await mediaClient.post({
              mediaType: PHOTO,
              imageId: notPostedImageId,
              albumId: editorUserStaticPrivateAlbum.id,
              title: [{ locale: 'en-US', text: 'A failing photo title'}],
              description: [{ locale: 'en-US', text: 'A failing photo description'}],
              storageType: 0,
            }),
          );
          expect(body.errors).toEqual({ albumId: ['albumNotWritable'] });
        });

        test('Plain user cannot post to non-existing album', async () => {
          await mediaClient.setUser(plainUser);
          const body = checkBadRequest(
            await mediaClient.post({
              mediaType: PHOTO,
              imageId: notPostedImageId,
              albumId: 'nachoAlbum',
              title: [{ locale: 'en-US', text: 'A failing photo title'}],
              description: [{ locale: 'en-US', text: 'A failing photo description'}],
              storageType: 0,
            }),
          );
          expect(body.errors).toEqual({ albumId: ['doesNotExist'] });
        });

        test('Bad storageType fails', async () => {
          await mediaClient.setUser(plainUser);
          const body = checkBadRequest(
            await mediaClient.post({
              mediaType: PHOTO,
              imageId: notPostedImageId,
              albumId: plainUser.albumId,
              title: [{ locale: 'en-US', text: 'A failing photo title'}],
              description: [{ locale: 'en-US', text: 'A failing photo description'}],
              storageType: 27,
            }),
          );
          expect(body.errors).toEqual({ storageType: ['invalidType'] });
        });
      });
    });

    describe('PUT', () => {
      describe('Successes', () => {
        test('Update imageId', async () => {
          const initialImageId = await postImageAs(plainUser);
          const photo = await postPhotoAs(plainUser, initialImageId);
          const secondImageId = await postImageAs(plainUser);

          await mediaClient.setUser(plainUser);
          const body = checkSuccess(
            await mediaClient.put(photo.id, {
              imageId: secondImageId,
            }),
          );
          expect(body.imageId).toEqual(secondImageId);
        });

        test('Update title and description', async () => {
          const initialImageId = await postImageAs(plainUser);
          const photo = await postPhotoAs(plainUser, initialImageId);
          await mediaClient.setUser(plainUser);
          const body = checkSuccess(
            await mediaClient.put(photo.id, {
              title: [{ locale: 'en-US', text: 'a new title'}],
              description: [{ locale: 'en-US', text: 'a new description'}],
            }),
          );
          expect(body.title).toEqual([{ locale: 'en-US', text: 'a new title', original: true}]);
          expect(body.description).toEqual([{ locale: 'en-US', text: 'a new description', original: true}]);
        });
      });

      describe('Failures', () => {
        test('Cannot change mediaType', async () => {
          await mediaClient.setUser(plainUser);
          const body = checkBadRequest(
            await mediaClient.put(invalidPhoto.id, {
              mediaType: VIDEO,
            }),
          );
          expect(body.errors).toEqual({ mediaType: ['immutable'] });
        });

        test('Cannot change storageType', async () => {
          await mediaClient.setUser(plainUser);
          const body = checkBadRequest(
            await mediaClient.put(invalidPhoto.id, {
              storageType: 27,
            }),
          );
          expect(body.errors).toEqual({ storageType: ['immutable'] });
        });

        test('Cannot use an non-existing imageId', async () => {
          await mediaClient.setUser(plainUser);
          const body = checkBadRequest(
            await mediaClient.put(invalidPhoto.id, {
              imageId: 'not-an-id',
            }),
          );
          expect(body.errors).toEqual({ imageId: ['doesNotExist'] });
        });

        test('Cannot use an imageId already used by an other photo', async () => {
          const initialImageId = await postImageAs(plainUser);
          await postPhotoAs(plainUser, initialImageId);
          const secondImageId = await postImageAs(plainUser);
          const secondPhoto = await postPhotoAs(plainUser, secondImageId);

          await mediaClient.setUser(plainUser);
          const body = checkBadRequest(
            await mediaClient.put(secondPhoto.id, {
              imageId: initialImageId,
            }),
          );
          expect(body.errors).toEqual({ imageId: ['duplicatedImageId'] });
        });
      });
    });
  });

  describe('Video tests', () => {
    const createdVideoKeys =
      '["actions","album","date","description","height","id","imageId","lastEditionDate","lastEditor",' +
      '"mediaSubType","mediaType","status","storageType","submitter","thumbHeight","thumbWidth","title","users",' +
      '"vendorKey","width"]';
    // TODO: rajouter author

    describe('POST', () => {
      describe('ACLs', () => {
        test('Guest cannot POST', async () => {
          await mediaClient.clearToken();
          checkUnauthorised(
            await mediaClient.post({
              mediaType: VIDEO,
              mediaSubType: YOUTUBE,
              vendorKey: '1PcGJIjhQjg',
              albumId: plainUser.albumId,
              title: [{ locale: 'en-US', text: 'A new YouTube video title'}],
              description: [{ locale: 'en-US', text: 'A new YouTube video description'}],
              storageType: 0,
            }),
          );
        });
      });

      describe('Successes', () => {
        test('YouTube video', async () => {
          mediaClient.setLocalVideoThumb(LOCAL_THUMB_PATH);
          await mediaClient.setUser(plainUser);
          const body = checkSuccess(
            await mediaClient.post({
              mediaType: VIDEO,
              mediaSubType: YOUTUBE,
              vendorKey: '1PcGJIjhQjg',
              albumId: plainUser.albumId,
              title: [{ locale: 'en-US', text: 'A new YouTube video title'}],
              description: [{ locale: 'en-US', text: 'A new YouTube video description'}],
              storageType: 0,
            }),
          );
          expect(getSortedKeysAsString(body)).toEqual(createdVideoKeys);
          expect(looksLikeUUID(body.id)).toBeTruthy();
        });

        test('YouTube video with thumbUrl', async () => {
          await mediaClient.setUser(plainUser);
          const body = checkSuccess(
            await mediaClient.post({
              mediaType: VIDEO,
              mediaSubType: YOUTUBE,
              thumbUrl: LOCAL_THUMB_PATH,
              width: 123,
              height: 456,
              vendorKey: '1PcGJIjhQjg',
              albumId: plainUser.albumId,
              title: [{ locale: 'en-US', text: 'A new YouTube video title'}],
              description: [{ locale: 'en-US', text: 'A new YouTube video description'}],
              storageType: 0,
            }),
          );
          expect(getSortedKeysAsString(body)).toEqual(createdVideoKeys);
          expect(looksLikeUUID(body.id)).toBeTruthy();
          expect(body.width).toEqual('123');
          expect(body.height).toEqual('456');
        });

        test('Duplicated videos are allowed', async () => {
          // The reasoning is that videos may be posted to different albums, and we'd need to
          // check who can see what, otherwise videos may be hidden from certain people forever.
          mediaClient.setLocalVideoThumb(LOCAL_THUMB_PATH);
          await mediaClient.setUser(plainUser);
          const body1 = checkSuccess(
            await mediaClient.post({
              mediaType: VIDEO,
              mediaSubType: YOUTUBE,
              vendorKey: 'kmWSGtyfDbA',
              albumId: plainUser.albumId,
              title: [{ locale: 'en-US', text: 'A new YouTube video title'}],
              description: [{ locale: 'en-US', text: 'A new YouTube video description'}],
              storageType: 0,
            }),
          );
          expect(getSortedKeysAsString(body1)).toEqual(createdVideoKeys);
          expect(looksLikeUUID(body1.id)).toBeTruthy();
          mediaClient.setLocalVideoThumb(LOCAL_THUMB_PATH);
          const body2 = checkSuccess(
            await mediaClient.post({
              mediaType: VIDEO,
              mediaSubType: YOUTUBE,
              vendorKey: 'kmWSGtyfDbA',
              albumId: plainUser.albumId,
              title: [{ locale: 'en-US', text: 'A new YouTube video title'}],
              description: [{ locale: 'en-US', text: 'A new YouTube video description'}],
              storageType: 0,
            }),
          );
          expect(getSortedKeysAsString(body2)).toEqual(createdVideoKeys);
          expect(looksLikeUUID(body2.id)).toBeTruthy();
        });

        test('Vimeo video', async () => {
          await mediaClient.setUser(plainUser);
          mediaClient.setLocalVideoThumb(LOCAL_THUMB_PATH);
          const body = checkSuccess(
            await mediaClient.post({
              mediaType: VIDEO,
              mediaSubType: VIMEO,
              vendorKey: '16567910',
              albumId: plainUser.albumId,
              title: [{ locale: 'en-US', text: 'A new Vimeo video title'}],
              description: [{ locale: 'en-US', text: 'A new Vimeo video description'}],
              storageType: 0,
            }),
          );
          expect(getSortedKeysAsString(body)).toEqual(createdVideoKeys);
          expect(looksLikeUUID(body.id)).toBeTruthy();
        });

        test('Facebook video', async () => {
          await mediaClient.setUser(plainUser);
          mediaClient.setLocalVideoThumb(LOCAL_THUMB_PATH);
          const body = checkSuccess(
            await mediaClient.post({
              mediaType: VIDEO,
              mediaSubType: FACEBOOK,
              vendorKey: 'showhey.miyata/videos/1854604844577137',
              albumId: plainUser.albumId,
              title: [{ locale: 'en-US', text: 'A new Facebook video title'}],
              description: [{ locale: 'en-US', text: 'A new Facebook video description'}],
              storageType: 0,
            }),
          );
          expect(getSortedKeysAsString(body)).toEqual(createdVideoKeys);
          expect(looksLikeUUID(body.id)).toBeTruthy();
        });

        test('Dailymotion video', async () => {
          await mediaClient.setUser(plainUser);
          mediaClient.setLocalVideoThumb(LOCAL_THUMB_PATH);
          const body = checkSuccess(
            await mediaClient.post({
              mediaType: VIDEO,
              mediaSubType: DAILYMOTION,
              vendorKey: 'x1buew',
              albumId: plainUser.albumId,
              title: [{ locale: 'en-US', text: 'A new Dailymotion video title'}],
              description: [{ locale: 'en-US', text: 'A new Dailymotion video description'}],
              storageType: 0,
            }),
          );
          expect(getSortedKeysAsString(body)).toEqual(createdVideoKeys);
          expect(looksLikeUUID(body.id)).toBeTruthy();
        });

        test('Instagram video', async () => {
          await mediaClient.setUser(plainUser);
          mediaClient.setLocalVideoThumb(LOCAL_THUMB_PATH);
          const body = checkSuccess(
            await mediaClient.post({
              mediaType: VIDEO,
              mediaSubType: INSTAGRAM,
              vendorKey: 'Bks-3LhgiDQ',
              albumId: plainUser.albumId,
              title: [{ locale: 'en-US', text: 'A new Instagram video title'}],
              description: [{ locale: 'en-US', text: 'A new Instagram video description'}],
              storageType: 0,
            }),
          );
          expect(getSortedKeysAsString(body)).toEqual(createdVideoKeys);
          expect(looksLikeUUID(body.id)).toBeTruthy();
        });

        test('Plain user can post to their own static album', async () => {
          await mediaClient.setUser(plainUser);
          mediaClient.setLocalVideoThumb(LOCAL_THUMB_PATH);
          const body = checkSuccess(
            await mediaClient.post({
              mediaType: VIDEO,
              mediaSubType: YOUTUBE,
              vendorKey: 'RoAV-FSDGlA',
              albumId: plainUserStaticAlbum.id,
              title: [{ locale: 'en-US', text: 'A failing YouTube video title'}],
              description: [{ locale: 'en-US', text: 'A failing YouTube video description'}],
              storageType: 0,
            }),
          );
          expect(getSortedKeysAsString(body)).toEqual(createdVideoKeys);
          expect(looksLikeUUID(body.id)).toBeTruthy();
          expect(body.album.id).toEqual(plainUserStaticAlbum.id);
        });

        test("Plain user can post to another user's public album", async () => {
          await mediaClient.setUser(plainUser);
          mediaClient.setLocalVideoThumb(LOCAL_THUMB_PATH);
          const body = checkSuccess(
            await mediaClient.post({
              mediaType: VIDEO,
              mediaSubType: YOUTUBE,
              vendorKey: '1PcGJIjhQjg',
              albumId: editorUserStaticPublicAlbum.id,
              title: [{ locale: 'en-US', text: 'A successful YouTube video title'}],
              description: [{ locale: 'en-US', text: 'A successful YouTube video description'}],
              storageType: 0,
            }),
          );
        });
      });

      describe('Failures', () => {
        test('Invalid mediaSubType fails', async () => {
          await mediaClient.setUser(plainUser);
          mediaClient.setLocalVideoThumb(LOCAL_THUMB_PATH);
          const body = checkBadRequest(
            await mediaClient.post({
              mediaType: VIDEO,
              mediaSubType: 'sds',
              albumId: plainUser.albumId,
              title: [{ locale: 'en-US', text: 'A new YouTube video title'}],
              description: [{ locale: 'en-US', text: 'A new YouTube video description'}],
              vendorKey: '1PcGJIjhQjg',
              storageType: 0,
            }),
          );
          expect(body.errors).toEqual({ mediaSubType: ['invalidType'] });
        });

        test('Invalid vendorKey fails because thumbnail is invalid', async () => {
          mediaClient.setLocalVideoThumb(LOCAL_BAD_THUMB_PATH);
          await mediaClient.setUser(plainUser);
          const body = checkBadRequest(
            await mediaClient.post({
              mediaType: VIDEO,
              mediaSubType: YOUTUBE,
              vendorKey: 'badKey',
              albumId: plainUser.albumId,
              title: [{ locale: 'en-US', text: 'A failing YouTube video title'}],
              description: [{ locale: 'en-US', text: 'A failing YouTube video description'}],
              storageType: 0,
            }),
          );
          expect(body.errors.topLevelError.code).toEqual(10011);
        });

        test('Invalid storageType fails', async () => {
          await mediaClient.setUser(plainUser);
          mediaClient.setLocalVideoThumb(LOCAL_THUMB_PATH);
          const body = checkBadRequest(
            await mediaClient.post({
              mediaType: VIDEO,
              mediaSubType: YOUTUBE,
              vendorKey: '1PcGJIjhQjg',
              albumId: plainUser.albumId,
              title: [{ locale: 'en-US', text: 'A failing YouTube video title'}],
              description: [{ locale: 'en-US', text: 'A failing YouTube video description'}],
              storageType: 27,
            }),
          );
          expect(body.errors).toEqual({ storageType: ['invalidType'] });
        });

        test('Aggregate albumId fails', async () => {
          await mediaClient.setUser(plainUser);
          mediaClient.setLocalVideoThumb(LOCAL_THUMB_PATH);
          const body = checkBadRequest(
            await mediaClient.post({
              mediaType: VIDEO,
              mediaSubType: YOUTUBE,
              vendorKey: '1PcGJIjhQjg',
              albumId: aggregateAlbum.id,
              title: [{ locale: 'en-US', text: 'A failing YouTube video title'}],
              description: [{ locale: 'en-US', text: 'A failing YouTube video description'}],
              storageType: 0,
            }),
          );
          expect(body.errors).toEqual({ albumId: ['albumTypeNotAllowed'] });
        });

        test("Plain user cannot post to another user's private album", async () => {
          await mediaClient.setUser(plainUser);
          mediaClient.setLocalVideoThumb(LOCAL_THUMB_PATH);
          const body = checkBadRequest(
            await mediaClient.post({
              mediaType: VIDEO,
              mediaSubType: YOUTUBE,
              vendorKey: '1PcGJIjhQjg',
              albumId: editorUserStaticPrivateAlbum.id,
              title: [{ locale: 'en-US', text: 'A failing YouTube video title'}],
              description: [{ locale: 'en-US', text: 'A failing YouTube video description'}],
              storageType: 0,
            }),
          );
          expect(body.errors).toEqual({ albumId: ['albumNotWritable'] });
        });

        test('Plain user cannot post to non-existing album', async () => {
          await mediaClient.setUser(plainUser);
          mediaClient.setLocalVideoThumb(LOCAL_THUMB_PATH);
          const body = checkBadRequest(
            await mediaClient.post({
              mediaType: VIDEO,
              mediaSubType: YOUTUBE,
              vendorKey: '1PcGJIjhQjg',
              albumId: 'nachoalbum',
              title: [{ locale: 'en-US', text: 'A failing YouTube video title'}],
              description: [{ locale: 'en-US', text: 'A failing YouTube video description'}],
              storageType: 0,
            }),
          );
          expect(body.errors).toEqual({ albumId: ['doesNotExist'] });
        });

        test('Bad storageType fails', async () => {
          await mediaClient.setUser(plainUser);
          mediaClient.setLocalVideoThumb(LOCAL_THUMB_PATH);
          const body = checkBadRequest(
            await mediaClient.post({
              mediaType: VIDEO,
              mediaSubType: YOUTUBE,
              vendorKey: '1PcGJIjhQjg',
              albumId: plainUser.albumId,
              title: [{ locale: 'en-US', text: 'A failing YouTube video title'}],
              description: [{ locale: 'en-US', text: 'A failing YouTube video description'}],
              storageType: 27,
            }),
          );
          expect(body.errors).toEqual({ storageType: ['invalidType'] });
        });
      });
    });

    describe('PUT', () => {
      describe('Successes', () => {
        test('Switch video', async () => {
          const video = await postVideoAs(plainUser, '9cTG0U6IMHU');

          await mediaClient.setUser(plainUser);
          const newVendorKey = 'kZ75GzTL27o';
          mediaClient.setLocalVideoThumb(LOCAL_THUMB_PATH);
          const body = checkSuccess(
            await mediaClient.put(video.id, {
              vendorKey: newVendorKey,
            }),
          );
          expect(body.vendorKey).toEqual(newVendorKey);
        });

        test('Switch video and mediaSubType', async () => {
          const video = await postVideoAs(plainUser, '_k8G0DaAPMk');

          await mediaClient.setUser(plainUser);
          const newVendorKey = '15697415';
          mediaClient.setLocalVideoThumb(LOCAL_THUMB_PATH);
          const body = checkSuccess(
            await mediaClient.put(video.id, {
              vendorKey: newVendorKey,
              mediaSubType: VIMEO,
            }),
          );
          expect(body.vendorKey).toEqual(newVendorKey);
          expect(body.mediaSubType).toEqual(VIMEO);
        });
      });

      describe('Failure', () => {
        let video1;
        beforeAll(async () => {
          video1 = await postVideoAs(plainUser, '1Mxxqi2AuRU');
        });

        test('Bad mediaSubType fails', async () => {
          await mediaClient.setUser(plainUser);
          const newVendorKey = '15697415';
          const body = checkBadRequest(
            await mediaClient.put(video1.id, {
              vendorKey: newVendorKey,
              mediaSubType: 'notgood',
            }),
          );
          expect(body.errors).toEqual({ mediaSubType: ['invalidType'] });
        });
      });
    });
  });

  describe('DELETE', () => {
    describe('Photo ACLs', () => {
      test('Guest and writer cannot delete', async () => {
        const imageId = await postImageAs(plainUser);
        const photo = await postPhotoAs(plainUser, imageId);

        mediaClient.clearToken();
        checkUnauthorised(await mediaClient.delete(photo.id));

        mediaClient.setUser(writerUser);
        checkUnauthorised(await mediaClient.delete(photo.id));
      });

      test('Owner can delete', async () => {
        const imageId = await postImageAs(plainUser);
        const photo = await postPhotoAs(plainUser, imageId);

        mediaClient.setUser(plainUser);
        checkSuccess(await mediaClient.delete(photo.id));
      });

      test('Editor can delete', async () => {
        const imageId = await postImageAs(plainUser);
        const photo = await postPhotoAs(plainUser, imageId);

        mediaClient.setUser(editorUser);
        checkSuccess(await mediaClient.delete(photo.id));
      });

      test('Admin can delete', async () => {
        const imageId = await postImageAs(plainUser);
        const photo = await postPhotoAs(plainUser, imageId);

        mediaClient.setUser(adminUser);
        checkSuccess(await mediaClient.delete(photo.id));
      });
    });

    describe('Video ACLs', () => {
      test('Guest and writer cannot delete', async () => {
        const video = await postVideoAs(plainUser, '_k8G0DaAPMk');

        mediaClient.clearToken();
        checkUnauthorised(await mediaClient.delete(video.id));

        mediaClient.setUser(writerUser);
        checkUnauthorised(await mediaClient.delete(video.id));
      });

      test('Owner can delete', async () => {
        const video = await postVideoAs(plainUser, '_k8G0DaAPMk');

        mediaClient.setUser(plainUser);
        checkSuccess(await mediaClient.delete(video.id));
      });

      test('Editor can delete', async () => {
        const video = await postVideoAs(plainUser, '_k8G0DaAPMk');

        mediaClient.setUser(editorUser);
        checkSuccess(await mediaClient.delete(video.id));
      });

      test('Admin can delete', async () => {
        const video = await postVideoAs(plainUser, '_k8G0DaAPMk');

        mediaClient.setUser(adminUser);
        checkSuccess(await mediaClient.delete(video.id));
      });
    });
  });
});

describe('Album tests', () => {
  const albumClient = new ResourceClient(client, ALBUM);
  const userClient = new ResourceClient(client, USER);

  const createStaticAlbum = async (user, data) => {
    await albumClient.setUser(user);
    try {
      const body = checkSuccess(await albumClient.post(data));
      return body;
    } catch (e) {
      console.error(e);
      throw(e);
    }
  };

  const createUser = async (username, email) => {
    await userClient.clearToken();
    const password = '123456789';
    const body = checkSuccess(
      await userClient.post({
        username,
        userP: password,
        userPC: password,
        email,
      }),
    );
    return body;
  };

  describe('POST', () => {
    test('For each user, a new aggregate album is created', async () => {
      const newUser = await createUser('albumTest1', 'albumTest1@email.com');
      albumClient.clearToken();
      const body = checkSuccess(await albumClient.get(newUser.album.id));
      expect(body.media).toEqual([]);
    });

    test('Guest user cannot create static album', async () => {
      albumClient.clearToken();
      checkUnauthorised(await albumClient.post({
        title: [{ locale: 'en-US', text: 'will not work'}],
      }));
    });

    test('Logged in user can create static album', async () => {
      albumClient.setUser(plainUser);
      const {title, description} = checkSuccess(await albumClient.post({
        title: [{ locale: 'en-US', text: 'will work'}],
        description: [{ locale: 'en-US', text: 'ok'}],
      }));

      expect(title[0].original).toBeTruthy();
      expect(description[0].original).toBeTruthy();
    });

    test('Title is mandatory but not description', async () => {
      await albumClient.setUser(plainUser);
      const body1 = checkBadRequest(await albumClient.post({}));
      expect(body1.errors).toEqual({ title: ['notEmptyInvalid'] });

      const body2 = checkBadRequest(await albumClient.post({
        title: [{ locale: 'en-US', text: ''}],
      }));
      expect(body2.errors).toEqual({ title: ['emptyText'] });
    });

    test('Plain user can create private albums', async () => {
      albumClient.setUser(plainUser);
      const { albumContributions, albumType } = checkSuccess(
        await albumClient.post({
          title: [{ locale: 'en-US', text: 'will Album for actions'}],
          albumContributions: 'private'
        }),
      );

      expect(albumContributions).toEqual('private');
      expect(albumType).toEqual('simple');
    });

    test('Plain user can create public albums', async () => {
      albumClient.setUser(plainUser);
      const { albumContributions, albumType } = checkSuccess(
        await albumClient.post({
          title: [{ locale: 'en-US', text: 'Album for actions - public'}],
          albumContributions: 'public',
        }),
      );

      expect(albumContributions).toEqual('public');
      expect(albumType).toEqual('simple');
    });
  });

  describe('GET', () => {
    const mediaClient = new ResourceClient(client, MEDIA);

    const postVideoWithUserAs = async (submitter, vendorKey, users = []) => {
      await mediaClient.setUser(submitter);
      mediaClient.setLocalVideoThumb(LOCAL_THUMB_PATH);
      const body = checkSuccess(
        await mediaClient.post({
          mediaType: VIDEO,
          mediaSubType: YOUTUBE,
          albumId: submitter.albumId,
          title: [{ locale: 'en-US', text: 'A new video title'}],
          description: [{ locale: 'en-US', text: 'A new video description'}],
          vendorKey,
          users: users,
          storageType: 0,
        }),
      );
      return body;
    };

    const postVideoWithToStaticAlbum = async (submitter, vendorKey, albumId) => {
      await mediaClient.setUser(submitter);
      mediaClient.setLocalVideoThumb(LOCAL_THUMB_PATH);
      const body = checkSuccess(
        await mediaClient.post({
          mediaType: VIDEO,
          mediaSubType: YOUTUBE,
          vendorKey,
          albumId,
          title: [{ locale: 'en-US', text: 'A new video title'}],
          description: [{ locale: 'en-US', text: 'A new video description'}],
          users: [],
          storageType: 0,
        }),
      );
      return body;
    };

    test('Aggregate album contains media items containing tagged users', async () => {
      const videoKey = '9cTG0U6IMHU';
      const newUser = await createUser('albumGetTest1', 'albumGetTest1@email.com');
      const {
        userId: newUserId,
        album: { id: newAggAlbumId },
      } = newUser;
      const videoBody = await postVideoWithUserAs(plainUser, videoKey, [newUserId]);
      const { id: newVideoId } = videoBody;

      const { media } = checkSuccess(await albumClient.get(newAggAlbumId));
      const mediaCount = media.length;

      const mediaIds = media.map((m) => m.id);
      expect(mediaIds).toEqual([newVideoId]);
      const users = media.filter(({ users }) => {
        return users.includes(newUserId);
      });
      expect(users.length).toEqual(mediaCount);
    });

    test('Static album contains user media', async () => {
      const { media: initialAlbumContent } = checkSuccess(
        await albumClient.get(plainUserStaticAlbum.id),
      );

      const videoKey = '9cTG0U6IMHU';
      const { id: newVideoId } = await postVideoWithToStaticAlbum(
        plainUser,
        videoKey,
        plainUserStaticAlbum.id,
      );

      const { media: secondAlbumContent } = checkSuccess(
        await albumClient.get(plainUserStaticAlbum.id),
      );

      expect(secondAlbumContent.length).toEqual(initialAlbumContent.length + 1);
      expect(secondAlbumContent.map((m) => m.id).includes(newVideoId)).toBeTruthy();
    });

    test('Album content can be sorted and/or paginated', async () => {
      const album = await createStaticAlbum(plainUser, {
        title: [{ locale: 'en-US', text: 'Album for sorting' }],
      });
      await postVideoWithToStaticAlbum(plainUser, '123', album.id);
      await postVideoWithToStaticAlbum(plainUser, '456', album.id);
      await postVideoWithToStaticAlbum(plainUser, '789', album.id);
      const { media: mediaDefault } = checkSuccess(await albumClient.get(album.id));
      const { media: mediaAsc } = checkSuccess(
        await albumClient.get(album.id, { dirItems: 'ASC' }),
      );
      const { media: mediaDesc } = checkSuccess(
        await albumClient.get(album.id, { dirItems: 'DESC' }),
      );
      const { media: mediaPageAsc } = checkSuccess(
        await albumClient.get(album.id, { dirItems: 'ASC', countItems: 1, startItem: 2 }),
      );
      const { media: mediaPageDesc } = checkSuccess(
        await albumClient.get(album.id, { dirItems: 'DESC', countItems: 1, startItem: 2 }),
      );

      const listDefault = mediaDefault.map((m) => m.vendorKey);
      const listAsc = mediaAsc.map((m) => m.vendorKey);
      const listDesc = mediaDesc.map((m) => m.vendorKey);
      const listPageAsc = mediaPageAsc.map((m) => m.vendorKey);
      const listPageDesc = mediaPageDesc.map((m) => m.vendorKey);

      expect(mediaDefault.length).toEqual(3);
      expect(listDefault).toEqual(['789', '456', '123']);

      expect(mediaAsc.length).toEqual(3);
      expect(listAsc).toEqual(['123', '456', '789']);

      expect(mediaDesc.length).toEqual(3);
      expect(listDesc).toEqual(['789', '456', '123']);

      expect(mediaPageAsc.length).toEqual(1);
      expect(listPageAsc).toEqual(['789']);

      expect(mediaPageDesc.length).toEqual(1);
      expect(listPageDesc).toEqual(['123']);
    });

    test("Aggregate album can't be added to", async () => {
      const newUser = await createUser('albumAddTest1', 'albumAddTest1@email.com');
      const {
        userId: newUserId,
        album: { id: newAggAlbumId },
      } = newUser;
      await userClient.setUser(adminUser);
      checkSuccess(await userClient.put(newUserId, { status: 'member' }));

      let response;

      await albumClient.setUser({ id: newUserId });
      response = checkSuccess(await albumClient.get(newAggAlbumId));
      expect(response.actions.add).toEqual(false);

      await albumClient.setUser(adminUser);
      response = checkSuccess(await albumClient.get(newAggAlbumId));
      expect(response.actions.add).toEqual(false);
    });

    test("Private simple album can't be added to (except by owner and admin/editor)", async () => {
      albumClient.setUser(plainUser);
      const { id: albumId, albumContributions, albumType } = checkSuccess(
        await albumClient.post({
          title: [{ locale: 'en-US', text: 'Album for actions'}],
          albumContributions: 'private'
        }),
      );

      expect(albumContributions).toEqual('private');
      expect(albumType).toEqual('simple');
      let response;

      await albumClient.clearToken();
      checkSuccess(await albumClient.get(albumId));

      await albumClient.setUser(plainUser);
      response = checkSuccess(await albumClient.get(albumId));
      expect(response.actions.add).toEqual(true);

      await albumClient.setUser(otherUser);
      checkSuccess(await albumClient.get(albumId));

      await albumClient.setUser(writerUser);
      checkSuccess(await albumClient.get(albumId));

      await albumClient.setUser(editorUser);
      response = checkSuccess(await albumClient.get(albumId));
      expect(response.actions.add).toEqual(true);

      await albumClient.setUser(adminUser);
      response = checkSuccess(await albumClient.get(albumId));
      expect(response.actions.add).toEqual(true);
    });

    test('Public simple album can be added to (except by guest)', async () => {
      const { id: albumId } = await createStaticAlbum(plainUser, {
        title: [{ locale: 'en-US', text: 'Album for actions'}],
        albumContributions: 'public',
      });

      let response;

      await albumClient.clearToken();
      response = checkSuccess(await albumClient.get(albumId));
      expect(response.actions.add).toEqual(false);

      await albumClient.setUser(plainUser);
      response = checkSuccess(await albumClient.get(albumId));
      expect(response.actions.add).toEqual(true);

      await albumClient.setUser(otherUser);
      response = checkSuccess(await albumClient.get(albumId));
      expect(response.actions.add).toEqual(true);

      await albumClient.setUser(writerUser);
      response = checkSuccess(await albumClient.get(albumId));
      expect(response.actions.add).toEqual(true);

      await albumClient.setUser(editorUser);
      response = checkSuccess(await albumClient.get(albumId));
      expect(response.actions.add).toEqual(true);

      await albumClient.setUser(adminUser);
      response = checkSuccess(await albumClient.get(albumId));
      expect(response.actions.add).toEqual(true);
    });
  });

  describe('PUT', () => {
    let updatableAlbumId;

    beforeAll(async () => {
      const body = await createStaticAlbum(plainUser, {
        title: [{ locale: 'en-US', text: 'Album for updates' }],
      });

      updatableAlbumId = body.id;
    });

    test('Guest cannot update album title and description', async () => {
      albumClient.clearToken();
      checkUnauthorised(await albumClient.put(updatableAlbumId, {}));
    });

    test('Writer cannot update album title and description', async () => {
      albumClient.setUser(writerUser);
      checkUnauthorised(await albumClient.put(updatableAlbumId, {}));
    });

    test('Owner can update album title and description', async () => {
      albumClient.setUser(plainUser);
      const { title, description } = checkSuccess(
        await albumClient.put(updatableAlbumId, {
          title: [{ locale: 'en-US', text: 'ownerTitle' }],
          description: [{ locale: 'en-US', text: 'ownerDescription' }],
        }),
      );

      expect(title).toEqual([{'locale': 'en-US', 'text': 'ownerTitle', original: true }]);
      expect(description).toEqual([{'locale': 'en-US', 'text': 'ownerDescription', original: true }]);
    });

    test('Editor can update album title and description', async () => {
      albumClient.setUser(editorUser);
      const { title, description } = checkSuccess(
        await albumClient.put(updatableAlbumId, {
          title: [{ locale: 'en-US', text: 'editorTitle' }],
          description: [{ locale: 'en-US', text: 'editorDescription' }],
        }),
      );

      expect(title).toEqual([{'locale': 'en-US', 'text': 'editorTitle', original: true }]);
      expect(description).toEqual([{'locale': 'en-US', 'text': 'editorDescription', original: true }]);
    });

    test('Admin can update album title and description', async () => {
      albumClient.setUser(adminUser);
      const { title, description } = checkSuccess(
        await albumClient.put(updatableAlbumId, {
          title: [{ locale: 'en-US', text: 'adminTitle' }],
          description: [{ locale: 'en-US', text: 'adminDescription' }],
        }),
      );

      expect(title).toEqual([{'locale': 'en-US', 'text': 'adminTitle', original: true }]);
      expect(description).toEqual([{'locale': 'en-US', 'text': 'adminDescription', original: true }]);
    });

    test('Owner can create invisible album and then make it visible', async() => {
      albumClient.setUser(plainUser);
      const { id } = checkSuccess(
        await albumClient.post({
          albumVisibility: 'private',
          title: [{ locale: 'en-US', text: 'Invisible', }],
        }),
      );
      checkSuccess(
        await albumClient.put(id, {
          albumVisibility: 'visible',
          title: [{ locale: 'en-US', text: 'Visible', }],
        }),
      );
    });
  });

  describe('DELETE', () => {
    test('Aggregate album cannot be deleted', async () => {
      albumClient.setUser(adminUser);
      const body = checkBadRequest(await albumClient.delete(aggregateAlbum.id));
      expect(body.errors.topLevelError.code).toEqual(12002);
    });

    test('Non-empty static album cannot be deleted', async () => {
      const { id: albumId } = await createStaticAlbum(plainUser, {
        title: [{ locale: 'en-US', text: 'Static album for delete' }],
      });

      const mediaClient = new ResourceClient(client, MEDIA);
      await mediaClient.setUser(plainUser);
      mediaClient.setLocalVideoThumb(LOCAL_THUMB_PATH);
      checkSuccess(
        await mediaClient.post({
          mediaType: VIDEO,
          mediaSubType: YOUTUBE,
          albumId: albumId,
          title: [{ locale: 'en-US', text: 'A new video title'}],
          description: [{ locale: 'en-US', text: 'A new video description'}],
          vendorKey: '1PcGJIjhQjg',
          storageType: 0,
        }),
      );

      albumClient.setUser(adminUser);
      const body = checkBadRequest(await albumClient.delete(albumId));
      expect(body.errors.topLevelError.code).toEqual(12001);
    });

    test('Guest and writer cannot delete empty static album', async () => {
      const { id: deletableAlbumId } = await createStaticAlbum(plainUser, {
        title: [{ locale: 'en-US', text: 'Album for delete' }],
      });

      albumClient.clearToken();
      checkUnauthorised(await albumClient.delete(deletableAlbumId));

      albumClient.setUser(writerUser);
      checkUnauthorised(await albumClient.delete(deletableAlbumId));
    });

    test('Owner can delete empty static album', async () => {
      const { id: deletableAlbumId } = await createStaticAlbum(plainUser, {
        title: [{ locale: 'en-US', text: 'Album for delete' }],
      });

      albumClient.setUser(plainUser);
      checkSuccess(await albumClient.delete(deletableAlbumId));
    });

    test('Editor can delete empty static album', async () => {
      const { id: deletableAlbumId } = await createStaticAlbum(plainUser, {
        title: [{ locale: 'en-US', text: 'Album for delete' }],
      });

      albumClient.setUser(editorUser);
      checkSuccess(await albumClient.delete(deletableAlbumId));
    });

    test('Admin can delete empty static album', async () => {
      const { id: deletableAlbumId } = await createStaticAlbum(plainUser, {
        title: [{ locale: 'en-US', text: 'Album for delete' }],
      });

      albumClient.setUser(adminUser);
      checkSuccess(await albumClient.delete(deletableAlbumId));
    });
  });
});

describe('Comment tests', () => {
  const albumClient = new ResourceClient(client, ALBUM);
  const commentClient = new ResourceClient(client, COMMENT);
  const videoClient = new ResourceClient(client, VIDEO);

  describe('GET', () => {
    const commentInfo =
      '["actions","content","date","id","lastEditionDate","lastEditor","parentId","parentType","status","submitter","tone"]';

    test('Retrieve a single comment', async () => {
      const body = checkSuccess(await commentClient.get(singleComment.id));
      expect(getSortedKeysAsString(body)).toEqual(commentInfo);
      expect(body.content).toEqual(singleComment.content);
      expect(body.date).toEqual(singleComment.date);
      expect(body.tone).toEqual(singleComment.tone);
    });

    test('XSS content is NOT escaped - See application/views/script.phtml', async () => {
      const body = checkSuccess(await commentClient.get(XSSComment.id));
      expect(body.content).toEqual([{ locale: 'en-US', text: "</script><script>alert('this is an XSS')</script>" }]);
    });

    test('Check only allowed users can see invalid comment', async () => {
      // Logged-out
      checkUnauthorised(await commentClient.get(invalidComment.id));

      // Logged-in
      await commentClient.setUser(plainUser);
      checkUnauthorised(await commentClient.get(invalidComment.id));

      // Banned user
      await commentClient.setUser(bannedUser);
      checkUnauthorised(await commentClient.get(invalidComment.id));

      // Owner
      await commentClient.setUser(writerUser);
      checkSuccess(await commentClient.get(invalidComment.id));

      // Editor
      await commentClient.setUser(editorUser);
      checkSuccess(await commentClient.get(invalidComment.id));

      // Admin
      await commentClient.setUser(adminUser);
      checkSuccess(await commentClient.get(invalidComment.id));
    });

    test('List comments on an item', async () => {
      const body = checkSuccess(await videoClient.getComments(photoIdForComments));
      expect(body.length).toEqual(3);

      expect(getSortedKeysAsString(body[0])).toEqual(commentInfo);
      expect(getSortedKeysAsString(body[1])).toEqual(commentInfo);
      expect(getSortedKeysAsString(body[2])).toEqual(commentInfo);
    });

    describe('List by id, aka batch GET', () => {
      test('Owner can get 2 valid photos', async() => {
        await commentClient.setUser(plainUser);
        const body = checkSuccess(await commentClient.batchGet(commentsForBatch));
        expect(body.length).toEqual(1);
      })

      test('Invalid photos are not part of batch responses for guest ', async() => {
        await commentClient.setUser(editorUser);
        const body = checkSuccess(await commentClient.batchGet(commentsForBatch));
        expect(body.length).toEqual(2);
      })
    });

  });

  describe('POST', () => {
    test('Create a single comment and retrieve it', async () => {
      const albumId = 'a8a7b0fd-e0f2-49d9-8974-e6292d7e667b';
      await commentClient.setUser(plainUser);

      const commentPayload = {
        content: [{ locale: 'en-US', text: 'This is a new comment' }],
        itemType: 'mediaalbum',
        itemId: albumId,
        tone: 'neutral',
      };
      const body = checkSuccess(await commentClient.post(commentPayload));

      const output = [{ locale: 'en-US', text: 'This is a new comment', original: true}];

      expect(body.id).toBeDefined();
      expect(body.content).toEqual(output);

      const comments = checkSuccess(await albumClient.getComments(albumId));
      expect(comments.length).toEqual(1);
      expect(comments[0].content).toEqual(output);
    });
  });

  describe('PUT', () => {
    test('Update existing comments', async () => {
      // All comments were posted by writerUser
      checkUnauthorised(await commentClient.put(commentsForUpdate[0].id, { content: [{ locale: 'en-US', text: 'modified' }] }));

      await commentClient.setUser(bannedUser);
      checkUnauthorised(await commentClient.put(commentsForUpdate[1].id, { content: [{ locale: 'en-US', text: 'modified' }] }));

      await commentClient.setUser(plainUser);
      checkUnauthorised(await commentClient.put(commentsForUpdate[2].id, { content: [{ locale: 'en-US', text: 'modified' }] }));

      await commentClient.setUser(writerUser);
      const ownerBody = checkSuccess(
        await commentClient.put(commentsForUpdate[3].id, { content: [{ locale: 'en-US', text: 'modified' }] }),
      );
      expect(ownerBody.content).toEqual([{ locale: 'en-US', text: 'modified', original: true }]);

      await commentClient.setUser(editorUser);
      const editorBody = checkSuccess(
        await commentClient.put(commentsForUpdate[4].id, { content: [{ locale: 'en-US', text: 'modified' }] }),
      );
      expect(editorBody.content).toEqual([{ locale: 'en-US', text: 'modified', original: true }]);

      await commentClient.setUser(adminUser);
      const adminBody = checkSuccess(
        await commentClient.put(commentsForUpdate[5].id, { content: [{ locale: 'en-US', text: 'modified' }] }),
      );
      expect(adminBody.content).toEqual([{ locale: 'en-US', text: 'modified', original: true }]);
    });
  });

  describe('DELETE', () => {
    test('Delete existing comments', async () => {
      // All comments were posted by writerUser
      commentClient.setToken(null);
      checkUnauthorised(await commentClient.delete(commentsForDelete[0].id));

      await commentClient.setUser(bannedUser);
      checkUnauthorised(await commentClient.delete(commentsForDelete[1].id));

      await commentClient.setUser(plainUser);
      checkUnauthorised(await commentClient.delete(commentsForDelete[2].id));

      await commentClient.setUser(writerUser);
      const ownerBody = checkSuccess(await commentClient.delete(commentsForDelete[3].id));
      expect(ownerBody.status).toBeTruthy();

      await commentClient.setUser(editorUser);
      const editorBody = checkSuccess(await commentClient.delete(commentsForDelete[4].id));
      expect(editorBody.status).toBeTruthy();

      await commentClient.setUser(adminUser);
      const adminBody = checkSuccess(await commentClient.delete(commentsForDelete[5].id));
      expect(adminBody.status).toBeTruthy();
    });

    test('Comments are deleted along with their parent', async () => {
      const imageClient = new ResourceClient(client, IMAGE);
      await imageClient.setUser(plainUser);
      const imageResponse = checkSuccess(await imageClient.postFormData({ type: 0 }, [img640]));
      const imageId = imageResponse[0].key;

      const photoClient = new ResourceClient(client, MEDIA);
      await photoClient.setUser(plainUser);
      const newPhoto = checkSuccess(
        await photoClient.post({
          mediaType: PHOTO,
          albumId: plainUserStaticAlbum.id,
          title: [{ locale: 'en-US', text: 'A new photo title' }],
          description: [{ locale: 'en-US', text: 'A new photo description' }],
          imageId: imageId,
          storageType: 0,
        }),
      );

      await commentClient.setUser(writerUser);
      const commentPayload = {
        content: [{ locale: 'en-US', text: 'This is a new photo comment for deletion' }],
        itemType: 'photo',
        itemId: newPhoto.id,
        tone: 'neutral',
      };
      const newComment = checkSuccess(await commentClient.post(commentPayload));

      const { status } = checkSuccess(await photoClient.delete(newPhoto.id));
      expect(status).toBeTruthy();

      checkNotFound(await commentClient.get(newComment.id));
    });
  });
});

describe ('Translation tests', () => {
  const commentClient = new ResourceClient(client, COMMENT);
  const translationPath = `/translations/comments/${translatedComment.id}`;

  test('Manage comment translations', async () => {
    const updatedFrench = 'updatedFrench';

    // Initial
    const { content: enContent } = checkSuccess(await commentClient.get(translatedComment.id));
    expect(enContent).toEqual([{ locale: 'en-US', text: translatedComment.enContent, original: true }]);

    // Add
    const addPayload = { translation: [{ field: 'content', locale: 'fr-FR', text: translatedComment.frContent }]};
    checkSuccess(await client.post({
      path: translationPath,
      data: addPayload,
      token: null,
    }));
    const { content: enAndFrContent } = checkSuccess(await commentClient.get(translatedComment.id));
    expect(enAndFrContent).toEqual([
      { locale: 'en-US', text: translatedComment.enContent, original: true},
      { locale: 'fr-FR', text: translatedComment.frContent }
    ]);

    // Update
    const updatePayload = { translation: [{ field: 'content', locale: 'fr-FR', text: updatedFrench }]};
    checkSuccess(await client.put({
      path: translationPath,
      data: updatePayload,
      token: null,
    }));
    const { content: updatedEnAndFrContent } = checkSuccess(await commentClient.get(translatedComment.id));
    expect(updatedEnAndFrContent).toEqual([
      { locale: 'en-US', text: translatedComment.enContent, original: true},
      { locale: 'fr-FR', text: updatedFrench }
    ]);

    // Remove
    const removedFrPayload = { translation: [{ field: 'content', locale: 'fr-FR', text: null }]};
    checkSuccess(await client.put({
      path: translationPath,
      data: removedFrPayload,
      token: null,
    }));
    const { content: removedFrContent } = checkSuccess(await commentClient.get(translatedComment.id));
    expect(removedFrContent).toEqual([
      { locale: 'en-US', text: translatedComment.enContent, original: true},
    ]);
  });

});

describe.only('Reaction tests', () => {
  const reactionClient = new ResourceClient(client, REACTION);

  const defaultItemId = singleComment.id;
  const defaultItemType = 'comment';
  const defaultType = 'scared';

  const getReactionPayload = (params) => {
    const defaultPayload = {
      itemId: defaultItemId,
      itemType: defaultItemType,
      type: defaultType,
    };
    return Object.assign({}, defaultPayload, params);
  };

  describe('Reaction list GET', () => {
    test('Logged-out users cannot list reactions', async () => {
      reactionClient.clearToken();
      checkUnauthorised(await reactionClient.list());
    });

    test('Must list my own reactions - default pagination', async () => {
      await reactionClient.setUser(plainUser);
      const reactionsList = checkSuccess(await reactionClient.list());
      expect(reactionsList).toHaveLength(3);
      expect(reactionsList[0].submitter.id).toEqual(plainUser.id);
      expect(reactionsList[0].id).toEqual(laughingReaction.id);
      expect(reactionsList[1].id).toEqual(scaredReaction.id);
      expect(reactionsList[2].id).toEqual(angryReaction.id);
    });

    test('Must list my own reactions - page 1', async () => {
      await reactionClient.setUser(plainUser);
      const reactionsList = checkSuccess(await reactionClient.list({ start: 0, count: 1 }));
      expect(reactionsList).toHaveLength(1);
      expect(reactionsList[0].submitter.id).toEqual(plainUser.id);
      expect(reactionsList[0].id).toEqual(laughingReaction.id);
    });

    test('Must list my own reactions - page 2', async () => {
      await reactionClient.setUser(plainUser);
      const reactionsList = checkSuccess(await reactionClient.list({ start: 1, count: 1 }));
      expect(reactionsList).toHaveLength(1);
      expect(reactionsList[0].submitter.id).toEqual(plainUser.id);
      expect(reactionsList[0].id).toEqual(scaredReaction.id);
    });
  });

  describe('Reaction POST', () => {
    test('Logged-out users cannot create reactions', async () => {
      reactionClient.clearToken();
      checkUnauthorised(await reactionClient.post( getReactionPayload()));
    });

    test('Happy path', async () => {
      await reactionClient.setUser(plainUser);
      checkSuccess(await reactionClient.post( getReactionPayload()));
    });

    test('Target object must exist', async () => {
      await reactionClient.setUser(plainUser);
      const body = checkBadRequest(await reactionClient.post( getReactionPayload({ itemId: '123' })));
      expect(body).toEqual({ code: 16001, errors: { itemId: ['itemIdDoesNotExist'] } });
    });

    test('Incomplete data fails', async () => {
      await reactionClient.setUser(plainUser);
      const body = checkBadRequest(await reactionClient.post({}));
      expect(body).toEqual({ code: 16001, errors: {
        itemId: ['notEmptyInvalid'],
        itemType: ['notEmptyInvalid'],
        type: ['notEmptyInvalid'],
      }});
    });

    test('Duplicated data fails', async () => {
      const payload = getReactionPayload({ itemId: translatedComment.id });
      await reactionClient.setUser(plainUser);
      checkSuccess(await reactionClient.post(payload));

      const body = checkBadRequest(await reactionClient.post(payload));
      expect(body).toEqual({ code: 16001, errors: { itemId: ['duplicatedEntry'] } });
    });
  });

  describe('Reaction PUT', () => {
    test('PUT results in a 400', async () => {
      await reactionClient.setUser(plainUser);
      const body = checkBadRequest(await reactionClient.put(laughingReaction.id, getReactionPayload()));
      expect(body).toEqual({ code: 17002 });
    });
  });

  describe('Reaction GET', () => {
    test('GET results in a 400', async () => {
      await reactionClient.setUser(plainUser);
      const body = checkBadRequest(await reactionClient.get(laughingReaction.id));
      expect(body).toEqual({ code: 17002 });
    });
  });

  describe('Reaction DELETE', () => {
    test('Logged-out users cannot delete reactions', async () => {
      reactionClient.clearToken();
      checkUnauthorised(await reactionClient.delete(laughingReaction.id));
    });

    test('Reaction must exist', async () => {
      await reactionClient.setUser(plainUser);
      checkNotFound(await reactionClient.delete('123'));
    });

    test('Reactions must be deleted when their parent item is deleted', async () => {
      await reactionClient.setUser(otherUser);
      const initialReactionsList = checkSuccess(await reactionClient.list());
      expect(initialReactionsList).toHaveLength(1);

      const commentClient = new ResourceClient(client, COMMENT);
      await commentClient.setUser(otherUser);
      await commentClient.delete(commentsForReactionDelete[0].id);

      const afterDeleteReactionsList = checkSuccess(await reactionClient.list());
      expect(afterDeleteReactionsList).toHaveLength(0);
    });

    test('Owner can delete a reaction', async () => {
      await reactionClient.setUser(writerUser);
      checkSuccess(await reactionClient.delete(scaredReactionForDelete.id));
      checkNotFound(await reactionClient.delete(scaredReactionForDelete.id));
    });
  });

  describe('Item reaction lifecycle', () => {
    const reactionTypes = ['angry', 'laughing'];

    const assertNoReactions = ({ reactions: { counts, userReactions} }) => {
      expect(Object.keys(counts)).toHaveLength(0);
      expect(userReactions).toHaveLength(0);
    };

    const assertFirstReaction = ({ reactions: { counts, userReactions} }) => {
      expect(Object.keys(counts)).toEqual([reactionTypes[0]]);
      expect(userReactions).toHaveLength(0);
    };

    const assertTwoReactions = ({ reactions: { counts, userReactions} }) => {
      expect(Object.keys(counts)).toEqual(reactionTypes);
      expect(userReactions).toHaveLength(0);
    };

    const assertSecondReaction = ({ reactions: { counts, userReactions} }) => {
      expect(Object.keys(counts)).toEqual([reactionTypes[1]]);
      expect(userReactions).toHaveLength(0);
    };

    const assertOwnerReactions = ({ reactions: { counts, userReactions} }) => {
      expect(Object.keys(counts)).toEqual(reactionTypes);
      expect(Object.keys(userReactions)).toHaveLength(2);
    };

    test.only('Albums', async () => {
      const itemClient = new ResourceClient(client, ALBUM);
      const itemClientForPlainUser = new ResourceClient(client, ALBUM);
      await itemClientForPlainUser.setUser(plainUser);
      await reactionClient.setUser(plainUser);

      let created;
      const reactions = [];
      const itemId = albumForReactions.id;
      const itemType = 'mediaalbum';

      // Item has no reactions
      assertNoReactions(checkSuccess(await itemClient.get(itemId)));

      // Post item reaction
      created = checkSuccess(await reactionClient.post(
        getReactionPayload({ itemType, itemId, type: reactionTypes[0] })));
      reactions.push(created.id);

      // Item has one reaction
      assertFirstReaction(checkSuccess(await itemClient.get(itemId)));

      // Post item reaction
      created = checkSuccess(await reactionClient.post(
        getReactionPayload({ itemType, itemId, type: reactionTypes[1] })));
      reactions.push(created.id);

      // Item has two reactions
      assertTwoReactions(checkSuccess(await itemClient.get(itemId)));

      // Owner can see they have two reactions
      assertOwnerReactions(checkSuccess(await itemClientForPlainUser.get(itemId)));

      // Delete one reaction
      await reactionClient.delete(reactions[0]);

      // Item has one reaction
      assertSecondReaction(checkSuccess(await itemClient.get(itemId)));

      // Delete another reaction
      await reactionClient.delete(reactions[1]);

      // Item has no reactions
      assertNoReactions(checkSuccess(await itemClient.get(itemId)));
    });

    test('Comments', async () => {});

    test('Photos', async () => {});

    test('Videos', async () => {});
  });
});