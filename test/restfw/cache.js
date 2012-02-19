var constants = require('../constants'),
	fs = require('fs');

module.exports = {
	clear: function(){
		var files = fs.readdirSync(constants.site.cacheDir);
		for(var i = 0, l = files.length; i < l; i++){
			if(files[i].substr(0, 4) != 'zend') {
				//console.log("skipping '" + constants.site.cacheDir + files[i] + "'");
				continue;
			}
			fs.unlinkSync(constants.site.cacheDir + files[i]);
			//console.log("deleting '" + constants.site.cacheDir + files[i] + "'");
		}
	}
}
