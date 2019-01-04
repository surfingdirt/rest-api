var firstPhoto = {
	"id": '1',
	"itemType": "photo",
	"title": "firstPhotoTitle",
	"description": "firstPhotoDescription",
	"date": "2002-02-18 10:00:00",
	"submitter": { id: '6', title: 'Writeruser' },
	"lastEditor": { id: null, title: null },
	"lastEditionDate": null,
	"latitude": null,
	"longitude": null,
	"dpt": { id: null, title: null },
	"spot": { id: null, title: null },
	"trick": { id: null, title: null },
	"status": 'valid',
	"album": { id: '1', title: 'photoAlbumTitleEn' },
	"mediaType": 'photo',
	"uri": 'tata.jpg',
	"width": '720',
	"height": '540',
	"size": '56789',
	"mediaSubType": 'jpg',
	"thumbnailUri": 'tata.jpg',
	"thumbnailWidth": '160',
	"thumbnailHeight": '120',
	"thumbnailSubType": 'jpg',
	author: { id: '1', title: 'Plainuser' },
	"externalKey": null		
}, firstPhotoFr = {
		"id": '1',
		"itemType": "photo",
		"title": "firstPhotoTitle",
		"description": "firstPhotoDescription",
		"date": "2002-02-18 10:00:00",
		"submitter": { id: '6', title: 'Writeruser' },
		"lastEditor": { id: null, title: null },
		"lastEditionDate": null,
		"latitude": null,
		"longitude": null,
		"dpt": { id: null, title: null },
		"spot": { id: null, title: null },
		"trick": { id: null, title: null },
		"status": 'valid',
		"album": { id: '1', title: 'photoAlbumTitleFr' },
		"mediaType": 'photo',
		"uri": 'tata.jpg',
		"width": '720',
		"height": '540',
		"size": '56789',
		"mediaSubType": 'jpg',
		"thumbnailUri": 'tata.jpg',
		"thumbnailWidth": '160',
		"thumbnailHeight": '120',
		"thumbnailSubType": 'jpg',
		author: { id: '1', title: 'Plainuser' },
		"externalKey": null		
	};


