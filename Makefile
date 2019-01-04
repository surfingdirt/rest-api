SITE=http://api.ridedb.dev/

cleancache:
#	curl "$(SITE)admin/clear-apc-cache?authCheck=ra45HuiB@&mode=user"
#	curl "$(SITE)admin/clear-apc-cache?authCheck=ra45HuiB@&mode=opcode"
	rm -rf data/cache/api/z
	rm -rf data/cache/api/HTMLPurifier/*
	rm -rf data/cache/api/app/z*
	rm -rf data/cache/test/z*
	rm -rf data/cache/test/app/z*
	rm -rf data/cache/test/HTMLPurifier/*
	rm -f public/media/*.jpg
	rm -f public/media/thumbnails/*.jpg

installdb:
	mysql ridedb_prod -uroot < data/sql/structure.sql
	mysql ridedb_prod -uroot < data/sql/cleanup_and_inserts.sql

installtestdb:
	mysql ridedb_test -uroot < data/sql/structure.sql

full-backup: files-backup sql-backup

files-backup:
	tar czvf ./data/backups/files.tgz application bin library models public test tools Makefile

sql-backup:
	mysqldump --user=ridedb -p --database ridedb_prod > ./data/backups/ridedb_dump.sql

reset-test-fixtures:
	mysql ridedb_test -uroot < data/sql/test_fixtures.sql
	
tests:
	rm -f public/media/*.*
	rm -f public/media/thumbnails/*.*
	for name in sessions #riders media albums comments spots tricks locations countries regions messages checkins notifications ; do \
		make reset-test-fixtures ; \
		nodeunit test/$$name.js ; \
	done
