export const getSortedKeysAsString = (obj) => {
  return JSON.stringify(Object.keys(obj).sort());
};
