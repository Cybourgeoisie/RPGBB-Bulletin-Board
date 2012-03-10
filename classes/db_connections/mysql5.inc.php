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
/classes/db_connections/mysql5.inc.php
******************************************
The database class for mysql 5.0. Uses
improved MySQL connections.

	Contents:
	__construct: 
		connects to the database.
	db_query: 
		queries the database with a SQL statement
	__destruct: 
		closes the connection.

******************************************/

if (!defined('SCROLLIO')): 
	die('You are unauthorised to view this file.'); 
endif;

class dbc {

	protected $db_conn, $db_prefix, $db_result;
	
	function __construct() {
		global $db, $db_constructs;
		// Increase the number of connections made
		$db_constructs++;
		
		$this->db_prefix = $db['prefix'];
		
		$this->db_conn = new mysqli($db['host'], $db['user'], $db['pass'], $db['name']);
		
		if (mysqli_connect_errno()):
	    	die("Connection attempt failed: " . mysqli_connect_error());
		endif;
	}
	
	function db_query($query) {
		// Increase the number of queries run
		global $db_queries;
		$db_queries++;
		
		// clear the current db_result_array
		unset($this->db_result_array);
		
		// returns the result of the query
		if (!($this->db_result = $this->db_conn->query($query))):
			printf("Error: %s\n", $this->db_conn->error);
		endif;
		
		return $this->db_result;
	}
	
	function db_escape($text) {
		return $this->db_conn->real_escape_string($text);
	}
	
	function db_fetch_array($result_type = MYSQLI_ASSOC) {
		$i = 1;
		while ($this->db_row = $this->db_result->fetch_array($result_type)) :
				foreach ($this->db_row as $key => $item):
					$this->db_result_array[$i][$key] = $item;
				endforeach;
				$i++;
			endwhile;
		
		return $this->db_result_array;
	} 
	
	function db_result_num_rows() {
		return $this->db_result->num_rows;
	}
	
	function db_free() {
		if ($this->db_result != '') {
			$this->db_result->free();
		}
	}
	
	function __destruct() {
	//	$this->db_free();
		$this->db_conn->close();
	}
}

?>