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
/classes/db_connections/.inc.php
******************************************
The database handler class.

	Contents:
	__construct: 
		refers back to parent to construct
	
	__destruct: 
		refers back to parent to destruct

******************************************/

if (!defined('SCROLLIO')): 
	die('You are unauthorised to view this file.'); 
endif;

class dbc_calls {

	protected $dbc, $check, $db_result;
	
	function __construct() {
		global $dbc, $check;
		
		// Make sure that there is only one DBC and one Check at any given time
		if (!isset($dbc)):
			$dbc = new dbc();
		endif;
		
		if (!isset($check)):
			$check = new forms();
		endif;
		
		$this->dbc = $dbc;
		$this->check = $check;
	}
	
	function escape($text) {
		return $this->dbc->db_escape($text);
	}
	
	function exists($text, $db_field, $db_table) {
		$query = "SELECT $db_field FROM $db_table WHERE $db_field = \"$text\"";
		$result = $this->select($query);

		if ($result != false):
			return TRUE;
		endif;
		
		return FALSE;
	}
	
	function getMemberInfo($member_id = 0) {
		if ($member_id == 0):
			$query = 'SELECT *, DATE_FORMAT(member_register_date, "%b %e, %Y") as member_join_date FROM ' . DB_PREFIX . '_members WHERE member_id != 0';
		else:
			$query = 'SELECT *, DATE_FORMAT(member_register_date, "%b %e, %Y") as member_join_date FROM ' . DB_PREFIX . '_members WHERE member_id = ' . $member_id;
		endif;
		
		return(self::select($query));
	}
	
	function getMemberProfileInfo($member_id = 0) {
		if ($member_id == 0):
			$query = 'SELECT * FROM ' . DB_PREFIX . '_members_to_info WHERE member_id != 0';
		else:
			$query = 'SELECT * FROM ' . DB_PREFIX . '_members_to_info WHERE member_id = ' . $member_id;
		endif;
		
		if ($members_responses = self::select($query)):
			foreach($members_responses as $member_response):
				$query= 'SELECT * FROM ' . DB_PREFIX . '_members_information WHERE info_id=' . $member_response[info_id];
				$information = self::select($query);
				$list[$member_id][$information[1][info_id]] = array(
					'info_title' => $information[1][info_title], 
					'info_response' => $member_response[info_response]);
			endforeach;
		endif;
		
		return $list;
	}
	
	function getNumberOfMembers() {
		$query = 'SELECT member_id FROM ' . DB_PREFIX . '_members WHERE member_id != 0';
		self::select($query);
		return $this->dbc->db_result_num_rows();
	}
	
	function get_value($where_text, $db_field_get, $db_field_where, $db_table) {
		$query = "SELECT $db_field_get FROM $db_table WHERE $db_field_where = \"$where_text\"";
		$result = $this->select($query);
				
		return $result[1][$db_field_get];
	}
	
	function insert($table, $array) {
		$i=0;
		foreach ($array as $field => $value):
			$fields[] = $field;
			
			// Keep MySQL values intact, but filter out everything else
			if ($value != 'UTC_TIMESTAMP()'):
				$values[] = '"' . $this->check->text_filter($value) . '"';
			else:
				$values[] = $value;
			endif;
			
		endforeach;
		
		$fields = '(' . implode(',',$fields) . ')';
		$values = '(' . implode(',',$values) . ')';
		
		$query = 'INSERT INTO ' . DB_PREFIX . '_' . $table . ' ' . $fields . ' VALUES ' . $values;
		
		return $this->dbc->db_query($query);
	}
	
	function prepare_result_array($result_type = MYSQLI_ASSOC) {
		foreach ($this->dbc->db_fetch_array($result_type) as $row_key => $row):
			foreach ($row as $item_key => $item):
				$return_array[$row_key][$item_key] = $this->check->text_prepare($item);
			endforeach;
		endforeach;
		
		return $return_array;
	}
	
	function result_exists() {
		if ($this->dbc->db_result_num_rows() > 0):
			return TRUE;
		endif;
		
		return FALSE;
	}
	
	function select($select_query, $return_row_count = 0) {
		// As long as a select_query is provided, run.
		if (!is_null($select_query)):
			$this->dbc->db_query($select_query);
		
			if (!$this->result_exists()):
				return FALSE;
			elseif ($return_row_count == 1):
				return $this->dbc->db_result_num_rows();
			endif;
		
			return $this->prepare_result_array();
		endif;
	}
	
	function selectNum($select_query, $return_row_count = 0) {
		$this->dbc->db_query($select_query);
		
		if (!$this->result_exists()):
			return FALSE;
		elseif ($return_row_count == 1):
			return $this->dbc->db_result_num_rows();
		endif;
		
		return $this->prepare_result_array(MYSQLI_NUM);
	}
	
	function query($query) {
		return $this->dbc->db_query($query);
	}
	
	function update($table, $array, $where) {
		$i=0;
		$set_array = array();
		
		foreach ($array as $field => $value):
			$value = '"' . $this->check->text_filter($value) . '"';
			$set_array[] = $field . ' = ' . $value;
		endforeach;
		
		$set = implode(',', $set_array);
		
		$query = 'UPDATE ' . DB_PREFIX . '_' . $table . ' SET ' . $set . ' WHERE ' . $where;
		
		return $this->dbc->db_query($query);
	}
	
	function getWhosOnline() {
		$query = 'SELECT session_data FROM ' . DB_PREFIX . '_sessions WHERE DATE_SUB(NOW(),INTERVAL 5 MINUTE) <= session_last_accessed';
		$results = self::select($query);
		$number = $this->dbc->db_result_num_rows();
		
		return array('number'=>$number, 'session'=>$results);
	}
	
	function __destruct() { }
}

?>