import fs from 'fs';
import { cacheDir } from './constants';

export const clearCache = () => {
  const files = fs.readdirSync(cacheDir);

  for (let i = 0; i < files.length; i++) {
    if (files[i].substr(0, 4) !== 'zend') {
      continue;
    }

    fs.unlinkSync(cacheDir + files[i]);
  }
};
