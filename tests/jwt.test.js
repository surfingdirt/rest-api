import { default as StatelessClient, getResourcePath } from './RestClient/StatelessClient';
import { clearCache } from './RestClient/cache';
import { hostUrl, JWT_TTL } from './RestClient/constants';
import { USER, TOKEN } from './RestClient/resources';
import { plainUser, bannedUser } from './RestClient/users';
import { getSortedKeysAsString, getDateForBackend } from './RestClient/utils';

const user1Path = getResourcePath(USER, 1);
const tokenPath = getResourcePath(TOKEN);

const user1publicInfo =
  '["avatar","city","country","date","gear","gender","lang","latitude","level",' +
  '"longitude","otherSports","rideType","userId","username","zip"]';

const invalidToken = 'no-way-this-is-gonna-work';

const client = new StatelessClient(hostUrl);

beforeAll(() => {
  // Synchronous operation.
  clearCache();
});

test('banned user cannot login', async () => {
  const { username, password } = bannedUser;
  const loginResponse = await client.post({
    path: tokenPath,
    data: { userP: password, username: username },
  });
  expect(loginResponse.statusCode).toBe(403);
});

test('logged-out request to user 1: should see public info', async () => {
  const path = user1Path;
  const response = await client.get({ path });

  expect(response.statusCode).toBe(200);
  expect(getSortedKeysAsString(response.body)).toEqual(user1publicInfo);
});

test('request to user 1 with invalid token', async () => {
  const path = user1Path;
  const token = invalidToken;

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
  const loginResponseBody = JSON.parse(loginResponse.body);
  expect(loginResponseBody.token).toBeDefined();
  user1Token = loginResponseBody.token;

  // Tweak server time to be seconds after the expiration date
  await client.setDate(getDateForBackend(JWT_TTL + 2));

  // Request user 1 with user1Token while server thinks it's the future
  const futureResponse = await client.get({ path: user1Path, token: user1Token });
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
