<?php
if($_SERVER['SERVER_ADDR']!='127.0.0.1'){    
    $host = 'mysql2.000webhost.com';
    $username = 'a8621381_mojo';
    $password = 'Devoo123$$$';
    $database = 'a8621381_mojo'; 
    $mysqli = new mysqli($host, $username, $password, $database);
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL Live: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    //echo $mysqli->host_info . "\n";
}else{
    $mysqli = new mysqli("localhost", "root", "", "googlemap_soil");
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    //echo $mysqli->host_info . "\n";
    
    $mysqli = new mysqli("127.0.0.1", "root", "", "googlemap_soil", 3306);
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    
    //echo $mysqli->host_info . "\n";
}
?>
