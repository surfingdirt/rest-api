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
    return await this.client.get({ path, token: this.token, urlParams, debugBackend: this.debugBackend });
  }

  async get(id, urlParams = null) {
    const path = getResourcePath(this.resource, id);
    return await this.client.get({ path, token: this.token, urlParams, debugBackend: this.debugBackend });
  }

  async post(id, data, files) {}
  async put(id, data, files) {}
  async delete(id) {}

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
