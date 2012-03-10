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
/functions/has_authority.inc.php
******************************************
To determine the level of authority of the user
and compare it to the task attempted

Contents:

	member_is_admin:
		Determines if a member is an admin through DB check
		
	has_authority:
		Determines if a logged-in member has the authority to do stuff
******************************************/

if (!defined('SCROLLIO')): 
	die('You are unauthorised to view this file.'); 
endif;

// Determines if the member is the administrator
function member_is_admin($member_id = 'NULL') {
	
	// Initialize
		global $dbc_calls;
	
		if (!isset($dbc_calls)):
			$dbc_calls = new dbc_calls;
		endif;
	
	// If the member_id is not numeric, fail.
	if (!is_numeric($member_id)):
		return false;
	endif;
	
	// Check if the user is an admin, return true or false
	$member_is_admin = $dbc_calls->select('SELECT member_is_admin FROM ' . DB_PREFIX . '_members WHERE member_id = "' . $member_id . '" LIMIT 1');
	return (bool)($member_is_admin[1]['member_is_admin'] == 1);
}

// Determines if the post is one's own
function member_is_owner($member_id = 'NULL', $post_id = 'NULL', $thread_id = 'NULL') {
	// Initialize
		global $dbc_calls;
	
		if (!isset($dbc_calls)):
			$dbc_calls = new dbc_calls;
		endif;
	
	// If the member_id or post_id is not numeric, fail.
	if (!is_numeric($member_id) || !is_numeric($post_id)):
		return false;
	endif;
	
	// Grab the author
	$author = $dbc_calls->select('SELECT post_author FROM ' . DB_PREFIX . '_posts WHERE post_id = "' . $post_id . '"');
	
	// Compare the author_id with the member_id. If they are not one and the same, return false
	if ($member_id != $author[1]['post_author']):
		return false;
	
	// If they are the same, but the thread_id is provided (to find out if more posts follow), then do further checking
	elseif (is_numeric($thread_id)):
		// Get the most recent post of the thread, compare it to the submitted post
		$query = 'SELECT post_id FROM ' . DB_PREFIX . '_posts WHERE (thread_id = "' . $thread_id . '" && post_id >= "' . $post_id . '")';
		$num_posts = $dbc_calls->select($query, 1);
				
		// If $num_posts contains 1 result, which is the same post as the one provided, then return true
		if ($num_posts == 1):
			return true;
		endif;
		
		// If the number of results is not 1, then false
		return false;
		
	// If there are no more checks and the user == author, return true
	else:
		return true;
	endif;
}

