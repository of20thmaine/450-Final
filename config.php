<?php
/*
 * Provides the connection interface between database server and php interpreter on main server.
 */
	$DATABASE_HOST = 'localhost';
	$DATABASE_USER = 'root';
	$DATABASE_PASS = '';
	$DATABASE_NAME = 'agtodi_db';

	$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

	if ( mysqli_connect_errno() ) {
		die ('Our apologies, we experienced a connection error.');
	}
