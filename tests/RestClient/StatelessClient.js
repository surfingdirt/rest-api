import rp from 'request-promise';

import { dateSetter } from './constants';

const DEBUG_QUERY_PARAMS = 'XDEBUG_SESSION_START=PHP_STORM';
// Only network errors throw exceptions (not application exceptions)
const SIMPLE_REQUESTS = false;

export const TYPE_JSON = 'json';
export const TYPE_FORM_DATA = 'formData';

export const getResourcePath = (type, id) => {
  if (id) {
    return `/${type}/${id}`;
  } else {
    return `/${type}`;
  }
};

export default class RestClient {
  constructor(hostUrl) {
    this.hostUrl = hostUrl;
  }

  getFullUri(path, debugBackend) {
    return `${this.hostUrl}${path}${debugBackend ? `?${DEBUG_QUERY_PARAMS}` : ''}`;
  }

  getHeaders(token) {
    const headers = {};
    if (token) {
      headers['Authorization'] = `Bearer ${token}`;
    }
    return headers;
  }

  /*
   * State-less methods: token must be passed-in when necessary.
   */
  async get({ path, token = null, debugBackend = false }) {
    const options = {
      uri: this.getFullUri(path, debugBackend),
      headers: this.getHeaders(token),
      json: true,
      simple: SIMPLE_REQUESTS,
      resolveWithFullResponse: true,
    };

    return await rp(options);
  }

  async post({ path, data, type = TYPE_FORM_DATA, token = null, debugBackend = false }) {
    const options = {
      method: 'POST',
      uri: this.getFullUri(path, debugBackend),
      headers: this.getHeaders(token),
      simple: SIMPLE_REQUESTS,
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

  async delete({ path, token = null, debugBackend = false }) {
    const options = {
      method: 'DELETE',
      uri: this.getFullUri(path, debugBackend),
      headers: this.getHeaders(token),
      simple: SIMPLE_REQUESTS,
      resolveWithFullResponse: true,
    };
    return await rp(options);
  }

  async put() {
    // TODO
  }
  async setDate(date = null) {
    const usp = new URLSearchParams();
    usp.append(dateSetter.arg, date || 'NOW');

    return await this.get({ path: `${dateSetter.path}?${usp.toString()}` });
  }

  /*
   * Stateful methods
   */
  setResource(resourceName) {}

  login() {}

  logout() {}
}
