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

  return `${Y}-${M}-${D} ${h}:${i}:${s}`;
}