var constants = require('./constants'),
	site = constants.site,
	http = require('http'),
	restfw = require('./restfw'),
	cache = restfw.cache,
	querystring = require('querystring'),
	resource = '/sessions/',
	accept = 'application/json; q=1.0';

/**
 * Sessions story: logged out user visits any page, then a restricted page, then logs in, visits the same restricted page, then logs out, revisits same page.
 *     
 *  GET index
 *  ==> response should have a sId cookie = $sessionId1
 *  ==> response should have a 200 header
 *     
 *  GET restricted page (/private-messages/)
 *  ==> response should have a 403 header
 *     
 *  POST sessions with username and password
 *  ==> response should have a different sId cookie = $sessionId2
 *  ==> response should be {"ok":true, "sessionId": $sessionId2} if JSON or the equivalent HTML
 *     
 *  GET same restricted page
 *  ==> response should have a 200 header
 * 
 *  DELETE session/$sessionId with username and password
 *  ==> response should have a different sId cookie  $sessionId3
 *  ==> response should be {"sessionId": $sessionId3, "userId": 0} if JSON or the equivalent HTML
 *
 */
exports.testSessionStory = function(test) {
	var initialSessionId = '';

	var initialRequest = function() {
		http.get({
			host: site.host,
			port: site.port,
			path: site.baseUrl + '/',
			headers: {
				'Accept': accept,
				'Connection': 'keep-alive'
			}
		}, function(response) {
			var responseData = '';
			response.setEncoding('utf8');

			response.on('data', function (chunk) {
				responseData += chunk;
			});

			response.on('end', function() {
				test.equal(200, response.statusCode, 'Bad status code');
				test.ok(
					typeof response.headers['set-cookie'][0] == 'string',
					'Missing cookie header');
				test.ok(
					!!response.headers['set-cookie'][0],
					'Empty cookie header');
				test.ok(
					/sId=[0-9a-z]{26,32};/.test(response.headers['set-cookie'][0]),
					'Missing session cookie header');
				initialSessionId = response.headers['set-cookie'][0].match(/sId=([0-9a-z]{26,32});/)[1];

			  	unauthorisedRequest();
			});
		});
	};

	var unauthorisedRequest = function() {
		http.get({
			host: site.host,
			port: site.port,
			path: site.baseUrl + '/messages/',
			headers: {
				'Accept': accept,
				'Cookie': 'sId=' + initialSessionId,
				'Connection': 'keep-alive'
			}
		}, function(response) {
			var responseData = '';
			response.setEncoding('utf8');
			response.on('data', function (chunk) {
				responseData += chunk;
			});

			response.on('end', function() {
				test.equal(403, response.statusCode, 'Unexpected status code');
				test.ok(typeof response.headers['set-cookie'] == 'undefined', 'Unexpected cookie header');

				loginRequest();
			});
		});		
	};

	var loginRequest = function() {
		var loginData = querystring.stringify({
				"username": "plainuser",
				"userP": "123456789"
			});
			
		var request = http.request({
			method: 'POST',
			host: site.host,
			port: site.port,
			path: site.baseUrl + '/sessions/',
			headers: {
				'Accept': accept,
				'Cookie': 'sId=' + initialSessionId,
				'Connection': 'keep-alive',
				'Content-Type': 'application/x-www-form-urlencoded',
				'Content-Length': loginData.length
			}
		}, function(response) {
			var responseData = '';
			var responseOutput;
		
			response.setEncoding('utf8');
			response.on('data', function (chunk) {
				responseData += chunk;
			});
		
			response.on('end', function() {
				test.equal(200, response.statusCode, 'Bad status code');
				if(200 != response.statusCode){
					test.done();
					return;
				}
				try {
					responseOutput = JSON.parse(responseData);
				} catch (e) {
					test.ok(false, "Not JSON");
					test.done();
					return;
				}
			
				test.equal(responseOutput.rider.userId, 1, "Bad user id");
				
				test.ok(typeof response.headers['set-cookie'] == 'object', 'Missing cookie header');
				test.ok(typeof response.headers['set-cookie'][response.headers['set-cookie'].length - 1] == 'string', 'Empty cookie header');
				test.ok(/sId=[0-9a-z]{26,32};/.test(response.headers['set-cookie'][0]), 'Missing session cookie header');				
				loggedInSessionId = response.headers['set-cookie'][response.headers['set-cookie'].length - 1].match(/sId=([0-9a-z]{26,32});/)[1];
				test.notEqual(loggedInSessionId, initialSessionId, 'Session id has not changed');
				
				authorisedRequest();
			});
		}).on('error', function(e) {
			console.log('error', e);
		});
		request.write(loginData, 'utf8');
		request.end();	
	};

	var authorisedRequest = function() {
		http.get({
			host: site.host,
			port: site.port,
			path: site.baseUrl + '/messages/',
			headers: {
				'Accept': accept,
				'Cookie': 'sId=' + loggedInSessionId,
				'Connection': 'keep-alive'
			}
		}, function(response) {
			var responseData = '';
			response.setEncoding('utf8');
			response.on('data', function (chunk) {
				responseData += chunk;
			});

			response.on('end', function() {
				test.equal(200, response.statusCode, 'Unexpected status code');
				test.ok(typeof response.headers['set-cookie'] == 'undefined', 'Unexpected cookie header');

				logoutRequest();
			});
		});		
	};

	var logoutRequest = function() {
		var loginData = querystring.stringify({
				"username": "plainuser",
				"userP": "123456789"
			});
			
		var request = http.request({
			method: 'DELETE',
			host: site.host,
			port: site.port,
			path: site.baseUrl + '/sessions/' + loggedInSessionId,
			headers: {
				'Accept': accept,
				'Cookie': 'sId=' + loggedInSessionId,
				'Connection': 'keep-alive'
			}
		}, function(response) {
			var responseData = '';
			var responseOutput;
		
			response.setEncoding('utf8');
			response.on('data', function (chunk) {
				responseData += chunk;
			});
		
			response.on('end', function() {
				test.equal(200, response.statusCode, 'Bad status code');
				if(200 != response.statusCode){
					test.done();
					return;
				}
				try {
					responseOutput = JSON.parse(responseData);
				} catch (e) {
					test.ok(false, "Not JSON");
					test.done();
					return;
				}
			
				test.equal(responseOutput.rider.userId, 0, "Bad user id");
				
				test.ok(typeof response.headers['set-cookie'] == 'object', 'Missing cookie header');
				test.ok(typeof response.headers['set-cookie'][0] == 'string', 'Empty cookie header');
				test.ok(/sId=[0-9a-z]{26,32};/.test(response.headers['set-cookie'][0]), 'Missing session cookie header');				
				var loggedOutSessionId = response.headers['set-cookie'][0].match(/sId=([0-9a-z]{26,32});/)[1];
				
				test.notEqual(loggedOutSessionId, loggedInSessionId, 'Session id has not changed');
				test.notEqual(loggedOutSessionId, initialSessionId, 'Session id is back to initial session id');
				test.done();
			});
		}).on('error', function(e) {
			console.log('error', e);
		});
		request.write(loginData, 'utf8');
		request.end();	
	};

	initialRequest();
};
