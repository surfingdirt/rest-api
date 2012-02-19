module.exports = {	
	locationsForGuest: [
		{   id: '4',
		    title: 'validSpotTitle4',
		    description: 'validSpotDescription4',
		    date: '2003-01-01 09:00:00',
		    submitter: { id: '4', title: 'Adminuser' },
		    lastEditor: { id: null, title: null },
		    lastEditionDate: null,
		    dpt: { id: null, title: null },
		    longitude: '2.30587549',
		    latitude: '48.77391276',
		    status: 'valid',
		    difficulty: '3',
		    spotType: '3',
		    groundType: '3',
		    itemType: 'spot'
		  },{
			id: '3',
		    title: 'validSpotTitle3',
		    description: 'validSpotDescription3',
		    date: '2003-01-01 09:00:00',
		    submitter: { id: '3', title: 'Banneduser' },
		    lastEditor: { id: null, title: null },
		    lastEditionDate: null,
		    dpt: { id: null, title: null },
		    longitude: '2.30687549',
		    latitude: '48.77491276',
		    status: 'valid',
		    difficulty: '2',
		    spotType: '2',
		    groundType: '2',
		    itemType: 'spot'
		  }, {
			id: '1',
		    title: 'firstValidSpotTitle',
		    description: 'firstValidSpotDescription',
		    date: '2003-01-01 09:00:00',
		    submitter: { id: '1', title: 'Plainuser' },
		    lastEditor: { id: null, title: null },
		    lastEditionDate: null,
		    dpt: { id: null, title: null },
		    longitude: '2.30887549',
		    latitude: '48.77691276',
		    status: 'valid',
		    difficulty: '2',
		    spotType: '2',
		    groundType: '2',
		    itemType: 'spot'
		  }, {
		    userId: '1',
		    username: 'plainuser',
		    date: '2011-01-01 21:23:00',
		    lang: 'fr',
		    country: {id: 1, title: 'France'},
		    city: 'toulouse',
		    zip: '31000',
		    gender: '1',
		    level: '2',
		    gear: 'pro95',
		    otherSports: 'snowboard',
		    rideType: '110',
		    avatar: '/media/avatars/1.jpg',
		    latitude: '48.77591276',
		    longitude: '2.30787549',
		    itemType: 'user'
		  }
	],
	
	locationsForPlainuser: [
		{   id: '4',
		    title: 'validSpotTitle4',
		    description: 'validSpotDescription4',
		    date: '2003-01-01 09:00:00',
		    "submitter": { id: '4', title: 'Adminuser' },
		    lastEditor: { id: null, title: null },
		    lastEditionDate: null,
		    dpt: { id: null, title: null },
		    longitude: '2.30587549',
		    latitude: '48.77391276',
		    status: 'valid',
		    difficulty: '3',
		    spotType: '3',
		    groundType: '3',
		    itemType: 'spot'
		  },{
			id: '3',
		    title: 'validSpotTitle3',
		    description: 'validSpotDescription3',
		    date: '2003-01-01 09:00:00',
		    submitter: { id: '3', title: 'Banneduser' },
		    lastEditor: { id: null, title: null },
		    lastEditionDate: null,
		    dpt: { id: null, title: null },
		    longitude: '2.30687549',
		    latitude: '48.77491276',
		    status: 'valid',
		    difficulty: '2',
		    spotType: '2',
		    groundType: '2',
		    itemType: 'spot'
		  }, {
			id: '2',
			title: 'firstInvalidSpotTitle',
			description: 'firstInvalidSpotDescription',
			date: '2003-01-01 09:00:00',
			submitter: { id: '1', title: 'Plainuser' },
			lastEditor: { id: null, title: null },
			lastEditionDate: null,
			dpt: { id: null, title: null },
			longitude: '2.30787549',
			latitude: '48.77591276',
			status: 'invalid',
			difficulty: '2',
			spotType: '2',
			groundType: '2',
			itemType: 'spot'
		  },{
			id: '1',
		    title: 'firstValidSpotTitle',
		    description: 'firstValidSpotDescription',
		    date: '2003-01-01 09:00:00',
		    submitter: { id: '1', title: 'Plainuser' },
		    lastEditor: { id: null, title: null },
		    lastEditionDate: null,
		    dpt: { id: null, title: null },
		    longitude: '2.30887549',
		    latitude: '48.77691276',
		    status: 'valid',
		    difficulty: '2',
		    spotType: '2',
		    groundType: '2',
		    itemType: 'spot'
		  }, {
		    userId: '1',
		    username: 'plainuser',
		    date: '2011-01-01 21:23:00',
		    lang: 'fr',
		    country: {id: 1, title: 'France'},
		    city: 'toulouse',
		    zip: '31000',
		    gender: '1',
		    level: '2',
		    gear: 'pro95',
		    otherSports: 'snowboard',
		    rideType: '110',
		    avatar: '/media/avatars/1.jpg',
		    latitude: '48.77591276',
		    longitude: '2.30787549',
		    lastLogin: '2011-08-01 15:55:55',
		    firstName: 'prenom',
		    lastName: 'nom',
		    site: 'http://www.mountainboard.fr',
		    occupation: 'occupation',
		    email: 'user1@example.org',
		    birthDate: '1980-07-01',
		    itemType: 'user'
		  }	
	],
	
	validLocationsInCountry:[
		{"id":"5","title":"validSpotTitle5","description":"validSpotDescription5","date":"2003-01-01 09:00:00","submitter":{"id":"5","title":"Editoruser"},"lastEditor":{"id":null,"title":null},"lastEditionDate":null,"dpt":{"id":1,"title":'Ain'},"longitude":"2.30487549","latitude":"48.77291276","status":"valid","difficulty":"2","spotType":"2","groundType":"2","itemType":"spot"},
		
		 {"id":"4","title":"validSpotTitle4","description":"validSpotDescription4","date":"2003-01-01 09:00:00","submitter":{"id":"4","title":"Adminuser"},"lastEditor":{"id":null,"title":null},"lastEditionDate":null,"dpt":{"id":null,"title":null},"longitude":"2.30587549","latitude":"48.77391276","status":"valid","difficulty":"3","spotType":"3","groundType":"3","itemType":"spot"},
		
		 {"id":"3","title":"validSpotTitle3","description":"validSpotDescription3","date":"2003-01-01 09:00:00","submitter":{"id":"3","title":"Banneduser"},"lastEditor":{"id":null,"title":null},"lastEditionDate":null,"dpt":{"id":null,"title":null},"longitude":"2.30687549","latitude":"48.77491276","status":"valid","difficulty":"2","spotType":"2","groundType":"2","itemType":"spot"},
		
		 {"id":"1","title":"firstValidSpotTitle","description":"firstValidSpotDescription","date":"2003-01-01 09:00:00","submitter":{"id":"1","title":"Plainuser"},"lastEditor":{"id":null,"title":null},"lastEditionDate":null,"dpt":{"id":null,"title":null},"longitude":"2.30887549","latitude":"48.77691276","status":"valid","difficulty":"2","spotType":"2","groundType":"2","itemType":"spot"},
		
		 {"userId":"1","username":"plainuser","date":"2011-01-01 21:23:00","lang":"fr","country":{"id":"1","title":"France"},"city":"toulouse","zip":"31000","gender":"1","level":"2","gear":"pro95","otherSports":"snowboard","rideType":"110","avatar":"\/media\/avatars\/1.jpg","latitude":"48.77591276","longitude":"2.30787549","itemType":"user"}	                         
	],
	allLocationsInCountry:[
		{"id":"5","title":"validSpotTitle5","description":"validSpotDescription5","date":"2003-01-01 09:00:00","submitter":{"id":"5","title":"Editoruser"},"lastEditor":{"id":null,"title":null},"lastEditionDate":null,"dpt":{"id":1,"title":'Ain'},"longitude":"2.30487549","latitude":"48.77291276","status":"valid","difficulty":"2","spotType":"2","groundType":"2","itemType":"spot"},
		
		{"id":"4","title":"validSpotTitle4","description":"validSpotDescription4","date":"2003-01-01 09:00:00","submitter":{"id":"4","title":"Adminuser"},"lastEditor":{"id":null,"title":null},"lastEditionDate":null,"dpt":{"id":null,"title":null},"longitude":"2.30587549","latitude":"48.77391276","status":"valid","difficulty":"3","spotType":"3","groundType":"3","itemType":"spot"},
		
		{"id":"3","title":"validSpotTitle3","description":"validSpotDescription3","date":"2003-01-01 09:00:00","submitter":{"id":"3","title":"Banneduser"},"lastEditor":{"id":null,"title":null},"lastEditionDate":null,"dpt":{"id":null,"title":null},"longitude":"2.30687549","latitude":"48.77491276","status":"valid","difficulty":"2","spotType":"2","groundType":"2","itemType":"spot"},
		
		{"id":"2","title":"firstInvalidSpotTitle","description":"firstInvalidSpotDescription","date":"2003-01-01 09:00:00","submitter":{"id":"1","title":"Plainuser"},"lastEditor":{"id":null,"title":null},"lastEditionDate":null,"dpt":{"id":null,"title":null},"longitude":"2.30787549","latitude":"48.77591276","status":"invalid","difficulty":"2","spotType":"2","groundType":"2","itemType":"spot"},
		
		{"id":"1","title":"firstValidSpotTitle","description":"firstValidSpotDescription","date":"2003-01-01 09:00:00","submitter":{"id":"1","title":"Plainuser"},"lastEditor":{"id":null,"title":null},"lastEditionDate":null,"dpt":{"id":null,"title":null},"longitude":"2.30887549","latitude":"48.77691276","status":"valid","difficulty":"2","spotType":"2","groundType":"2","itemType":"spot"},
		
		{"userId":"1","username":"plainuser","date":"2011-01-01 21:23:00","lang":"fr","country":{"id":"1","title":"France"},"city":"toulouse","zip":"31000","gender":"1","level":"2","gear":"pro95","otherSports":"snowboard","rideType":"110","avatar":"\/media\/avatars\/1.jpg","latitude":"48.77591276","longitude":"2.30787549","lastLogin":"2011-08-01 15:55:55","firstName":"prenom","lastName":"nom","site":"http:\/\/www.mountainboard.fr","occupation":"occupation","email":"user1@example.org","birthDate":"1980-07-01","status":"member","itemType":"user"}
	],
	
	validLocationsInRegion:[
		{"id":"5","title":"validSpotTitle5","description":"validSpotDescription5","date":"2003-01-01 09:00:00","submitter":{"id":"5","title":"Editoruser"},"lastEditor":{"id":null,"title":null},"lastEditionDate":null,"dpt":{"id":1,"title":'Ain'},"longitude":"2.30487549","latitude":"48.77291276","status":"valid","difficulty":"2","spotType":"2","groundType":"2","itemType":"spot"},
	],
	
	distanceCloseNoMaxAsGuest: [
		{"id":"5","title":"validSpotTitle5","description":"validSpotDescription5","date":"2003-01-01 09:00:00","submitter":{"id":"5","title":"Editoruser"},"lastEditor":{"id":null,"title":null},"lastEditionDate":null,"dpt":{"id":"1","title":"Ain"},"longitude":"2.30487549","latitude":"48.77291276","status":"valid","difficulty":"2","spotType":"2","groundType":"2","itemType":"spot","distance":33.68},
		
		{"id":"4","title":"validSpotTitle4","description":"validSpotDescription4","date":"2003-01-01 09:00:00","submitter":{"id":"4","title":"Adminuser"},"lastEditor":{"id":null,"title":null},"lastEditionDate":null,"dpt":{"id":null,"title":null},"longitude":"2.30587549","latitude":"48.77391276","status":"valid","difficulty":"3","spotType":"3","groundType":"3","itemType":"spot","distance":33.65},
		
		{"id":"3","title":"validSpotTitle3","description":"validSpotDescription3","date":"2003-01-01 09:00:00","submitter":{"id":"3","title":"Banneduser"},"lastEditor":{"id":null,"title":null},"lastEditionDate":null,"dpt":{"id":null,"title":null},"longitude":"2.30687549","latitude":"48.77491276","status":"valid","difficulty":"2","spotType":"2","groundType":"2","itemType":"spot","distance":33.61},
		
		{"userId":"1","username":"plainuser","date":"2011-01-01 21:23:00","lang":"fr","country":{"id":"1","title":"France"},"city":"toulouse","zip":"31000","gender":"1","level":"2","gear":"pro95","otherSports":"snowboard","rideType":"110","avatar":"\/media\/avatars\/1.jpg","latitude":"48.77591276","longitude":"2.30787549","itemType":"user","distance":33.58},
		
		{"id":"1","title":"firstValidSpotTitle","description":"firstValidSpotDescription","date":"2003-01-01 09:00:00","submitter":{"id":"1","title":"Plainuser"},"lastEditor":{"id":null,"title":null},"lastEditionDate":null,"dpt":{"id":null,"title":null},"longitude":"2.30887549","latitude":"48.77691276","status":"valid","difficulty":"2","spotType":"2","groundType":"2","itemType":"spot","distance":33.55}
	],
	
	distanceFarNoxMaxAsGuest: [
	    // Empty!
	],
	
	distanceFaxMax10000AsGuest: [
 		{"id":"5","title":"validSpotTitle5","description":"validSpotDescription5","date":"2003-01-01 09:00:00","submitter":{"id":"5","title":"Editoruser"},"lastEditor":{"id":null,"title":null},"lastEditionDate":null,"dpt":{"id":"1","title":"Ain"},"longitude":"2.30487549","latitude":"48.77291276","status":"valid","difficulty":"2","spotType":"2","groundType":"2","itemType":"spot","distance":4422.64},
		
		{"id":"4","title":"validSpotTitle4","description":"validSpotDescription4","date":"2003-01-01 09:00:00","submitter":{"id":"4","title":"Adminuser"},"lastEditor":{"id":null,"title":null},"lastEditionDate":null,"dpt":{"id":null,"title":null},"longitude":"2.30587549","latitude":"48.77391276","status":"valid","difficulty":"3","spotType":"3","groundType":"3","itemType":"spot","distance":4422.75},
		
		{"id":"3","title":"validSpotTitle3","description":"validSpotDescription3","date":"2003-01-01 09:00:00","submitter":{"id":"3","title":"Banneduser"},"lastEditor":{"id":null,"title":null},"lastEditionDate":null,"dpt":{"id":null,"title":null},"longitude":"2.30687549","latitude":"48.77491276","status":"valid","difficulty":"2","spotType":"2","groundType":"2","itemType":"spot","distance":4422.86},
		
		{"userId":"1","username":"plainuser","date":"2011-01-01 21:23:00","lang":"fr","country":{"id":"1","title":"France"},"city":"toulouse","zip":"31000","gender":"1","level":"2","gear":"pro95","otherSports":"snowboard","rideType":"110","avatar":"\/media\/avatars\/1.jpg","latitude":"48.77591276","longitude":"2.30787549","itemType":"user","distance":4422.97},
		
		{"id":"1","title":"firstValidSpotTitle","description":"firstValidSpotDescription","date":"2003-01-01 09:00:00","submitter":{"id":"1","title":"Plainuser"},"lastEditor":{"id":null,"title":null},"lastEditionDate":null,"dpt":{"id":null,"title":null},"longitude":"2.30887549","latitude":"48.77691276","status":"valid","difficulty":"2","spotType":"2","groundType":"2","itemType":"spot","distance":4423.09}
	],
};	
