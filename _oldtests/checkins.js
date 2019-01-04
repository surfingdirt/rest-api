var constants = require('./constants'),
	site = constants.site,
	http = require('http'),
	client = http.createClient(site.port),
	restfw = require('./restfw'),
	restClient = restfw.client,
	cache = restfw.cache,
	querystring = require('querystring'),
	fixtures = require('./fixtures').checkins;

cache.clear();

restClient.init({
	host: site.host,
	port: site.port,
	resource: '/checkins/',
});

exports.testSetDate = function(test) {
	restClient.setDate(test, '2011-08-02 15:55:55');
};

/*
- list all checkins for a given date (+/- a margin to account for timezones at the given spot)
	/checkins/?date=20120623

- list all checkins for a given spot at a given date
	/checkins/spots/1/?date=20120623
- list all checkins for a given country at a given date
	/checkins/countries/1/?date=20120623
- list all checkins for a given region at a given date
	/checkins/regions/1/?date=20120623

- list all checkins for a given user
	/checkins/riders/1/
- get current checkin for a user
	/checkins/riders/1/current/

- get current checkins for a spot
	/checkins/spots/1/current/

- get closest checkins to a spot
	/checkins/near/spot/1/
- get closest checkins to a location
	/checkins/near/location/?lat=&lon=

- post a checkin to a spot
*/

exports.listAllCheckins = {
	onGivenDateNone: function(test) {
		restClient.reset();
		restClient.list(test, [], {'date': '2015-01-01'});
	},	
	onGivenDate: function(test) {
		restClient.reset();
		restClient.list(test, fixtures.allCheckins.onGivenDate, {'date': '2011-08-02 10:00:00'});
	},
	forGivenSpotOnGivenDate: function(test) {
		restClient.reset();
		restClient.setResource('/checkins/spots/5/');
		restClient.list(test, fixtures.allCheckins.forGivenSpotOnGivenDate, {'date': '2011-08-03 10:45:00'});
	},
	forGivenCountryOnGivenDate: function(test) {
		restClient.reset();
		restClient.setResource('/checkins/countries/1/');
		restClient.list(test, fixtures.allCheckins.forGivenCountryOnGivenDate, {'date': '2011-08-03 10:45:00'});
	},
	forGivenRegionOnGivenDate: function(test) {
		restClient.reset();
		restClient.setResource('/checkins/regions/1/');
		restClient.list(test, fixtures.allCheckins.forGivenRegionOnGivenDate, {'date': '2011-08-03 10:45:00'});
	},
	forGivenRiderOnGivenDate: function(test) {
		restClient.reset();
		restClient.setResource('/checkins/riders/5/');
		restClient.list(test, fixtures.allCheckins.forGivenRider, {'date': '2011-08-03 10:45:00'});
	},
};

exports.testSetDate2 = function(test) {
	restClient.setDate(test, '2012-01-01 11:00:00');
};

exports.listCurrentCheckins = {
	forGivenRider: function(test) {
		restClient.reset();
		restClient.setResource('/checkins/riders/4/current');
		restClient.list(test, fixtures.currentCheckins.forGivenRider);
	},
	atGivenSpot: function(test) {
		restClient.reset();
		restClient.setResource('/checkins/spot/5/current');
		restClient.list(test, fixtures.currentCheckins.atGivenSpot);
	},
};

exports.listClosestCheckins = {
	aroundGivenSpot: function(test) {
		restClient.reset();
		restClient.setResource('/checkins/spots/5/around/');
		restClient.list(test, fixtures.closestCheckins.aroundGivenSpot);
	},
	aroundGivenLocation: function(test) {
		restClient.reset();
		restClient.setResource('/checkins/around/');
		restClient.list(test, fixtures.closestCheckins.aroundGivenLocation, {lat: 48.0, lon: 2.0});
	},
};

exports.testSetDate3 = function(test) {
	restClient.setDate(test, '2012-02-01 10:00:00');
};

exports.postCheckins = {
	asGuestFail: function(test) {
		restClient.reset();
		restClient.setResource('/checkins/');
		restClient.post({"spot": "1"}, function(reponseOutput, response) {
			test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
			test.done();
		});			
	},	
	
	asPlainUserMinimal: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.post({
				"spot": "1",
			}, test, {"resourceId": 6, "errors":[]}, "stringify", "string", function(test){
				restClient.get(6, test, fixtures.postCheckins.minimal, 'json');
			});
		});		
	},
	/*	
	asPlainUserWithCheckinDate: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.post({
				"spot": "1",
				"checkinDate": "2012-03-01 10:00:00"
			}, test, {"resourceId": 7, "errors":[]}, "stringify", "string", function(test){
				restClient.get(7, test, fixtures.postCheckins.withCheckinDate, 'json');
			});
		});		
	},
		
	asPlainUserWithCheckinDatePast: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.post({
				"spot": "1",
				"checkinDate": "2012-01-01 10:00:00"
			}, test, {"resourceId": null, "errors": {"checkinDate": ['dateTooFarInThePast']}}, "stringify");
		});
	},
		
	asPlainUserWithDuration: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.post({
				"spot": "1",
				"checkinDuration": "3600",
				"checkinDate": "2012-03-01 10:00:00"
			}, test, {"resourceId": 8, "errors":[]}, "stringify", "string", function(test){
				restClient.get(8, test, fixtures.postCheckins.withDuration, 'json');
			});
		});		
	},*/
		
};
/*
exports.testSetDate4 = function(test) {
	restClient.setDate(test, '2012-02-28 14:00:00');
};

exports.putCheckins = {
	asWrongUser: function(test) {
		restClient.reset();
		restClient.login("otheruser", "123456789", test, function(sessionId){
			restClient.put(8, {"checkinDuration": "1800"}, function(reponseOutput, response) {
				test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
				test.done();
			});
		});		
	},
	asPlainUser: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.put(8,
				{"checkinDuration": "1800"},
				test,
				{"resourceId":"8", "errors": []},
				'json',
				function(test){
					restClient.get(8, test, fixtures.putCheckins.updated, 'json');
				}
			);
		});		
	},
	setDate: function(test) {
		restClient.setDate(test, '2012-04-28 14:00:00');
	},
	asPlainUserTooLateToUpdate: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.put(8,
				{"checkinDuration": "1800"},
				test,
				{"resourceId":"8", "errors": {"checkinDate": ["dateTooFarInThePast"]}},
				'json',
				function(test){
					restClient.get(8, test, fixtures.putCheckins.updated, 'json');
				}
			);
		});		
	},
	
};

exports.deleteCheckins = {
	asGuestFail: function(test) {
		restClient.reset();
		restClient.del(8, function(reponseOutput, response) {
			test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
			test.done();
		});
	},
	
	asOtherUserFail: function(test) {
		restClient.reset();
		restClient.login("otheruser", "123456789", test, function(sessionId){
			restClient.del(8, function(reponseOutput, response) {
				test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
				test.done();
			});
		});
	},
	
	asPlainUserSuccess: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.get(8, test, fixtures.putCheckins.updated, 'json', function(test){
				restClient.del(8, function(reponseOutput, response) {
					restClient.get(8, function(responseOutput, response){
						test.equal(404, response.statusCode, 'Unexpected status code ' + response.statusCode);
						test.done();							
					});
				});
			});
		});
	},
};*/