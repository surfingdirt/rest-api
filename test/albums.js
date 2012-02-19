/*
 * albums.js: albums 1, 2, 3, as in prod. Album 4 is non empty...? Album 5 is created by plain user on the fly.
 * fixtures: albums 1,2,3 created by adminuser 4.
 * album 5 => user 1 plainuser
 * album 6 => user 3 banneduser
 * album 7 => user 4 adminuser
 * album 8 => user 5 editoruser
 * album 9 => user 6 writeruser
 * album 10 => user 7 otheruser
 * album 11 => user 8 pendinguser
 * 
 * album 12 is non-empty and was created by plainuser
 * album 13 is created by plain user on the fly
 * album 14 is created by admin on the fly
 */



var constants = require('./constants'),
	site = constants.site,
	http = require('http'),
	client = http.createClient(site.port),
	restfw = require('./restfw'),
	restClient = restfw.client,
	cache = restfw.cache,
	querystring = require('querystring'),
	fixtures = require('./fixtures').albums,
	mediaFixtures = require('./fixtures').media;

cache.clear();

restClient.init({
	host: site.host,
	port: site.port,
	resource: '/albums/',
});

// It should freeze the date on the server
exports.testSetDate = function(test) {
	restClient.setDate(test, '2011-08-01 15:55:55');
};

/******************************************************************************
 * ALBUMS CRUD
 *****************************************************************************/

// Guest users should be able to see public albums
exports.testListPublicAlbumsAsGuestSuccess = function(test){
	restClient.reset();
	restClient.list(test, fixtures.listAllPublicAlbums);
};

// Guest should be allowed to see the content of a public album
exports.testGetPublicAlbumAsGuest = function(test){
	restClient.reset();
	restClient.setProperties(Object.keys(fixtures.photoAlbum.defaultList));
	restClient.get(1, test, fixtures.photoAlbum.defaultList, 'json');
}

// Default pagination parameters should be used
exports.testDefaultAlbumPagination = function(test){
	restClient.reset();
	restClient.setProperties(Object.keys(fixtures.photoAlbum.paginatedList));
	restClient.get(1, test, fixtures.photoAlbum.defaultList, 'json');
};

// Pagination parameters should control how many media are returned in the album
exports.testAlbumPagination = function(test){
	restClient.reset();
	restClient.setProperties(Object.keys(fixtures.photoAlbum.paginatedList));
	restClient.get(1, test, fixtures.photoAlbum.paginatedList, 'json', null, {start:2, count:2, dir: 'ASC'});
}

// Logged out users should not be allowed to create an album
exports.testGuestCreateAlbumFail = function(test){
	restClient.reset();
	restClient.post({"title": "uselessTitle"}, function(reponseOutput, response) {
		test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
		test.done();
	});
};

// Logged out users should not be allowed to update an album
exports.testGuestUpdateAlbumFail = function(test){
	restClient.reset();
	restClient.put(1, {"title": "wontUpdate"}, function(reponseOutput, response) {
		test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
		test.done();
	});
};

// A logged in user should be allowed to create albums
exports.testPlainUserCreateAlbumSuccess = function(test){
	restClient.reset();
	restClient.login("plainuser", "123456789", test, function(sessionId){
		restClient.post({
			"title": "newAlbum",
			"description": "awesomeAlbum",
		}, test, {"resourceId": "13", "errors": []}, "stringify");
	});
};

// An album owner should be allowed to update their album
exports.testPlainUserUpdateOwnAlbumSuccess = function(test){
	restClient.reset();
	restClient.login("plainuser", "123456789", test, function(sessionId){
		restClient.put(13, {
			"title": "updatedPlainUserAlbumTitle",
			"description": "updatedPlainUserAlbumDescription",
		}, test, {"resourceId":"13", "errors": []}, '', function afterUpdate(test){
			restClient.get(13, test, fixtures.plainUserAlbum.updatedBySelf, 'json');
		});
	});
};

// A non-empty manually-created album should not be allowed to be deleted
exports.testDeleteNonEmptyManuallyCreatedAlbumFail = function(test){
	restClient.reset();
	restClient.login("plainuser", "123456789", test, function(sessionId){
		restClient.del(12, function(responseData, response){
			test.equal(403, response.statusCode, 'Unexpected status code ' + response.statusCode);
			test.done();
		});
	});
};

