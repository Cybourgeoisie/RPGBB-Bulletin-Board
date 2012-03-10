<?php 
/******************************************
Scrollio V.a.c.0.9
*******************************************
Copyright 2008, Richard Benjamin Heidorn
e-mail: rbenh@washington.edu
website: http://www.scrollio.com
/******************************************
Scrollio is free software. It can be modified and redistributed 
under the terms of the GNU General Public License. Under no
conditions can Scrollio, or any modification of it, be sold or
used proprietarily or for commercial benefit. You should have 
received a copy of the GNU General Public License along with 
Scrollio, /license.txt. If not, see http://www.gnu.org/licenses/
/******************************************
JQuery is also dually released under the GNU GPL and MIT licenses. 
Scrollio respects all copyrights and properties of jQuery.
JQuery license (http://dev.jquery.com/browser/trunk/jquery/GPL-LICENSE.txt)
******************************************/

// Check to make sure that the config file isn't already configured
if ($fh = fopen('./config.inc.php', 'r+')):
	
	if (($filesize = filesize('./config.inc.php')) == 0):
		$filesize = 1;
	endif;
	
	$contents = fread($fh, $filesize);
	if (preg_match('/\$db/', $contents)):
		$written = true;
	endif;
	
	fclose($fh);
endif;

// If the file doesn't have anything in it, then run
if (!$written):

	$check = '';
	$finish = false;

	if (isset($_POST['db_install_submit'])):
		// Check to ensure installation should go on
		$check = true;
		
		if (($host = trim($_POST['host'])) == ''):
			$check = 'You must provide a host name';
		elseif (($name = trim($_POST['name'])) == ''):
			$check = 'You must provide a database name';
		elseif (($user = trim($_POST['user'])) == ''):
			$check = 'You must provide a database user name';
		//elseif (($pass = trim($_POST['pass'])) == ''):
		//	$check = 'You must provide a database password';
		elseif (($username = trim($_POST['username'])) == ''):
			$check = 'You must provide a forum user name';
		elseif (($password = $_POST['password']) == ''):
			$check = 'You must provide a password for your forum user name';
		elseif (($email = trim($_POST['email'])) == ''):
			$check = 'You must provide a email address';
		elseif (!preg_match("/^[a-zA-Z0-9\_\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z]{2,4}$/i", $email)):
			$check = 'You must provide a valid email address';
		endif;
		
		// If everything is peachy keen, continue
		if ($check === true):
			// Check the DB connection
			$db_conn = @mysqli_connect($host, $user, $pass, $name);
			if ($db_conn):
				print 'You are connected. <br />';
				
				// Using the opened file from earlier, write the db connection information.
				$db = '';
				$db .= '<?php' . "\n";
				$db .=  '$db = array(' . "\n";
				$db .= '\'type\' => \'mysql5\',' . "\n";
				$db .= '\'host\' => \'' . $host . '\',' . "\n";
				$db .= '\'name\' => \'' . $name . '\',' . "\n";
				$db .= '\'user\' => \'' . $user . '\',' . "\n";
				$db .= '\'pass\' => \'' . $pass . '\',' . "\n";
				$db .= '\'prefix\' => \'' . $prefix . '\'' . "\n";
				$db .= ');' . "\n\n";
				$db .= 'define(DB_PREFIX, $db[prefix]);' . "\n";
				$db .= 'define(PATH_TO_FILES, \'./\');' . "\n"  . '?>';
				
				// Copy the information to the file
				if ($fh = fopen('./config.inc.php', 'w')):
					if (fwrite ($fh, $db)):	
						// Grab the query data
						if ($fq = fopen('./install/mysql_5_create_scrollio_db.txt', 'r')):
							$query = fread($fq, filesize('./install/mysql_5_create_scrollio_db.txt'));
						else:
							die ('The SQL file could not be opened');
						endif;
						
						// Further check and prep username, password, and email
						if (get_magic_quotes_gpc()):
							$username = stripslashes($username);
						endif;
		
						// Convert special characters and mysql injection vars
						$username = mysqli_real_escape_string($db_conn, $username);
						$username = htmlentities($username, ENT_QUOTES, 'UTF-8');
						
						// Encrypt password
						$password = sha1($password);
						
						$query = preg_replace('/DBPREFIX/', $prefix, $query);
						$query = preg_replace('/DBNAME/', $name, $query);
						$query = preg_replace('/USERNAMEHERE/', $username, $query);
						$query = preg_replace('/PASSWORDHERE/', $password, $query);
						$query = preg_replace('/EMAILHERE/', $email, $query);
						
						// Install the DB and such
						if (mysqli_multi_query($db_conn, $query)):
 						   do {
 					       /* store first result set */
 						       if ($result = mysqli_store_result($db_conn)):
 						           while ($row = mysqli_fetch_row($result)):
 						               printf("%s\n", $row[0]);
 						           endwhile;
 									mysqli_free_result($result);
    						   endif;
    						} while (mysqli_next_result($db_conn));
						
						// CHMOD the install file if possible
						if (@chmod('./config.php', 0644)):
							print '<p>The file permissions for ./config.php has successfully been changed to 0644</p>';
						else:
							print '<p>It is highly recommended that you change your file permissions for ./config.php file to 0644.</p>';
						endif;
						
						// Finit!
							$finish = true;
						endif;
						fclose($fq);
					endif;
					fclose($fh);
				endif;
				mysqli_close($db_conn);
			else:
				$check = 'We could not connect to MySQL. Check your information.';
			endif;
		endif;
	endif;

	// If we aren't finished, load the page
	if (!$finish):
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Install Scrollio</title>
</head>

