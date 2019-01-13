import { default as StatelessClient } from './RestClient/StatelessClient';
import ResourceClient from './RestClient/ResourceClient';
import { clearCache } from './RestClient/cache';
import { hostUrl } from './RestClient/constants';
import { USER } from './RestClient/resources';
import {
  plainUser,
  bannedUser,
  otherUser,
  writerUser,
  editorUser,
  adminUser,
  pendingUser,
} from './RestClient/users';
import { getSortedKeysAsString } from './RestClient/utils';

const client = new StatelessClient(hostUrl);
const userClient = new ResourceClient(client, USER);

beforeAll(() => {
  // Synchronous operation.
  clearCache();
});

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
    '"longitude","site","userId","username","zip"]';

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
    userClient.setDebugBackend(true);
    const { statusCode, body } = await userClient.get(plainUser.id);
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
  // testCreateUserAsPlainUserFail - It should fail to create a user because only guest is allowed to do so
  // testCreateUserAsGuestFail - It should fail to create a user and return errors because of missing/invalid data
  // testCreateUserAsGuestSuccess - It should create a new user and return its id
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
