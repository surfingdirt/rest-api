const mysql = require('promise-mysql');
const fs = require('fs');
const path = require('path');

const runSQL = (filename, connection) => {
  const queryFile = path.normalize(`${__dirname}/../data/sql/${filename}`);
  const queries = fs.readFileSync(queryFile).toString();
  return connection.query(queries);
}

let connection;
// TODO: read application.ini in the test section to get this info
mysql.createConnection({
  host     : 'localhost',
  user     : 'root',
  password : '',
  database : 'ridedb_test',
  multipleStatements: true,
}).then((conn) => {
  connection = conn;
  return runSQL('cleanup_and_inserts.sql', connection);
}).then((db) => {
  runSQL('test_fixtures.sql', connection);
  connection.end();
}).catch((e) => {
  console.log('It didn\'t work', e);
});