// Determines if the member of a group has the authority
function group_member_has_authority($member_id = NULL, $member_status = NULL, $task = NULL, $task_info = NULL) {
	
	// Note: 
	// $info is an array that follows that rules of the has_authority 
	// Except that the order of the array elements denotes priority
	
	// Initialize
		global $dbc_calls;
	
		if (!isset($dbc_calls)):
			$dbc_calls = new dbc_calls;
		endif;
		
		$setting = NULL;
		$task_array = array('view','read','post','edit','delete','lock');
		
	// If the information suggests a guest, provide dummy values.
	if (is_null($member_id) && is_null($member_status) && !is_null($task)):
		$member_id = -1;
		$member_status = 0;
	// If the member_id is not numeric, the member status is not numeric or null, or the task is not provided, then flag
	elseif (!is_numeric($member_id) || (!is_numeric($member_status) && !is_null($member_status)) || is_null($task)):
		return false;
	endif;
	
	// If the task is not part of the task array, then flag
	if (!in_array($task, $task_array)):
		return false;
	endif;
		
	// Reassign $task to work in MySQL
	$task = 'can_' . $task;
	
	// Get all valid task information
	foreach ($task_info as $key => $value):
		if (is_numeric($value)):
			$information['where'][$key] = $key . ' = "' . $value . '"';
		endif;
	endforeach;

	// Append higher values if none are provided
	if (!isset($information['where']['cat_id'])):
		if (!isset($information['where']['forum_id'])):
			if (!isset($information['where']['thread_id'])):
				// If there is no post information, then you're screwed
				if (!isset($information['where']['post_id'])):
					return false;
				else:
					// Grab the thread, forum, and cat IDs
					// Might be too intensive for MySQL
					$select = 'SELECT p.thread_id, t.forum_id, f.cat_id FROM ' . DB_PREFIX . '_posts as p, ' . DB_PREFIX . '_threads as t, ';
					$select .= DB_PREFIX . '_forums as f WHERE p.' . $information['where']['post_id'];
					$select .= ' AND t.thread_id = p.thread_id AND f.forum_id = t.forum_id';
				endif;
			else:
				// Grab the forum and cat IDs
				// Might be too intensive for MySQL
				$select = 'SELECT t.forum_id, f.cat_id FROM ' . DB_PREFIX . '_threads as t, ';
				$select .= DB_PREFIX . '_forums as f WHERE t.' . $information['where']['thread_id'] . ' AND f.forum_id = t.forum_id';
			endif;
		else:
			// Grab the forum and cat IDs
			// Might be too intensive for MySQL
			$select = 'SELECT cat_id FROM ' . DB_PREFIX . '_forums WHERE ' . $information['where']['forum_id'];
		endif;
	else:
		$select = NULL;
	endif;
	
	// Run the select, grab the higher values and attribute them from the highest priority (lowest-level values) 
	// to lowest priority (higher-level). Then reverse the order of the results and append to maintain the low-to-high 
	// priority of the $information['where'] values
	if ($higher_values = $dbc_calls->select($select)):
		if (isset($higher_values[1]['thread_id'])):
			$values['where']['thread_id'] = 'thread_id = "' . $higher_values[1]['thread_id'] . '"';
		endif;
		
		if (isset($higher_values[1]['forum_id'])):
			$values['where']['forum_id'] = 'forum_id = "' . $higher_values[1]['forum_id'] . '"';
		endif;
	
		if (isset($higher_values[1]['cat_id'])):
			$values['where']['cat_id'] = 'cat_id = "' . $higher_values[1]['cat_id'] . '"';
		endif;
		
		$values['where'] = array_reverse($values['where'], true);
		$information['where'] = $values['where'] + $information['where'];
	endif;
	
	// First look at the user's status and grab the default values
	// Determine the group_id by the member_status
	switch($member_status):
		case NULL: // Guest
			$group_id = 0; // Guests
		break;
		case (-1): // Banned member
			$group_id = 3; // Banned
		break;
		case (0): // Unauthorized member
			$group_id = 2; // Awaiting Authorization
		break;
		case (1): // Authorized member
			$group_id = 1; // Members
		break;
		default:
			$group_id = 0; // Guests
		break;
	endswitch;
	
	
	
	// Because of the many heavy queries that can be run every single page, disable any functionality for post and thread checks
	// To enable, simply remove the following if block
	// Also re-enable the has_authority between each post in thread.php
	if (isset($information['where']['thread_id'])):
		unset($information['where']['thread_id']);
		if (isset($information['where']['post_id'])):
			unset($information['where']['post_id']);
		endif;
	endif;
	// End of the block
	
	
	
	// Run the select query, get the task information
	if (isset($information['where'])):
	// For each bit of information found, search. If no results return, look at higher levels (going from post_id to thread_id to forum_id...)
		foreach($information['where'] as $key => $where):
			// Find the groups that the user belongs to that fall into the proper information
			$select = 'SELECT ' . $task . ' FROM ' . DB_PREFIX . '_group_permissions WHERE group_id = ' . $group_id . ' AND ' . $where;
			// If the member has group permissions, then determine it.
			if ($setting_result = $dbc_calls->select($select)):
				// If the setting result is found, return the boolean value
				$setting = (bool)$setting_result[1][$task];
			endif;
		endforeach;
	endif;
	
	// Then follow up with the group(s) the user may belong to
	// If the user is a valid member, then checks for groups status may be conducted
	if ($member_status == 1 && isset($information['where'])):
		// For each bit of information found, search. If no results return, look at higher levels (going from post_id to thread_id to forum_id...)
		foreach($information['where'] as $key => $where):
			// Find the groups that the user belongs to that fall into the proper information
			$select = 'SELECT p.' . $task . ' FROM ' . DB_PREFIX . '_group_members as m, ' . DB_PREFIX . '_group_permissions as p';
			$select .= ' WHERE m.member_id = "' . $member_id . '" AND p.' . $where . ' AND m.group_id = p.group_id';
			// If the member has group permissions, then determine it.
			if ($setting_result = $dbc_calls->select($select)):
				// If the setting result is found, return the boolean value
				$setting = (bool)$setting_result[1][$task];
			endif;
		endforeach;
	endif;
	
	// If the setting is provided, return that value
	if (isset($setting)):
		return $setting;
	endif;
	
	// Otherwise default to false
	return false;
}

