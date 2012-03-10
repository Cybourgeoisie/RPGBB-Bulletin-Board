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
/classes/forms.class.php
******************************************
Processes user input from forms and the like.

	Contents:
	__construct: not set
	compare:
		simply compares two values to see if they are the same or different
	email_validate:
		uses a regex to validate an e-mail address
	exists:
		checks to see if a value exists within the database
	get_value:
		grabs an immediate value from mysql
	password_validate:
		ctype_graph in a hyena costume
	insert_into:
		compiles, sends, and returns information of an INSERT SQL query
	salt:
		takes two values, uses one as salt for the other value
	text_filter:
		takes text, strips tags, white space, and adds slashes
		if magic quotes is off
	text_prepare:
		prepares text for for display - strips slashes if magic quotes
		is off and includes BBCode if necessary
	update_sql:
		same as insert_into, but with UPDATE SQL
	url_filter:
		urlencodes() a URL
	url_validate:
		checks for a valid URL
	__destruct: not set
		

******************************************/
	
if (!defined('SCROLLIO')): 
	die('You are unauthorised to view this file.'); 
endif;

class forms {

	protected $dbc_calls;

	function __construct() { }
	
	function compare($t1, $t2) {
		if ($t1 == $t2):
			return TRUE;
		endif;
		
		return FALSE;
	}
	
	function email_validate($email) {
		if (eregi("^[a-zA-Z0-9\_\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z]{2,4}$", $email)) :
			return TRUE;
		endif;
		
		return FALSE;
	}
	
	function hash($password) {
		return sha1($password);
	}
	
	function long_prepare($text, $bbcode = 1) {
		// After running text_filter, only need to prepare for specific cases
		
		// Newlines
		$text = nl2br($text);
		
		if ($bbcode == 1):
			// Run the BBCode function
			$text = bbcode($text);
		endif;
		
		return $text;
	}
	
	function password_validate($text) {
		return ctype_graph($text);
	}
	
	function salt($password, $salt) {
		$salt = substr($salt, 0, 2);
		return crypt($password,$salt);
	}
	
	function short_prepare($text, $bbcode = 0) {
		// After running text_filter, only need to prepare for specific cases
		
		if ($bbcode == 1):
			// Run the BBCode function
			$text = bbcode($text);
		endif;
		
		return $text;
	}
	
	function text_filter($text) {
		global $dbc_calls;
		
		// Trim excess whitespace, flag if nothing exists
		$text = trim($text);
		
		if ($text == ''):
			return FALSE;
		endif;
		
		// Avoid double escaping
		if (get_magic_quotes_gpc()):
			$text = stripslashes($text);
		endif;
		
		// Convert special characters and mysql injection vars
		$text = $dbc_calls->escape($text);
		$text = htmlentities($text, ENT_QUOTES, 'UTF-8');
		
		return $text;
	}
	
	function text_prepare($text) {
		// Strip slashes from the mysql_real_escape_string in the DBC class
		if (get_magic_quotes_gpc()):
			$text = stripslashes($text);
		endif;
		
		// Keep HTML encoded
		// $text = htmlentities($text, ENT_QUOTES, 'UTF-8');
		// HTML is already encoded. Solved that problem.
		// Kept comments for history's sake
		
		return $text;
	}
	
	function url_filter($url) {
		$url = urlencode($url);
		$url = $this->text_filter($url);
		
		return $url;
	}
	
	function url_prepare($url) {
		$url = $this->text_prepare($url);
		$url = urldecode($url);
		
		return $url;
	}
	
	function url_validate($url) {
		// Might be too loose. There's no <>, though. Hmm.
		if (eregi("^s?https?:\/\/[-_.!~*()a-zA-Z0-9;\/?:\@&=+\$,%#]+$", $url)) :
			return TRUE;
		endif;
		
		return FALSE;
	}
	
	function __destruct() { }
	
}

?>