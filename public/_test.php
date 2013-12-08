<?php
// Include necessary files
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
	@set_time_limit(1000);
	@set_magic_quotes_runtime(0);
include_once '../sys/core/init.inc.php';
// Load the admin object
$obj = new Admin($dbo);
// Generate a salted hash of "admin"
$pass = $obj->testSaltedHash("nihao123!");
echo 'Hash of "nihao123!":<br />'.$pass."<br /><br />";

// Load a hash of the word test and output it
$hash1 = $obj->testSaltedHash("test");
echo "Hash 1 without a salt:<br />", $hash1, "<br /><br />";
// Pause execution for a second to get a different timestamp
sleep(1);
// Load a second hash of the word test
$hash2 = $obj->testSaltedHash("test");
echo "Hash 2 without a salt:<br />", $hash2, "<br /><br />";
// Pause execution for a second to get a different timestamp
sleep(1);
// Rehash the word test with the existing salt
$hash3 = $obj->testSaltedHash("test", $hash2);
echo "Hash 3 with the salt from hash 2:<br />", $hash3;

//echo $_SESSION['token'];
?>