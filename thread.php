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
	require_once(PATH_TO_FILES . 'common.inc.php');
	$template->setVar('PAGE_NAME', 'View Thread');
	$template->displayPage('header');

	// Initialize
	if (!isset($check)):
		$check = new forms();
	endif;
	$alternating_posts = 0;

// Determine if the thread can be read
if (!has_authority('read', array('thread_id'=>$_GET['id']))):
	print "You do not have the authority to read this thread.";
	
// If the thread ID is specified and is numeric, run the page
elseif (is_numeric($_GET['id'])):
	$thread_call = new thread();

	// Get the thread information. If there is no thread, flag and end.
	if (!($thread = $thread_call->getThreadInfo())):
		// Display the error
		$template->setVar('PROCESS_ERROR', 'This is not a valid thread');
		$template->displayPage('process');
	else:
	
		// Get the forum name
		$forum = $thread_call->getForumName($thread[1][forum_id]);
	
		// If there are no posts, flag and end.
		if (!$posts = $thread_call->getPosts()):
			// Display the error
			$template->setVar('PROCESS_ERROR', 'There are no posts in this thread. Would you like to add one?');
			$template->displayPage('process');
		else:
			
			// Get all the proper information from forum and thread and set the variables
			foreach($forum[1] as $key => $value):
				$template->setVar(strtoupper($key), $value);
			endforeach;
			foreach($thread[1] as $key => $value):
				$template->setVar(strtoupper($key), $value);
			endforeach;
		
			// Get the pages navigations, add 1 to thread replies to cover for the first post
			$list_pages = list_pages($thread[1][thread_replies] + 1, $template->getVar('POST_DISPLAY_NUM'), 'thread');
				// Compile the page navigations and set the template variables
				$template->setVar('THREAD_PAGES', '[ ' . $list_pages[links] . ']');
				$template->setVar('THREAD_PAGES_OPTIONS', '<select class="list_pages_dropdown">' . $list_pages[options] . '</select>');
			
			// Start Page
			$template->displayPage('posts_start');
			
			/* DISPLAY EACH POST */
			// For each post, grab the info, prepare as necessary, and display the post
			foreach($posts as $post_info):
				
				
				// Make sure that the user has the authority to read the post; otherwise, just skip over
				// This is turned off and defaulted to true to speed up MySQL
				if (true /*has_authority('read', array('post_id' => $post_info['post_id']))*/):
				
				
					// Produce all information
					foreach($post_info as $key => $value):
						$template->setVar(strtoupper($key), $value);
					endforeach;
				
					// Prepare and Append BBCode to the Signature
					if ($template->getVar('SIGNATURE')!=''):
						$signature = $check->long_prepare($template->getVar('SIGNATURE'));
						$template->setVar('SIGNATURE', $signature);
					else:
						$template->setVar('SIGNATURE', '');
					endif;
				
					// Prepare and Append the img src to the Avatar if it exists
					if ($template->getVar('AVATAR')!=''):
						$avatar = "<br /><img src=\"" . $check->url_prepare($template->getVar('AVATAR')) . "\" class=\"post_avatar\" />";
						$template->setVar('AVATAR', $avatar);
					else:
						$template->setVar('AVATAR', '');
					endif;
				
					// Prepare the Post Body
					$post_body = $check->long_prepare($template->getVar('POST_BODY'));
					$template->setVar('POST_BODY',$post_body);
				
					// Set up the Post Options based on the user's authority
					$post_options = post_options($_GET['id'], $post_info[post_id], $_SESSION['member_id'], $post_info[post_author], $_SESSION['member_status']);
					$template->setVar('POST_OPTIONS',$post_options);
					
					// Alternate between two post displays
						if ($alternating_posts%2 == 0):
						$template->displayPage('post');
					else:
						$template->displayPage('post_alt');
					endif;
					$alternating_posts++;
				endif;
				
			endforeach;
			
			// End Page
			$template->displayPage('posts_end');
		endif;
	endif;
endif;

	$template->setVar('DB_CONSTRUCTS', $db_constructs);
	$template->setVar('DB_QUERIES', $db_queries);
	$template->setVar('PAGE_TIME_TO_LOAD', microtime()-$time);
	$template->displayPage('footer');
?>