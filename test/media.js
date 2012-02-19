/**
 * GET valid media
 * 
 * GET invalid media as guest fail
 * GET invalid media as other user fail
 * GET invalid media as owner success
 * GET invalid media as admin success
 * GET invalid media as editor success
 * 
 * List media fail
 * 
 * POST media as guest fail
 * POST invalid media as plainuser fail
 * POST valid media as plainuser success
 * 
 * PUT valid media as guest fail
 * PUT invalid media update as owner fail
 * PUT valid media update as owner success
 * PUT valid media update as editor success
 * PUT valid media update as admin success
 * 
 * DELETE plainuser's media as  other user fail 
 * POST valid media as plainuser and then DELETE media as owner success
 * POST valid media as plainuser and then DELETE media as editor success
 * POST valid media as plainuser and then DELETE media as admin success
 */


var constants = require('./constants'),
	site = constants.site,
	http = require('http'),
	client = http.createClient(site.port),
	restfw = require('./restfw'),
	restClient = restfw.client,
	cache = restfw.cache,
	querystring = require('querystring'),
	fixtures = require('./fixtures').media;

cache.clear();

restClient.init({
	host: site.host,
	port: site.port,
	resource: '/media/',
});

// It should freeze the date on the server
exports.testSetDate = function(test) {
	restClient.setDate(test, '2011-08-01 15:55:55');
};

// Guest users should be able to see valid media
exports.testGetValidMediaSuccess = function(test){
	restClient.reset();
	restClient.get(1, test, fixtures.firstPhoto, 'json');
};

exports.testGetInvalidMedia = {
	//Guest users should not be able to see other's invalid media
	asGuestFail: function(test){
		restClient.reset();
		restClient.get(6, function(responseData, response){
			test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
			test.done();		
		});
	},

	// Other users should not be able to see a given user's invalid media
	asOtherUserFail: function(test){
		restClient.reset();
		restClient.login("otheruser", "123456789", test, function(sessionId){
			restClient.get(6, function(responseData, response){
				test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
				test.done();		
			});
		});
	},
	
	// Owner should be able to see their invalid media
	asOwnerSuccess: function(test){
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.get(6, test, fixtures.plainUserInvalidPhoto, 'json');				
		});
	},
	
	// Admins should be able to see invalid media
	asAdminSuccess: function(test){
		restClient.reset();
		restClient.login("adminuser", "123456789", test, function(sessionId){
			restClient.get(6, test, fixtures.plainUserInvalidPhotoEn, 'json');				
		});
	},
	
	// Editors should be able to see invalid media
	asEditorSuccess: function(test){
		restClient.reset();
		restClient.login("editoruser", "123456789", test, function(sessionId){
			restClient.get(6, test, fixtures.plainUserInvalidPhotoEn, 'json');				
		});
	}
};

// listing media is forbidden by design: albums are the way to list media
exports.listMediaForbidden = function(test){
	restClient.reset();
	restClient.list(function(responseData, response){
		test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
		test.done();		
	});	
};

