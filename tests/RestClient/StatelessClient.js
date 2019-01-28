import rp from 'request-promise';
import fs from 'fs';
import { basename } from 'path';

import { dateSetter } from './constants';
import { TOKEN } from './resources';

// Only network errors throw exceptions (not application exceptions)
const SIMPLE_REQUESTS = false;

export const getResourcePath = (type, id = null) => {
  let path;
  if (id) {
    path = `/${type}/${id}`;
  } else {
    path = `/${type}`;
  }

  return path;
};

export default class StatelessClient {
  constructor(hostUrl) {
    this.hostUrl = hostUrl;
    this.uuids = [];
  }

  getFullUri({ path, urlParams = null, debugBackend = false }) {
    let fullUri = `${this.hostUrl}${path}`;
    const usp = new URLSearchParams();
    if (urlParams) {
      for (let arg in urlParams) {
        usp.append(arg, urlParams[arg]);
      }
    }
    if (debugBackend) {
      usp.append('XDEBUG_SESSION_START', 'PHP_STORM');
    }
    const argString = usp.toString();
    if (argString) {
      fullUri = `${fullUri}?${argString}`;
    }

    return fullUri;
  }

  getHeaders(token) {
    const headers = {
      Accept: 'application/json',
      'Content-Type': 'application/json',
    };
    if (token) {
      headers['Authorization'] = `Bearer ${token}`;
    }
    if (this.uuids.length > 0) {
      headers['X-uuids'] = JSON.stringify(this.uuids);
    }
    return headers;
  }

  setUUIDs(uuids) {
    this.uuids = uuids;
  }

  clearUUIDs() {
    this.uuids = [];
  }

  /*
   * State-less methods: token must be passed-in when necessary.
   */
  async get({ path, token = null, urlParams = null, debugBackend = false }) {
    const options = {
      uri: this.getFullUri({ path, urlParams, debugBackend }),
      headers: this.getHeaders(token),
      json: true,
      simple: SIMPLE_REQUESTS,
      resolveWithFullResponse: true,
    };

    const response = await rp(options);
    this.clearUUIDs();

    return response;
  }

  async _sendData({ method, path, data, token = null, debugBackend = false }) {
    const options = {
      method,
      uri: this.getFullUri({ path, debugBackend }),
      headers: this.getHeaders(token),
      simple: SIMPLE_REQUESTS,
      resolveWithFullResponse: true,
      json: true,
      body: data,
    };

    const response = await rp(options);
    this.clearUUIDs();

    return response;
  }

  post({ path, data, token, debugBackend }) {
    return this._sendData({ data, method: 'POST', path, token, debugBackend });
  }

  async postFormData({ path, data, files = [], token, urlParams, debugBackend }) {
    const fileData = [];
    files.forEach(({ filePath, contentType }) => {
      const filename = basename(filePath);
      const value = fs.createReadStream(`./data/files/${filePath}`);
      fileData.push({
        value,
        options: {
          filename,
          contentType,
        },
      });
    });

    const options = {
      method: 'POST',
      uri: this.getFullUri({ path, debugBackend }),
      headers: this.getHeaders(token),
      simple: SIMPLE_REQUESTS,
      resolveWithFullResponse: true,
      // 'files' is the name of the variable holding upload info on the backend
      formData: Object.assign({}, data, { files: fileData }),
    };
    const response = await rp(options);
    this.clearUUIDs();

    return response;
  }

  put({ path, data, token, debugBackend }) {
    return this._sendData({ data, method: 'PUT', path, token, debugBackend });
  }

  async delete({ path, token = null, debugBackend = false }) {
    const options = {
      method: 'DELETE',
      uri: this.getFullUri({ path, debugBackend }),
      headers: this.getHeaders(token),
      simple: SIMPLE_REQUESTS,
      resolveWithFullResponse: true,
    };
    const response = await rp(options);
    this.clearUUIDs();

    return response;
  }

  async login(username, password) {
    try {
      const loginResponse = await this.post({
        path: getResourcePath(TOKEN),
        data: { userP: password, username: username },
      });
      const loginResponseBody = loginResponse.body;
      if (!loginResponseBody.token) {
        throw new Error();
      }
      return loginResponseBody.token;
    } catch (e) {
      throw new Error(`Login as '${username}' failed`);
    }
  }

  async logout(token) {
    try {
      await this.delete({
        path: getResourcePath(TOKEN),
        token,
      });
    } catch (e) {
      throw new Error(`Logout with token '${token}' failed`);
    }
  }

  async setDate(date = null) {
    const usp = new URLSearchParams();
    usp.append(dateSetter.arg, date || 'NOW');
    usp.append('XDEBUG_SESSION_START', 'PHP_STORM');

    return await this.get({ path: `${dateSetter.path}?${usp.toString()}` });
  }

  async clearCache() {
    await this.get({ path: '/test/clear-cache' });
  }
}