// The main function - determines the authority of a member for all tasks
function has_authority($task, $info = NULL) {
	
	// Notes:
	// $info should have the correct information necessary
	// to carry out all has_authority queries. For that matter,
	// its keys are standardized by purpose. The reason we use an
	// array instead of multiple parameters is for extensibility
	// and concise form.
	// --------------------------------
	// Post ID - $info['post_id']
	// Thread ID - $info['thread_id']
	// Forum ID - $info['forum_id']
	// Category ID - $info['cat_id']
	// Member ID - $info['member_id']
	// Member Status - $info['member_status']
	// --------------------------------
	// :: Below are Experimental ::
	// Page ID - $info['page_id']
	
	// Initlialize
		global $dbc_calls;
		
		if (!isset($dbc_calls)):
			$dbc_calls = new dbc_calls;
		endif;
		
		// Set into the reverse order of priority
		// - When the foreach searches for the setting, each successive find will override the last
		$info_array = array('cat_id'=>$info['cat_id'], 'forum_id'=>$info['forum_id'], 
							'thread_id'=>$info['thread_id'], 'post_id'=>$info['post_id']);
		
	// If member_id is not specified, default to the current user
	if (!isset($info['member_id'])):
		$member_id = $_SESSION['member_id'];
	else:
		$member_id = $info['member_id'];
	endif;
	
	// If member_status is not specified, default to the current user
	if (!isset($info['member_status'])):
		$member_status = $_SESSION['member_status'];
	else:
		$member_status = $info['member_status'];
	endif;
	
	// Determine the kind of authority needed for a certain task
	switch($task):
		
		case 'view':
			
			// Check if the user has the authority to do so
			if (group_member_has_authority($member_id, $member_status, 'view', $info_array)):
				return true;
			elseif (member_is_admin($member_id)):
				return true;
			endif;
			
			return false;
		break;
		
		case 'read':
			
			// Check if the user has the authority to do so
			if (group_member_has_authority($member_id, $member_status, 'read', $info_array)):
				return true;
			elseif (member_is_admin($member_id)):
				return true;
			endif;
			
			return false;
		break;
		
		case 'post':
			
			// Check if the user has the authority to do so
			if (group_member_has_authority($member_id, $member_status, 'post', $info_array)):
				return true;
			elseif (member_is_admin($member_id)):
				return true;
			endif;
			
			return false;
		break;
		
		case 'edit':
			
			// Check if the user is the author; checks to see if the user has authority as well
			if (member_is_owner($member_id, $info['post_id'])):
				return true;
			// Check if the user has the authority to do so otherwise
			elseif (group_member_has_authority($member_id, $member_status, 'edit', $info_array)):
				return true;
			elseif (member_is_admin($member_id)):
				return true;
			endif;
			
			return false;
		break;
		
		case 'delete':
			
			// If the user is the author and the post is the most recent, then true
			if (member_is_owner($member_id, $info['post_id'], $info['thread_id'])):
				return true;
			// Check if the user has the authority to do so otherwise
			elseif (group_member_has_authority($member_id, $member_status, 'delete', $info_array)):
				return true;
			elseif (member_is_admin($member_id)):
				return true;
			endif;
			
			return false;
		break;
		
		default:
			print '<p>No case specified for has_authority().</p>';
			return false;
		break;
		
	endswitch;
}

?>