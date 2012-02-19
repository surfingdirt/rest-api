var constants = require('./constants'),
	site = constants.site,
	http = require('http'),
	client = http.createClient(site.port),
	restfw = require('./restfw'),
	restClient = restfw.client,
	cache = restfw.cache,
	querystring = require('querystring'),
	fixtures = require('./fixtures').countries;

cache.clear();

restClient.init({
	host: site.host,
	port: site.port,
	resource: '/countries/',
});

exports.testSetDate = function(test) {
	restClient.setDate(test, '2011-08-01 15:55:55');
};

exports.testGet = {
	franceAsGuest: function(test) {
		restClient.reset();
		restClient.get(1, test, fixtures.france, 'json');		
	},
	
	newZealandAsGuest: function(test) {
		restClient.reset();
		restClient.get(3, function(responseData, response){
			test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
			test.done();		
		});		
	},
	
	newZealandAsPlainuser: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function onLogin(sessionId){
			restClient.get(3, function(responseData, response){
				test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
				test.done();		
			});
		});
	},
	
	newZealandAsAdmin: function(test) {
		restClient.reset();
		restClient.login("adminuser", "123456789", test, function onLogin(sessionId){
			restClient.get(3, test, fixtures.newZealand, 'json');
		});
	},
};

exports.testList = {
	asGuest: function(test) {
		restClient.reset();
		restClient.list(test, fixtures.allValid);
	},
	asPlainuser: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function onLogin(sessionId){
			restClient.list(test, fixtures.allValid);
		});
	},
	asAdmin: function(test) {
		restClient.reset();
		restClient.login("adminuser", "123456789", test, function onLogin(sessionId){
			restClient.list(test, fixtures.all);
		});		
	},
};