<body>
<div style="font-family:Geneva">
<p style="text-align:center;">Welcome to the installation screen for Scrollio alpha candidate v.0.9.0.</p>
<p style="color:#F00">This version of the Scrollio forum software is a candidate for release. <br /> It is not stable and not recommended for commercial usage.</p>
<p>You are embarking on a new journey, as am I. This is the first edition of Scrollio, before all the hubbub began and all the fans couldn't contain themselves without shrieking its name. <i>Scrollio! Scrollio!</i> Without further past-tense predictions, I present you this piece of crap installation file.</p>
<h1>Legal Rabba Dabba go Fabba yo Mama</h1>
<p>This software is protected under the <a href="./license.txt">GNU GPL v.3 lisence</a>. All rights are reserved, and Scrollio is copyright its owner. JQuery is included in this package under the GPL license, and its copyright(s) are reserved by its owner(s).</p>
<p>If you've obtained this copy through a P2P or file-sharing program, then good for you. You're doing what the three persons in one Open Source God (heretofore recognized as Tim Berners-Lee, Linus Torvalds, and Steve Wozniak, for whom Trent Reznor sometimes fills in) intended of programming software.</p>
<p>You are free to make any and all alterations to this software. Although I would recommend waiting until the official project programmer sweats it out a few more work weeks, programming his arse off all the features you know and love of forum software.</p>
<p>Any changes that you do make to this software are protected under GPL v.3, which means that you may never sell any copies of this program, and any changes you make the program can be distributed only with full GPL rights.</p>
<p>If you agree to everything above, then you're well on your way to being awesome.</p>
<h1>Requirements</h1>
<p>Your PHP Version: <?php 
				if ((real)phpversion() < (real)'5.0.0'):
					echo '<span style="color:#FF0000">' .  phpversion() . ' - You are not compatible.</span>'; 
				else:
					echo '<span style="color:#009900">' .  phpversion() . ' - You are compatible!</span>'; 
				endif;
?></p>
<p>You MUST have PHP 5 enabled. This program uses PHP classes as defined in PHP 5. If you're not using PHP 5, then I would upgrade, because the rest of the world did so about 4 years ago.</p>
<p>Right now, Scrollio only definitely supports MySQL 5. I think we support MySQL 3 and 4, but I'm not positive. So, if you're running on either, you're a guinea pig of the special kind. Please kindly aid Scrollio by reporting your experience with Scrollio on any older version of MySQL than 5. The faster we learn about our capabilities, the more databases we can support.</p>
<h1>Setting Things Up</h1>
<p>Before you can use Scrollio, you must have the following bits of information:
<ul>
	<li>The Database Host Location (this is almost always 'localhost')</li>
    <li>The Database Name - You'll need to make one before running the installation. If you are using a shared host, your database name may be preceded by 'website_', where 'website' is usually a truncated series of letters from your website address. You'll need the full name.</li>
	<li>A Database User for the database named above with (at least) the following priviledges: Create, Select, Insert, Update, Delete, Index, Drop. For heightened security, you can give him only the aforementioned priviledges, which is recommended for candidate releases of Scrollio.</li>
    <li>The password for the User above. This will not be blotted out in asterisks.</li>
</ul>
You are also prompted for a prefix for the tables. Change this prefix only if you wish to install multiple Scrollio forums on one database. If none is provided, it will default to 'scrollio'.
</p>
<h1>Install, damn it!</h1>
<?php
// Print any errors
print '<h3 style="color:#FF0000">' .  $check . '</h3>';
?>
<form action="./install.php#install" method="post" id="install">
	<table><tr>
    	<td>Database Host Location</td>
        <td><input type="text" name="host" value="<?php echo ($_POST['host']) ? $_POST['host'] : 'localhost'; ?>" size="20" /></td>
    </tr><tr>
    	<td>Database Name</td>
        <td><input type="text" name="name" value="<?php echo $_POST['name']; ?>" size="20" /></td>
    </tr><tr>
    	<td>Database User</td>
        <td><input type="text" name="user" value="<?php echo $_POST['user']; ?>" size="20" /></td>
    </tr><tr>
    	<td>Database Password</td>
        <td><input type="text" name="pass" value="<?php echo $_POST['pass']; ?>" size="20" /></td>
    </tr><tr>
    	<td>Table Prefix</td>
        <td><input type="text" name="prefix" value="<?php echo ($_POST['prefix']) ? $_POST['prefix'] : 'scrollio'; ?>" size="10" /></td>
    </tr><tr>
    	<td>Admin User Name</td>
        <td><input type="text" name="username" value="<?php echo $_POST['username']; ?>" size="20" /></td>
    </tr><tr>
    	<td>Admin Password</td>
        <td><input type="text" name="password" value="<?php echo $_POST['password']; ?>" size="20" /></td>
    </tr><tr>
    	<td>Admin E-mail</td>
        <td><input type="text" name="email" value="<?php echo $_POST['email']; ?>" size="20" /></td>
    </tr></table>
    <input type="submit" name="db_install_submit" value="'Bout Time." /> | <input type="reset" />
</form>
</div>
</body>
</html>

<?php
	else:
		echo '<p>Scrollio is successfully installed. It is deeply recommended that you delete the /install folder and .install.php for safety.</p><p><a href="./forum.php">To the forum.</a></p>';
	endif;
else:
	echo 'Your config.inc.php file is already set!';
endif;
?>