module.exports = {
	fromWriterToPlainUser: {
		"id": '1', "content": "message6To1Content","date": "2002-02-18 10:00:00", "submitter": {id: '6', title: 'Writeruser'}, "lastEditor": { id: null, title: null }, "lastEditionDate": null,"toUser": { id: '1', title: 'Plainuser' },"read": '1',"status": "valid"
			},
	fromOtherToPlainUserValid: {"id": '2', "content": "message7To1Content","date": "2002-02-18 11:00:00", "submitter": { id: '7', title: 'Otheruser' }, "lastEditor": { id: null, title: null }, "lastEditionDate": null,"toUser": { id: '1', title: 'Plainuser' },"read": '0',"status": "valid"},
	fromOtherToPlainUserInvalid: {"id": '3', "content": "message7To1ContentInvalid","date": "2002-02-18 12:00:00", "submitter": { id: '7', title: 'Otheruser' }, "lastEditor": { id: null, title: null }, "lastEditionDate": null,"toUser": { id: '1', title: 'Plainuser' },"read": '0',"status": "invalid"},
	createdByMe: {"id": '6', "content": "createdMessage1To7Content","date": "2011-08-01 15:55:55", "submitter": { id: '1', title: 'Plainuser' }, "lastEditor": { id: null, title: null }, "lastEditionDate": null,"toUser": { id: '7', title: 'Otheruser' },"read": '0',"status": "valid"}
};