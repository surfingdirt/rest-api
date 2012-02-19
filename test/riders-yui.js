var http = require('http'),
	client = http.createClient(80),
	YUITest = require('yuitest'),
	host = 'test.redesign-zend.fr';

YUITest.TestRunner.add(new YUITest.TestCase({
	name: "Riders",
	
	setUp: function(){
	
	},
	
	tearDown: function(){
		
	},
	
	testMember : function (){
    	var request = client.request(
        	'GET',
        	'/riders',
        	{'host': host}
        ),	responseData = '',
        	test = this;
    	
        request.end();
        
        request.on('response', function (response) {
          response.setEncoding('utf8');
          response.on('data', function (chunk) {
        	  responseData += chunk;
          });
          
          response.on('end', function(){
        	  test.resume(function(){
        		  YUITest.Assert.areEqual(responseData, '[{"userId":"1","username":"plainuser","email":"user1@example.org","status":"member","lang":"fr","firstName":"prenom","lastName":"nom","birthDate":"1980-07-01","country":"france","city":"toulouse","zip":"31000","gender":"1","level":"2","site":"http:\\/\\/www.mountainboard.fr","occupation":"occupation","gear":"pro95","otherSports":"snowboard","rideType":"110","avatar":"\\/media\\/avatars\\/1.jpg"},{"userId":"2","username":"adminuser","email":"admin1@example.org","status":"admin","lang":"en","firstName":"first","lastName":"last","birthDate":"1950-01-08","country":"usa","city":"oakland","zip":"94610","gender":"2","level":"3","site":"http:\\/\\/www.mntnbrd.rs","occupation":"dev","gear":"grasshopper","otherSports":null,"rideType":null,"avatar":null}]');
        	  });
          });	
        });
        
        this.wait(200);
	}
    
}));