import jwt from 'jsonwebtoken';
import { JWT_SECRET, JWT_TTL } from './constants';

import { getResourcePath } from './StatelessClient';

export default class ResourceClient {
  constructor(client, resource, debugBackend = false) {
    this.resource = resource;
    this.client = client;
    this.token = null;
    this.debugBackend = debugBackend;
  }

  /* REST methods */

  async list(urlParams = null) {
    const path = getResourcePath(this.resource);
    return await this.client.get({
      path,
      token: this.token,
      urlParams,
      debugBackend: this.debugBackend,
    });
  }

  async get(id, urlParams = null) {
    const path = getResourcePath(this.resource, id);
    return await this.client.get({
      path,
      token: this.token,
      urlParams,
      debugBackend: this.debugBackend,
    });
  }

  async post(data, urlParams = null) {
    const path = getResourcePath(this.resource);
    return await this.client.post({
      path,
      data,
      token: this.token,
      urlParams,
      debugBackend: this.debugBackend,
    });
  }

  async postFormData(data, files, urlParams = null) {
    const path = getResourcePath(this.resource);
    return await this.client.postFormData({
      path,
      data,
      files,
      token: this.token,
      urlParams,
      debugBackend: this.debugBackend,
    });
  }

  async put(id, data, urlParams = null) {
    const path = getResourcePath(this.resource, id);
    return await this.client.put({
      path,
      data,
      token: this.token,
      urlParams,
      debugBackend: this.debugBackend,
    });
  }

  async delete(id) {
    const path = getResourcePath(this.resource, id);
    return await this.client.delete({
      path,
      token: this.token,
      debugBackend: this.debugBackend,
    });
  }

  /* Helpers */

  setUUIDs(uuids) {
    this.client.setUUIDs(uuids);
  }

  clearUUIDs() {
    this.client.clearUUIDs();
  }


  setDebugBackend(debugBackend) {
    this.debugBackend = !!debugBackend;
  }

  async setUser(user) {
    if (user.id) {
      this.token = jwt.sign({
        uid: user.id,
        exp: Math.floor(Date.now() / 1000) + JWT_TTL
      }, JWT_SECRET);
    } else {
      this.token = await this.client.login(user.username, user.password);
    }
  }

  setToken(token) {
    this.token = token;
  }
}
