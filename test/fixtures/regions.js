var	ain = {"id":"1","title":"Ain","simpleTitle":"ain","prefix":"de l'","status":"valid","country":{id:'1', title: "France"},"bounds":["1.2","2.3","3.4","4.5"],"code":"a"},
aisne = {"id":"2","title":"Aisne","simpleTitle":"aisne","prefix":"de l'","status":"valid","country":{id:'2', title: "Spain"},"bounds":["2.3","3.4","4.5","5.6"],"code":"b"},
allier = {"id":"3","title":"Allier","simpleTitle":"allier","prefix":"de l'","status":"invalid","country":{id:'1', title: "France"},"bounds":["3.4","4.5","5.6","6.7"],"code":"c"};

module.exports = {	
	ain: ain,
	aisne: aisne,
	allier: allier,
	allValid: [ain, aisne],
	all: [ain, aisne, allier],
	validFrance: [ain],
	allFrance: [ain, allier],
	validSpain: [aisne]
};