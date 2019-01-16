import { getResourcePath, StatelessClient } from './StatelessClient';
import { TOKENS } from './resources';

export default class ResourceClient {
  constructor(client, resource, debugBackend = false) {
    this.resource = resource;
    this.client = client;
    this.token = null;
    this.debugBackend = debugBackend;
  }

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

  setDebugBackend(debugBackend) {
    this.debugBackend = !!debugBackend;
  }

  async setUser(user) {
    this.token = await this.client.login(user.username, user.password);
  }

  setToken(token) {
    this.token = token;
  }
}