// A member should be allowed to create a static album
exports.testMemberCreateStaticSuccess = function(test){
	restClient.reset();
	restClient.login("plainuser", "123456789", test, function(sessionId){
		restClient.setDebug(false);	
		restClient.post({
			"title": "staticPublicAlbumTitle",
			"description": "staticPublicAlbumDescription",
		}, test, {"resourceId": "14", "errors": []}, "json", "stringify", function afterPost(test){
			restClient.get(14, test, fixtures.staticAlbum);
		});
	});
};

// An admin should be allowed to edit a public album
exports.testAdminUpdatePublicAlbumSuccess = function(test){
	restClient.reset();
	restClient.login("adminuser", "123456789", test, function(sessionId){
		restClient.put(1, {
			"title": "updatedPhotoAlbumTitle"
		}, test, {"resourceId":"1", "errors": []}, '', function afterUpdate(test){
			restClient.get(1, test, fixtures.updatedPhotoAlbum, 'json');
		});
	});
};

// An admin should be allowed to edit another user's album
exports.testAdminUpdatePlainUsersAlbumSuccess = function(test){
	restClient.reset();
	restClient.login("adminuser", "123456789", test, function(sessionId){
		restClient.put(13, {
			"title": "updatedByAdminPlainUserAlbumTitle"
		}, test, {"resourceId":"13", "errors": []}, '', function afterUpdate(test){
			restClient.get(13, test, fixtures.plainUserAlbum.updatedByAdmin, 'json');
		});
	});
};

// An editor should be allowed to edit another user's album
exports.testEditorUpdatePlainUsersAlbumSuccess = function(test){
	restClient.reset();
	restClient.login("editoruser", "123456789", test, function(sessionId){
		restClient.put(13, {
			"title": "updatedByEditorPlainUserAlbumTitle",
		}, test, {"resourceId":"13", "errors": []}, '', function afterUpdate(test){
			restClient.get(13, test, fixtures.plainUserAlbum.updatedByEditor, 'json');
		});
	});
};

// An album owner should be allowed to delete an empty, manually created album
exports.testPlainUserDeleteOwnEmptyAlbumSuccess = function(test){
	restClient.reset();
	restClient.login("plainuser", "123456789", test, function onLogin(sessionId){
		restClient.del(13, function onDeleteResponse(responseData, response){
			test.equal(200, response.statusCode, 'Unexpected status code ' + response.statusCode);
			restClient.get(13, function afterDelete(responseData, response){
				test.equal(404, response.statusCode, 'Unexpected status code ' + response.statusCode);
				test.done();	
			});			
		});
	});
};

/******************************************************************************
 * LISTING A USER'S ALBUMS
 *****************************************************************************/

//A guest user should be able to list another user's public albums
exports.testGuestShouldSeePlainUsersPublicAlbums = function(test){
	restClient.reset();
	restClient.setResource('/riders/1/albums/');
	restClient.list(test, fixtures.publicPlainUserAlbums);
};

// A logged-in user should be able to list all their albums
exports.testPlainUserShouldSeeHisAlbums = function(test){
	restClient.reset();
	restClient.setResource('/albums/');
	restClient.login("plainuser", "123456789", test, function onLogin(sessionId){
		restClient.put(14, {
			"status": "invalid",
		}, test, {"resourceId":"14", "errors": []}, '', function afterUpdate(test){
			restClient.setResource('/riders/1/albums/');
			restClient.list(test, fixtures.allPlainUserAlbums);
		});
	});
};

// An admin should be able to list all of a user's albums
exports.testAdminShouldSeeAllOfPlainUsersAlbums = function(test){
	restClient.reset();
	restClient.setResource('/riders/1/albums/');
	restClient.login("adminuser", "123456789", test, function onLogin(sessionId){
		restClient.list(test, fixtures.allPlainUserAlbums);
	});
};

// An editor should be able to list all of a user's albums
exports.testEditorShouldSeeAllOfPlainUsersAlbums = function(test){
	restClient.reset();
	restClient.setResource('/riders/1/albums/');
	restClient.login("editoruser", "123456789", test, function onLogin(sessionId){
		restClient.list(test, fixtures.allPlainUserAlbums);
	});
};
