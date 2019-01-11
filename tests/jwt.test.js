import fetch from 'node-fetch';
import rp from 'request-promise';

import constants from './constants';
import { clearCache } from './cache';
import { plainUser } from './users';

const TYPE_JSON = 'json';
const TYPE_FORM_DATA = 'formData';

const { host } = constants;
const hostUrl = `http://${host}`;

const rider1Path = '/riders/1';
const tokensPath = '/tokens';

const rider1publicInfo =
  '["avatar","city","country","date","gear","gender","lang","latitude","level",' +
  '"longitude","otherSports","rideType","userId","username","zip"]';

const invalidToken = 'nowaythisisgonnawork';

function getSortedKeysAsString(obj) {
  return JSON.stringify(Object.keys(obj).sort());
}

function getUri(path, debugBackend) {
  return `${hostUrl}${path}${debugBackend ? '?XDEBUG_SESSION_START=PHP_STORM' : ''}`;
}

function getHeaders(token) {
  const headers = {};
  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }
  return headers;
}

async function makeGetRequest({ path, token = null, debugBackend = false }) {
  const options = {
    uri: getUri(path, debugBackend),
    headers: getHeaders(token),
    json: true,
    simple: false, // Only network errors throw exceptions (not application exceptions)
    resolveWithFullResponse: true,
  };

  return await rp(options);
}

async function makePostRequest({
  path,
  data,
  type = TYPE_JSON,
  token = null,
  debugBackend = false,
}) {
  const options = {
    method: 'POST',
    uri: getUri(path, debugBackend),
    headers: getHeaders(token),
    simple: false, // Only network errors throw exceptions (not application exceptions)
    resolveWithFullResponse: true,
  };

  switch (type) {
    case TYPE_JSON:
      Object.assign(options, { json: true, body: data });
      break;
    case TYPE_FORM_DATA:
      Object.assign(options, { formData: data });
      break;
    default:
      throw new Error(`Type not supported: '${type}'`);
  }

  return await rp(options);
}

beforeAll(() => {
  clearCache();
});

/*
 Scenario:
 x logged-out request to user 1 => fine

 x request to user 1 with invalid token => error

 - login request as user 1 => fine
 - logged-in request to user 1 in the future so token has expired => error
 - logged-in request to user 1 => fine
 - log-out request => fine
 - request with deleted token to user 1 => error
 */

test('logged-out request to user 1: should see public info', async () => {
  const path = rider1Path;
  const response = await makeGetRequest({ path });

  expect(response.statusCode).toBe(200);
  expect(getSortedKeysAsString(response.body)).toEqual(rider1publicInfo);
});

test('request to user 1 with invalid token', async () => {
  const path = rider1Path;
  const token = invalidToken;

  const response = await makeGetRequest({ path, token });
  expect(response.statusCode).toBe(403);

});

test('token management is working properly', async () => {
  const path = tokensPath;

  const { username, password } = plainUser;
  const data = {'userP': password, 'username': username};

  const type = TYPE_FORM_DATA;

  const loginResponse = await makePostRequest({ path, data, type });
  expect(loginResponse.statusCode).toBe(200);

  const loginResponseBody = JSON.parse(loginResponse.body);
  expect(loginResponseBody.token).toBeDefined();
});
