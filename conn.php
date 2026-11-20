<?php

//error_reporting(E_ALL ^ E_DEPRECATED);
	//CONNECTING HOST
//mysql_connect("localhost","root","");
//CONNECTING DATABASE
//mysql_select_db("online_enroll");

error_reporting(E_ALL);

$conn = mysqli_connect("localhost", "root", "", "online_enroll");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


?>