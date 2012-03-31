var constants = require('./constants'),
	site = constants.site,
	http = require('http'),
	client = http.createClient(site.port),
	restfw = require('./restfw'),
	cache = restfw.cache,
	querystring = require('querystring'),
	accept = 'application/json; q=1.0';

// It should send a new password by email when the user POSTS data to /lost-password/
exports.testLostPassword = function(test){
	var data = querystring.stringify({
			"username": "plainuser",
		}),
			
		request = client.request('POST', '/lost-password/', {
			'Host': site.host,
			'Accept': accept,
			'Connection': 'keep-alive',
			'Content-Type': 'application/x-www-form-urlencoded',
			'Content-Length': data.length
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
			test.equal(200, response.statusCode, 'Bad response status code');
			if(200 != response.statusCode){
				test.done();
				return;
			}
		
			responseOutput = JSON.parse(responseData);
			test.equal(responseOutput.status, true, "Bad status returned");
			test.equal(responseOutput.resourceId, 1, "Bad resource id returned");
			
			test.done();
		});
	});		
	request.write(data, 'utf8');
	request.end();
};

// It should fail to activate the new password for plain user
exports.testFailToActivateNewPassword = function(test){
	var data = querystring.stringify({
			"aK": "wrongkey",
		}),
			
		request = client.request('PUT', '/riders/1/activate-new-password/', {
			'Host': site.host,
			'Accept': accept,
			'Connection': 'keep-alive',
			'Content-Type': 'application/x-www-form-urlencoded',
			'Content-Length': data.length
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
			test.equal(404, response.statusCode, 'Bad response status code');
			test.done();
		});
	});		
	request.write(data, 'utf8');
	request.end();	
};

// IT should activate the new password for plain user
exports.testActivateNewPassword = function(test){
	var data = querystring.stringify({
			"aK": "randomkeyfortestrandomkeyfortest",
		}),
			
		request = client.request('PUT', '/riders/1/activate-new-password/', {
			'Host': site.host,
			'Accept': accept,
			'Connection': 'keep-alive',
			'Content-Type': 'application/x-www-form-urlencoded',
			'Content-Length': data.length
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
			test.equal(200, response.statusCode, 'Bad response status code');
			test.done();
		});
	});		
	request.write(data, 'utf8');
	request.end();	
};


// It should fail to confirm pending user
exports.testFailToConfirmPendingUser = function(test){
	var data = querystring.stringify({
			"aK": "wrongkey",
		}),
			
		request = client.request('PUT', '/riders/8/confirmation/', {
			'Host': site.host,
			'Accept': accept,
			'Connection': 'keep-alive',
			'Content-Type': 'application/x-www-form-urlencoded',
			'Content-Length': data.length
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
			test.equal(404, response.statusCode, 'Bad response status code');
			test.done();
		});
	});		
	request.write(data, 'utf8');
	request.end();
};

// It should confirm pending user
exports.testConfirmPendingUser = function(test){
	var data = querystring.stringify({
			"aK": "art85dnh2obrozxtqo830shfcmsp4acl",
		}),
			
		request = client.request('PUT', '/riders/8/confirmation/', {
			'Host': site.host,
			'Accept': accept,
			'Connection': 'keep-alive',
			'Content-Type': 'application/x-www-form-urlencoded',
			'Content-Length': data.length
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
			test.equal(200, response.statusCode, 'Bad response status code');
			if(200 != response.statusCode){
				test.done();
				return;
			}
		
			responseOutput = JSON.parse(responseData);
			test.equal(responseOutput.status, true, "Bad status returned");
			test.equal(responseOutput.alreadyDone, false, "Bad value for alreadyDone");
			
			test.done();
		});
	});		
	request.write(data, 'utf8');
	request.end();
};

// It should return 'alreadyDone=true' after trying to activate pending user a second time
exports.testConfirmPendingUserAgain = function(test){
	var data = querystring.stringify({
			"aK": "art85dnh2obrozxtqo830shfcmsp4acl",
		}),
			
		request = client.request('PUT', '/riders/8/confirmation/', {
			'Host': site.host,
			'Accept': accept,
			'Connection': 'keep-alive',
			'Content-Type': 'application/x-www-form-urlencoded',
			'Content-Length': data.length
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
			test.equal(200, response.statusCode, 'Bad response status code');
			if(200 != response.statusCode){
				test.done();
				return;
			}
		
			responseOutput = JSON.parse(responseData);
			test.equal(responseOutput.status, true, "Bad status returned");
			test.equal(responseOutput.alreadyDone, true, "Bad value for alreadyDone");
			
			test.done();
		});
	});		
	request.write(data, 'utf8');
	request.end();
};

