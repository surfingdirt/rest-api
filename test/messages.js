var constants = require('./constants'),
	site = constants.site,
	http = require('http'),
	client = http.createClient(site.port),
	restfw = require('./restfw'),
	restClient = restfw.client,
	cache = restfw.cache,
	querystring = require('querystring'),
	fixtures = require('./fixtures').messages;


cache.clear();

restClient.init({
	host: site.host,
	port: site.port,
	resource: '/messages/',
});

// It should freeze the date on the server
exports.testSetDate = function(test) {
	restClient.setDate(test, '2011-08-01 15:55:55');
};

// Logged out users should see a 403 error
exports.testUnauthorised = function(test){
	var request = client.request('GET', '/messages/', {
			'Host': site.host,
			'Accept': 'application/json; q=1.0',
			'Connection': 'keep-alive'
		});
		
	request.on('response', function (response) {
		response.on('end', function(){
			//console.log('responseData --', responseData, "--");
			test.equals(403, response.statusCode);
			test.done();
		});
	});		
	request.end();	
};

// It should show the list of messages for plainuser
exports.testListDefault = function(test){
	restClient.reset();
	restClient.login("plainuser", "123456789", test, function(sessionId){	
		restClient.list(test, [fixtures.fromWriterToPlainUser, fixtures.fromOtherToPlainUserValid]);
	});
};

// It should show a subset of the list of messages
exports.testListSubset = function(test){
	restClient.reset();
	restClient.login("plainuser", "123456789", test, function(sessionId){	
		restClient.list(test, [fixtures.fromOtherToPlainUserValid], {start:1, count:1});
	});
};

// It should show only new messages
exports.testListNew = function(test){
	restClient.reset();
	restClient.login("plainuser", "123456789", test, function(sessionId){	
		restClient.list(test, [fixtures.fromOtherToPlainUserValid], {"new": 1});
	});
};

// It should create a new message
exports.testCreateMessage = function(test){
	restClient.reset();
	restClient.login("plainuser", "123456789", test, function(sessionId){
		restClient.post({
			"content": "createdMessage1To7Content",
			"toUser": 7,
		}, test, {
			"resourceId": "6",
			"errors": []
		}, "stringify");
	});
};

// It should show a message from this user to other user (created in the previous test)
exports.testShowMessageFromMe = function(test){
	restClient.reset();
	restClient.login("plainuser", "123456789", test, function(sessionId){
		restClient.get(6, test, fixtures.createdByMe, 'json');
	});
};

// It should show a message to this user from another user
exports.testShowMessageToMe = function(test){
	restClient.reset();
	restClient.login("plainuser", "123456789", test, function(sessionId){
		restClient.get(2, test, fixtures.fromOtherToPlainUserValid, 'json');
	});	
};

// It should fail to delete a message (403)
// (We don't want to allow that ever. Otherwise we need to duplicate them for sent and received.)
exports.testFailToDelete = function(test){
	restClient.reset();
	restClient.login("plainuser", "123456789", test, function(sessionId){
		var request = client.request('DELETE', '/messages/6', {
				'Host': site.host,
				'Accept': 'application/json; q=1.0',
				'Connection': 'keep-alive',
				'Cookie': 'PHPSESSID=' + sessionId
			});
			
		request.on('response', function (response) {
			response.on('end', function(){
				//console.log('responseData --', responseData, "--");
				test.equals(403, response.statusCode);
				test.done();
			});
		});		
		request.end();		
	});	
};

// It should fail to update a message (403) between other users
exports.testFailToUpdate = function(test){
	restClient.reset();
	restClient.login("plainuser", "123456789", test, function(){
		restClient.put(5, {'content': 'duh'}, function(responseOutput, response){
			test.equals(403, response.statusCode);
			test.done();
		});
	});
};
