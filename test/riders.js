var constants = require('./constants'),
	site = constants.site,
	http = require('http'),
	client = http.createClient(site.port),
	restfw = require('./restfw'),
	restClient = restfw.client,
	cache = restfw.cache,
	querystring = require('querystring'),
	fixtures = require('./fixtures').riders;


cache.clear();

restClient.init({
	host: site.host,
	port: site.port,
	resource: '/riders/'
});

// It should freeze the date on the server
exports.testSetDate = function(test) {
	restClient.setDate(test, '2011-08-01 15:55:55');
};
/*
// It should return a 404 because user does not exist
exports.testNotFound = function(test){
	var request = client.request('GET', '/riders/2500', {
		'Host': site.host,
		'Accept': 'application/json; q=1.0',
		'Connection': 'keep-alive'
	}),
	responseData;
	
	request.on('response', function (response) {
		response.setEncoding('utf8');
		response.on('data', function (chunk) {
			responseData += chunk;
		});
	
		response.on('end', function(){
			//console.log('response.statusCode', response.statusCode);
			test.equal(404, response.statusCode, 'Unexpected status code ' + response.statusCode);
			test.done();
		});
	});		
	request.end();	
},

// It should retrieve plainuser's data when logging in as different users
exports.testPlainuserData = {
	asGuest: function(test){
		try{
			restClient.reset();
			restClient.setProperties(Object.keys(fixtures.plainuser.guest));
			restClient.get(1, test, fixtures.plainuser.guest, 'json');
		} catch(e) {
			test.ok(false, e.message);
			test.done();
		}
	},		
	asOtherUser: function(test){
		try{
			restClient.reset();
			restClient.login("otheruser", "123456789", test, function(sessionId){
				restClient.setProperties(Object.keys(fixtures.plainuser.otheruser));
				restClient.get(1, test, fixtures.plainuser.otheruser, 'json');
			});
		} catch(e) {
			test.ok(true, e.message);
			test.done();
		}
	},
	asWriter: function(test){
		try{
			restClient.reset();
			restClient.login("writeruser", "123456789", test, function(sessionId){
				restClient.setProperties(Object.keys(fixtures.plainuser.writer));
				restClient.get(1, test, fixtures.plainuser.writer, 'json');
			});
		} catch(e) {
			test.ok(true, e.message);
			test.done();
		}
	},
	asEditor: function(test){
		try{
			restClient.reset();
			restClient.login("editoruser", "123456789", test, function(sessionId){
				restClient.setProperties(Object.keys(fixtures.plainuser.editor));
				restClient.get(1, test, fixtures.plainuser.editor, 'json');
			});
		} catch(e) {
			test.ok(true, e.message);
			test.done();
		}
	},
	asAdmin: function(test){
		try{
			restClient.reset();
			restClient.login("adminuser", "123456789", test, function(sessionId){
				restClient.setProperties(Object.keys(fixtures.plainuser.admin));
				restClient.get(1, test, fixtures.plainuser.admin, 'json');
			});
		} catch(e) {
			test.ok(false, e.message);
			test.done();
		}
	},
	asSelf: function(test){
		try{
			restClient.reset();
			restClient.login("plainuser", "123456789", test, function(sessionId){
				// Here we set the expected lastLogin date dynamically because
				// it was updated on login.
				var fixture = fixtures.plainuser.self;
					fixture.lastLogin = restClient.getDate();
				restClient.setProperties(Object.keys(fixtures.plainuser.self));
				restClient.get(1, test, fixtures.plainuser.self, 'json');
			});
		} catch(e) {
			test.ok(false, e.message);
			test.done();
		}
	}
};

// It should retrieve the list of all valid users in the DB

exports.testAllUsersListAsGuest = function(test){
	restClient.reset();
	restClient.setProperties(Object.keys(fixtures.plainuser.guest));
	restClient.list(test, [fixtures.plainuser.guest, fixtures.adminuser.guest, fixtures.editoruser.guest, fixtures.writeruser.guest, fixtures.otheruser.guest]);
};

// It should retrieve the list of all users in the DB
exports.testAllUsersListAsAdmin = function(test){
	restClient.reset();
	restClient.setProperties(Object.keys(fixtures.plainuser.guest));
	restClient.login("adminuser", "123456789", test, function(sessionId){
		restClient.list(test, [fixtures.plainuser.admin2, fixtures.banneduser.admin, fixtures.adminuser.admin, fixtures.editoruser.admin2, fixtures.writeruser.admin2, fixtures.otheruser.admin2, fixtures.pendinguser.admin]);
	});
};

// It should return a 403 error
exports.testBannedUserAsGuest = function(test){
	var request = client.request('GET', '/riders/3', {
		'Host': site.host,
		'Accept': 'application/json; q=1.0',
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
			test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
			test.done();
		});
	});		
	request.end();	
},

// It should return a 403 error
exports.testPendingUserAsGuest = function(test){
	var request = client.request('GET', '/riders/8', {
		'Host': site.host,
		'Accept': 'application/json; q=1.0',
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
			test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
			test.done();
		});
	});		
	request.end();	
},

// It should retrieve the 3rd and 4th valid users
exports.test3rdAnd4thUsersAsGuest = function(test){
	restClient.reset();
	restClient.setProperties(Object.keys(fixtures.plainuser.guest));
	restClient.list(test, [fixtures.editoruser.guest, fixtures.writeruser.guest], {start:2, count:2});
};

// It should retrieve the 2nd and 3rd valid users, sorted by username ascending
exports.test2ndAnd3rdUsersAsGuest = function(test){
	restClient.reset();
	restClient.setProperties(Object.keys(fixtures.plainuser.guest));
	restClient.list(test, [fixtures.editoruser.guest, fixtures.otheruser.guest], {start:1, count:2, sort: 'username'});
};

// It should retrieve the 2nd and 3rd valid users, sorted by username descending
exports.test2ndAnd3rdUsersDescAsGuest = function(test){
	restClient.reset();
	restClient.setProperties(Object.keys(fixtures.plainuser.guest));
	restClient.list(test, [fixtures.plainuser.guest, fixtures.otheruser.guest], {start:1, count:2, sort: 'username', dir: 'desc'});
};

// It should retrieve the user with given id in json format
exports.testPlainUserJsonAsGuest = function(test){
	restClient.reset();
	restClient.setProperties(Object.keys(fixtures.plainuser.guest));
	restClient.get(1, test, fixtures.plainuser.guest, 'json');
};

// It should fail to create a user because only guest is allowed to do so
exports.testCreateUserAsPlainUserFail = function(test){
	restClient.reset();
	restClient.login("plainuser", "123456789", test, function(sessionId){
		restClient.post({
			"username": "createduser",
			"userP": "123456789",
			"userPC": "123456789",
			"email": "testa1234988@toto.com",
			"trickQuestion": "slide"
		}, function(reponseOutput, response) {
			test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
			test.done();
		});
	});
};

// It should fail to create a user and return errors because of missing/invalid data
exports.testCreateUserAsGuestFail = function(test){
	restClient.reset();
	restClient.post({
			"username": "createduser",
			"userP": "123456789",
			"userPC": "a"
		}, test, {
			"resourceId": null,
			"errors": {
				"userPC": ["notSame"],
				"email": ["isEmpty"]
			}
		}, 'json', "stringify", undefined, true
	);	
};

// It should create a new user and return its id
exports.testCreateUserAsGuestSuccess = function(test){
	restClient.reset();
	restClient.post({
		"username": "createduser",
		"userP": "123456789",
		"userPC": "123456789",
		"email": "testa1234988@toto.com",
		"trickQuestion": "slide"
	}, test, {
		"resourceId": "10",
		"errors": []
	}, "stringify");
};

// It should update createduser status to member
exports.testAdminMakesCreateduserAMember = function(test){
	restClient.reset();
	restClient.login("adminuser", "123456789", test, function(sessionId){
		restClient.put(10, {
			'status': 'member'
		}, test, {"resourceId":"10", "errors": []}, '', function afterUpdate(test){
			restClient.get(10, test, {
				"userId": "10",
				"username": "createduser",
				"email": "testa1234988@toto.com",
				"date": restClient.getDate(),
				"lang": "en",
				"firstName": null,
				"lastName": null,
				"birthDate": null,
				"country": {id: null, title: null},
				"city": null,
				"zip": null,
				"gender": null,
				"level": null,
				"site": null,
				"occupation": null,
				"gear": null,
				"otherSports": null,
				"rideType": '',
				"avatar": null,
				"lastLogin": null,
				"status": "member",
				"latitude": null,
				"longitude": null,
				"birthDate": "0000-00-00"
			});		
		});		
	});
};

// It should fail to update the existing user 10
exports.testUpdateCreatedUserAsPlainuser = function(test){
	restClient.reset();
	restClient.login("plainuser", "123456789", test, function(sessionId){
		var request = client.request('PUT', '/riders/10', {
			'Host': site.host,
			'Accept': 'application/json; q=1.0',
			'Connection': 'keep-alive',
			'Cookie': 'sId=' + sessionId
		}),
		responseData;
		
		request.on('response', function (response) {
			response.setEncoding('utf8');
			response.on('data', function (chunk) {
				responseData += chunk;
			});
		
			response.on('end', function(){
				//console.log('response.headers', response.headers);
				test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
				test.done();
			});
		});		
		request.end();			
	});	
}

// It should update the existing user 10
exports.testUpdateCreatedUserAsSelf = function(test){
	restClient.reset();
	restClient.login("createduser", "123456789", test, function afterLogin(sessionId){
		//id, jsonData, test, expectedOutput, rawFormat, callback
		
		restClient.put(10, {
			"firstName": "MyFirstName",
			"lastName": "MyLastName",
			"lang":"fr",
			"birthDate": "01/01/2000",
			"country": 1,
			"city": "Toulouse",
			"zip": "31",
			"gender": "1",
			"level": "2",
			"site": "http://www.toto.com",
			"occupation": "bla",
			"gear": "myboard",
			"otherSports": "snow",
			"rideType[]": ["1", "2"],
			"avatar": "/images/avatars/user10.jpg"
		}, test, {"resourceId":"10", "errors": []}, '', function afterUpdate(test){
			restClient.get(10, test, {
				"userId": "10",
				"username": "createduser",
				"email": "testa1234988@toto.com",
				"date": restClient.getDate(),
				"lang":"fr",
				"firstName": "MyFirstName",
				"lastName": "MyLastName",
				"birthDate": "2000-01-01",
				"country": {id:1, title: "France"},
				"city": "Toulouse",
				"zip": "31",
				"gender": "1",
				"level": "2",
				"site": "http://www.toto.com",
				"occupation": "bla",
				"gear": "myboard",
				"otherSports": "snow",
				"rideType": "110",
				"avatar": "/images/avatars/user10.jpg",
				"lastLogin": restClient.getDate(),
				"latitude": null,
				"longitude": null
			});
		});
	});	
}

// It should fail to update createduser's password if passwords are different
exports.testFailUpdateCreatedUserPassword = function(test){
	restClient.reset();
	restClient.login("createduser", "123456789", test, function afterLogin(sessionId){
		restClient.put(10, {
			"userP": "aaaaaa",
			"userPC": "bbbbbb",
		}, test, {"resourceId":"10", "errors": {"userPC": ["notSame"]}}, undefined, undefined, true);	
	});
}

// It should update createduser's password
exports.testUpdateCreatedUserPassword = function(test){
	restClient.reset();
	restClient.login("createduser", "123456789", test, function afterLogin(sessionId){
		restClient.put(10, {
			"userP": "987654321",
			"userPC": "987654321",
		}, test, {"resourceId":"10", "errors": []});	
	});
}

// It should fail to login with the old password
exports.testFailToLoginCreatedUserWithOldPassword = function(test){
	restClient.reset();	

	var loginData = querystring.stringify({
			"username": "createduser",
			"userP": "123456789"
		}),
			
		request = client.request('POST', '/sessions/', {
			'Host': site.host,
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
			test.equals(403, response.statusCode, 'Bad status code after login: ' + response.statusCode);
			responseOutput = JSON.parse(responseData);
			test.done();
		});
	});		
	request.write(loginData, 'utf8');
	request.end();		
}

// It should let createduser login with the new password
exports.testLoginWithUpdatedCreatedUserPassword = function(test){
	restClient.reset();
	restClient.login("createduser", "987654321", test, function afterLogin(sessionId){
		test.done();	
	});
}

// It should not let plain user edit createduser
exports.testPlainuserCannotUpdateCreatedUser = function(test){
	restClient.reset();
	restClient.login("plainuser", "123456789", test, function afterLogin(sessionId){
		restClient.put(10, {
			"userP": "aaaa",
			"userPC": "aaaa",
		}, function afterPut(responseOutput, response){
			test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
			test.done();			
		}, {"resourceId":"10", "errors": []});	
	});
}

// It should return a 403 status code
exports.failToDeletePlainUserAsGuest = function(test){
	restClient.reset();
	var request = client.request('DELETE', '/riders/1', {
		'Host': site.host,
		'Accept': 'application/json; q=1.0',
		'Connection': 'keep-alive',
	}),
	responseData;
		
	request.on('response', function (response) {
		response.setEncoding('utf8');
		response.on('data', function (chunk) {
			responseData += chunk;
		});
	
		response.on('end', function(){
			//console.log('response.headers', response.headers);
			test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
			test.done();
		});
	});		
	request.end();			
}

// It should delete plainuser
exports.deletePlainUserAsAdmin = function(test){
	restClient.reset();
	restClient.login("adminuser", "123456789", test, function(sessionId){
		var request = client.request('DELETE', '/riders/1', {
			'Host': site.host,
			'Accept': 'application/json; q=1.0',
			'Connection': 'keep-alive',
			'Cookie': 'sId=' + sessionId
		}),
		responseData = '';
		
		request.on('response', function (response) {
			response.setEncoding('utf8');
			response.on('data', function (chunk) {
				responseData += chunk;
			});
		
			response.on('end', function(){
				//console.log('responseData', responseData);
				var responseOutput = JSON.parse(responseData);
				test.equal(200, response.statusCode, 'Unexpected status code ' + response.statusCode);
				test.equal('1', responseOutput.resourceId);
				test.equal(true, responseOutput.status);
				test.done();
			});
		});		
		request.end();			
	});		
};

exports.plainUserIsNotFound = function(test){
	var request = client.request('GET', '/riders/1', {
			'Host': site.host,
			'Accept': 'application/json; q=1.0',
			'Connection': 'keep-alive'
		}),
		responseData = "";
		
	request.on('response', function (response) {
		response.setEncoding('utf8');
		response.on('data', function (chunk) {
			responseData += chunk;
		});
	
		response.on('end', function(){
			//console.log('responseData --', responseData, "--");
			test.equals(404, response.statusCode);
			test.done();
		});
	});		
	request.end();
};

*/
var verifyRegistrationErrors = function(postData, expectedErrors, test) {
	var loginData = querystring.stringify(postData),
	
	request = client.request('POST', '/riders/', {
		'Host': site.host,
		'Accept': 'application/json; q=1.0',
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
			test.equal(400, response.statusCode, 'Bad status code');
			if(400 != response.statusCode){
				test.done();
				return;
			}
			
			if(responseData.length > 0){
				responseOutput = JSON.parse(responseData);
			}
			
			test.deepEqual(responseOutput.errors, expectedErrors, "Actual errors different than expected");
			test.done();
		});
	});		
	request.write(loginData, 'utf8');
	request.end();
};

exports.testErrorReporting = {
	allEmpty: function(test) {
		var postData = {
			 'username': '',
			 'email': '',
			 'userP': '',
			 'userPC': ''
			},
			expectedErrors = {
				'username': ['isEmpty'],
				'email': ['isEmpty'],
				'userP': ['isEmpty'],
				'userPC': ['isEmpty']
			};
		
		verifyRegistrationErrors(postData, expectedErrors, test);
	},

	userPEmpty: function(test) {
		var postData = {
			 'username': 'me',
			 'email': 'me@example.com',
			 'userP': '',
			 'userPC': '123456789'
			},
			expectedErrors = {
				'userP': ['isEmpty'],
				'userPC': ['notSame']
			};
		
		verifyRegistrationErrors(postData, expectedErrors, test);
	},

	userPCEmpty: function(test) {
		var postData = {
			 'username': 'me',
			 'email': 'me@example.com',
			 'userP': '123456789',
			 'userPC': ''
			},
			expectedErrors = {
				'userPC': ['notSame']
			};
		
		verifyRegistrationErrors(postData, expectedErrors, test);
	},

};


