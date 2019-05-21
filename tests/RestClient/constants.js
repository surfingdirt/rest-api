import {realpathSync} from 'fs';

export const hostUrl = 'http://test.api.ridedb.wrk';
export const JWT_SECRET = 'wouldntyouliketoknow5748right';
export const JWT_TTL = 24 * 3600;
export const port = 80;
export const baseUrl = '/';
export const cacheDir = realpathSync(__dirname + '/../../data/cache/test/');
export const dateSetter = { path: '/test/freeze-time', arg: 'datetime' };

// These paths are relative to the API (ie the Docker image).
export const LOCAL_THUMB_PATH = '../tests/data/files/testUpload.jpg';
export const LOCAL_BAD_THUMB_PATH = '../tests/data/files/testBadUpload.jpg';