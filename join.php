<?php
require "database.php";

require "customers.class.php";
$cust = new Customer();
$cust->join_record();

?>