// POST media
exports.postMedia = {
	asGuestFail: function(test) {
		restClient.reset();
		restClient.post({"title": "uselessTitle"}, function(reponseOutput, response) {
			test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
			test.done();
		});			
	},
	invalidVideoAsPlainUserFail: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.post({
				"title": "invalidMediaPost",
				"mediaType": "video",
			}, test, {"resourceId": null, "errors": {"media":["isEmpty","videoCodeNotValid"], "description":["isEmpty"]}}, "stringify");
		});		
	},
	validVideodAsPlainUserSuccess: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.post({
				"title": "validVideoPostTitle",
				"description": "validVideoPostDescription",
				"status": "valid",
				//"dpt": "31",
				//"spot": "",
				//"trick": "",
				"longitude": "1.20000000",
				"latitude": "2.30000000",
				"albumId": "2",
				"mediaType": "video",
				"media": '<iframe width="360" height="240" src="http://www.youtube.com/embed/N8SS-rUEZPg"></iframe>',
				"author": "1",
			}, test, {"resourceId": "7", "errors": []}, "string", "stringify", function(test){
				restClient.get(7, test, fixtures.validVideoPost, 'json');
			});
		});		
	},
	invalidPhotoAsPlainUserFail: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.postWithFiles({
				"title": "validPhotoPostTitle",
				"description": "validPhotoPostDescription",
				"status": "valid",
				//"dpt": "31",
				//"spot": "",
				//"trick": "",
				"longitude": "3.40000000",
				"latitude": "5.60000000",
				"albumId": "1",
				"mediaType": "photo",
				"author": "1",
				// TODO: add a validator on media element, to report an error in case the file has a bad mime type
				// currently exceptions break everything
				
			}, [fixtures.invalidPhotoFile], test, {"resourceId": null, "errors": {"media":["badMimeType"]}}, "stringify");
		});		
	},
	validPhotoAsPlainUserSuccess: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.postWithFiles({
				"title": "validPhotoPostTitle",
				"description": "validPhotoPostDescription",
				"status": "valid",
				//"dpt": "31",
				//"spot": "",
				//"trick": "",
				"longitude": "3.40000000",
				"latitude": "5.60000000",
				"albumId": "1",
				"mediaType": "photo",
				"author": "1",
			}, [fixtures.validPhotoFile], test, {"resourceId": "8", "errors": []}, "string", "stringify", function(test){
				restClient.get(8, test, fixtures.validPhotoPost, 'json');
			});
		});				
	}
};

// PUT media
exports.putVideo = {

	validAsGuestFail: function(test) {
		restClient.reset();
		restClient.put(7, {"title": "invalideMediaTitleUpdate"}, function(reponseOutput, response) {
			test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
			test.done();
		});			
	},

	invalidAsOwnerFail: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.put(7,
				{"status": "bla"},
				test,
				{"resourceId":"7", "errors": {"status": ["notInArray"]}}
			);
		});			
	},
	
	validAsOwnerSuccess: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.put(7,
				{"title": "updatedMediaTitle"},
				test,
				{"resourceId":"7", "errors": []},
				'json',
				function(test){
					restClient.get(7, test, fixtures.updatedVideo, 'json');
				}
			);
		});
	},
	
	validAsEditorSuccess: function(test) {
		restClient.reset();
		restClient.login("editoruser", "123456789", test, function(sessionId){
			restClient.put(7,
				{"title": "updated2MediaTitle"},
				test,
				{"resourceId":"7", "errors": []},
				'json',
				function(test){
					restClient.get(7, test, fixtures.updatedVideoEditor, 'json');
				}
			);
		});					
	},
	
	validAsAdminSuccess: function(test) {
		restClient.reset();
		restClient.login("adminuser", "123456789", test, function(sessionId){
			restClient.put(7,
				{"title": "updated3MediaTitle"},
				test,
				{"resourceId":"7", "errors": []},
				'json',
				function(test){
					restClient.get(7, test, fixtures.updatedVideoAdmin, 'json');
				}
			);
		});							
	}
};

