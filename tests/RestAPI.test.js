import ResourceClient from './RestClient/ResourceClient';
import { default as StatelessClient, getResourcePath } from './RestClient/StatelessClient';
import { clearCacheFiles } from './RestClient/cache';
import { hostUrl, JWT_TTL } from './RestClient/constants';
import { USER, TOKEN } from './RestClient/resources';
import {
  plainUser,
  bannedUser,
  adminUser,
  writerUser,
  pendingUser,
  editorUser,
  otherUser,
} from './RestClient/users';
import { cleanupTestDatabase } from './RestClient/database';
import { getDateForBackend, getSortedKeysAsString } from './RestClient/utils';

const user1Path = getResourcePath(USER, 1);
const tokenPath = getResourcePath(TOKEN);

const client = new StatelessClient(hostUrl);

beforeAll(async (done) => {
  await cleanupTestDatabase();
  done();
});

beforeEach(() => {
  clearCacheFiles();
  return client.clearCache();
});

describe('Token tests', () => {
  test('logged-out request results in 200', async () => {
    const path = user1Path;
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
    const path = user1Path;
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
      path: user1Path,
      token: user1Token,
      debugBackend: true,
    });
    expect(futureResponse.statusCode).toBe(403);

    // Sets time back
    await client.setDate();

    // Request user 1 with user1Token while server thinks it's the present again
    const loginNowResponse = await client.get({ path: user1Path, token: user1Token });
    expect(loginNowResponse.statusCode).toBe(200);

    // Delete token as self (ie, logout)
    const deleteResponse = await client.delete({
      path: getResourcePath(TOKEN),
      token: user1Token,
    });
    expect(deleteResponse.statusCode).toBe(200);

    // Request user 1 with blacklisted user1Token
    const loginAgainResponse = await client.get({ path: user1Path, token: user1Token });
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

  describe('Single user data ACLs', () => {
    const plainUserPublicInfo =
      '["avatar","city","country","date","firstName","lang","lastName","latitude",' +
      '"longitude","site","userId","username","zip"]';

    const plainUserSelfInfo =
      '["avatar","city","country","date","email","firstName","lang","lastName","latitude",' +
      '"longitude","site","status","userId","username","zip"]';

    const plainUserAdminInfo =
      '["avatar","city","country","date","email","firstName","lang","lastLogin","lastName","latitude",' +
      '"longitude","site","status","userId","username","zip"]';

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

  describe('Listing users', () => {
    test('Retrieve all valid users as guest', async () => {
      await userClient.setToken(null);
      const { body } = await userClient.list();
      const userIds = body.map((u) => u.userId).join(',');
      expect(userIds).toEqual('1,4,5,6,7');
    });

    test('Retrieve all users in the database as admin', async () => {
      await userClient.setUser(adminUser);
      const { body } = await userClient.list();
      const userIds = body.map((u) => u.userId).join(',');
      expect(userIds).toEqual('1,3,4,5,6,7,8');
    });

    test('Retrieve 3rd and 4th valid users as guest', async () => {
      await userClient.setToken(null);
      const { body } = await userClient.list({ start: 2, count: 2 });
      const userIds = body.map((u) => u.userId).join(',');
      expect(userIds).toEqual('5,6');
    });

    test('Retrieve 2nd and 3rd valid users sorted by username ascending as guest', async () => {
      await userClient.setToken(null);
      const { body } = await userClient.list({ start: 1, count: 2, sort: 'username' });
      const userIds = body.map((u) => u.userId).join(',');
      expect(userIds).toEqual('5,7');
    });

    test('Retrieve 2nd and 3rd valid users sorted by username descending as guest', async () => {
      await userClient.setToken(null);
      const { body } = await userClient.list({ start: 1, count: 2, sort: 'username', dir: 'desc' });
      const userIds = body.map((u) => u.userId).join(',');
      expect(userIds).toEqual('1,7');
    });
  });

  describe('Create users', () => {
    const createdUserKeys =
      '["avatar","city","country","date","email","firstName","lang","lastName","latitude",' +
      '"longitude","site","status","userId","username","zip"]';
// Need email and status: must treat user creating a new one as himself

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
      const { statusCode, body } = await userClient.post({
        username: 'somenewuser',
        userP: '123456789',
        userPC: '123456789',
        email: 'someemail@email.com',
      });
      expect(statusCode).toEqual(200);
      expect(getSortedKeysAsString(body)).toEqual(createdUserKeys);
    });
  });

  describe('Update users', () => {
    // testAdminMakesCreateduserAMember - It should update createduser status to member
    // testUpdateCreatedUserAsPlainuser - It should fail to update the existing user 10
    // testUpdateCreatedUserAsSelf - It should update the existing user 10
    // testFailUpdateCreatedUserPassword - It should fail to update createduser's password if passwords are different
    // testUpdateCreatedUserPassword - It should update createduser's password
    // testFailToLoginCreatedUserWithOldPassword - It should fail to login with the old password
    // testLoginWithUpdatedCreatedUserPassword - It should let createduser login with the new password
    // testPlainuserCannotUpdateCreatedUser - It should not let plain user edit createduser
  });

  describe('Delete users', () => {
    // failToDeletePlainUserAsGuest - It should return a 403 status code
    // deletePlainUserAsAdmin - It should delete plainuser
    // plainUserIsNotFound - xxxxx
  });
});
