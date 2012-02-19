/*
 * GET a valid trick and an invalid trick
 * LIST tricks
 * POST a trick
 * PUT a trick
 * DELETE a trick
 * 
 * add a trick to some media
 */

var constants = require('./constants'),
	site = constants.site,
	http = require('http'),
	client = http.createClient(site.port),
	restfw = require('./restfw'),
	restClient = restfw.client,
	cache = restfw.cache,
	querystring = require('querystring'),
	fixtures = require('./fixtures').tricks;

cache.clear();

restClient.init({
	host: site.host,
	port: site.port,
	resource: '/tricks/',
});

// It should freeze the date on the server
exports.testSetDate = function(test) {
	restClient.setDate(test, '2011-08-01 15:55:55');
};

// Guest users should be able to see the first valid trick
exports.testGetFirstTrick = function(test){
	restClient.reset();
	restClient.setProperties(Object.keys(fixtures.firstTrick));
	restClient.get(1, test, fixtures.firstTrick, 'json');
};

exports.testGetInvalidTrick = {
	//Guest users should not be able to see other's invalid tricks
	asGuestFail: function(test){
		restClient.reset();
		restClient.get(2, function(responseData, response){
			test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
			test.done();		
		});
	},

	// Other users should not be able to see a given user's invalid tricks
	asOtherUserFail: function(test){
		restClient.reset();
		restClient.login("otheruser", "123456789", test, function(sessionId){
			restClient.get(2, function(responseData, response){
				test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
				test.done();		
			});
		});
	},
		
	// Owner should be able to see their invalid trick
	asOwnerSuccess: function(test){
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.setProperties(Object.keys(fixtures.firstInvalidTrick));
			restClient.get(2, test, fixtures.firstInvalidTrick, 'json');				
		});
	},
	
	// Admins should be able to see invalid tricks
	asAdminSuccess: function(test){
		restClient.reset();
		restClient.login("adminuser", "123456789", test, function(sessionId){
			restClient.setProperties(Object.keys(fixtures.firstInvalidTrick));
			restClient.get(2, test, fixtures.firstInvalidTrick, 'json');				
		});
	},
		
	// Editors should be able to see invalid tricks
	asEditorSuccess: function(test){
		restClient.reset();
		restClient.login("editoruser", "123456789", test, function(sessionId){
			restClient.setProperties(Object.keys(fixtures.firstInvalidTrick));
			restClient.get(2, test, fixtures.firstInvalidTrick, 'json');				
		});
	}
};

exports.list = {
	asGuest: function(test) {
		// get 1,3,4
		restClient.reset();
		restClient.setProperties(Object.keys(fixtures.list.asGuest[0]));
		restClient.list(test, fixtures.list.asGuest);
	},
	asGuestLimitedReversed: function(test) {
		// get 4,3
		restClient.reset();
		restClient.setProperties(Object.keys(fixtures.list.asGuest[0]));
		restClient.list(test, [fixtures.list.asGuest[2], fixtures.list.asGuest[1]], {count: 2, dir: 'desc'});
	},
	asPlainuser: function(test) {
		// get 1,2,3,4
		restClient.reset();
		restClient.setProperties(Object.keys(fixtures.list.allTricks[0]));
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.list(test, fixtures.list.allTricks);
		});		
	},
	asAdmin: function(test) {
		// get 1,2,3,4
		restClient.reset();
		restClient.setProperties(Object.keys(fixtures.list.allTricks[0]));
		restClient.login("adminuser", "123456789", test, function(sessionId){
			restClient.list(test, fixtures.list.allTricks);
		});
	},
	asEditor: function(test) {
		// get 1,2,3,4
		restClient.reset();
		restClient.setProperties(Object.keys(fixtures.list.allTricks[0]));
		restClient.login("editoruser", "123456789", test, function(sessionId){
			restClient.list(test, fixtures.list.allTricks);
		});
	},
};

// POST
exports.postTrick = {
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
			}, test, {"resourceId": null, "errors": {"description":["isEmpty"]}}, "stringify");
		});		
	},
	validAsPlainUserSuccess: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.post({
				"title": "validTitle",
				"description": "validDescription",
				"difficulty": "3",
				"trickTip": "tip!",
			}, test, {"resourceId": "5", "errors": []}, "string", "stringify", function(test){
				restClient.setProperties(Object.keys(fixtures.validPost));
				restClient.get(5, test, fixtures.validPost, 'json');
			});
		});		
	}
};

// PUT
exports.putMedia = {
	validAsGuestFail: function(test) {
		restClient.reset();
		restClient.put(5, {"title": "invalideTitleUpdate"}, function(reponseOutput, response) {
			test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
			test.done();
		});			
	},
	
	invalidAsOwnerFail: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.put(5,
				{"status": "bla"},
				test,
				{"resourceId":"5", "errors": {"status": ["notInArray"]}}
			);
		});			
	},	
	
	validAsOwnerSuccess: function(test) {
		restClient.reset();
		restClient.setDebug(false);
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.put(5,
				{"title": "updatedTitle"},
				test,
				{"resourceId":"5", "errors": []},
				'json',
				function(test){
					restClient.setDebug(false);
					restClient.setProperties(Object.keys(fixtures.updatedTrick));
					restClient.get(5, test, fixtures.updatedTrick, 'json');
				}
			);
		});
	},	
};

//DELETE
exports.deleteTrick = {
	asOtherUserFail: function(test) {
		restClient.reset();
		restClient.login("otheruser", "123456789", test, function(sessionId){
			restClient.del(5, function(reponseOutput, response) {
				test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
				test.done();
			});
		});
	},
	
	asPlainUserSuccess: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.setProperties(Object.keys(fixtures.updatedTrick));
			restClient.get(5, test, fixtures.updatedTrick, 'json', function(test){
				restClient.del(5, function(reponseOutput, response) {
					restClient.get(5, function(responseOutput, response){
						test.equal(404, response.statusCode, 'Unexpected status code ' + response.statusCode);
						test.done();							
					});
				});
			});
		});
	},
};