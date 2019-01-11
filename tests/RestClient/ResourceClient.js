import { getResourcePath, StatelessClient } from './StatelessClient';
import { TOKENS } from './resources';

export default class ResourceClient {
  constructor(client, resource) {
    this.resource = resource;
    this.client = client;
    this.token = null;
  }

  async login(username, password) {
    try {
      const loginResponse = await this.client.post({
        path: getResourcePath(TOKENS),
        data: {userP: password, username: username},
      });
      const loginResponseBody = JSON.parse(loginResponse.body);
      if (!loginResponseBody.token) {
        throw new Error();
      }
      this.token = loginResponseBody.token;
    } catch (e) {
      throw new Error(`Login as '${username}' failed`);
    }
  }

  async logout() {
    try {
      await this.client.delete({
        path: getResourcePath(TOKENS),
        token: this.token,
      });
    } catch (e) {
      throw new Error(`Logout with token '${this.token}' failed`);
    }
    this.token = null;
  }

  async list() {}
  async get(id) {}
  async post(id, data, files) {}
  async put(id, data, files) {}
  async delete(id) {}
}
