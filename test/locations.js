var constants = require('./constants'),
	site = constants.site,
	http = require('http'),
	client = http.createClient(site.port),
	restfw = require('./restfw'),
	restClient = restfw.client,
	cache = restfw.cache,
	querystring = require('querystring'),
	fixtures = require('./fixtures').locations;

cache.clear();

restClient.init({
	host: site.host,
	port: site.port,
	resource: '/locations/',
});

exports.testSetDate = function(test) {
	restClient.setDate(test, '2011-08-01 15:55:55');
};

/**
 * Existing notifications from sql fixtures
 */
//Guest users should be able to see new notifications from everyone
exports.testWithinBounds = {
	asGuest: function(test){
		restClient.reset();
		restClient.list(test, fixtures.locationsForGuest, {
			swLon: 2.305,
			swLat: 48.773,
			neLon: 2.309,
			neLat: 48.777,
		});
	},

	asPlainuser: function(test){
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.list(test, fixtures.locationsForPlainuser, {
				swLon: 2.305,
				swLat: 48.773,
				neLon: 2.309,
				neLat: 48.777,
			});
		});
	}
};

exports.testDistance = {
	closeNoMaxAsGuest: function(test) {
		restClient.reset();
		restClient.list(test, fixtures.distanceCloseNoMaxAsGuest, {lat:49, lon:2});
	},

	farNoMaxAsGuest: function(test) {
		restClient.reset();
		restClient.list(test, fixtures.distanceFarNoxMaxAsGuest, {lat:9, lon:2});
	},
	
	farMax10000AsGuest: function(test) {
		restClient.reset();
		restClient.list(test, fixtures.distanceFaxMax10000AsGuest, {lat:9, lon:2, max: 10000});
	},
	
};

exports.testWithinCountry = {
	franceAsGuest: function(test) {
		restClient.reset();
		restClient.setResource('/countries/1/locations/');
		restClient.list(test, fixtures.validLocationsInCountry);
	},		
	franceAsAdmin: function(test) {
		restClient.reset();
		restClient.setResource('/countries/1/locations/');
		restClient.login("adminuser", "123456789", test, function onLogin(sessionId){
			restClient.list(test, fixtures.allLocationsInCountry);
		});		
	}
};

exports.testWithinRegion = {
	franceAsGuest: function(test) {
		restClient.reset();
		restClient.setResource('/regions/1/locations/');
		restClient.list(test, fixtures.validLocationsInRegion);
	},		
};