module.exports = {	
	notificationsForGuestSinceJan2003: {
		"range":"custom",
		"from":"2003-01-01 08:00:00",
		"newItems":{
			"newElementsAndMetadata":[{
				"parent":{
					"id":"5",
					"itemType": "spot",
					"title":"validSpotTitle5",
					"description":"validSpotDescription5",
					"date":"2003-01-01 09:00:00",
					"submitter": { id: '5', title: 'Editoruser' },
					"lastEditor": { id: null, title: null },
					"lastEditionDate":null,
					"dpt": { id: 1, title: 'Ain' },
					"longitude":"2.30487549",
					"latitude":"48.77291276",
					"status":"valid",
					"difficulty":"2",
					"spotType":"2",
					"groundType":"2"
				},
				"children":[]
			}, {
				"parent":{
					"id":"4",
					"itemType": "spot",
					"title":"validSpotTitle4",
					"description":"validSpotDescription4",
					"date":"2003-01-01 09:00:00",
					"submitter": { id: '4', title: 'Adminuser' },
					"lastEditor": { id: null, title: null },
					"lastEditionDate":null,
					"dpt": { id: null, title: null },
					"longitude":"2.30587549",
					"latitude":"48.77391276",
					"status":"valid",
					"difficulty":"3",
					"spotType":"3",
					"groundType":"3"
				},
				"children":[]
			}, {
				"parent":{
					"id":"3",
					"itemType": "spot",
					"title":"validSpotTitle3",
					"description":"validSpotDescription3",
					"date":"2003-01-01 09:00:00",
					"submitter": { id: '3', title: 'Banneduser' },
					"lastEditor": { id: null, title: null },
					"lastEditionDate":null,
					"dpt": { id: null, title: null },
					"longitude":"2.30687549",
					"latitude":"48.77491276",
					"status":"valid",
					"difficulty":"2",
					"spotType":"2",
					"groundType":"2"
				},
				"children":[]
			}, {
				"parent":{
					"id":"1",
					"itemType": "spot",
					"title":"firstValidSpotTitle",
					"description":"firstValidSpotDescription",
					"date":"2003-01-01 09:00:00",
					"submitter": { id: '1', title: 'Plainuser' },
					"lastEditor": { id: null, title: null },
					"lastEditionDate":null,
					"dpt": { id: null, title: null },
					"longitude":"2.30887549",
					"latitude":"48.77691276",
					"status":"valid",
					"difficulty":"2",
					"spotType":"2",
					"groundType":"2"
				},
				"children":[]
			}, {
				"parent":{
					"id":"4",
					"itemType": "trick",
					"title":"trick4Title",
					"description":"trick4Description",
					"date":"2011-01-01 23:23:01",
					"submitter": { id: '1', title: 'Plainuser' },
					"lastEditor": { id: null, title: null },
					"lastEditionDate":null,
					"status":"valid",
					"difficulty":"2",
					"trickTip":"bonus4"
				},
				"children":[]
			}, {
				"parent":{
					"id":"3",
					"itemType": "trick",
					"title":"trick3Title",
					"description":"trick3Description",
					"date":"2011-01-01 23:23:00",
					"submitter": {id: '7', title: 'Otheruser'},
					"lastEditor": { id: null, title: null },
					"lastEditionDate":null,
					"status":"valid",
					"difficulty":"2",
					"trickTip":"bonus3"
				},
				"children":[]
			}, {
				"parent":{
					"id":"1",
					"itemType": "trick",
					"title":"firstValidTrickTitle",
					"description":"firstValidTrickDescription",
					"date":"2011-01-01 21:23:00",
					"submitter": { id: '1', title: 'Plainuser' },
					"lastEditor": { id: null, title: null },
					"lastEditionDate":null,
					"status":"valid",
					"difficulty":"2",
					"trickTip":"bonus1"
				},
				"children":[]
			}
		],
		"oldElementsAndNewMetadata":[{
			parent: firstPhoto,
			children: [{
				count: '3',
			    dataType: 'comment',
			    link: '/photo/firstphototitle_1/#commentId1'
			}]}                            
		]}
	},
	
	notificationsForGuestSinceJan2011: {
		"range":"custom",
		"from":"2011-01-01 08:00:00",
		"newItems":{
			"newElementsAndMetadata":[{
				"parent":{
					"id":"4",
					"itemType": "trick",
					"title":"trick4Title",
					"description":"trick4Description",
					"date":"2011-01-01 23:23:01",
					"submitter": { id: '1', title: 'Plainuser' },
					"lastEditor": { id: null, title: null },
					"lastEditionDate":null,
					"status":"valid",
					"difficulty":"2",
					"trickTip":"bonus4"
				},
				"children":[]
			}, {
				"parent":{
					"id":"3",
					"itemType": "trick",
					"title":"trick3Title",
					"description":"trick3Description",
					"date":"2011-01-01 23:23:00",
					"submitter": {id: '7', title: 'Otheruser'},
					"lastEditor": { id: null, title: null },
					"lastEditionDate":null,
					"status":"valid",
					"difficulty":"2",
					"trickTip":"bonus3"
				},
				"children":[]
			}, {
				"parent":{
					"id":"1",
					"itemType": "trick",
					"title":"firstValidTrickTitle",
					"description":"firstValidTrickDescription",
					"date":"2011-01-01 21:23:00",
					"submitter": { id: '1', title: 'Plainuser' },
					"lastEditor": { id: null, title: null },
					"lastEditionDate":null,
					"status":"valid",
					"difficulty":"2",
					"trickTip":"bonus1"
				},
				"children":[]
			}
		],
		"oldElementsAndNewMetadata":[{
 			parent: firstPhoto,
			children: [{
				count: '3',
			    dataType: 'comment',
			    link: '/photo/firstphototitle_1/#commentId1'
			}]}
		]}
	},

	notificationsForPlainuserSinceJan2003 : {
		"range":"custom",
		"from":"2003-01-01 08:00:00",
		"newItems":{
			"newElementsAndMetadata":[{
				"parent":{
					"id":"5",
					"itemType": "spot",
					"title":"validSpotTitle5",
					"description":"validSpotDescription5",
					"date":"2003-01-01 09:00:00",
					"submitter": { id: '5', title: 'Editoruser' },
					"lastEditor": { id: null, title: null },
					"lastEditionDate":null,
					"dpt": { id: 1, title: 'Ain' },
					"longitude":"2.30487549",
					"latitude":"48.77291276",
					"status":"valid",
					"difficulty":"2",
					"spotType":"2",
					"groundType":"2"
				},
				"children":[]
			}, {
				"parent":{
					"id":"4",
					"itemType": "spot",
					"title":"validSpotTitle4",
					"description":"validSpotDescription4",
					"date":"2003-01-01 09:00:00",
					"submitter": { id: '4', title: 'Adminuser' },
					"lastEditor": { id: null, title: null },
					"lastEditionDate":null,
					"dpt": { id: null, title: null },
					"longitude":"2.30587549",
					"latitude":"48.77391276",
					"status":"valid",
					"difficulty":"3",
					"spotType":"3",
					"groundType":"3"
				},
				"children":[]
			}, {
				"parent":{
					"id":"3",
					"itemType": "spot",
					"title":"validSpotTitle3",
					"description":"validSpotDescription3",
					"date":"2003-01-01 09:00:00",
					"submitter": { id: '3', title: 'Banneduser' },
					"lastEditor": { id: null, title: null },
					"lastEditionDate":null,
					"dpt": { id: null, title: null },
					"longitude":"2.30687549",
					"latitude":"48.77491276",
					"status":"valid",
					"difficulty":"2",
					"spotType":"2",
					"groundType":"2"
				},
				"children":[]
			}, {
				"parent":{
					"id":"4",
					"itemType": "trick",
					"title":"trick4Title",
					"description":"trick4Description",
					"date":"2011-01-01 23:23:01",
					"submitter": { id: '1', title: 'Plainuser' },
					"lastEditor": { id: null, title: null },
					"lastEditionDate":null,
					"status":"valid",
					"difficulty":"2",
					"trickTip":"bonus4"
				},
				"children":[]
			}, {
				"parent":{
					"id":"3",
					"itemType": "trick",
					"title":"trick3Title",
					"description":"trick3Description",
					"date":"2011-01-01 23:23:00",
					"submitter": {id: '7', title: 'Otheruser'},
					"lastEditor": { id: null, title: null },
					"lastEditionDate":null,
					"status":"valid",
					"difficulty":"2",
					"trickTip":"bonus3"
				},
				"children":[]
			}
		],
		"oldElementsAndNewMetadata":[{
 			parent: firstPhotoFr,
			children: [{
				count: '1',
			    dataType: 'comment',
			    link: '/photo/firstphototitle_1/#commentId3'
			}]}
		]}		
	},

	notificationsForPlainuserSinceJan2011 : {
		"range":"custom",
		"from":"2011-01-01 08:00:00",
		"newItems":{
			"newElementsAndMetadata":[{
				"parent":{
					"id":"4",
					"itemType": "trick",
					"title":"trick4Title",
					"description":"trick4Description",
					"date":"2011-01-01 23:23:01",
					"submitter": { id: '1', title: 'Plainuser' },
					"lastEditor": { id: null, title: null },
					"lastEditionDate":null,
					"status":"valid",
					"difficulty":"2",
					"trickTip":"bonus4"
				},
				"children":[]
			}, {
				"parent":{
					"id":"3",
					"itemType": "trick",
					"title":"trick3Title",
					"description":"trick3Description",
					"date":"2011-01-01 23:23:00",
					"submitter": { id: '7', title: 'Otheruser' },
					"lastEditor": { id: null, title: null },
					"lastEditionDate":null,
					"status":"valid",
					"difficulty":"2",
					"trickTip":"bonus3"
				},
				"children":[]
			}
		],
		"oldElementsAndNewMetadata":[{
 			parent: firstPhotoFr,
			children: [{
				count: '1',
			    dataType: 'comment',
			    link: '/photo/firstphototitle_1/#commentId3'
			}]}
		]}				
	},
	
	notificationsForOtherUserSinceJan2003: {
		"range":"custom",
		"from":"2003-01-01 08:00:00",
		"newItems":{
			"newElementsAndMetadata":[{
				"parent":{
					"id":"5",
					"itemType": "spot",
					"title":"validSpotTitle5",
					"description":"validSpotDescription5",
					"date":"2003-01-01 09:00:00",
					"submitter": { id: '5', title: 'Editoruser' },
					"lastEditor": { id: null, title: null },
					"lastEditionDate":null,
					"dpt": { id: 1, title: 'Ain' },
					"longitude":"2.30487549",
					"latitude":"48.77291276",
					"status":"valid",
					"difficulty":"2",
					"spotType":"2",
					"groundType":"2"
				},
				"children":[]
			}, {
				"parent":{
					"id":"4",
					"itemType": "spot",
					"title":"validSpotTitle4",
					"description":"validSpotDescription4",
					"date":"2003-01-01 09:00:00",
					"submitter": { id: '4', title: 'Adminuser' },
					"lastEditor": { id: null, title: null },
					"lastEditionDate":null,
					"dpt": { id: null, title: null },
					"longitude":"2.30587549",
					"latitude":"48.77391276",
					"status":"valid",
					"difficulty":"3",
					"spotType":"3",
					"groundType":"3"
				},
				"children":[]
			}, {
				"parent":{
					"id":"3",
					"itemType": "spot",
					"title":"validSpotTitle3",
					"description":"validSpotDescription3",
					"date":"2003-01-01 09:00:00",
					"submitter": { id: '3', title: 'Banneduser' },
					"lastEditor": { id: null, title: null },
					"lastEditionDate":null,
					"dpt": { id: null, title: null },
					"longitude":"2.30687549",
					"latitude":"48.77491276",
					"status":"valid",
					"difficulty":"2",
					"spotType":"2",
					"groundType":"2"
				},
				"children":[]
			}, {
				"parent":{
					"id":"1",
					"itemType": "spot",
					"title":"firstValidSpotTitle",
					"description":"firstValidSpotDescription",
					"date":"2003-01-01 09:00:00",
					"submitter": { id: '1', title: 'Plainuser' },
					"lastEditor": { id: null, title: null },
					"lastEditionDate":null,
					"dpt": { id: null, title: null },
					"longitude":"2.30887549",
					"latitude":"48.77691276",
					"status":"valid",
					"difficulty":"2",
					"spotType":"2",
					"groundType":"2"
				},
				"children":[]
			}
		],
		"oldElementsAndNewMetadata":[{
 			parent: firstPhoto,
			children: [{
				count: '2',
			    dataType: 'comment',
			    link: '/photo/firstphototitle_1/#commentId1'
			}]}
		]}		
	},
	
	notificationsForOtheruserSinceJan2011: {
		"range":"custom",
		"from":"2011-01-01 08:00:00",
		"newItems":{
			"newElementsAndMetadata":[],
			"oldElementsAndNewMetadata":[{
	 			parent: firstPhoto,
				children: [{
					count: '2',
				    dataType: 'comment',
				    link: '/photo/firstphototitle_1/#commentId1'
				}
			]}]
		}		
	},	
	
	dynamicPosts : {
		empty: {
			"range":"custom",
			"from":"2011-10-01 00:00:00",
			"newItems":{
				"newElementsAndMetadata":[],
				"oldElementsAndNewMetadata":[]
			}
		},
		
		messageSeenByOtheruser: {
			"range":"custom",
			"from":"2011-10-01 00:00:00",
			"newItems":{
				"newElementsAndMetadata":[{
				     parent: {"id": '6', "itemType": "privatemessage", "content": "fromPlainToOther","date": "2011-10-01 10:00:00", "submitter": { id: '1', title: 'Plainuser' }, "lastEditor": { id: null, title: null }, "lastEditionDate": null,"toUser": { id: '7', title: 'Otheruser' },"read": '0',"status": "valid"},
				     children: []
				}],
				"oldElementsAndNewMetadata":[]
			}
		},
		
		newSpotAndMessageForOtherUser: {
			"range":"custom",
			"from":"2011-10-01 00:00:00",
			"newItems":{
				"newElementsAndMetadata":[{
				     parent: {"id": '6', "itemType": "privatemessage", "content": "fromPlainToOther","date": "2011-10-01 10:00:00", "submitter": { id: '1', title: 'Plainuser' }, "lastEditor": { id: null, title: null }, "lastEditionDate": null,"toUser": { id: '7', title: 'Otheruser' },"read": '0',"status": "valid"},
				     children: []
				}, {
				     parent: {"id": '6', "itemType":"spot", "title": "validSpotTitle","description": "validSpotDescription","date": '2011-10-01 10:00:00',"submitter": { id: '1', title: 'Plainuser' },"lastEditor": { id: null, title: null },"lastEditionDate": null,"dpt": { id: null, title: null },"longitude": "1.20000000","latitude": "2.30000000","status": 'valid',"difficulty": '1',"spotType": null,"groundType": null},
				     children: []
				}],
				"oldElementsAndNewMetadata":[]
			}
		},
		
		newSpot: {
			"range":"custom",
			"from":"2011-10-01 00:00:00",
			"newItems":{
				"newElementsAndMetadata":[{
				     parent: {"id": '6', "itemType":"spot", "title": "validSpotTitle","description": "validSpotDescription","date": '2011-10-01 10:00:00',"submitter": { id: '1', title: 'Plainuser' },"lastEditor": { id: null, title: null },"lastEditionDate": null,"dpt": { id: null, title: null },"longitude": "1.20000000","latitude": "2.30000000","status": 'valid',"difficulty": '1',"spotType": null,"groundType": null},
				     children: []
				}],
				"oldElementsAndNewMetadata":[]
			}
		},
		
		newSpotAndTrick: {
			"range":"custom",
			"from":"2011-10-01 00:00:00",
			"newItems":{
				"newElementsAndMetadata":[{
				     parent: {"id": '6', "itemType":"spot", "title": "validSpotTitle","description": "validSpotDescription","date": '2011-10-01 10:00:00',"submitter": { id: '1', title: 'Plainuser' },"lastEditor": { id: null, title: null },"lastEditionDate": null,"dpt": { id: null, title: null },"longitude": "1.20000000","latitude": "2.30000000","status": 'valid',"difficulty": '1',"spotType": null,"groundType": null},
				     children: []
				}, {
				     parent: {"id": '5', "itemType":"trick","title": "validTitle","description": "validDescription","date": '2011-10-01 10:00:00',"submitter": { id: '1', title: 'Plainuser' },"lastEditor": { id: null, title: null },"lastEditionDate": null,"status": 'valid',"difficulty": '3',"trickTip": 'tip!',},
				     children: []
				}],
				"oldElementsAndNewMetadata":[]
			}
		},
		
		newVideo: {
			"range":"custom",
			"from":"2011-10-01 00:00:00",
			"newItems":{
				"newElementsAndMetadata":[{
				     parent: {"id": '7',"itemType":"video","title": "validVideoPostTitle","description": "validVideoPostDescription","date": "2011-10-01 10:00:00","submitter": { id: '1', title: 'Plainuser' },"lastEditor": { id: null, title: null },"lastEditionDate": null,"longitude": '1.20000000',"latitude": '2.30000000',"dpt": { id: null, title: null },"spot": { id: null, title: null },"trick": { id: null, title: null },"status": 'valid',"album": {id: '2', 'title': 'videoAlbumTitleEn'},"mediaType": 'video',"uri": 'N8SS-rUEZPg',"width": '360',"height": '240',"size": '0',"mediaSubType": 'youtube',"thumbnailUri": 'fakeThumb',"thumbnailWidth": '160',"thumbnailHeight": '120',"thumbnailSubType": 'jpg',"author": { id: '1', title: 'Plainuser' },"externalKey": null			},
				     children: []
				}],
				"oldElementsAndNewMetadata":[]
			}

		},
		
		newSpotTrickAndVideo: {
			"range":"custom",
			"from":"2011-10-01 00:00:00",
			"newItems":{
				"newElementsAndMetadata":[{
				     parent: {"id": '6', "itemType":"spot", "title": "validSpotTitle","description": "validSpotDescription","date": '2011-10-01 10:00:00',"submitter": { id: '1', title: 'Plainuser' },"lastEditor": { id: null, title: null },"lastEditionDate": null,"dpt": { id: null, title: null },"longitude": "1.20000000","latitude": "2.30000000","status": 'valid',"difficulty": '1',"spotType": null,"groundType": null},
				     children: []
				}, {
				     parent: {"id": '5', "itemType":"trick","title": "validTitle","description": "validDescription","date": '2011-10-01 10:00:00',"submitter": { id: '1', title: 'Plainuser' },"lastEditor": { id: null, title: null },"lastEditionDate": null,"status": 'valid',"difficulty": '3',"trickTip": 'tip!',},
				     children: []
				},{
				     parent: {"id": '7',"itemType":"video","title": "validVideoPostTitle","description": "validVideoPostDescription","date": "2011-10-01 10:00:00","submitter": { id: '1', title: 'Plainuser' },"lastEditor": { id: null, title: null },"lastEditionDate": null,"longitude": '1.20000000',"latitude": '2.30000000',"dpt": { id: null, title: null },"spot": { id: null, title: null },"trick": { id: null, title: null },"status": 'valid',"album": {id: '2', 'title': 'videoAlbumTitleEn'},"mediaType": 'video',"uri": 'N8SS-rUEZPg',"width": '360',"height": '240',"size": '0',"mediaSubType": 'youtube',"thumbnailUri": 'fakeThumb',"thumbnailWidth": '160',"thumbnailHeight": '120',"thumbnailSubType": 'jpg',"author": { id: '1', title: 'Plainuser' },"externalKey": null			},
				     children: []
				}],
				"oldElementsAndNewMetadata":[]
			}			
		},

		newSpotMessageAndVideoForOtherUser: {
			"range":"custom",
			"from":"2011-10-01 00:00:00",
			"newItems":{
				"newElementsAndMetadata":[{
				     parent: {"id": '6', "itemType": "privatemessage", "content": "fromPlainToOther","date": "2011-10-01 10:00:00", "submitter": { id: '1', title: 'Plainuser' }, "lastEditor": { id: null, title: null }, "lastEditionDate": null,"toUser": { id: '7', title: 'Otheruser' },"read": '0',"status": "valid"},
				     children: []
				}, {
				     parent: {"id": '6', "itemType":"spot", "title": "validSpotTitle","description": "validSpotDescription","date": '2011-10-01 10:00:00',"submitter": { id: '1', title: 'Plainuser' },"lastEditor": { id: null, title: null },"lastEditionDate": null,"dpt": { id: null, title: null },"longitude": "1.20000000","latitude": "2.30000000","status": 'valid',"difficulty": '1',"spotType": null,"groundType": null},
				     children: []
				}, {
				     parent: {"id": '7',"itemType":"video","title": "validVideoPostTitle","description": "validVideoPostDescription","date": "2011-10-01 10:00:00","submitter": { id: '1', title: 'Plainuser' },"lastEditor": { id: null, title: null },"lastEditionDate": null,"longitude": '1.20000000',"latitude": '2.30000000',"dpt": { id: null, title: null },"spot": { id: null, title: null },"trick": { id: null, title: null },"status": 'valid',"album": {id: '2', 'title': 'videoAlbumTitleEn'},"mediaType": 'video',"uri": 'N8SS-rUEZPg',"width": '360',"height": '240',"size": '0',"mediaSubType": 'youtube',"thumbnailUri": 'fakeThumb',"thumbnailWidth": '160',"thumbnailHeight": '120',"thumbnailSubType": 'jpg',"author": { id: '1', title: 'Plainuser' },"externalKey": null			},
				     children: []
				}],
				"oldElementsAndNewMetadata":[]
			}
		},
		
		newSpotTrickVideoAndAlbum: {
			"range":"custom",
			"from":"2011-10-01 00:00:00",
			"newItems":{
				"newElementsAndMetadata":[{
					parent: {"id": "15","itemType":"mediaalbum","title": 'newAlbum',"description": "awesomeAlbum","date": '2011-10-01 10:00:00',"submitter": { id: '1', title: 'Plainuser' },"lastEditor": { id: null, title: null },"lastEditionDate": null,"status": 'valid',"albumType": 'simple',"albumAccess": 'public',"albumCreation": 'user'},
					children: []
				}, {
				     parent: {"id": '6', "itemType":"spot", "title": "validSpotTitle","description": "validSpotDescription","date": '2011-10-01 10:00:00',"submitter": { id: '1', title: 'Plainuser' },"lastEditor": { id: null, title: null },"lastEditionDate": null,"dpt": { id: null, title: null },"longitude": "1.20000000","latitude": "2.30000000","status": 'valid',"difficulty": '1',"spotType": null,"groundType": null},
				     children: []
				}, {
				     parent: {"id": '5', "itemType":"trick","title": "validTitle","description": "validDescription","date": '2011-10-01 10:00:00',"submitter": { id: '1', title: 'Plainuser' },"lastEditor": { id: null, title: null },"lastEditionDate": null,"status": 'valid',"difficulty": '3',"trickTip": 'tip!',},
				     children: []
				},{
				     parent: {"id": '7',"itemType":"video","title": "validVideoPostTitle","description": "validVideoPostDescription","date": "2011-10-01 10:00:00","submitter": { id: '1', title: 'Plainuser' },"lastEditor": { id: null, title: null },"lastEditionDate": null,"longitude": '1.20000000',"latitude": '2.30000000',"dpt": { id: null, title: null },"spot": { id: null, title: null },"trick": { id: null, title: null },"status": 'valid',"album": {id: '2', 'title': 'videoAlbumTitleEn'},"mediaType": 'video',"uri": 'N8SS-rUEZPg',"width": '360',"height": '240',"size": '0',"mediaSubType": 'youtube',"thumbnailUri": 'fakeThumb',"thumbnailWidth": '160',"thumbnailHeight": '120',"thumbnailSubType": 'jpg',"author": { id: '1', title: 'Plainuser' },"externalKey": null			},
				     children: []
				}],
				"oldElementsAndNewMetadata":[]
			}			
		},
		
		
	}
};	
