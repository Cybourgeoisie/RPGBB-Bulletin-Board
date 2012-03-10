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
/functions/post_options.inc.php
******************************************
To display the edit, quote, and delete options.
******************************************/

if (!defined('SCROLLIO')): 
	die('You are unauthorised to view this file.'); 
endif;

function post_options($thread_id, $post_id, $member_id = 0, $post_author_id = 1, $member_status = 0) {
	
	// Fundamental options
	$options = '&nbsp;<a href="./usercp.php?mode=messages&type=write&to='.$post_author_id.'">' . PM_BUTTON . '</a>&nbsp;';
	
	// If you are the member, or your authority permits you, provide Edit option
	if (has_authority('edit', array('post_id' => $post_id, 'thread_id' => $thread_id))):
		$options .= '&nbsp;<a href="./post.php?thread='.$thread_id.'&type=edit&p='.$post_id.'">' . EDIT_BUTTON . '</a>&nbsp;';
	endif;
	
	// If you are the member, or your authority permits you, provide Delete option
	if (has_authority('delete', array('thread_id' => $thread_id, 'post_id' => $post_id))):
		$options .= '&nbsp;<a href="./process.php?delete&thread='.$thread_id.'&p='.$post_id.'">' . DELETE_BUTTON . '</a>&nbsp;';
	endif;
	
	return $options;
}

?>