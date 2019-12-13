export const getSortedKeysAsString = (obj) => {
  return JSON.stringify(Object.keys(obj).sort());
};

export const getDateForBackend = (offsetAsSeconds = 0) => {
  const newDate = new Date((new Date()).getTime() + (offsetAsSeconds * 1000));

  const Y = newDate.getFullYear();
  let M = newDate.getMonth() + 1;
  M = M < 10 ? `0${M}` : M;
  let D = newDate.getDate();
  D = D < 10 ? `0${D}` : D;
  let h = newDate.getHours();
  h = h < 10 ? `0${h}` : h;
  let i = newDate.getMinutes();
  i = i < 10 ? `0${i}` : i;
  let s = newDate.getSeconds();
  s = s < 10 ? `0${s}` : s;
  let v = newDate.getMilliseconds();
  v = v < 10 ? `0${v}` : v;

  return `${Y}-${M}-${D} ${h}:${i}:${s}.${v}`;
}

export const uuidv4 = () => {
  return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (c) => {
    const r = (Math.random() * 16) | 0,
      v = c == 'x' ? r : (r & 0x3) | 0x8;
    return v.toString(16);
  });
};

export const looksLikeUUID = (str) => {
  const regexp = /[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/;
  return !!str.match(regexp);
};

export const checkResponse = ({statusCode, body}, expectedStatusCode, msg = '') => {
  if (statusCode != expectedStatusCode) {
    console.error('Response status code:', statusCode);
    console.error('Response body:', JSON.stringify(body, null, 2));
    if (!body) {
      throw new Error('No response body ' + msg);
    }
    if (body.error) {
      throw new Error('Stopped because of unexpected error in body ' + msg);
    } else {
      throw new Error(`Unexpected HTTP response code: ${statusCode} - expected: ${expectedStatusCode} ` + msg);
    }
  }
  return body;
};
export const checkSuccess = (response, msg) => checkResponse(response, 200, msg);
export const checkBadRequest = (response, msg) => checkResponse(response, 400, msg);
export const checkUnauthorised = (response, msg) => checkResponse(response, 403, msg);
export const checkNotFound = (response, msg) => checkResponse(response, 404, msg);