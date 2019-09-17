import mysql from 'promise-mysql';
import fs from 'fs'
import path  from 'path';

export const cleanupTestDatabase = async () => {
  const runSQL = async (filename, connection) => {
    const queryFile = path.normalize(`${__dirname}/../../data/sql/${filename}`);
    const queries = fs.readFileSync(queryFile).toString();
    return connection.query(queries);
  }

  // TODO: read application.ini in the test section to get this info
  const connection = await mysql.createConnection({
    host     : 'localhost',
    user     : 'root',
    password : '',
    database : 'ridedb_test',
    multipleStatements: true,
  });
  try {
    await runSQL('structure.sql', connection);
    await runSQL('test_fixtures.sql', connection);
  } catch (e) {
    console.error('Failed to clear DB', e);
    throw e;
  }
  connection.end();
};

