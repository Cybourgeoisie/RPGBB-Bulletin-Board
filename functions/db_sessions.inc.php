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

/*****************************************
/functions/db_sessions.inc.php
******************************************
The Session information.

Not really anything I'm ready to mess with.
******************************************/

$sess_conn = NULL;

function open_session() {
	global $sess_conn;	
	global $db;
	
	$sess_conn = new mysqli($db['host'], $db['user'], $db['pass'], $db['name']);
	return true;
}

function close_session() {
	global $sess_conn;

	return $sess_conn->close;
}

function read_session($sid) {
	global $sess_conn, $db;

 	$q = sprintf('SELECT session_data FROM '.DB_PREFIX.'_sessions WHERE session_id="%s"', $sess_conn->real_escape_string($sid)); 
	$r = $sess_conn->query($q);
	
	// Retrieve the results:
	if ($r->num_rows == 1) {
	
		list($data) = $r->fetch_array(MYSQLI_NUM);
		
		// Return the data:
		return $data;
	} else { // Return an empty string.
		return '';
	}
} 

function write_session($sid, $data) {
	global $sess_conn, $db;

 	$q = sprintf('REPLACE INTO '.DB_PREFIX.'_sessions (session_id, session_data) VALUES ("%s", "%s")', $sess_conn->real_escape_string($sid), $sess_conn->real_escape_string($data)); 
	$r = $sess_conn->query($q);

	return $sess_conn->affected_rows;
} 

function destroy_session($sid) {

	global $sess_conn, $db;

 	$q = sprintf('DELETE FROM '.DB_PREFIX.'_sessions WHERE session_id="%s"', $sess_conn->real_escape_string($sid)); 
	$r = $sess_conn->query($q);
	
	// Clear the $_SESSION array:
	$_SESSION = array();

	return $sess_conn->affected_rows;
}

function clean_session($expire) {

	global $sess_conn, $db;

 	$q = sprintf('DELETE FROM '.DB_PREFIX.'_sessions WHERE DATE_ADD(session_last_accessed, INTERVAL %d SECOND) < UTC_TIMESTAMP()', (int) $expire); 
	$r = $sess_conn->query($q);

	return $sess_conn->affected_rows;

}

# **************************** #
# ***** END OF FUNCTIONS ***** #
# **************************** #

// Declare the functions to use:
session_set_save_handler('open_session', 'close_session', 'read_session', 'write_session', 'destroy_session', 'clean_session');

// Start the Session:
session_start();

?>