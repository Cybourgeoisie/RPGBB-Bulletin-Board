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
/classes/forum.class.php
******************************************
Produces and displays forum information.

	Contents:
	__construct: 
		connects to the database and produces the
		array of forum layout information.
	
	get_array:
		prints array produced in construct
		
	__destruct: 
		closes the database connection.

******************************************/
	
if (!defined('SCROLLIO')): 
	die('You are unauthorised to view this file.'); 
endif;

class forum extends dbc_calls {

	protected $dbc, $cat_array, $forum_listing;

	function __construct() {
		parent::__construct();
		
		// Set up basic values for the current Category ID, Forum ID, and the page start for displaying threads
		(is_numeric($_GET[forum])) ? $this->forum_id = $_GET[forum] : $this->forum_id = 0;
		(is_numeric($_GET[category])) ? $this->category_id = $_GET[category] : $this->category_id = 0;
		(is_numeric($_GET[start])) ? $this->start = $_GET[start] : $this->start = 0;
	}
	
	function getCategory() {
		if ($this->category_id == 0):
			$where = 'cat_status=1';
		else:
			$where = 'cat_status=1 && cat_id = ' . $this->category_id;
		endif;
		
		$query = 'SELECT cat_id, cat_name, cat_desc, cat_order FROM ' . DB_PREFIX . '_categories WHERE ' . $where . ' ORDER BY cat_order';
		
		return(parent::select($query));
	}
	
	function getCategoryList() {
		foreach ($this->getCategory() as $category):
			$this->forum_listing[$category[cat_id]] = $category;
			$this->getForumList($category[cat_id]);
		endforeach; 
		
		return $this->forum_listing;
	}
	
	function getForum($category_id = 0, $parent_id = 0) {
		if ($category_id == 0):
			$where = 'f.forum_id = ' . $this->forum_id;
		else:
			$where = 'f.cat_id = ' . $category_id . ' && f.parent_id = ' . $parent_id;
		endif;
		
		$query = 'SELECT f.forum_id, f.forum_name, f.forum_desc, f.forum_order, f.forum_threads, f.forum_posts FROM ' . DB_PREFIX . '_forums as f'; 
		$query .= ' WHERE ' . $where . ' ORDER BY f.forum_order';
		
		return(parent::select($query));
	}
	
	function getForumLastUpdated($forum_id) {
		$query = 'SELECT p.post_id, p.thread_id, p.post_author, DATE_FORMAT(p.post_time + INTERVAL ' . HOUR_DIFF_FROM_GMT . ' HOUR, "%a, %b %e, %Y at %l:%i %p") as last_updated_time,';
		$query .= ' m.member_name as last_updated_author FROM ' . DB_PREFIX . '_posts p, ' . DB_PREFIX . '_members m';
		$query .= ' WHERE p.forum_id = '. $forum_id .' && m.member_id = p.post_author ORDER BY post_id DESC LIMIT 1';
		
		// If there is a result, then grab the page
		if ($return = parent::select($query)):
			// Get the starting page
			$start = parent::select('SELECT thread_replies FROM ' . DB_PREFIX . '_threads WHERE forum_id = "' . $forum_id . '" ORDER BY thread_last_updated DESC LIMIT 1');
			$start = $start[1][thread_replies] + 1;
			if ($start%POST_DISPLAY_NUM == 0):
				$start = $start - POST_DISPLAY_NUM;
			else:
				$start = ((int)($start/POST_DISPLAY_NUM))*POST_DISPLAY_NUM;
			endif;
		
			$return[1][last_updated_start] = $start;
		endif;
		
		return $return;
	}
	
	function getForumList($cat_id, $parent_id = 0) {	
		if ($forum_array = $this->getForum($cat_id, $parent_id)):
			foreach ($forum_array as $forum):
				$this->forum_listing[$cat_id][$parent_id][$forum[forum_id]] = $forum;
				$this->getForumList($cat_id, $forum[forum_id]);
			endforeach;
		endif;
	}
		
	function getThreads() {
		$query = 'SELECT t.*, m.member_name as author FROM ' . DB_PREFIX . '_threads t, ' . DB_PREFIX . '_members m';
		$query .= ' WHERE t.forum_id = ' . $this->forum_id . ' && m.member_id=t.thread_author';
		$query .= ' ORDER BY t.thread_last_updated DESC LIMIT ' . $this->start . ', '.THREAD_DISPLAY_NUM.'';
		return(parent::select($query));
	}
	
	function getThreadLastUpdated($thread_id) {
		$query = 'SELECT p.post_id, p.thread_id, p.post_author, DATE_FORMAT(p.post_time + INTERVAL ' . HOUR_DIFF_FROM_GMT . ' HOUR, "%a, %b %e, %Y at %l:%i %p") as last_updated_time,';
		$query .= ' m.member_name as last_updated_author FROM ' . DB_PREFIX . '_posts p, ' . DB_PREFIX . '_members m';
		$query .= ' WHERE p.thread_id = '. $thread_id .' && m.member_id = p.post_author ORDER BY post_id DESC LIMIT 1';
		
		// If there is a result, then grab the page
		if ($return = parent::select($query)):
			// Get the starting page
			$start = parent::select('SELECT thread_replies FROM ' . DB_PREFIX . '_threads WHERE thread_id = "' . $thread_id . '" LIMIT 1');
			$start = $start[1][thread_replies] + 1;
			if ($start%POST_DISPLAY_NUM == 0):
				$start = $start - POST_DISPLAY_NUM;
			else:
				$start = ((int)($start/POST_DISPLAY_NUM))*POST_DISPLAY_NUM;
			endif;
		
			$return[1][last_updated_start] = $start;
		endif;
		
		return $return;
	}
	
	function __destruct() { }
	
}

?>