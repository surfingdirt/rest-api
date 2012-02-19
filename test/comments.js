var constants = require('./constants'),
	site = constants.site,
	http = require('http'),
	client = http.createClient(site.port),
	restfw = require('./restfw'),
	restClient = restfw.client,
	cache = restfw.cache,
	querystring = require('querystring'),
	fixtures = require('./fixtures').comments;

cache.clear();

restClient.init({
	host: site.host,
	port: site.port,
	resource: '/comments/',
});

exports.testSetDate = function(test) {
	restClient.setDate(test, '2011-08-01 15:55:55');
};

exports.testGet = {
	asGuestSuccess: function(test) {
		restClient.reset();
		restClient.get(1, test, fixtures.firstComment, 'json');
	},
		
	asGuestFail: function(test) {
		restClient.reset();
		restClient.get(2, function(responseData, response){
			test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
			test.done();		
		});		
	},
		
	asPlainuser: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function onLogin(sessionId){
			restClient.get(2, test, fixtures.firstInvalidComment, 'json');
		});
	},

	asAdmin: function(test) {
		restClient.reset();
		restClient.login("adminuser", "123456789", test, function onLogin(sessionId){
			restClient.get(2, test, fixtures.firstInvalidComment, 'json');
		});		
	},
	
	asEditor: function(test) {
		restClient.reset();
		restClient.login("editoruser", "123456789", test, function onLogin(sessionId){
			restClient.get(2, test, fixtures.firstInvalidComment, 'json');
		});				
	}
};

exports.testList = {
	asGuestSuccess: function(test) {
		restClient.reset();
		restClient.list(test, fixtures.list.asGuest, {itemType: 'photo', itemId: 1});
	},
		
	asPlainuser: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function onLogin(sessionId){
			restClient.list(test, fixtures.list.allComments, {itemType: 'photo', itemId: 1});
		});		
	},
	
	asAdmin: function(test) {
		restClient.reset();
		restClient.login("adminuser", "123456789", test, function onLogin(sessionId){
			restClient.list(test, fixtures.list.allComments, {itemType: 'photo', itemId: 1});
		});		
	},
	
	asEditor: function(test) {
		restClient.reset();
		restClient.login("editoruser", "123456789", test, function onLogin(sessionId){
			restClient.list(test, fixtures.list.allComments, {itemType: 'photo', itemId: 1});
		});				
	}
};

exports.testPost = {
	asGuestFail: function(test) {
		restClient.reset();
		restClient.post({"content": "uselessTitle"}, function(reponseOutput, response) {
			test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
			test.done();
		});
	},
	
	asPlainuserFailNoParent: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.post({"content": "uselessTitle"}, function(reponseOutput, response) {
				// Responds with a 404 because no parent was found
				test.equal(404, response.statusCode, 'Unexpected status code ' + response.statusCode);
				test.done();
			});
		});		
	},

	asPlainuserFailNoContent: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.post(
				{"itemId": 1, "itemType": "video",},
				test,
				{"resourceId": null, "errors": {"content":["isEmpty"]}}
			);
		});		
	},

	asPlainuserSuccess: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.post(
				{"itemId": 1, "itemType": "video", "content": "validContent", "tone": 3},
				test,
				{"resourceId":"5","errors":[]},
				"json",
				"stringify",
				function(test){
					restClient.get(5, test, fixtures.validPost, 'json');
				}
			);
		});		
	}
};

exports.testPut = {
	asGuestFail: function(test) {
		restClient.reset();
		restClient.put(5, {"content": "invalidUpdate"}, function(reponseOutput, response) {
			test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
			test.done();
		});			
	},
	
	asOtheruserFail: function(test) {
		restClient.reset();
		restClient.login("otheruser", "123456789", test, function(sessionId){
			restClient.put(5, {"content": "invalidUpdate"}, function(reponseOutput, response) {
				test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
				test.done();
			});
		});
	},
	
	asPlainuserSuccess: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.put(5,
				{"content": "updatedcontent"},
				test,
				{"resourceId":"5", "errors": []},
				'json',
				function(test){
					restClient.get(5, test, fixtures.updatedComment, 'json');
				}
			);
		});
	}
};

exports.testDelete = {
	asGuestFail: function(test) {
		restClient.reset();
		restClient.del(5, function(reponseOutput, response) {
			test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
			test.done();
		});
	},
		
	asOtheruserFail: function(test) {
		restClient.reset();
		restClient.login("otheruser", "123456789", test, function(sessionId){
			restClient.del(5, function(reponseOutput, response) {
				test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
				test.done();
			});
		});
	},
		
	asPlainuserSuccess: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.del(5, function(reponseOutput, response) {
				restClient.get(5, function(responseOutput, response){
					test.equal(404, response.statusCode, 'Unexpected status code ' + response.statusCode);
					test.done();							
				});
			});
		});
	}
};