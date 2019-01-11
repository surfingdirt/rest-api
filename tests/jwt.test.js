import rp from 'request-promise';

import {default as RestClient, getResourcePath, TYPE_FORM_DATA} from "./RestClient";
import { clearCache } from './RestClient/cache';
import { hostUrl } from './RestClient/constants';
import {RIDERS, TOKENS} from "./RestClient/resources";
import { plainUser } from './RestClient/users';
import {getSortedKeysAsString} from "./RestClient/utils";

const rider1Path = getResourcePath(RIDERS, 1);
const tokensPath = getResourcePath(TOKENS);

const rider1publicInfo =
  '["avatar","city","country","date","gear","gender","lang","latitude","level",' +
  '"longitude","otherSports","rideType","userId","username","zip"]';

const invalidToken = 'nowaythisisgonnawork';

const client = new RestClient(hostUrl);

beforeAll(() => {
  clearCache();
});

/*
 Scenario:
 x logged-out request to user 1 => fine

 x request to user 1 with invalid token => error

 x login request as user 1 => fine
 - logged-in request to user 1 in the future so token has expired => error
 - logged-in request to user 1 => fine
 - log-out request => fine
 - request with deleted token to user 1 => error
 */

test('logged-out request to user 1: should see public info', async () => {
  const path = rider1Path;
  const response = await client.get({ path });

  expect(response.statusCode).toBe(200);
  expect(getSortedKeysAsString(response.body)).toEqual(rider1publicInfo);
});

test('request to user 1 with invalid token', async () => {
  const path = rider1Path;
  const token = invalidToken;

  const response = await client.get({ path, token });
  expect(response.statusCode).toBe(403);

});

test('token management is working properly', async () => {
  const path = tokensPath;
  const { username, password } = plainUser;

  // Login request as user 1
  const loginResponse = await client.post({ path, data: {'userP': password, 'username': username} });
  expect(loginResponse.statusCode).toBe(200);
  const loginResponseBody = JSON.parse(loginResponse.body);
  expect(loginResponseBody.token).toBeDefined();

  // Tweak server time to be in the far future

  // Request user 1 with token while server thinks it's the future

  // Sets time back
});
