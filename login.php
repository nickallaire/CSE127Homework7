<?php

// The login.php is invoked when the user is either trying to create a new
// account or to login. If it's the former, the NEW parameter will be set.
// To send a user to a different page (after possibly executing some code,
// you can use the statement:
//
//     header('Location: view.php');
//
// This will send the user to view.php. To use this mechanism, the
// statement must be executed before any of the document is output.

// Start session
session_start();

// Error message to be displayed
$message = '';

// True if user exists/valid logic credentials
$found = False;

// True if valid submission data
$valid_data = False;

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

// Form username
$user = '';
if (isset($_POST['USER'])) {
	$user = $_POST['USER'];
}

// Form password
$pass = '';
if (isset($_POST['PASS'])) {
	$pass = $_POST['PASS'];
}

// Check if user is already logged in
if (isset($_SESSION['username'])) {
	header('Location: view.php?user='.$_SESSION['username']);
}

// Check if username and password are not empty
if (strlen($user) > 0 && strlen($pass) > 0) {
	$valid_data = True;
}

if ($valid_data == True && $valid_token == True) {

	// Connect to database
	$db_conn = pg_connect('host=localhost dbname=chattr user=student password=hacktheplanet');

	// Check if database connection is successful
	if (!$db_conn) {
		echo 'Failed to connect to database.';
		exit;
	}

	if(isset($_POST['NEW'])) {
		// Your new user creation code goes here. If the user name
		// already exists, then display an error. Otherwise, create a new
		// user account and send him to view.php.

		// Query all usernames
		$result = pg_query($db_conn, 'SELECT username FROM users');

		// Check if query is successful
		if (!$result) {
			echo 'Query failed.';
			exit;
		}

		// Checks if user already exists
		while ($row = pg_fetch_row($result)) {
			if ($user == $row[0]) {
				$found = True;
			}
		}

		// Display error message if user exists, else create user and start session
		if ($found == True) {
				$message = "User {$user} already exists!";

		} else {
			if (ctype_alnum($user)) {
				$hash = hash('sha256', $user.(filter_var($pass, FILTER_SANITIZE_STRING)));
				$insert = pg_prepare($db_conn, "insert_query","INSERT INTO users(username, password) VALUES ($1, $2)");
				$insert = pg_execute($db_conn, "insert_query", array("$user", "$hash"));
				
        		session_regenerate_id(true);

				$_SESSION['username'] = $user;
				header('Location: view.php?user='.$_SESSION['username']);
			} else {
				$message = 'Username cannot contain special characters, please try again.';
			}
		}
		
	} else {
		// Your user login code goes here. If the user name and password
		// are not correct, then display an error. Otherwise, log in the
		// user and send him to view.php.

		// Query all usernames
		$result = pg_query($db_conn, 'SELECT username, password FROM users');
		
		// Check if query is successful
		if (!$result) {
			echo 'Query failed.';
			exit;
		}

		// Verify username and get password
		while ($row = pg_fetch_row($result)) {
			if ($user == $row[0]) {
				$hash = $row[1];
				break;
			}
		}

		// Verify password matches hashed password
		$passVerify = hash('sha256', $user.(filter_var($pass, FILTER_SANITIZE_STRING)));

		if ($passVerify == $hash) {
			$found = True;
		}

		// If valid data start session, else display error message
		if ($found == True) {
			session_regenerate_id(true);

			$_SESSION['username'] = $user;
			header('Location: view.php?user='.$user);
		} else {
			$message = 'Login Failed!';
		}
	}
} else {
	$message = 'Login Failed! <H3>Missing username or password</H3>'; 	
}
?>

<DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 3.2//EN">
<HEAD Content-Security-Policy: script-src 'self' https://ajax.googleapis.com https://www.google-analytics.com; child-src 'none'; object-src 'none'; upgrade-insecure-requests>
    <TITLE>Chattr</TITLE>
</HEAD>
<BODY BGCOLOR=WHITE>
	<TABLE ALIGN="CENTER">
		<TR><TD>
			<H1>Chattr</H1>
		</TD></TR>
		<TR><TD>
			<H2><?php print $message ?></H2>
			<a href="index.php">Back</a>
		</TD></TR>
	</TABLE>
</BODY>
