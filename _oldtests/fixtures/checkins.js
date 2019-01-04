var checkin1 = {"id":"1","submitter":{"id":"1","title":"Plainuser"},"spot":{"id":"1","title":"firstValidSpotTitle"},"checkinDate":"2011-08-02 09:00:00","checkinDuration":"14400","date":"2011-07-01 09:00:00","lastEditor":{"id":null,"title":null},"lastEditionDate":null,"status":"valid","country":{"id":"1","title":"France"},"region":{"id":null,"title":null},"latitude":"48.77691276","longitude":"2.30887549"},
	checkin3 = {"id":"3","submitter":{"id":"5","title":"Editoruser"},"spot":{"id":"3","title":"validSpotTitle3"},"checkinDate":"2011-08-03 11:00:00","checkinDuration":"14400","date":"2011-07-01 09:00:00","lastEditor":{"id":null,"title":null},"lastEditionDate":null,"status":"valid","country":{"id":"1","title":"France"},"region":{"id":null,"title":null},"latitude":"48.77491276","longitude":"2.30687549"},
	checkin4 = {"id":"4","submitter":{"id":"4","title":"Adminuser"},"spot":{"id":"5","title":"validSpotTitle5"},"checkinDate":"2011-08-03 11:00:00","checkinDuration":"14400","date":"2011-07-01 09:00:00","lastEditor":{"id":null,"title":null},"lastEditionDate":null,"status":"valid","country":{"id":"1","title":"France"},"region":{"id":"1","title":"Ain"},"latitude":"48.77291276","longitude":"2.30487549"},
	checkin5 = {"id":"5","submitter":{"id":"4","title":"Adminuser"},"spot":{"id":"5","title":"validSpotTitle5"},"checkinDate":"2012-01-01 10:00:00","checkinDuration":"14400","date":"2012-01-01 09:00:00","lastEditor":{"id":null,"title":null},"lastEditionDate":null,"status":"valid","country":{"id":"1","title":"France"},"region":{"id":"1","title":"Ain"},"latitude":"48.77291276","longitude":"2.30487549"}


module.exports = {	
	allCheckins: {
		onGivenDate: [checkin1],
	    forGivenSpotOnGivenDate: [checkin4],
	    forGivenCountryOnGivenDate: [checkin3, checkin4],
  	    forGivenRegionOnGivenDate: [checkin4],
   	    forGivenRider: [checkin3],
	},
	currentCheckins: {
		forGivenRider: [checkin5],
		atGivenSpot: [checkin5]
	},
	closestCheckins: {
		aroundGivenSpot: [checkin5],
		aroundGivenLocation: [checkin5]
	},
	postCheckins: {
		minimal: {"id":"6","submitter":{"id":"1","title":"Plainuser"},"spot":{"id":"1","title":"firstValidSpotTitle"},"checkinDate":"2012-02-01 10:00:00","checkinDuration":"14400","date":"2012-02-01 10:00:00","lastEditor":{"id":null,"title":null},"lastEditionDate":null,"status":"valid","country":{"id":"1","title":"France"},"region":{"id":null,"title":null},"latitude":"48.77691276","longitude":"2.30887549"},
		withCheckinDate: {"id":"7","submitter":{"id":"1","title":"Plainuser"},"spot":{"id":"1","title":"firstValidSpotTitle"},"checkinDate":"2012-03-01 10:00:00","checkinDuration":"14400","date":"2012-02-01 10:00:00","lastEditor":{"id":null,"title":null},"lastEditionDate":null,"status":"valid","country":{"id":"1","title":"France"},"region":{"id":null,"title":null},"latitude":"48.77691276","longitude":"2.30887549"},
		withDuration: {"id":"8","submitter":{"id":"1","title":"Plainuser"},"spot":{"id":"1","title":"firstValidSpotTitle"},"checkinDate":"2012-03-01 10:00:00","checkinDuration":"3600","date":"2012-02-01 10:00:00","lastEditor":{"id":null,"title":null},"lastEditionDate":null,"status":"valid","country":{"id":"1","title":"France"},"region":{"id":null,"title":null},"latitude":"48.77691276","longitude":"2.30887549"},
	},
	putCheckins: {
		updated: {"id":"8","submitter":{"id":"1","title":"Plainuser"},"spot":{"id":"1","title":"firstValidSpotTitle"},"checkinDate":"2012-03-01 10:00:00","checkinDuration":"1800","date":"2012-02-01 10:00:00","lastEditor":{"id":"1","title":"Plainuser"},"lastEditionDate":"2012-02-28 14:00:00","status":"valid","country":{"id":"1","title":"France"},"region":{"id":null,"title":null},"latitude":"48.77691276","longitude":"2.30887549"},
	},
	deleteCheckins: {},
};