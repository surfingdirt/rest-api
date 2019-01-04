/*
 * LIST 
 */

var constants = require('./constants'),
	site = constants.site,
	http = require('http'),
	client = http.createClient(site.port),
	restfw = require('./restfw'),
	restClient = restfw.client,
	cache = restfw.cache,
	querystring = require('querystring'),
	fixtures = require('./fixtures').notifications;

cache.clear();

restClient.init({
	host: site.host,
	port: site.port,
	resource: '/notifications/',
});

exports.testSetDate = function(test) {
	restClient.setDate(test, '2011-08-01 15:55:55');
};

/**
 * Existing notifications from sql fixtures
 */
//Guest users should be able to see new notifications from everyone
exports.testNotificationsForGuestSinceJan2003 = function(test){
	restClient.reset();
	restClient.list(test, fixtures.notificationsForGuestSinceJan2003, {
		from: '2003-01-01 08:00:00'
	});
};

// Guest users should be able to see new notifications from everyone
exports.testNotificationsForGuestSinceJan2011 = function(test){
	restClient.reset();
	restClient.list(test, fixtures.notificationsForGuestSinceJan2011, {
		from: '2011-01-01 08:00:00'
	});
};

// Plainuser should see notifications for his new items
exports.testNotificationsForPlainuserSinceJan2003 = function(test){
	restClient.reset();
	restClient.login("plainuser", "123456789", test, function onLogin(sessionId){
		restClient.list(test, fixtures.notificationsForPlainuserSinceJan2003, {
			from: '2003-01-01 08:00:00'
		});
	});
};

// Plainuser should see notifications for his new items
exports.testNotificationsForPlainuserSinceJan2011 = function(test){
	restClient.reset();
	restClient.login("plainuser", "123456789", test, function onLogin(sessionId){
		restClient.list(test, fixtures.notificationsForPlainuserSinceJan2011, {
			from: '2011-01-01 08:00:00'
		});
	});
};

//Otheruser should see notifications for his new items (no tricks)
exports.testNotificationsForOtheruserSinceJan2003 = function(test){
	restClient.reset();
	restClient.login("otheruser", "123456789", test, function onLogin(sessionId){
		restClient.setDebug(false);
		restClient.list(test, fixtures.notificationsForOtherUserSinceJan2003, {
			from: '2003-01-01 08:00:00'
		});
	});
};

// Otheruser should not see notifications for his new items (no tricks)
exports.testNotificationsForOtheruserSinceJan2011 = function(test){
	restClient.reset();
	restClient.login("otheruser", "123456789", test, function onLogin(sessionId){
		restClient.setDebug(false);
		restClient.list(test, fixtures.notificationsForOtheruserSinceJan2011, {
			from: '2011-01-01 08:00:00'
		});
	});
};


/**
 * Dynamic notifications
 * notifications:
	plainuser posts a media
		otheruser sees notification
		admin sees notification
		plainuser doesnt
	plainuser posts an album
		otheruser doesn't see notification
		admin sees notification (because of config)
		plainuser doesnt
 */

exports.testMessageNotifications = {
	testSetDateForPost: function(test) {
		restClient.setDate(test, '2011-10-01 10:00:00');
	},
		
	postAsPlainuser: function(test) {
		restClient.reset();
		restClient.setResource('/messages/');
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.post({
				"content": "fromPlainToOther",
				"toUser": 7,
			}, test, {"resourceId": "6", "errors": []}, "stringify");
		});		
	},

	otherUserSeesNotification: function(test) {
		restClient.reset();
		restClient.setResource('/notifications/');
		restClient.login("otheruser", "123456789", test, function onLogin(sessionId){
			restClient.list(test, fixtures.dynamicPosts.messageSeenByOtheruser, {
				from: '2011-10-01 00:00:00'
			});
		});
	},
	
	plainUserSeesNothing: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function onLogin(sessionId){
			restClient.list(test, fixtures.dynamicPosts.empty, {
				from: '2011-10-01 00:00:00'
			});
		});
	},	

	adminUserSeesNothing: function(test) {
		restClient.reset();
		restClient.login("adminuser", "123456789", test, function onLogin(sessionId){
			restClient.list(test, fixtures.dynamicPosts.empty, {
				from: '2011-10-01 00:00:00'
			});
		});
	},	

	editorUserSeesNothing: function(test) {
		restClient.reset();
		restClient.login("editoruser", "123456789", test, function onLogin(sessionId){
			restClient.list(test, fixtures.dynamicPosts.empty, {
				from: '2011-10-01 00:00:00'
			});
		});
	},	
};

exports.testSpotNotifications = {
	testSetDateForPost: function(test) {
		restClient.setDate(test, '2011-10-01 10:00:00');
	},
		
	postAsPlainuser: function(test) {
		restClient.reset();
		restClient.setResource('/spots/');
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.post({
				"title": "validSpotTitle",
				"description": "validSpotDescription",
				"status": "valid",
				"longitude": "1.20000000",
				"latitude": "2.30000000",
			}, test, {"resourceId": "6", "errors": []}, "stringify");
		});		
	},

	testSetDateForRead: function(test) {
		restClient.setDate(test, '2011-10-01 20:00:00');
	},
		
	otherUserSeesNotification: function(test) {
		restClient.reset();
		restClient.setResource('/notifications/');
		restClient.login("otheruser", "123456789", test, function onLogin(sessionId){
			restClient.list(test, fixtures.dynamicPosts.newSpotAndMessageForOtherUser, {
				from: '2011-10-01 00:00:00'
			});
		});
	},
	
	plainUserSeesNothing: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function onLogin(sessionId){
			restClient.list(test, fixtures.dynamicPosts.empty, {
				from: '2011-10-01 00:00:00'
			});
		});
	},	

	adminUserSeesNotification: function(test) {
		restClient.reset();
		restClient.login("adminuser", "123456789", test, function onLogin(sessionId){
			restClient.list(test, fixtures.dynamicPosts.newSpot, {
				from: '2011-10-01 00:00:00'
			});
		});
	},	

	editorUserSeesNotification: function(test) {
		restClient.reset();
		restClient.login("editoruser", "123456789", test, function onLogin(sessionId){
			restClient.list(test, fixtures.dynamicPosts.newSpot, {
				from: '2011-10-01 00:00:00'
			});
		});
	},	
};

