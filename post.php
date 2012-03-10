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
	require_once('./config.inc.php');
	define('SCROLLIO', TRUE);
	define('PAGE_NAME', 'Post');
	require_once(PATH_TO_FILES . 'common.inc.php');
	$template->displayPage('header');

// Figure out what kind of post will be done
// Default to new thread post
if (is_numeric($_GET['thread'])):
	// Basic information
		$mode = 'post';
		$post_type = $_GET['type'];
		$id = $_GET['thread'];
		
	// If the user wants to edit a post, these two variables will have valid values
		$post_id = $_GET['p'];
		$thread_id = $_GET['thread'];

// Or new forum thread, where no thread_id is provided
elseif (is_numeric($_GET['forum'])):
	$mode = 'thread';
	$id = $_GET['forum'];
	
// Otherwise, this is an illegal entry
else:
	$mode = FALSE;
endif;

if (!isset($dbc_calls)):
	$dbc_calls = new dbc_calls;
endif;

// Check for current post, grab the author
if (is_numeric($post_id)):
	$author = $dbc_calls->select('SELECT post_author FROM ' . DB_PREFIX . '_posts WHERE post_id='.$post_id);
	$author_id = $author[1]['post_author'];
endif;


// If the user has no authority to post, then print a note
if (!$mode || !has_authority('post', array('thread_id'=>$_GET['thread'], 'forum_id'=>$_GET['forum']))):
	print "Please register or log in if you would like to post.";

// If the user has authority, continue
elseif ($mode && (is_numeric($post_id) || is_null($post_id)) && (is_numeric($thread_id) || is_null($thread_id))):
	if ($post_type == 'edit'):
		$post_header = "<h2>Edit $mode</h2>";
		$post_info = $dbc_calls->select('SELECT post_title, post_body FROM ' . DB_PREFIX . '_posts WHERE post_id='.$post_id.' && thread_id='.$thread_id);
	else:
		$post_header = "<h2>Add new $mode</h2>";
	endif;

	$template->setVar('POST_HEADER', $post_header);
	$template->setVar('AUTHOR_ID', $author_id);
	$template->setVar('THREAD_ID', $thread_id);
	$template->setVar('POST_ID', $post_id);
	$template->setVar('POST_TYPE', $post_type);
	$template->setVar('MODE', $mode);
	$template->setVar('PARENT_ID', $id);
	
	if (isset($post_info)):
		$template->setVar('POST_TITLE', $post_info[1]['post_title']);
		$template->setVar('POST_BODY', $post_info[1]['post_body']);
	else:
		$template->setVar('POST_TITLE', '');
		$template->setVar('POST_BODY', '');
	endif;

	$template->displayPage('write_post');

else:
	print "An error has occurred. Please return to the main page.";
endif;
?>

<?php 
	$template->setVar('DB_CONSTRUCTS', $db_constructs);
	$template->setVar('DB_QUERIES', $db_queries);
	$template->setVar('PAGE_TIME_TO_LOAD', microtime()-$time);
	$template->displayPage('footer');
	unset($db);
?>