import { default as StatelessClient, getResourcePath } from './RestClient/StatelessClient';
import ResourceClient from './RestClient/ResourceClient';
import { clearCache } from './RestClient/cache';
import { hostUrl } from './RestClient/constants';
import { USER } from './RestClient/resources';
import { plainUser, otherUser, writerUser, editorUser, adminUser } from './RestClient/users';
import { getSortedKeysAsString } from './RestClient/utils';

const plainUserPublicInfo =
  '["avatar","city","country","date","firstName","lang","lastName","latitude",' +
  '"longitude","site","userId","username","zip"]';

const plainUserSelfInfo =
  '["avatar","city","country","date","email","firstName","lang","lastName","latitude",' +
  '"longitude","site","userId","username","zip"]';

const plainUserAdminInfo =
  '["avatar","city","country","date","email","firstName","lang","lastLogin","lastName","latitude",' +
  '"longitude","site","status","userId","username","zip"]';

const client = new StatelessClient(hostUrl);
const userClient = new ResourceClient(client, USER);

beforeAll(() => {
  // Synchronous operation.
  clearCache();
});

test('Missing user request should return a 404', async () => {
  const { statusCode } = await userClient.get(2500);
  expect(statusCode).toEqual(404);
});

test("Retrieve plainuser's data as guest", async () => {
  userClient.setToken(null);
  const { body } = await userClient.get(plainUser.id);
  expect(getSortedKeysAsString(body)).toEqual(plainUserPublicInfo);
});

test("Retrieve plainuser's data as other user", async () => {
  // Other user request
  await userClient.setUser(otherUser);
  userClient.setDebugBackend(true);
  const { body } = await userClient.get(plainUser.id);
  expect(getSortedKeysAsString(body)).toEqual(plainUserPublicInfo);
});

test("Retrieve plainuser's data as writer user", async () => {
  // Writer user request
  await userClient.setUser(writerUser);
  const { body } = await userClient.get(plainUser.id);
  expect(getSortedKeysAsString(body)).toEqual(plainUserPublicInfo);
});

test("Retrieve plainuser's data as admin", async () => {
  // Admin user request
  await userClient.setUser(adminUser);
  const { body } = await userClient.get(plainUser.id);
  expect(getSortedKeysAsString(body)).toEqual(plainUserAdminInfo);
});

test("Retrieve plainuser's data as self", async () => {
  // Self user request
  await userClient.setUser(plainUser);
  const { body } = await userClient.get(plainUser.id);
  expect(getSortedKeysAsString(body)).toEqual(plainUserSelfInfo);
});

// test.only('', async () => {
//
// });

// testAllUsersListAsGuest - It should retrieve the list of all valid users in the DB
// testAllUsersListAsAdmin - It should retrieve the list of all users in the DB
// testBannedUserAsGuest - It should return a 403 error
// testPendingUserAsGuest - It should return a 403 error
// test3rdAnd4thUsersAsGuest - It should retrieve the 3rd and 4th valid users
// test2ndAnd3rdUsersAsGuest - It should retrieve the 2nd and 3rd valid users, sorted by username ascending
// test2ndAnd3rdUsersDescAsGuest - It should retrieve the 2nd and 3rd valid users, sorted by username descending
// testPlainUserJsonAsGuest - It should retrieve the user with given id in json format
// testCreateUserAsPlainUserFail - It should fail to create a user because only guest is allowed to do so
// testCreateUserAsGuestFail - It should fail to create a user and return errors because of missing/invalid data
// testCreateUserAsGuestSuccess - It should create a new user and return its id
// testAdminMakesCreateduserAMember - It should update createduser status to member
// testUpdateCreatedUserAsPlainuser - It should fail to update the existing user 10
// testUpdateCreatedUserAsSelf - It should update the existing user 10
// testFailUpdateCreatedUserPassword - It should fail to update createduser's password if passwords are different
// testUpdateCreatedUserPassword - It should update createduser's password
// testFailToLoginCreatedUserWithOldPassword - It should fail to login with the old password
// testLoginWithUpdatedCreatedUserPassword - It should let createduser login with the new password
// testPlainuserCannotUpdateCreatedUser - It should not let plain user edit createduser
// failToDeletePlainUserAsGuest - It should return a 403 status code
// deletePlainUserAsAdmin - It should delete plainuser
// plainUserIsNotFound - xxxxx