exports.testTrickNotifications = {
	testSetDateForPost: function(test) {
		restClient.setDate(test, '2011-10-01 10:00:00');
	},
		
	postAsPlainuser: function(test) {
		restClient.reset();
		restClient.setResource('/tricks/');
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.post({
				"title": "validTitle",
				"description": "validDescription",
				"difficulty": "3",
				"trickTip": "tip!",
			}, test, {"resourceId": "5", "errors": []}, "stringify");
		});		
	},

	testSetDateForRead: function(test) {
		restClient.setDate(test, '2011-10-01 20:00:00');
	},
		
	otherUserDoesNotSeeTrickNotification: function(test) {
		// because of notification config = 0 for t
		restClient.reset();
		restClient.setResource('/notifications/');
		restClient.login("otheruser", "123456789", test, function onLogin(sessionId){
			restClient.list(test, fixtures.dynamicPosts.newSpotAndMessageForOtherUser, {
				from: '2011-10-01 00:00:00'
			});
		});
	},
	
	plainUserSeesNothing: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function onLogin(sessionId){
			restClient.list(test, fixtures.dynamicPosts.empty, {
				from: '2011-10-01 00:00:00'
			});
		});
	},	

	adminUserSeesNotification: function(test) {
		restClient.reset();
		restClient.login("adminuser", "123456789", test, function onLogin(sessionId){
			restClient.list(test, fixtures.dynamicPosts.newSpotAndTrick, {
				from: '2011-10-01 00:00:00'
			});
		});
	},	

	editorUserSeesNotification: function(test) {
		restClient.reset();
		restClient.login("editoruser", "123456789", test, function onLogin(sessionId){
			restClient.list(test, fixtures.dynamicPosts.newSpotAndTrick, {
				from: '2011-10-01 00:00:00'
			});
		});
	},	
};

exports.testMediaNotifications = {
	testSetDateForPost: function(test) {
		restClient.setDate(test, '2011-10-01 10:00:00');
	},
		
	postAsPlainuser: function(test) {
		restClient.reset();
		restClient.setResource('/media/');
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
			}, test, {"resourceId": "7", "errors": []}, "stringify");
		});		
	},

	testSetDateForRead: function(test) {
		restClient.setDate(test, '2011-10-01 20:00:00');
	},
		
	otherUserDoesNotSeeTrickNotification: function(test) {
		// because of notification config = 0 for t
		restClient.reset();
		restClient.setResource('/notifications/');
		restClient.login("otheruser", "123456789", test, function onLogin(sessionId){
			restClient.list(test, fixtures.dynamicPosts.newSpotMessageAndVideoForOtherUser, {
				from: '2011-10-01 00:00:00'
			});
		});
	},
	
	plainUserSeesNothing: function(test) {
		restClient.reset();
		restClient.login("plainuser", "123456789", test, function onLogin(sessionId){
			restClient.list(test, fixtures.dynamicPosts.empty, {
				from: '2011-10-01 00:00:00'
			});
		});
	},	

	adminUserSeesNotification: function(test) {
		restClient.reset();
		restClient.login("adminuser", "123456789", test, function onLogin(sessionId){
			restClient.list(test, fixtures.dynamicPosts.newSpotTrickAndVideo, {
				from: '2011-10-01 00:00:00'
			});
		});
	},	

	editorUserSeesNotification: function(test) {
		restClient.reset();
		restClient.login("editoruser", "123456789", test, function onLogin(sessionId){
			restClient.list(test, fixtures.dynamicPosts.newSpotTrickAndVideo, {
				from: '2011-10-01 00:00:00'
			});
		});
	},	
};

exports.testAlbumNotifications = {
	testSetDateForPost: function(test) {
		restClient.setDate(test, '2011-10-01 10:00:00');
	},
		
	postAsPlainuser: function(test) {
		restClient.reset();
		restClient.setResource('/albums/');
		restClient.login("plainuser", "123456789", test, function(sessionId){
			restClient.post({
				"title": "newAlbum",
				"description": "awesomeAlbum",
			}, test, {"resourceId": "15", "errors": []}, "stringify");
		});		
	},

	testSetDateForRead: function(test) {
		restClient.setDate(test, '2011-10-01 20:00:00');
	},
		
	plainUserSeesNothing: function(test) {
		restClient.reset();
		restClient.setResource('/notifications/');
		restClient.login("plainuser", "123456789", test, function onLogin(sessionId){
			restClient.list(test, fixtures.dynamicPosts.empty, {
				from: '2011-10-01 00:00:00'
			});
		});
	},	

	adminUserSeesNotification: function(test) {
		restClient.reset();
		restClient.login("adminuser", "123456789", test, function onLogin(sessionId){
			restClient.list(test, fixtures.dynamicPosts.newSpotTrickVideoAndAlbum, {
				from: '2011-10-01 00:00:00'
			});
		});
	},	

	editorUserDoesNotSeeNotification: function(test) {
		restClient.reset();
		restClient.login("editoruser", "123456789", test, function onLogin(sessionId){
			restClient.list(test, fixtures.dynamicPosts.newSpotTrickAndVideo, {
				from: '2011-10-01 00:00:00'
			});
		});
	},	
};