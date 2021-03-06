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
    this.oAuthTokenEmail = null;
    this.localAvatarUrl = null;
    this.localVideoThumb = null;
    this.locale = 'en-US';
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
    if (this.locale) {
      headers['Accept-Language'] = this.locale;
    }

    if (this.uuids.length > 0) {
      headers['X-uuids'] = JSON.stringify(this.uuids);
    }
    if (this.oAuthTokenEmail) {
      headers['X-oAuthTokenEmail'] = this.oAuthTokenEmail;
    }
    if (this.localVideoThumb) {
      headers['X-localVideoThumb'] = this.localVideoThumb;
    }
    if (this.localAvatarUrl) {
      headers['X-localAvatarUrl'] = this.localAvatarUrl;
    }
    return headers;
  }

  setLocale(locale) {
    this.locale = locale;
  }

  setUUIDs(uuids) {
    this.uuids = uuids;
  }

  clearUUIDs() {
    this.uuids = [];
  }

  setOAuthTokenEmail(email) {
    this.oAuthTokenEmail = email;
  }

  clearOAuthTokenEmail() {
    this.oAuthTokenEmail = null;
  }

  setLocalVideoThumb(url) {
    this.localVideoThumb = url;
  }

  clearLocalVideoThumb() {
    this.localVideoThumb = null;
  }

  setLocalAvatarUrl(url) {
    this.localAvatarUrl = url;
  }

  clearLocalAvatarUrl() {
    this.localAvatarUrl = null;
  }

  postRequestCleanup() {
    this.clearUUIDs();
    this.clearLocalVideoThumb();
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
    this.postRequestCleanup();

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
    this.postRequestCleanup();

    return response;
  }

  post({ path, data, token, debugBackend }) {
    return this._sendData({ data, method: 'POST', path, token, debugBackend });
  }

  async postFormData({ path, data, files = [], token, urlParams, debugBackend }) {
    const fileData = [];
    files.forEach(({ filePath, contentType }) => {
      const filename = basename(filePath);
      const value = fs.createReadStream(fs.realpathSync(__dirname + `/../data/files/${filePath}`));
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
      json: true,
      uri: this.getFullUri({ path, debugBackend }),
      headers: this.getHeaders(token),
      simple: SIMPLE_REQUESTS,
      resolveWithFullResponse: true,
      // 'files' is the name of the variable holding upload info on the backend
      formData: Object.assign({}, data, { 'files[]': fileData }),
    };
    const response = await rp(options);
    this.postRequestCleanup();

    return response;
  }

  put({ path, data, token, debugBackend }) {
    return this._sendData({ data, method: 'PUT', path, token, debugBackend });
  }

  async delete({ path, token = null, debugBackend = false }) {
    const options = {
      method: 'DELETE',
      json: true,
      uri: this.getFullUri({ path, debugBackend }),
      headers: this.getHeaders(token),
      simple: SIMPLE_REQUESTS,
      resolveWithFullResponse: true,
    };
    const response = await rp(options);
    this.postRequestCleanup();

    return response;
  }

  async login(username, password, debugBackend = false) {
    try {
      const loginResponse = await this.post({
        path: getResourcePath(TOKEN),
        data: { userP: password, username: username },
        debugBackend
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

  async setDate(date = null, debugBackend = false) {
    return await this.get({
      path: dateSetter.path,
      urlParams: { [dateSetter.arg]: date || 'NOW' },
      debugBackend,
    });
  }

  async clearCache() {
    await this.get({ path: '/test/clear-cache' });
  }

  async clearPublicFiles() {
    await this.get({ path: '/test/clear-public-files' });
  }
}
