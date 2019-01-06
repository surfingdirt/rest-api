import fetch from 'node-fetch';

import constants from './constants';

const { host } = constants;
const accept = 'application/json; q=1.0';

test.only('logged out user should get a 200 and a cookie', async () => {
  // todo: cache.clear
  const url = `http://${host}/`;

  const response = await fetch(url, {
    headers: {
      Accept: accept,
      Connection: 'keep-alive',
    },
  });
  expect(response.status).toBe(200);
  expect(response.headers.has('set-cookie')).toBe(true);
});
