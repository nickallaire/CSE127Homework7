<?php
// Add your posting code here.
// 
// To send a user to a different page (after possibly executing some code,
// you can use the statement:
//
//     header('Location: view.php');
//
// This will send the user tp view.php. To use this mechanism, the
// statement must be executed before any of the document is output.

// Start session
session_start();

// Verify form token
$valid_token = False;
if (isset($_SESSION['TOKEN'])) {
	if (isset($_POST['TOKEN'])) {
		if ($_SESSION['TOKEN'] == $_POST['TOKEN']) {
			$valid_token = True;
			$_SESSION['TOKEN'] = NULL;
		}
	}
}

// Check if form token valid
if ($valid_token == True) {

	// Check if form submission 'TEXT' is set
	if(isset($_POST['TEXT']) && strlen($_POST['TEXT']) > 0) {

		// Get session username
		$username = $_SESSION['username'];

		// Assign submitted message
		$content = filter_var($_POST['TEXT'], FILTER_SANITIZE_STRING);

		// Set date timezone
		date_default_timezone_set('America/Los_Angeles');

		// Create date format
		$date = date('m/d/Y H:i:s');

		// Connect to database
		$db_conn = pg_connect('host=localhost dbname=chattr user=student password=hacktheplanet');

		// Insert message to database
		$insert_message = pg_prepare($db_conn, "insert_query", 'INSERT INTO messages(user_id, date_sent, message_content) VALUES ($1, $2, $3)');	
		$insert_message = pg_execute($db_conn, "insert_query", array("$username", "$date", "$content"));
	}
}

// Display username in URL if active session
if (isset($_SESSION['username'])) {
	header('Location: view.php?user='.$_SESSION['username']);
} else {
	header('Location: view.php');
}
?>
