<?php 
    session_start();
    $token = md5(uniqid(microtime(), True));
    $_SESSION['TOKEN'] = $token;
?>

<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 3.2//EN">
<HEAD Content-Security-Policy: script-src 'self' https://ajax.googleapis.com https://www.google-analytics.com; child-src 'none'; object-src 'none'; upgrade-insecure-requests>
    <TITLE>Chattr</TITLE>
</HEAD>
<BODY BGCOLOR=WHITE>
<TABLE ALIGN="CENTER">
<TR><TD>
<H1>Chattr</H1>
</TD></TR>

<?php

// Error message to be displayed
$message = "";

// True if user exists
$found = False;

// Assign $user if url has parameter user
if (isset($_GET['user'])) {
    $temp = $_GET['user'];
    $user = htmlspecialchars($temp, ENT_QUOTES);
}

// Assign $username if session is set
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
}

// Connect to database
$db_conn = pg_connect('host=localhost dbname=chattr user=student password=hacktheplanet');

// Check if $user is in the database
$result = pg_query($db_conn, 'SELECT username FROM users');
if (!$result) {
	echo 'Query failed.';
	exit;
}
while ($row = pg_fetch_row($result)) {
    if ($user == $row[0]) {
        $found = True;
        break;
    }
}

// $user is not contained in database
if ($found == False && isset($_GET['user'])) {
    $message = "User {$user} does not exist!";
}

// Check if no user signed in and no user var in url
if (!isset($_SESSION['username']) && !$_GET['user']) {
    $message = 'No user signed in or entered in url.';
}

// The following <TR> element should only appear if the user is
// logged in and viewing his own entry.
if (isset($_SESSION['username']) && ($user == $username || !$_GET['user'])) {
?>
    <TR><TD>
    <FORM ACTION="post.php" METHOD="POST">
    <TABLE CELLPADDING=5>
    <TR><TD id="message">Message:</TD><TD><INPUT TYPE="TEXT" NAME="TEXT">
    <INPUT TYPE="HIDDEN" NAME="TOKEN" VALUE=<?php echo $token ?>>
    <INPUT TYPE="SUBMIT" VALUE="Submit"></TD></TR>
    </TABLE>
    </FORM>
    </TD></TR>  
<?php
}

// The following <TR> element should always appear if the user
// exists.
if ($found == True || (isset($_SESSION['username']) && !$_GET['user'])) {
?>
    <TR><TD>
    <TABLE CELLPADDING=5>
    <TR><TH>When</TH><TH>Who</TH><TH>What</TH></TR>
	<?php

        // User name displayed in chat log
        $display_user = "";

        if (isset($_GET['user'])) {
                $display_user = $user;
            } else {
                $display_user = $username;
            }

        // Query all messages from the $display_user
        $message_query = pg_prepare($db_conn, "message_query", "SELECT m.date_sent, m.message_content FROM users u, messages m WHERE u.username=m.user_id and u.username=$1");
        $message_query = pg_execute($db_conn, "message_query", array("$display_user"));
        while ($content = pg_fetch_row($message_query)) {
    ?>
            <TR>
                <TD><?php echo $content[0] ?></TD>
                <TD><?php echo $display_user ?></TD>
                <TD><?php echo filter_var($content[1], FILTER_SANITIZE_STRING) ?></TD>
            </TR>
    <?php   
        }
    ?>
    </TABLE>
    </TD></TR>
<?php
}

// The following <TR> element should be displayed if the user
// name does not exist. Add code to display user name.
if ($found == False) {
?>
    <TR><TD>
    <H2><?php print $message ?></H2>
    </TD></TR>
<?php
}

// The following <TR> element should only be shown if the user
// is logged in.
if (isset($_SESSION['username'])) {
?>

<TR><TD><A HREF="logout.php">Logout</A></TR></TD>

<?php
// Done!
}
?>

</TABLE>
</BODY>