exports.putPhoto = {
	validAsGuestFail: function(test) {
		restClient.reset();
		restClient.put(8, {"title": "invalideMediaTitleUpdate"}, function(reponseOutput, response) {
			test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
			test.done();
		});			
	},

	invalidAsOwnerFail: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.put(8,
				{"status": "bla"},
				test,
				{"resourceId":"8", "errors": {"status": ["notInArray"]}}
			);
		});			
	},
		
	validAsOwnerSuccess: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.postWithFiles({
				"title": "differentPhotoTitle",
				"description": "differentPhotoDescription",
				"status": "valid",
				//"dpt": "31",
				//"spot": "",
				//"trick": "",
				"longitude": "3.40000000",
				"latitude": "5.60000000",
				"albumId": "1",
				"mediaType": "photo",
				"author": "1",
			}, [fixtures.differentPhotoFile], test, {"resourceId": "8", "errors": []}, "string", "stringify", function(test){
				restClient.get(8, test, fixtures.differentPhotoPost, 'json');
			},
			8);
		});			
	},

	validAsEditorSuccess: function(test) {
		restClient.reset();
		restClient.login("editoruser", "123456789", test, function(sessionId){
			restClient.put(8,
				{"title": "updatedPhotoEditorTitle"},
				test,
				{"resourceId":"8", "errors": []},
				'json',
				function(test){
					restClient.get(8, test, fixtures.updatedPhotoEditor, 'json');
				}
			);
		});					
	},
		
	validAsAdminSuccess: function(test) {
		restClient.reset();
		restClient.login("adminuser", "123456789", test, function(sessionId){
			restClient.put(8,
				{"title": "updatedPhotoAdminTitle"},
				test,
				{"resourceId":"8", "errors": []},
				'json',
				function putCallback(test){
					restClient.get(8, test, fixtures.updatedPhotoAdmin, 'json');
				}
			);
		});							
	}
};

// DELETE media
exports.deleteMedia = {
	asOtherUserFail: function(test) {
		restClient.reset();
		restClient.login("otheruser", "123456789", test, function(sessionId){
			restClient.del(7, function(reponseOutput, response) {
				test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
				test.done();
			});
		});
	},
	
	// POST a photo as plainuser then delete it as plainuser, and make sure it's gone
	asPlainUserSuccess: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.postWithFiles({
				"title": "validPhotoPostForDeleteTitle",
				"description": "validPhotoPostForDeleteDescription",
				"status": "valid",
				//"dpt": "31",
				//"spot": "",
				//"trick": "",
				"longitude": "3.40000000",
				"latitude": "5.60000000",
				"albumId": "1",
				"mediaType": "photo",
				"author": "1",
			}, [fixtures.validPhotoFile], test, {"resourceId": "9", "errors": []}, "string", "stringify", function(test){
				restClient.get(9, test, fixtures.validPhotoPostForDelete, 'json', function(test){
					restClient.del(9, function(reponseOutput, response) {
						restClient.get(9, function(responseOutput, response){
							test.equal(404, response.statusCode, 'Unexpected status code ' + response.statusCode);
							test.done();							
						});
					});
				});
			});
		});				
		
	},
	
	// POST a photo as plainuser and then delete it as editor, and make sure it's gone
	validAsEditorSuccess: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.postWithFiles({
				"title": "validPhotoPostForDeleteTitle",
				"description": "validPhotoPostForDeleteDescription",
				"status": "valid",
				//"dpt": "31",
				//"spot": "",
				//"trick": "",
				"longitude": "3.40000000",
				"latitude": "5.60000000",
				"albumId": "1",
				"mediaType": "photo",
				"author": "1",
			}, [fixtures.validPhotoFile], test, {"resourceId": "10", "errors": []}, "string", "stringify", function(test){
				//restClient.setDebug(true);
				restClient.get(10, test, fixtures.validPhotoPostForDelete2, 'json', function(test){
					restClient.reset();
					restClient.login("adminuser", "123456789", test, function(sessionId){
						restClient.del(10, function(reponseOutput, response) {
							restClient.get(10, function(responseOutput, response){
								test.equal(404, response.statusCode, 'Unexpected status code ' + response.statusCode);
								test.done();
							});
						});
					});
				});
			});
		});				

	},

	// POST a photo as plainuser and then delete it as admin, and make sure it's gone
	validAsAdminSuccess: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.postWithFiles({
				"title": "validPhotoPostForDeleteTitle",
				"description": "validPhotoPostForDeleteDescription",
				"status": "valid",
				//"dpt": "31",
				//"spot": "",
				//"trick": "",
				"longitude": "3.40000000",
				"latitude": "5.60000000",
				"albumId": "1",
				"mediaType": "photo",
				"author": "1",
			}, [fixtures.validPhotoFile], test, {"resourceId": "11", "errors": []}, "string", "stringify", function(test){
				restClient.get(11, test, fixtures.validPhotoPostForDelete3, 'json', function(test){
					restClient.reset();
					restClient.login("adminuser", "123456789", test, function(sessionId){
						restClient.del(11, function(reponseOutput, response) {
							restClient.get(11, function(responseOutput, response){
								test.equal(404, response.statusCode, 'Unexpected status code ' + response.statusCode);
								test.done();
							});
						});
					});
				});
			});
		});
	}
};

