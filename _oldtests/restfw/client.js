var http = require('http'),
	httpClient,
	sessionId,
	querystring = require('querystring'),
	fs = require('fs'),
	accept = 'application/json; q=1.0',
	options = {
		host: '',
		port: 80,
		resource: '', // url of the rest resource. Must start and end with a '/'
		properties: [] // list of properties that each resource instance must expose
	},
	datetime = null,
	expectedTestsOffset = 1,
	profile = 0,
	debugSessionId = '111630029',
	debug = 0;	
	
module.exports = {
	init: function(opt)	{
		if(typeof opt.host == 'string'){
			options.host = opt.host;
		}
		if(typeof opt.port == 'string'){
			options.port = opt.port;
		}
		if(typeof opt.resource == 'string'){
			options.resource = opt.resource;
		}
		if(typeof opt.properties == 'object' && opt.properties instanceof Array){
			options.properties = opt.properties;
		}

		httpClient = http.createClient(options.port);
	},
	
	setDebug: function(bool) {
		debug = !!bool;
	},
	
	setProfile: function(bool) {
		profile = !!bool;
	},
	
	setProperties: function(prop){
		options.properties = prop;
	},
	
	setResource: function(res) {
		options.resource = res;
	},
	
	getDate: function(){
		return datetime;
	},
	
	setDate: function(test, date, callback){
		var request = httpClient.request('GET', '/test/freeze-time/?datetime=' + encodeURIComponent(date), {
			'Host': options.host,
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
				var responseOutput = JSON.parse(responseData);
				test.equals(responseOutput.status, true);
				test.equals(responseOutput.datetime, date);
				datetime = responseOutput.datetime;
				if(typeof callback == "function"){
					callback(test);
				} else {
					test.done();
				}
			});
		});		
		request.end();		
	},
	
	getLocalDate: function(){
		var d = new Date(),
			pad = function(n) {return n<10 ? '0'+n : n};
			date = '';
		date += d.getFullYear() + '-';
		date += pad((d.getUTCMonth() + 1 )) + '-';
		date += pad(d.getUTCDate()) + ' ';
		date += pad(d.getUTCHours()) + ':';
		date += pad(d.getUTCMinutes()) + ':';
		date += pad(d.getUTCSeconds());		
		
		return date;
	},

	onError: function(){},
	
	/**
	 * Tests the resource list via a GET request
	 */	
	list: function(test, expectedOutput, params){
		var url = options.resource;
		if(typeof params == 'object') {
			var parts = [], p;
			for(p in params) {
				parts.push(p + '=' + encodeURIComponent(params[p]));
			}
			url += '?' + parts.join('&');
		}

		var accept = (typeof(rawFormat) == 'string' && rawFormat == 'html') ? 'text/html' : 'application/json',
			format = (typeof(rawFormat) == 'string' && rawFormat == 'html') ? 'html' : 'json',
			params = {
				host: options.host,
				Accept: accept
			},
			request,
			responseData = '',
			cookies = [];
		
		if(sessionId){
			cookies.push('sId=' + sessionId);
		}
		if(profile){
			cookies.push('start_profile=1; use_remote=1; debug_start_session=1; debug_session_id=' + debugSessionId + '; ZendDebuggerCookie=192.168.1.7%2C127.0.0.1%3A10137%3A0||08C|77742D65|19800050;');
		}
		if(debug){
			cookies.push('use_remote=1; debug_session_id=' + debugSessionId + '; debug_start_session=1; ZendDebuggerCookie=192.168.1.7%2C127.0.0.1%3A10137%3A0||08C|77742D65|104593673; debug_host=192.168.1.7,127.0.0.1; debug_fastfile=1; debug_port=10137; start_debug=1; send_debug_header=1; send_sess_end=1; debug_jit=1; debug_stop=1');
		}		
		if(cookies.length > 0) {
			params.Cookie = cookies.join('; ');
		}
		request = httpClient.request('GET', url, params);
		request.end();
		
		request.on('response', function (response) {
			response.setEncoding('utf8');
			response.on('data', function (chunk) {
				responseData += chunk;
			});
		
			response.on('end', function(){
				//console.log('responseData', responseData);
				if(typeof test == 'function'){
					test(responseData, response);
				} else {
					try {
						test.deepEqual(JSON.parse(responseData), expectedOutput, 'Response not identical to expected object');
					} catch(e) {
						console.log('response:', responseData);
						test.equal(false, true, 'Error while testing response data: ' + e);
					}
					test.done();
				}
			});
		});
	},

	/**
	 * Tests the resource retrieval via a GET request
	 */	
	get: function(id, test, expectedOutput, rawFormat, callback, extraParams){
		expectedTestsOffset++;
		
		var accept = (typeof(rawFormat) == 'string' && rawFormat == 'html') ? 'text/html' : 'application/json',
			format = (typeof(rawFormat) == 'string' && rawFormat == 'html') ? 'html' : 'json',
			params = {
				host: options.host,
				Accept: accept
			},
			cookies = [],
			request,
			responseData = '',
			responseOutput;
		
		extraParams = extraParams || {};
		
			
		if(sessionId){
			cookies.push('sId=' + sessionId);
		}
		if(debug){
			cookies.push('use_remote=1; debug_session_id=' + debugSessionId + '; debug_start_session=1; ZendDebuggerCookie=192.168.1.7%2C127.0.0.1%3A10137%3A0||08C|77742D65|104593673; debug_host=192.168.1.7,127.0.0.1; debug_fastfile=1; debug_port=10137; start_debug=1; send_debug_header=1; send_sess_end=1; debug_jit=1; debug_stop=1');
		}
		if(cookies.length > 0) {
			params.Cookie = cookies.join('; ');
		}
		
		var resource = options.resource + id;
		if(extraParams){
			resource += '?' + querystring.stringify(extraParams);			
		}
		
		request = httpClient.request('GET', resource, params);		
		
		request.end();

		request.on('response', function (response) {
			response.setEncoding('utf8');
			response.on('data', function (chunk) {
				responseData += chunk;
			});
		
			response.on('end', function(){
				//console.log('rawFormat', rawFormat, 'format', format, 'expectedOutput', expectedOutput, 'responseData', responseData);
				if(typeof test == 'function'){
					test(responseData, response);
				} else {
					test.equals(200, response.statusCode, 'Bad status code: ' + response.statusCode);
					if(format == 'json') {
						try {
							test.deepEqual(JSON.parse(responseData), expectedOutput, 'Response not identical to expected object');
						} catch(e) {
							console.log('response:', responseData);
							test.equal(false, true, 'Error while testing response data: ' + e);
						}
					} else {
						// console.log('rawFormat', rawFormat, 'format', format, 'responseData', responseData, 'expectedOutput', expectedOutput);	
						test.equal(responseData, expectedOutput);
					}
					if(typeof callback == 'function'){
						callback(test);
					} else {
						test.done();		
					}
				}
			});
		});
	},

	/**
     * Tests the resource creation via a POST request
	 */
	post: function(jsonData, test, expectedOutput, rawFormat, compareMethod, callback, expect400) {
		var data = querystring.stringify(jsonData),
			accept = (typeof(rawFormat) == 'string' && rawFormat == 'html') ? 'text/html; q=1.0' : 'application/json; q=1.0',
			format = (typeof(rawFormat) == 'string' && rawFormat == 'html') ? 'html' : 'json',
			params = {
				host: options.host,
				Accept: accept,
				'Connection': 'keep-alive',
				'Content-Type': 'application/x-www-form-urlencoded',
				'Content-Length': data.length					
			},
			cookies = [],
			request,			
			responseData = '',
			responseOutput;
		
		if(sessionId){
			cookies.push('sId=' + sessionId);
		}
		if(debug){
			cookies.push('use_remote=1; debug_session_id=' + debugSessionId + '; debug_start_session=1; ZendDebuggerCookie=192.168.1.7%2C127.0.0.1%3A10137%3A0||08C|77742D65|104593673; debug_host=192.168.1.7,127.0.0.1; debug_fastfile=1; debug_port=10137; start_debug=1; send_debug_header=1; send_sess_end=1; debug_jit=1; debug_stop=1');
		}
		if(cookies.length > 0) {
			params.Cookie = cookies.join('; ');
		}
		request = httpClient.request('POST', options.resource, params),

		request.on('response', function (response) {
			response.setEncoding('utf8');
			response.on('data', function (chunk) {
				responseData += chunk;
			});
		
			response.on('end', function(){
				if(typeof test == 'function'){
					test(responseData, response);
				} else {
					var expectedCode = expect400 ? 400 : 200;
					test.equals(expectedCode, response.statusCode, 'Bad status code: ' + response.statusCode, 'Response:', responseOutput);
					
					if(format == 'json') {
						try {
							test.deepEqual(JSON.parse(responseData), expectedOutput, 'Response not identical to expected object');
						} catch(e) {
							console.log('response:', responseData);
							test.equal(false, true, 'Error while testing response data: ' + e);
						}
					} else {
						test.equal(responseData, expectedOutput);
					}
					
					if(typeof callback == 'function'){
						callback(test);
					} else {
						test.done();		
					}
				}
			});
		});
		
		request.write(data, 'utf8');
		request.end();
		
	},
	put: function(id, jsonData, test, expectedOutput, rawFormat, callback, expect400) {
		var data = querystring.stringify(jsonData),
			accept = (typeof(rawFormat) == 'string' && rawFormat == 'html') ? 'text/html; q=1.0' : 'application/json; q=1.0',
			format = (typeof(rawFormat) == 'string' && rawFormat == 'html') ? 'html' : 'json',
			cookies = [],
			params = {
				host: options.host,
				Accept: accept,
				'Connection': 'keep-alive',
				'Content-Type': 'application/x-www-form-urlencoded',
				'Content-Length': data.length					
			},
			request,			
			responseData = '',
			responseOutput;
		
		if(sessionId){
			cookies.push('sId=' + sessionId);
		}
		if(debug){
			cookies.push('use_remote=1; debug_session_id=' + debugSessionId + '; debug_start_session=1; ZendDebuggerCookie=192.168.1.7%2C127.0.0.1%3A10137%3A0||08C|77742D65|104593673; debug_host=192.168.1.7,127.0.0.1; debug_fastfile=1; debug_port=10137; start_debug=1; send_debug_header=1; send_sess_end=1; debug_jit=1; debug_stop=1');
		}
		if(cookies.length > 0) {
			params.Cookie = cookies.join('; ');
		}
		
		request = httpClient.request('PUT', options.resource + id, params),

		request.on('response', function (response) {
			
			
			response.setEncoding('utf8');
			response.on('data', function (chunk) {
				responseData += chunk;
			});
		
			response.on('end', function(){
				//console.log('rawFormat', rawFormat, 'format', format, 'responseData', responseData, 'responseOutput', responseOutput, 'expectedOutput', expectedOutput);					
				
				if(typeof test == 'function'){
					test(responseData, response);
				} else {
					var expectedCode = expect400 ? 400 : 200;
					test.equals(expectedCode, response.statusCode, 'Bad status code: ' + response.statusCode, 'Response:', responseOutput);

					if(format == 'json') {
						try {
							test.deepEqual(JSON.parse(responseData), expectedOutput, 'Response not identical to expected object');
						} catch(e) {
							console.log('response:', responseData);
							test.equal(false, true, 'Error while testing response data: ' + e);
						}
					} else {
						test.equal(responseData, expectedOutput);
					}
					
					if(typeof callback == 'function'){
						callback(test);
					} else {
						test.done();		
					}

				}
			});
		});
		
		request.write(data, 'utf8');
		request.end();		
	},
	del: function(id, test, expectedOutput, rawFormat, callback){
		var accept = (typeof(rawFormat) == 'string' && rawFormat == 'html') ? 'text/html' : 'application/json',
			format = (typeof(rawFormat) == 'string' && rawFormat == 'html') ? 'html' : 'json',
			params = {
				host: options.host,
				Accept: accept
			},
			cookies = [],
			request,
			responseData = '',
			responseOutput;
		
		if(sessionId){
			cookies.push('sId=' + sessionId);
		}
		if(debug){
			cookies.push('use_remote=1; debug_session_id=' + debugSessionId + '; debug_start_session=1; ZendDebuggerCookie=192.168.1.7%2C127.0.0.1%3A10137%3A0||08C|77742D65|104593673; debug_host=192.168.1.7,127.0.0.1; debug_fastfile=1; debug_port=10137; start_debug=1; send_debug_header=1; send_sess_end=1; debug_jit=1; debug_stop=1');
		}
		if(cookies.length > 0) {
			params.Cookie = cookies.join('; ');
		}
			
		request = httpClient.request('DELETE', options.resource + id, params);		
		
		request.end();

		request.on('response', function (response) {
			response.setEncoding('utf8');
			response.on('data', function (chunk) {
				responseData += chunk;
			});
		
			response.on('end', function(){
				//console.log('rawFormat', rawFormat, 'format', format, 'expectedOutput', expectedOutput, 'responseData', responseData);
				if(typeof test == 'function'){
					test(responseData, response);
				} else {
					if(format == 'json') {
						try {
							test.deepEqual(JSON.parse(responseData), expectedOutput, 'Response not identical to expected object');
						} catch(e) {
							console.log('response:', responseData);
							test.equal(false, true, 'Error while testing response data: ' + e);
						}
					} else {
						// console.log('rawFormat', rawFormat, 'format', format, 'responseData', responseData, 'expectedOutput', expectedOutput);	
						test.equal(responseData, expectedOutput);
					}
					if(typeof callback == 'function'){
						callback(test);
					} else {
						test.done();		
					}
				}
			});
		});
		
	},
		
	login: function(username, password, test, callback) {
		expectedTestsOffset++;
		
		var loginData = querystring.stringify({
				"username": username,
				"userP": password
			}),
			
			request = httpClient.request('POST', '/sessions/', {
				'Host': options.host,
				'Accept': accept,
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
				test.equals(200, response.statusCode, 'Bad status code after login: ' + response.statusCode);
					
				responseOutput = JSON.parse(responseData);
				
				if(!(/sId=[0-9a-z]{26,32};/.test(response.headers['set-cookie'][response.headers['set-cookie'].length - 1]))){
					throw new Error('Missing session cookie header');
				}				
				sessionId = response.headers['set-cookie'][response.headers['set-cookie'].length - 1].match(/sId=([0-9a-z]{26,32});/)[1];
				//console.log('Calling callback with sessionId', sessionId, responseOutput, response.headers);
				callback(sessionId);
			});
		});		
		request.write(loginData, 'utf8');
		request.end();		
	},
	
	reset: function(){
		expectedTestsOffset	= 1;
		this.setDebug(false);
		this.clearSession();
	},
	
	clearSession: function(){
		sessionId = null;
	},
	setSession: function(id){
		sessionId = id;
	},

	/**
     * Tests the resource creation via a POST request, with files
     * Format of files: [{
     *   paramName: '',
     *   filename: '',
     *   mimeType: '',
     *   path: ''
     * }]
	 */
	postWithFiles: function(jsonData, files, test, expectedOutput, rawFormat, compareMethod, callback, putId) {
		var stack = [],
			post = [],
		    boundary = Math.random(),
			accept = (typeof(rawFormat) == 'string' && rawFormat == 'html') ? 'text/html; q=1.0' : 'application/json; q=1.0',
			format = (typeof(rawFormat) == 'string' && rawFormat == 'html') ? 'html' : 'json',
			params = {
				host: options.host,
				Accept: accept,
				'Connection': 'keep-alive',
				'Accept-Encoding': 'gzip, deflate'
			},
			cookies = [],
			request,			
			responseData = '',
			responseOutput,
			i,
			l,
			file_reader,
			file_contents = [],
			postLength = 0,
			readFiles = 0;
		
		for(param in jsonData) {
			post.push(new Buffer(EncodeFieldPart(boundary, param, jsonData[param]), 'ascii'));
		}
		
		if(files.length > 0) {
			_addFileBuffer(0);
		}
		
		function _addFileBuffer(index) {
			post.push(new Buffer(EncodeFilePart(boundary, files[index].mimeType, files[index].paramName, files[index].filename), 'ascii'));
			file_reader = fs.createReadStream(files[index].path, {encoding: 'binary'});
			file_contents[index] = '';
			file_reader.on('data', function(data){
			    file_contents[index] += data;
			});
			file_reader.on('end', function(){
				post.push(new Buffer(file_contents[index], 'binary'));
				if(index == files.length - 1){
					_doSendPostWithFiles();
				} else {
					_addFileBuffer(index + 1);
				}
			});
		}
			
		function _doSendPostWithFiles() {
			post.push(new Buffer("\r\n--" + boundary + "--"), 'ascii');
			for(i = 0; i < post.length; i++) {
				postLength += post[i].length;
			}

	        params['Content-Type'] = 'multipart/form-data; boundary=' + boundary,
	        params['Content-Length'] = postLength;
			
			if(sessionId){
				cookies.push('sId=' + sessionId);
			}
			if(debug){
				cookies.push('use_remote=1; debug_session_id=' + debugSessionId + '; debug_start_session=1; ZendDebuggerCookie=192.168.1.7%2C127.0.0.1%3A10137%3A0||08C|77742D65|104593673; debug_host=192.168.1.7,127.0.0.1; debug_fastfile=1; debug_port=10137; start_debug=1; send_debug_header=1; send_sess_end=1; debug_jit=1; debug_stop=1');
			}
			if(cookies.length > 0) {
				params.Cookie = cookies.join('; ');
			}

			if(putId){
				request = httpClient.request('POST', options.resource + putId, params);
			} else {
				request = httpClient.request('POST', options.resource, params);
			}

			request.on('response', function (response) {
				response.setEncoding('utf8');
				response.on('data', function (chunk) {
					responseData += chunk;
				});
			
				response.on('end', function(){
					//console.log('rawFormat', rawFormat, 'format', format, 'responseData', responseData, 'responseOutput', responseOutput, 'expectedOutput', expectedOutput);					
					
					if(typeof test == 'function'){
						test(responseData, response);
					} else {
						if(format == 'json') {
							try {
								test.deepEqual(JSON.parse(responseData), expectedOutput, 'Response not identical to expected object');
							} catch(e) {
								console.log('response:', responseData);
								test.equal(false, true, 'Error while testing response data: ' + e);
							}
						} else {
							test.equal(responseData, expectedOutput);
						}
						
						if(typeof callback == 'function'){
							expectedTestsOffset++;
							callback(test);
						} else {
							test.done();		
						}
					}
				});
			});
			
			for (var i = 0; i < post.length; i++) {
				request.write(post[i]);
			}
			request.end();			
		};
	},	
	
	putWithFiles: function(id, jsonData, files, test, expectedOutput, rawFormat, compareMethod, callback) {
		var stack = [],
			put = [],
		    boundary = Math.random(),
			accept = (typeof(rawFormat) == 'string' && rawFormat == 'html') ? 'text/html; q=1.0' : 'application/json; q=1.0',
			format = (typeof(rawFormat) == 'string' && rawFormat == 'html') ? 'html' : 'json',
			params = {
				host: options.host,
				Accept: accept,
				'Connection': 'keep-alive',
				'Accept-Encoding': 'gzip, deflate'
			},
			cookies = [],
			request,			
			responseData = '',
			responseOutput,
			i,
			l,
			file_reader,
			file_contents = [],
			putLength = 0,
			readFiles = 0;

		for(param in jsonData) {
			put.push(new Buffer(EncodeFieldPart(boundary, param, jsonData[param]), 'ascii'));
		}
		
		if(files.length > 0) {
			_addFileBuffer(0);
		}
		
		function _addFileBuffer(index) {
			put.push(new Buffer(EncodeFilePart(boundary, files[index].mimeType, files[index].paramName, files[index].filename), 'ascii'));
			file_reader = fs.createReadStream(files[index].path, {encoding: 'binary'});
			file_contents[index] = '';
			file_reader.on('data', function(data){
			    file_contents[index] += data;
			});
			file_reader.on('end', function(){
				put.push(new Buffer(file_contents[index], 'binary'));
				if(index == files.length - 1){
					_doSendPutWithFiles();
				} else {
					_addFileBuffer(index + 1);
				}
			});
		}
			
		function _doSendPutWithFiles() {
			put.push(new Buffer("\r\n--" + boundary + "--"), 'ascii');
			for(i = 0; i < put.length; i++) {
				putLength += put[i].length;
			}

	        params['Content-Type'] = 'multipart/form-data; boundary=' + boundary,
	        params['Content-Length'] = putLength;
			
			if(sessionId){
				cookies.push('sId=' + sessionId);
			}
			if(true){
				cookies.push('use_remote=1; debug_session_id=' + debugSessionId + '; debug_start_session=1; ZendDebuggerCookie=192.168.1.7%2C127.0.0.1%3A10137%3A0||08C|77742D65|104593673; debug_host=192.168.1.7,127.0.0.1; debug_fastfile=1; debug_port=10137; start_debug=1; send_debug_header=1; send_sess_end=1; debug_jit=1; debug_stop=1');
			}
			if(cookies.length > 0) {
				params.Cookie = cookies.join('; ');
			}

			request = httpClient.request('PUT', options.resource + id, params),

			request.on('response', function (response) {
				response.setEncoding('utf8');
				response.on('data', function (chunk) {
					responseData += chunk;
				});
			
				response.on('end', function(){
					//console.log('rawFormat', rawFormat, 'format', format, 'responseData', responseData, 'responseOutput', responseOutput, 'expectedOutput', expectedOutput);					
					
					if(typeof test == 'function'){
						test(responseData, response);
					} else {
						if(format == 'json') {
							try {
								test.deepEqual(JSON.parse(responseData), expectedOutput, 'Response not identical to expected object');
							} catch(e) {
								console.log('response:', responseData);
								test.equal(false, true, 'Error while testing response data: ' + e);
							}
						} else {
							test.equal(responseData, expectedOutput);
						}
						
						if(typeof callback == 'function'){
							expectedTestsOffset++;
							callback(test);
						} else {
							test.done();		
						}
					}
				});
			});
			
			for (var i = 0; i < put.length; i++) {
				request.write(put[i]);
			}
			request.end();			
		};
	},	
};

function EncodeFieldPart(boundary,name,value) {
    var return_part = "--" + boundary + "\r\n";
    return_part += "Content-Disposition: form-data; name=\"" + name + "\"\r\n\r\n";
    return_part += value + "\r\n";
    return return_part;
}

function EncodeFilePart(boundary,type,name,filename) {
    var return_part = "--" + boundary + "\r\n";
    return_part += "Content-Disposition: form-data; name=\"" + name + "\"; filename=\"" + filename + "\"\r\n";
    return_part += "Content-Type: " + type + "\r\n\r\n";
    return return_part;
}