var france = {
	"id":"1","title":"France","simpleTitle":"france","lang":"fr","status":"valid","bounds":["42.1331639","-5.982052","50.0380022","10.40955"]	
}, spain = {
	"id":"2","title":"Spain","simpleTitle":"spain","lang":"en","status":"valid","bounds":["36.073","-10.024","43.644","3.898"]	
}, newZealand = {
	"id":"3","title":"New Zealand","simpleTitle":"new-zealand","lang":"en","status":"invalid","bounds":["-45.0769319","166.6901726","-36.4428471","-176.9182306"]
}, japan = {
	"id":"4","title":"Japan","simpleTitle":"japan","lang":"en","status":"valid","bounds":["30.4661367","128.4613221","45.1679299","147.0445259"]	
};

module.exports = {	
	france : france,
	spain: spain,
	newZealand: newZealand,
	japan: japan,
	
	allValid: [
		france,
		spain,
		japan
	],
	
	all: [
		france,
		spain,
		newZealand,
		japan
	]
};