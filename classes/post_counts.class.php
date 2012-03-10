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
/classes/post_counts.class.php
******************************************
Processes user input from forms and the like.

	Contents:
	__construct: 
	
	update_all:
		increments everything concerning post counts
	
	__destruct: 
		

******************************************/
	
if (!defined('SCROLLIO')): 
	die('You are unauthorised to view this file.'); 
endif;

class post_counts {
	
	protected $dbc_calls;
	
	function __construct() {
		global $dbc_calls;
		
		if (!isset($dbc_calls)):
			$this->dbc_calls = new dbc_calls();
		else:
			$this->dbc_calls = $dbc_calls;
		endif;
	}
	
	function update_all($type, $member_id, $forum_id, $thread_id, $mode) {
		// First update the member post count
		$this->dbc_calls->query('UPDATE ' . DB_PREFIX . '_members SET member_posts = member_posts'.$type.'1 WHERE member_id = ' . $member_id);
		// Update the forum post count
		$this->dbc_calls->query('UPDATE ' . DB_PREFIX . '_forums SET forum_posts = forum_posts'.$type.'1 WHERE forum_id = ' . $forum_id);
			
		// If the mode is new post, increment the thread replies; if the mode is new thread, increment the thread count
		if ($mode == 'post'):
			// Update the thread post count
			$this->dbc_calls->query('UPDATE ' . DB_PREFIX . '_threads SET thread_replies = thread_replies'.$type.'1 WHERE thread_id = ' . $thread_id);
		elseif ($mode == 'thread'):
			// Update the forum thread count
			$this->dbc_calls->query('UPDATE ' . DB_PREFIX . '_forums SET forum_threads = forum_threads'.$type.'1 WHERE forum_id = ' . $forum_id);
		endif;
		
		// Now, if the forum is a child, update the post counts of all the children
		$this->update_parents($dbc, $type, $forum_id, $mode);
		
		// Update the thread last updated time if there is an increment
		if ($type == '+'):
			$this->dbc_calls->query('UPDATE ' . DB_PREFIX . '_threads SET thread_last_updated = UTC_TIMESTAMP() WHERE thread_id = ' . $thread_id);
		endif;
		
		unset($dbc);
	}
	
	function __destruct() { }
	
	private function update_parents($dbc, $type, $forum_id, $mode) {
		
		// Increment all the parent forum post counts first
		$this->recursive_update('forum_posts', $dbc, $type, $forum_id);
		
		// Increment the parent forum thread counts if the mode is new thread
		if($mode == 'thread'):
			$this->recursive_update('forum_threads', $dbc, $type, $forum_id);
		endif;
	}
	
	private function recursive_update($field, $dbc, $type, $forum_id) {
		$parent_id = $this->dbc_calls->select('SELECT parent_id FROM ' . DB_PREFIX . '_forums WHERE forum_id = ' . $forum_id);
		if ($parent_id[1]['parent_id'] != 0):
			$this->dbc_calls->query('UPDATE ' . DB_PREFIX . '_forums SET '.$field.' = '. $field . $type .'1 WHERE forum_id = ' . $parent_id[1]['parent_id']);
			$this->recursive_update($field, $dbc, $type, $parent_id[1]['parent_id']);
		endif;
	}
	
}

?>