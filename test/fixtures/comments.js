module.exports = {	
	firstComment: {
		"id": '1',
		"content": "myFirstComment",
		"date": '2011-01-01 21:23:00',
		"submitter": { id: '1', title: 'Plainuser' },
		"lastEditor": { id: null, title: null },
		"lastEditionDate": null,
		"status": 'valid',
		"tone": '2',
	},
	
	firstInvalidComment: {
		"id": '2',
		"content": "mySecondComment",
		"date": '2011-01-01 21:23:01',
		"submitter": { id: '1', title: 'Plainuser' },
		"lastEditor": { id: null, title: null },
		"lastEditionDate": null,
		"status": 'invalid',
		"tone": '3',
	},
	
	list: {
		asGuest: [
			// get 1,3,4
			{
				"id": '1',
				"content": "myFirstComment",
				"date": '2011-01-01 21:23:00',
				"submitter": { id: '1', title: 'Plainuser' },
				"lastEditor": { id: null, title: null },
				"lastEditionDate": null,
				"status": 'valid',
				"tone": '2',
			}, {
				"id": '3',
				"content": "comment3Title",
				"date": '2011-01-01 21:23:02',
				"submitter": { id: '7', title: 'Otheruser' },
				"lastEditor": { id: null, title: null },
				"lastEditionDate": null,
				"status": 'valid',
				"tone": '2',
			}, {
				"id": '4',
				"content": "comment4Title",
				"date": '2011-01-01 21:23:03',
				"submitter": { id: '1', title: 'Plainuser' },
				"lastEditor": { id: null, title: null },
				"lastEditionDate": null,
				"status": 'valid',
				"tone": '2',
			}
		],
		allComments: [
			// get 1,2,3,4
			{
				"id": '1',
				"content": "myFirstComment",
				"date": '2011-01-01 21:23:00',
				"submitter": { id: '1', title: 'Plainuser' },
				"lastEditor": { id: null, title: null },
				"lastEditionDate": null,
				"status": 'valid',
				"tone": '2',
			}, {
				"id": '2',
				"content": "mySecondComment",
				"date": '2011-01-01 21:23:01',
				"submitter": { id: '1', title: 'Plainuser' },
				"lastEditor": { id: null, title: null },
				"lastEditionDate": null,
				"status": 'invalid',
				"tone": '3',
			}, {
				"id": '3',
				"content": "comment3Title",
				"date": '2011-01-01 21:23:02',
				"submitter": { id: '7', title: 'Otheruser' },
				"lastEditor": { id: null, title: null },
				"lastEditionDate": null,
				"status": 'valid',
				"tone": '2',
			}, {
				"id": '4',
				"content": "comment4Title",
				"date": '2011-01-01 21:23:03',
				"submitter": { id: '1', title: 'Plainuser' },
				"lastEditor": { id: null, title: null },
				"lastEditionDate": null,
				"status": 'valid',
				"tone": '2',
			}
		]
	},
	
	validPost: {
		"id": '5',
		"content": "validContent",
		"date": '2011-08-01 15:55:55',
		"submitter": { id: '1', title: 'Plainuser' },
		"lastEditor": { id: null, title: null },
		"lastEditionDate": null,
		"status": 'valid',
		"tone": '3',
	},
	
	updatedComment: {
		"id": '5',
		"content": "updatedcontent",
		"date": '2011-08-01 15:55:55',
		"submitter": { id: '1', title: 'Plainuser' },
		"lastEditor": { id: '1', title: 'Plainuser' },
		"lastEditionDate": '2011-08-01 15:55:55',
		"status": 'valid',
		"tone": '3',
	},
	
	
};