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
  createdUser,
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
      debugBackend: true,
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

  describe('User GET', () => {
    const plainUserPublicInfo =
      '["avatar","city","date","firstName","lang","lastName",' +
      '"site","userId","username"]';

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
      const { statusCode, body } = await userClient.post({
        username: createdUser.username,
        userP: createdUser.password,
        userPC: createdUser.password,
        email: createdUser.email,
      });
      expect(statusCode).toEqual(200);
      expect(getSortedKeysAsString(body)).toEqual(createdUserKeys);
      expect(parseInt(body.userId, 10)).toEqual(createdUser.id);
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
      await userClient.setUser(createdUser);
      const { statusCode, body } = await userClient.put(createdUser.id, { firstName: 'yes' });
      expect(statusCode).toEqual(200);
      expect(body.firstName).toEqual('yes');
    });

    test('Requests with password mismatch are rejected', async () => {
      await userClient.setUser(createdUser);
      const { statusCode, body } = await userClient.put(createdUser.id, { userP: '123', userPC: '345' });
      expect(statusCode).toEqual(400);
      expect(body).toEqual({"errors": {"userPC": ["notSame"]}});
    });

    test('Requests with matching passwords are successful, and old password is made invalid', async () => {
      const newPassword = '345';

      await userClient.setUser(createdUser);
      const { statusCode } = await userClient.put(createdUser.id, { userP: newPassword, userPC: newPassword });
      expect(statusCode).toEqual(200);

      userClient.setToken(null);
      try {
        await userClient.setUser(createdUser);
      } catch (e) {
        expect(e.message).toEqual(`Login as '${createdUser.username}' failed`);
      }

      userClient.setToken(null);
      await userClient.setUser({username: createdUser.username, password: newPassword});
    });
  });

  describe('User DELETE', () => {
    let userIdToDelete;
    const userToDelete = {username: 'userToDelete', password: '123456789'};

    beforeAll(async() => {
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
      userClient.setDebugBackend(true);
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

describe.skip('Image tests', () => {
  // describe('POST ACLS: all and only bad users get a 403', () => {
  //   test('Guest cannot POST', async () => {});
  //   test('Pending user cannot POST', async () => {});
  //   test('Banned user cannot POST', async () => {});
  //   test('Plain user can POST', async () => {});
  //   test('Writer user can POST', async () => {});
  //   test('Editor user can POST', async () => {});
  //   test('Admin can POST', async () => {});
  // });
  //
  // describe('GET/PUT/DELETE ACLS: everyone gets a 403', () => {
  //   test('Guest cannot GET/PUT/DELETE', async () => {});
  //   test('Plain user cannot GET/PUT/DELETE', async () => {});
  //   test('Admin cannot GET/PUT/DELETE', async () => {});
  // });
  //
  // describe('POST error cases', () => {
  //   test('Admin cannot GET/PUT/DELETE', async () => {});
  // });
  //
  // describe('POST success cases', () => {
  //
  // });
});

describe.skip('Media tests', () => {

});

describe.skip('Album tests', () => {});