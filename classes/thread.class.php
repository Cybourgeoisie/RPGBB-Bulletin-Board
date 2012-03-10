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

class thread extends forum {
	
	protected $thread_id;

	function __construct() {
		parent::__construct();
		(is_numeric($_GET[id])) ? $this->thread_id = $_GET[id] : $this->thread_id = 0;
		(is_numeric($_GET[start])) ? $this->start = $_GET[start] : $this->start = 0;
	}
	
	function getForumName($forum_id) {
		$query = 'SELECT forum_id, forum_name FROM ' . DB_PREFIX . '_forums WHERE forum_id = '. $forum_id;
		return(parent::select($query));
	}
	
	function getPosts() {
		$query = 'SELECT p.post_title as post_title, p.post_body as post_body, p.post_id as post_id, p.post_author as post_author,';
		$query .= ' DATE_FORMAT(p.post_time + INTERVAL ' . HOUR_DIFF_FROM_GMT . ' HOUR, "%a, %b %e, %Y at %l:%i %p") as post_time, m.member_name as author, m.member_id as member_id,';
		$query .= ' m.member_signature as signature, m.member_avatar as avatar, m.member_posts as post_count';
		$query .= ' FROM ' . DB_PREFIX . '_posts p, ' . DB_PREFIX . '_members m WHERE p.thread_id = ' . $this->thread_id . ' AND m.member_id = p.post_author';
		$query .= ' ORDER BY p.post_id LIMIT ' . $this->start . ',' . POST_DISPLAY_NUM;
		return(parent::select($query));
	}
	
	function getThreadNumPosts() {
		$query = 'SELECT p.post_title as post_title, p.post_body as post_body, p.post_id as post_id, p.post_author as post_author,';
		$query .= ' DATE_FORMAT(p.post_time + INTERVAL ' . HOUR_DIFF_FROM_GMT . ' HOUR, "%a, %b %e, %Y at %l:%i %p") as post_time, m.member_name as author,';
		$query .= ' m.member_signature as signature, m.member_avatar as avatar, m.member_posts as post_count';
		$query .= ' FROM ' . DB_PREFIX . '_posts p, ' . DB_PREFIX . '_members m WHERE p.thread_id = ' . $this->thread_id . ' AND m.member_id = p.post_author';
		$query .= ' ORDER BY p.post_id LIMIT ' . $this->start . ',' . POST_DISPLAY_NUM;
		return(parent::select($query));
	}
	
	function getThreadInfo() {
		$query = 'SELECT * FROM ' . DB_PREFIX . '_threads WHERE thread_id = ' . $this->thread_id;
		return(parent::select($query));
	}
	
}

?>