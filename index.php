<?php
	session_start();

	if (isset($_SESSION['username'])) {

		header('Location: view.php?user='.$_SESSION['username']);
	}
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
<TR><TD>
<FORM ACTION="login.php" METHOD="POST">
<TABLE CELLPADDING=5>
<TR><TD>User name:</TD><TD><INPUT TYPE="TEXT" NAME="USER"></TD></TR>
<TR><TD>Password:</TD><TD><INPUT TYPE="PASSWORD" NAME="PASS"></TD></TR>
<TR><TD COLSPAN=2><INPUT TYPE="CHECKBOX" NAME="NEW" VALUE="YES">&nbsp;New user&nbsp;
				<INPUT TYPE="HIDDEN" NAME="TOKEN" VALUE=<?php echo $token ?>>
<INPUT TYPE="SUBMIT" VALUE="Submit"></TD></TR>
</TABLE>
</FORM>
</TD></TR>
</TABLE>
</BODY>