/******************************************************************************
 * POSTING MEDIA TO ALBUMS
 *****************************************************************************/

// A guest should not be allowed to post to any album
exports.testGuestPostToAlbumFail = function(test){
	restClient.reset();
	restClient.postWithFiles({"title": "uselessTitle", "album": "1"}, [fixtures.validPhotoFile], function(reponseOutput, response) {
		test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
		test.done();
	}, null, 'json');
};


// A logged-in user should not be allowed to post to their aggregate album
exports.testPlainUserPostToAggregateFail = function(test){
	restClient.reset();
	restClient.login("plainuser", "123456789", test, function(sessionId){
		restClient.postWithFiles({
			"title": "validPhotoPostTitle",
			"description": "validPhotoPostDescription",
			"status": "valid",
			"albumId": "5",
			"mediaType": "photo",
			"author": "1",
		}, [fixtures.validPhotoFile], test, {"resourceId": null, "errors": {"albumId":["albumTypeNotAllowed"]}}, "stringify");
	});
};

// A logged-in user should not be allowed to post to another user's album
exports.testPlainUserPostToEditorsAlbumFail = function(test){
	restClient.reset();
	restClient.login("otheruser", "123456789", test, function(sessionId){
		restClient.postWithFiles({
			"title": "validPhotoPostTitle",
			"description": "validPhotoPostDescription",
			"status": "valid",
			"albumId": "12",
			"mediaType": "photo",
			"author": "1",
		}, [fixtures.validPhotoFile], function(reponseOutput, response) {
			test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
			test.done();
		}, null, 'json');
	});
};

// A logged-in user should be allowed to post to their own manually-created album, and then should be able to retrieve the newly added media by listing the album's content
exports.testPlainUserPostToOwnAlbumSuccess = function(test){
	restClient.reset();
	restClient.login("plainuser", "123456789", test, function(sessionId){
		restClient.postWithFiles({
			"title": "validPhotoPostTitle",
			"description": "validPhotoPostDescription",
			"status": "valid",
			"albumId": "12",
			"mediaType": "photo",
			"author": "1",
		}, [fixtures.validPhotoFile], test, {"resourceId": "12", "errors": []});
	});
};

// An admin should be allowed to post to another user's album
exports.testAdminPostToPlainUsersAlbumSuccess = function(test){
	restClient.reset();
	restClient.login("adminuser", "123456789", test, function(sessionId){
		restClient.postWithFiles({
			"title": "validPhotoPostTitle",
			"description": "validPhotoPostDescription",
			"status": "valid",
			"albumId": "12",
			"mediaType": "photo",
			"author": "1",
		}, [fixtures.validPhotoFile], test, {"resourceId": "13", "errors": []});
	});
};

// An editor should be allowed to post to another user's album
exports.testEditorPostToPlainUsersAlbumSuccess = function(test){
	restClient.reset();
	//restClient.setDebug(true);
	restClient.login("editoruser", "123456789", test, function(sessionId){
		restClient.postWithFiles({
			"title": "validPhotoPostTitle",
			"description": "validPhotoPostDescription",
			"status": "valid",
			"albumId": "12",
			"mediaType": "photo",
			"author": "1",
		}, [fixtures.validPhotoFile], test, {"resourceId": "14", "errors": []});
	});
};