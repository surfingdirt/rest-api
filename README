A rest API to a database to store ride locations, photos, videos and other things

Required
Apache 2
PHP 5.3
MySQL 5.x
For testing: node.js and the following modules: nodeunit

Install
Create a ridedb_prod (and ridedb_test if you're going to run the functional tests) MySQL databases on localhost
Run make installdb from the application main directory
Also run make installtestdb if you're going to run the functional tests
For these last two steps, the Makefile assumes you're going to do it with the root MySQL user, without a password (BAD practice!). Edit if needed.
Run the script in ./bin/setup.sh to set write permissions on some folders

Copy application/configs/application.ini.template to application/configs/application.ini and edit it: 
- find the database section and update the constants there to reflect your MySQL setup.
 - change constants.AUTHCHECK to a secret password. This is used for triggering cache clearing remotely.
 - update the email and contact section
 - update the domain name constants to match your desired domain name
 Edit test/constants/site.js to match the testing domain you chose
 Apache must serve the following header (see httpd.conf.template):
 	Access-Control-Allow-Origin: *
 
 
 Give PHP/Apache write access to:
 - data/uploads
 - public/media
 - public/media/thumbnails
 - data/cache/api
 - data/cache/api/app
 - data/cache/api/HTMLPurifier
 - data/rawUploads
 