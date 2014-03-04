API_CREDENTIALS =  GoyY7hI2S5igDS4pG2Vdyg==:e02C96sqR5mvUoQXkCC2Gg==
DB_QUERY_URL = https://api.datalanche.com/my_database/query 
QUERY_URL = https://api.datalanche.com/query 
CURL_OPTS_DROP_SCHEMA = -X POST -u "$(API_CREDENTIALS)" -H "Content-Type: application/json" -d '{ "drop_schema": "my_new_schema", "cascade": true }'
CURL_OPTS_ALTER_DATABASE = -X POST -u "$(API_CREDENTIALS)" -H "Content-Type: application/json" -d '{ "alter_database": "my_new_database", "rename_to": "my_database"}'

#host api.datalanche.com
all: target

target:  test

test: pre_test test_schema test_table test_selects test_index test_alter_schema test_database # run examples test
	
test_schema: pre_test
	# schema examples
	# create a schema
	php ./examples/schema/create-schema.php

	# describe the schema
	php ./examples/schema/describe-schema.php

	# show the created schema
	php ./examples/schema/show-schemas.php

test_table: test_schema
	# table examples
	# create a table
	php ./examples/table/create-table.php

	# describe the table
	php ./examples/table/describe-table.php

	# show the tables in my_database, should return 2 tables
	php ./examples/table/show-tables.php

	# insert data into my_schema.my_table
	php ./examples/table/insert.php

	# update my_schema.my_table
	php ./examples/table/update.php

	# delete my_schema.my_table
	php ./examples/table/delete.php

	# alther the table name and the table descriptions
	php ./examples/table/alter-table.php

	# create table again after altering table.
	php ./examples/table/create-table.php

	# show table to make sure the new table is created before drop
	php ./examples/table/show-tables.php

	# drop my_schema.my_table
	php ./examples/table/drop-table.php

	# show table to make sure the new table is created before drop
	php ./examples/table/show-tables.php

test_selects: test_schema
	# create sample tables for selects
	sh ./test/create_sample_tables

	# testing select example
	php ./examples/table/select-all.php

	# testing select_search example
	php ./examples/table/select-search.php

	# testing select_join example
	php ./examples/table/select-join.php

test_index: test_selects
	# create index on my_schema.my_table
	php ./examples/index/create-index.php

	# show the tables with index
	php ./examples/table/describe-table.php

	# drop index on my_schema.my_table
	php ./examples/index/drop-index.php

	# show the tables with dropped index
	php ./examples/table/describe-table.php

	# create index on my_schema.my_table again for testing alterring index
	php ./examples/index/create-index.php

	# show the tables with index
	php ./examples/table/describe-table.php

	# alter index on my_schema.my_table
	php ./examples/index/alter-index.php

	# show the tables with alterred index
	php ./examples/table/describe-table.php

test_alter_schema: test_schema
	#echo drop the schema: my_new_schema before testing alter_schema example
	curl $(DB_QUERY_URL) $(CURL_OPTS_DROP_SCHEMA)

	# alter my_schema to my_new_schema
	php ./examples/schema/alter-schema.php

	# show schema which should show my_new_schema
	php ./examples/schema/show-schemas.php

	#create the schema again to test drop schema.
	php ./examples/schema/create-schema.php

	# show schema which should show my_schema and my_new_schema
	php ./examples/schema/show-schemas.php

	# drop my_schema
	php ./examples/schema/drop-schema.php

	# show schema which should show new_schema only
	php ./examples/schema/show-schemas.php

test_database:
	# database examples
	# describe the database
	php ./examples/database/describe-database.php

	# show the database
	php ./examples/database/show-databases.php

	# alther the database
	php ./examples/database/alter-database.php

	# show the database after altered
	php ./examples/database/show-databases.php

	# alter the my_new_database to my_database
	curl $(QUERY_URL) $(CURL_OPTS_ALTER_DATABASE)

	# show the database to check if the database is altered back to my_database
	php ./examples/database/show-databases.php

pre_test: # setup the production server
	sh ./test/pre

.PHONY: test test_schema test_tables test_database test_
