<?php
 // $Id$
 //
 // Authors:
 //      Jeff Buchbinder <jeff@freemedsoftware.org>
 //
 // FreeMED Electronic Medical Record and Practice Management System
 // Copyright (C) 1999-2006 FreeMED Software Foundation
 //
 // This program is free software; you can redistribute it and/or modify
 // it under the terms of the GNU General Public License as published by
 // the Free Software Foundation; either version 2 of the License, or
 // (at your option) any later version.
 //
 // This program is distributed in the hope that it will be useful,
 // but WITHOUT ANY WARRANTY; without even the implied warranty of
 // MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 // GNU General Public License for more details.
 //
 // You should have received a copy of the GNU General Public License
 // along with this program; if not, write to the Free Software
 // Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.

define ('INSTALLATION', "Stock FreeMED Install"); // installation name
define ('DB_HOST', "localhost"); // database (SQL) host location
define ('DB_NAME', "freemed"); // database name
define ('DB_USER', "root"); // SQL server username
define ('DB_PASSWORD', "");		// SQL server password
define ('PATID_PREFIX', "PAT"); // used to generate internal practice ID
define ('UI', "dojo");	// set default template
define ('HOST', 'localhost'); // host name for this system
define ('BASE_URL', '/freemed'); // offset (i.e. http://here/package)
define ('SESSION_PROTECTION', true); // strong session protection?
define ('RECORD_LOCK_TIMEOUT', 180); // record lock timeout in seconds
define ('DEFAULT_LANGUAGE', "en_US"); // default language
define ('INIT_ADDR',"127.0.0.1"); // this is the only IP address that can run the init wizard...
    // Logging settings	
    // Reasonable defaults!
define ('LOG_HIPAA', true); // This turns on all the logging required by HIPAA
		           // Which is alot, but this should be the default
			   // If you want to configure other log levels you 
			   // can do so here

    // Useful Tweaks			   
define ('LOG_LOGIN', true); // This will force logging of login attempts

   // Powerful and dangerous debugging
define ('LOG_MD5ATTEMPTS', false); // This is a dangerous tool to test authentication
				 // DO NOT turn this on for a production system
				// this will put crackable passwords in the logs
define ('LOG_SQL', false); 	// This logs the SQL Statments that have been entered
				// This is usefull for debugging why something is messing 
				// the database!!
define ('LOG_ERRORS', true);	// This logs major errors, this should be on by default!!

     // Debug Logging DONT CHANGE LEVEL FROM 100 IN A PRODUCTION SYSTEM!!
			   // This allows developers to have a pretty accurate 
			   // idea of whats gone....
			   // I have impleneted log levels


// Only define on of these
//define ('LOG_LEVEL',0);    	// Log level zero logs everything!!(avoid)unless you are Fred or Jeff
//define ('LOG_LEVEL',1);	   // Log level one logs everything reasonable
//define ('LOG_LEVEL',2);	   // Log all class traversals 
//define ('LOG_LEVEL',3);    	   // Log all page traversals (except for certain initiations)
//define ('LOG_LEVEL',4);	   // Log all database querys, this accelerates database debugging!!
//define ('LOG_LEVEL',99);	   // Typical logging


//define ('LOG_LEVEL',100);	   // Logs nothing except what is explicitly itemized (like LOG_HIPPA)
				   // this should be the default when not coding...
		
// Set log level
define('LOG_LEVEL', 100);

// Archive settings
define ('FSF_USERNAME', "demo");
define ('FSF_PASSWORD', "demo");

?>
