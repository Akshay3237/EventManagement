<?php
include('../config/db.php');
include('dbcreation.php');

createDatabase($conn, $dbname);
$conn->select_db($dbname);
createTables($conn);

?>