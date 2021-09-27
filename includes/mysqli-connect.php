<?php

// SQL connection
if( defined('DBSERVER') && defined('DBUSER') && defined('DBPASSWORD') && defined('DBNAME') ) {
    $connection = mysqli_connect(DBSERVER, DBUSER, DBPASSWORD, DBNAME);

    if ($connection == false){
        $dbgmsg = '';
        if (SQLDEBUG == true) {
            $dbgmsg = 'Following MySQL error was found: '.mysqli_connect_error();
        }
        die ('DB connection failed. '.$dbgmsg);
    }
} else {
    die ('Configuration file not found or failed login');
}

?>