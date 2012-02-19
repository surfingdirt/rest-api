var constants = require('./constants'),
	site = constants.site,
	http = require('http'),
	client = http.createClient(site.port),
	restfw = require('./restfw'),
	restClient = restfw.client,
	properties = [
		'userId','username','email','status','lang','firstName','lastName','birthDate','country','city',
		'zip','gender','level','site','occupation','gear','otherSports','rideType','avatar',
	],
	fixtures = {
		'plainuser':{"userId":"1","username":"plainuser","email":"user1@example.org","status":"member","lang":"fr","firstName":"prenom","lastName":"nom","birthDate":"1980-07-01","country":"france","city":"toulouse","zip":"31000","gender":"1","level":"2","site":"http:\/\/www.mountainboard.fr","occupation":"occupation","gear":"pro95","otherSports":"snowboard","rideType":"110","avatar":"\/media\/avatars\/1.jpg"},
		'adminuser':{"userId":"2","username":"adminuser","email":"admin1@example.org","status":"admin","lang":"en","firstName":"first","lastName":"last","birthDate":"1950-01-08","country":"usa","city":"oakland","zip":"94610","gender":"2","level":"3","site":"http:\/\/www.mntnbrd.rs","occupation":"dev","gear":"grasshopper","otherSports":null,"rideType":null,"avatar":null},
		'pendinguser':{"userId":"3","username":"pendinguser","email":"pending@example.org","status":"pending","lang":"en","firstName":"pen","lastName":"ding","birthDate":"0000-00-00","country":null,"city":null,"zip":null,"gender":null,"level":null,"site":null,"occupation":null,"gear":null,"otherSports":null,"rideType":null,"avatar":null},
		'banneduser':{"userId":"4","username":"banneduser","email":"banned1@example.org","status":"banned","lang":"fr","firstName":"ban","lastName":"ned","birthDate":null,"country":null,"city":null,"zip":null,"gender":null,"level":null,"site":null,"occupation":null,"gear":null,"otherSports":null,"rideType":null,"avatar":null},
		'editoruser':{"userId":"5","username":"editoruser","email":"editor1@example.org","status":"editor","lang":"en","firstName":"eddy","lastName":"tor","birthDate":"1988-05-25","country":"spain","city":"madrid","zip":null,"gender":"1","level":"3","site":null,"occupation":null,"gear":null,"otherSports":null,"rideType":null,"avatar":null},
		'writeruser':{"userId":"6","username":"writeruser","email":"writer1@example.org","status":"writer","lang":"fr","firstName":"wri","lastName":"ter","birthDate":"1984-03-18","country":"germany","city":null,"zip":null,"gender":null,"level":null,"site":null,"occupation":null,"gear":null,"otherSports":null,"rideType":null,"avatar":null},
		'otheruser':{"userId":"7","username":"otheruser","email":"user2@example.org","status":"member","lang":"en","firstName":"other","lastName":"user","birthDate":null,"country":null,"city":null,"zip":null,"gender":null,"level":null,"site":null,"occupation":null,"gear":null,"otherSports":null,"rideType":null,"avatar":null},
		'guest':{"userId":"8","username":"guest","email":"guest@example.org","status":"guest","lang":null,"firstName":null,"lastName":null,"birthDate":null,"country":null,"city":null,"zip":null,"gender":null,"level":null,"site":null,"occupation":null,"gear":null,"otherSports":null,"rideType":null,"avatar":null},
		'plainuserHtml':
'<dl class="rider">\n\
	<dt>userId</dt><dd>1</dd>\n\
	<dt>username</dt><dd>plainuser</dd>\n\
	<dt>email</dt><dd>user1@example.org</dd>\n\
	<dt>status</dt><dd>member</dd>\n\
	<dt>lang</dt><dd>fr</dd>\n\
	<dt>firstName</dt><dd>prenom</dd>\n\
	<dt>lastName</dt><dd>nom</dd>\n\
	<dt>birthDate</dt><dd>1980-07-01</dd>\n\
	<dt>country</dt><dd>france</dd>\n\
	<dt>city</dt><dd>toulouse</dd>\n\
	<dt>zip</dt><dd>31000</dd>\n\
	<dt>gender</dt><dd>1</dd>\n\
	<dt>level</dt><dd>2</dd>\n\
	<dt>site</dt><dd>http://www.mountainboard.fr</dd>\n\
	<dt>occupation</dt><dd>occupation</dd>\n\
	<dt>gear</dt><dd>pro95</dd>\n\
	<dt>otherSports</dt><dd>snowboard</dd>\n\
	<dt>rideType</dt><dd>110</dd>\n\
	<dt>avatar</dt><dd>/media/avatars/1.jpg</dd>\n\
</dl>'
};

restClient.init({
	host: site.host,
	port: site.port,
	resource: '/riders/',
	properties: properties
});

/**
 * It should retrieve the list of all valid users in the DB
 */	
exports.testAllUsers = function(test){
	restClient.list(test, [fixtures.plainuser, fixtures.adminuser, fixtures.editoruser, fixtures.writeruser, fixtures.otheruser]);
};

/**
 * It should retrieve the 3rd and 4th valid users
 */	
exports.test3rdAnd4thUsers = function(test){
	restClient.list(test, [fixtures.editoruser, fixtures.writeruser], {start:2, count:2});
};

/**
 * It should retrieve the 2nd and 3rd valid users, sorted by username ascending
 */	
exports.test2ndAnd3rdUsers = function(test){
	restClient.list(test, [fixtures.editoruser, fixtures.otheruser], {start:1, count:2, sort: 'username'});
};

/**
 * It should retrieve the 2nd and 3rd valid users, sorted by username descending
 */
exports.test2ndAnd3rdUsersDesc = function(test){
	restClient.list(test, [fixtures.plainuser, fixtures.otheruser], {start:1, count:2, sort: 'username', dir: 'desc'});
};

/**
 * It should retrieve the user with given id in json format
 */
exports.testPlainUserJson = function(test){
	restClient.get(1, test, fixtures.plainuser, 'json');
};
/**
 * It should retrieve the user with given id in html format
 */
exports.testPlainUserHtml = function(test){
	restClient.get(1, test, fixtures.plainuserHtml, 'html');
};
