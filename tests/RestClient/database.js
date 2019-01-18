import mysql from 'promise-mysql';
import fs from 'fs'
import path  from 'path';

export const cleanupTestDatabase = async () => {
  const runSQL = async (filename, connection) => {
    const queryFile = path.normalize(`${__dirname}/../../data/sql/${filename}`);
    try {
      const queries = fs.readFileSync(queryFile).toString();
      return connection.query(queries);
    } catch (e) {
      console.log(e);
    }
  }

  // TODO: read application.ini in the test section to get this info
  const connection = await mysql.createConnection({
    host     : 'localhost',
    user     : 'root',
    password : '',
    database : 'ridedb_test',
    multipleStatements: true,
  });
  await runSQL('test_fixtures.sql', connection);
  connection.end();
};

