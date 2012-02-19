/*
 * GET a valid spot and an invalid spot
 * LIST spots
 * POST a spot
 * PUT a spot
 * DELETE a spot
 * 
 * add a spot to some media
 */

var constants = require('./constants'),
	site = constants.site,
	http = require('http'),
	client = http.createClient(site.port),
	restfw = require('./restfw'),
	restClient = restfw.client,
	cache = restfw.cache,
	querystring = require('querystring'),
	fixtures = require('./fixtures').spots;

cache.clear();

restClient.init({
	host: site.host,
	port: site.port,
	resource: '/spots/',
});

// It should freeze the date on the server
exports.testSetDate = function(test) {
	restClient.setDate(test, '2011-08-01 15:55:55');
};

// Guest users should be able to see the first valid spot
exports.testGetFirstSpot = function(test){
	restClient.reset();
	restClient.get(1, test, fixtures.firstSpot, 'json');
};

exports.testGetInvalidSpot = {
	//Guest users should not be able to see other's invalid spots
	asGuestFail: function(test){
		restClient.reset();
		restClient.get(2, function(responseData, response){
			test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
			test.done();		
		});
	},

	// Other users should not be able to see a given user's invalid spots
	asOtherUserFail: function(test){
		restClient.reset();
		restClient.login("otheruser", "123456789", test, function(sessionId){
			restClient.get(2, function(responseData, response){
				test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
				test.done();		
			});
		});
	},
		
	// Owner should be able to see their invalid spots
	asOwnerSuccess: function(test){
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.get(2, test, fixtures.firstInvalidSpot, 'json');				
		});
	},
	
	// Admins should be able to see invalid spots
	asAdminSuccess: function(test){
		restClient.reset();
		restClient.login("adminuser", "123456789", test, function(sessionId){
			restClient.get(2, test, fixtures.firstInvalidSpot, 'json');				
		});
	},
		
	// Editors should be able to see invalid spots
	asEditorSuccess: function(test){
		restClient.reset();
		restClient.login("editoruser", "123456789", test, function(sessionId){
			restClient.get(2, test, fixtures.firstInvalidSpot, 'json');				
		});
	}
};

exports.list = {
	asGuest: function(test) {
		// get 1,3,4,5
		restClient.reset();
		restClient.list(test, fixtures.list.asGuest);
	},
	asGuestLimitedReversed: function(test) {
		// get 5,4
		restClient.reset();
		restClient.list(test, [fixtures.list.asGuest[3], fixtures.list.asGuest[2]], {count: 2, dir: 'desc'});
	},
	asPlainuser: function(test) {
		// get 1,2,3,4,5
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.list(test, fixtures.list.allSpots);
		});		
	},
	asAdmin: function(test) {
		// get 1,2,3,4,5
		restClient.reset();
		restClient.login("adminuser", "123456789", test, function(sessionId){
			restClient.list(test, fixtures.list.allSpots);
		});
	},
	asEditor: function(test) {
		// get 1,2,3,4,5
		restClient.reset();
		restClient.login("editoruser", "123456789", test, function(sessionId){
			restClient.list(test, fixtures.list.allSpots);
		});
	},
};

// POST
exports.post = {
	asGuestFail: function(test) {
		restClient.reset();
		restClient.post({"title": "uselessTitle"}, function(reponseOutput, response) {
			test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
			test.done();
		});			
	},
	invalidAsPlainUserFail: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.post({
				"title": "invalidPost",
			}, test, {"resourceId": null, "errors": {"description":["isEmpty"], "longitude":["isEmpty"], "latitude":["isEmpty"]}}, "stringify");
		});		
	},
	validAsPlainUserSuccess: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.post({
				"title": "validSpotTitle",
				"description": "validSpotDescription",
				"status": "valid",
				"longitude": "1.20000000",
				"latitude": "2.30000000",
			}, test, {"resourceId": "6", "errors": []}, "string", "stringify", function(test){
				restClient.get(6, test, fixtures.validSpotAsPlainuser, 'json');
			});
		});		
	}
};

// PUT
exports.put = {
	validAsGuestFail: function(test) {
		restClient.reset();
		restClient.put(6, {"title": "invalideTitleUpdate"}, function(reponseOutput, response) {
			test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
			test.done();
		});			
	},
	
	invalidAsOwnerFail: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.put(6,
				{"status": "bla"},
				test,
				{"resourceId":"6", "errors": {"status": ["notInArray"]}}
			);
		});			
	},	
	
	validAsOwnerSuccess: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.put(6,
				{"title": "updatedTitle"},
				test,
				{"resourceId":"6", "errors": []},
				'json',
				function(test){
					restClient.get(6, test, fixtures.updatedSpot, 'json');
				}
			);
		});
	},	
};

//DELETE
exports.deleteSpot = {
	asOtherUserFail: function(test) {
		restClient.reset();
		restClient.login("otheruser", "123456789", test, function(sessionId){
			restClient.del(6, function(reponseOutput, response) {
				test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
				test.done();
			});
		});
	},
	
	asPlainUserSuccess: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.get(6, test, fixtures.updatedSpot, 'json', function(test){
				restClient.del(6, function(reponseOutput, response) {
					restClient.get(6, function(responseOutput, response){
						test.equal(404, response.statusCode, 'Unexpected status code ' + response.statusCode);
						test.done();							
					});
				});
			});
		});
	},
};