var constants = require('./constants'),
	site = constants.site,
	http = require('http'),
	client = http.createClient(site.port),
	restfw = require('./restfw'),
	cache = restfw.cache,
	querystring = require('querystring'),
	resource = '/sessions/',
	accept = 'application/json; q=1.0';

/**
 * Sessions story: logged out user visits any page, then a restricted page, then logs in, visits the same restricted page, then logs out, revisits same page.
 *     
 *  GET index
 *  ==> response should have a PHPSESSID cookie = $sessionId1
 *  ==> response should have a 200 header
 *     
 *  GET restricted page (/private-messages/)
 *  ==> response should have a 403 header
 *     
 *  POST sessions with username and password
 *  ==> response should have a different PHPSESSID cookie = $sessionId2
 *  ==> response should be {"ok":true, "sessionId": $sessionId2} if JSON or the equivalent HTML
 *     
 *  GET same restricted page
 *  ==> response should have a 200 header
 * 
 *  DELETE session/$sessionId with username and password
 *  ==> response should have a different PHPSESSID cookie  $sessionId3
 *  ==> response should be {"sessionId": $sessionId3, "userId": 0} if JSON or the equivalent HTML
 *
 */
exports.testSessionStory = function(test){
	cache.clear();
	
	var initialSessionId = '', 
		loggedInSessionId = '',
	
	initialRequest = function(){
		var request = client.request('GET', '/', {
				'Host': site.host,
				'Accept': accept,
				'Connection': 'keep-alive'
			}),
			responseData;
		
		request.on('response', function (response) {
			response.setEncoding('utf8');
			response.on('data', function (chunk) {
				responseData += chunk;
			});
		
			response.on('end', function(){
				//console.log('response', response);
				test.equal(200, response.statusCode, 'Bad status code');
				
				test.ok(typeof response.headers['set-cookie'][0] == 'string', 'Missing cookie header');
				test.ok(!!response.headers['set-cookie'][0], 'Empty cookie header');
				test.ok(/PHPSESSID=[0-9a-z]{26,32};/.test(response.headers['set-cookie'][0]), 'Missing session cookie header');
				initialSessionId = response.headers['set-cookie'][0].match(/PHPSESSID=([0-9a-z]{26,32});/)[1];
console.log('initialSessionId', initialSessionId);
				unauthorisedRequest();
			});
		});		
		request.end();
	},
	
	unauthorisedRequest = function(){
		var request = client.request('GET', '/messages/', {
			'Host': site.host,
			'Accept': accept,
			'Cookie': 'PHPSESSID=' + initialSessionId,
			'Connection': 'keep-alive'
			
		}),
		responseData;
	
		request.on('response', function (response) {
			response.setEncoding('utf8');
			response.on('data', function (chunk) {
				responseData += chunk;
			});
		
			response.on('end', function(){
				//console.log('response.headers', response.headers);
				test.equal(403, response.statusCode, 'Unexpected status code');
				test.ok(typeof response.headers['set-cookie'] == 'undefined', 'Unexpected cookie header');
				
				
				loginRequest();
			});
		});		
		request.end();
	},
	
	loginRequest = function(){
		var loginData = querystring.stringify({
				"userN": "plainuser",
				"userP": "123456789"
			}),
			
			request = client.request('POST', '/sessions/', {
				'Host': site.host,
				'Accept': accept,
				'Cookie': 'PHPSESSID=' + initialSessionId,
				'Connection': 'keep-alive',
				'Content-Type': 'application/x-www-form-urlencoded',
				'Content-Length': loginData.length
			}),
		
		responseData = '',
		responseOutput;
	
		request.on('response', function (response) {
			response.setEncoding('utf8');
			response.on('data', function (chunk) {
				responseData += chunk;
			});
		
			response.on('end', function(){
				//console.log('responseData', responseData);
				test.equal(200, response.statusCode, 'Bad status code');
				if(200 != response.statusCode){
					test.done();
					return;
				}
				responseOutput = JSON.parse(responseData);
				
				test.equal(responseOutput.userId, 1, "Bad user id");
				
				test.ok(typeof response.headers['set-cookie'] == 'object', 'Missing cookie header');
				test.ok(typeof response.headers['set-cookie'][response.headers['set-cookie'].length - 1] == 'string', 'Empty cookie header');
				test.ok(/PHPSESSID=[0-9a-z]{26,32};/.test(response.headers['set-cookie'][0]), 'Missing session cookie header');				
				loggedInSessionId = response.headers['set-cookie'][response.headers['set-cookie'].length - 1].match(/PHPSESSID=([0-9a-z]{26,32});/)[1];
				//console.log('loggedInSessionId', loggedInSessionId, responseOutput);
				test.notEqual(loggedInSessionId, initialSessionId, 'Session id has not changed');
				
				authorisedRequest();
			});
		});		
		request.write(loginData, 'utf8');
		request.end();
	},
	
	authorisedRequest = function(){
		var request = client.request('GET', '/messages/', {
			'Host': site.host,
			'Accept': accept,
			'Cookie': 'PHPSESSID=' + loggedInSessionId,
			'Connection': 'keep-alive'
			
		}),
		responseData;
	
		request.on('response', function (response) {
			response.setEncoding('utf8');
			response.on('data', function (chunk) {
				responseData += chunk;
			});
		
			response.on('end', function(){
				//console.log('response.headers', response.headers);
				test.equal(200, response.statusCode, 'Unexpected status code');
				test.ok(typeof response.headers['set-cookie'] == 'undefined', 'Unexpected cookie header');
				
				logoutRequest();
			});
		});		
		request.end();
	},
	
	logoutRequest = function(){
		var request = client.request('DELETE', '/sessions/' + loggedInSessionId, {
				'Host': site.host,
				'Accept': accept,
				'Cookie': 'PHPSESSID=' + loggedInSessionId,
				'Connection': 'keep-alive'
			}),
		
			responseData = '',
			responseOutput;	
		
		request.on('response', function (response) {
			response.setEncoding('utf8');
			response.on('data', function (chunk) {
				responseData += chunk;
			});
		
			response.on('end', function(){
				//console.log('response.headers', response.headers);
				test.equal(200, response.statusCode, 'Bad status code');
				if(200 != response.statusCode){
					test.done();
					return;
				}
				
				responseOutput = JSON.parse(responseData);
				
				test.equal(responseOutput.userId, 0, "Bad user id");
				
				test.ok(typeof response.headers['set-cookie'] == 'object', 'Missing cookie header');
				test.ok(typeof response.headers['set-cookie'][0] == 'string', 'Empty cookie header');
				test.ok(/PHPSESSID=[0-9a-z]{26,32};/.test(response.headers['set-cookie'][0]), 'Missing session cookie header');				
				loggedOutSessionId = response.headers['set-cookie'][0].match(/PHPSESSID=([0-9a-z]{26,32});/)[1];
				
				test.notEqual(loggedOutSessionId, loggedInSessionId, 'Session id has not changed');
				test.notEqual(loggedOutSessionId, initialSessionId, 'Session id is back to initial session id');
				
				test.done();
			});
		});		
		request.end();
	};
	
	initialRequest();
	
};
