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
	$template->setVar('PAGE_NAME', 'Index');
	$template->displayPage('header');
	
	// Function to display each forum as necessary, including subforums
	function recursive_forum_display($info, $parent) {
		global $template, $forum_call, $alternating_forum;
		
		// For each forum in the array, display the forum
		if (is_array($info[$parent])):
			foreach($info[$parent] as $forum_id => $forum): 
				
				// Make sure that the user has the authority to view the forum; otherwise, just skip over
				if (has_authority('view', array('forum_id' => $forum['forum_id']))):
				
					// Alternate between two forum classes
					if ($alternating_forum%2 == 0):
						$template->setVar('ALTERNATING_FORUM', 'forum_alt_0');
					else:
						$template->setVar('ALTERNATING_FORUM', 'forum_alt_1');
					endif;
					$alternating_forum++;
						
					// Set the template variables
					foreach ($forum as $key => $value):
						$template->setVar(strtoupper($key), $value);
					endforeach;
					
					// Get the forum_last_updated information, set necessary variables
					$forum_last_updated = $forum_call->getForumLastUpdated($forum_id);
					if ($forum_last_updated != false):
						// Set template variables
						foreach ($forum_last_updated[1] as $key => $value):
							$template->setVar(strtoupper($key), $value);
						endforeach;
						$template->setVar('LAST_UPDATED_MESSAGE', '');
					else:
						// If no threads are found, then make sure that everything is reset and a message shown
						$template->setVar('LAST_UPDATED_MESSAGE', 'There are no threads here');
						$template->setVar('LAST_UPDATED_TIME', '');
						$template->setVar('LAST_UPDATED_AUTHOR', '');
						endif;
					
					// If this forum has a parent, append the 'sub' prefix
					if ($parent != 0):
						$template->setVar('PREFIX', 'sub');
					else:
						$template->setVar('PREFIX', '');
					endif;
								
					// Run the forum row
					$template->displayPage('forum');
				
					// If the forum has a child, run this entire function again
					if (is_array($info[$forum_id])):
						recursive_forum_display($info, $forum_id);
					endif; 
				endif;
			endforeach;
		endif;
	}

	
	/* FORUM PAGE */
	// Initialize
	$alternating_forum = 0;
	$alternating_thread = 0;
	$forum_call = new forum();
	$categories = $forum_call->getCategoryList();
	
	// If the forum number is specified and is numeric, get the information of the forum
	if (is_numeric($_GET[forum])):
		$forum = $forum_call->getForum();
		$template->setVar('THIS_FORUM_NAME',$forum[1]['forum_name']);
		$template->setVar('THIS_FORUM_ID',$forum[1]['forum_id']);
		
		// If there are no threads in the forum, send up the message
		if (!($threads = $forum_call->getThreads())):
			$threads[0]['message'] = $l['NO_THREADS_IN_FORUM'];
		endif;
	endif;

	// Get the parent, or set it to 0 if none exists
	(is_numeric($_GET[forum])) ? $parent = $_GET[forum] : $parent = 0;

	// Display forums and categories, if applicable
	foreach($categories as $category_id => $category_info):
		
		// Make sure that the user has the authority to view the category; otherwise, just skip over
		if (has_authority('view', array('cat_id' => $category_info['cat_id']))):
		
			// Set the template variables
			foreach ($category_info as $key => $value):
				$template->setVar(strtoupper($key), $value);
			endforeach;
			
			// If the category ID matches the category GET value, display that category info. Or if none is specified, all categories will be displayed.
			if ($category_id == $_GET['category'] || !isset($_GET['category'])):
				// If the forum ID matches the forum GET value, display that forum's children. Or if none is specified, all forums will be displayed.
				if (array_key_exists($_GET['forum'], $category_info) || !isset($_GET['forum'])): 
					
					// Append the 'sub' class if the forum has a forum parent
					if ($parent != 0):
						$template->setVar('PREFIX', 'sub');
					else:
						$template->setVar('PREFIX', '');
					endif;
					
					// Begin forum category, display all forums in the category, and end forum category
					$template->displayPage('forums_start');
					recursive_forum_display($category_info, $parent);
					$template->displayPage('forums_end');
				endif;
			endif;
		endif;
	endforeach;
	
	// Display threads within the selected forum if threads exist
	if (isset($threads)):
		
		// Set the forum ID back to the current forum
		$template->setVar('FORUM_ID', $forum[1][forum_id]);
		// Get the pages navigations
		$list_pages = list_pages($forum[1][forum_threads], $template->getVar('THREAD_DISPLAY_NUM'), 'forum');
			// Compile the page navigations and set the template variables
			$template->setVar('FORUM_PAGES', '[ ' . $list_pages[links] . ']');
			$template->setVar('FORUM_PAGES_OPTIONS', '<select class="list_pages_dropdown">' . $list_pages[options] . '</select>');
		
		
		// Make sure that the user has the authority to view the forum's thread; otherwise, just skip over
		if (has_authority('view', array('forum_id' => $_GET['forum']))):
		
			// Start the page
			$template->displayPage('threads_start');
			
			// For each thread, 
			foreach ($threads as $thread):
				
				// Make sure that the user has the authority to view the thread; otherwise, just skip over
				if (has_authority('view', array('forum_id' => $_GET['forum'], 'thread_id' => $thread['thread_id']))):
				
					// Set the template variables
					foreach ($thread as $key => $value):
						$template->setVar(strtoupper($key), $value);
					endforeach;
					
					// Display a message if necessary
					if ($thread[message]):
						// Display the error
						print '<div class="thread">';
						print ($thread[message]);
						print '</div>';
					else:
						
						// Alternate between two thread classes
						if ($alternating_thread%2 == 0):
							$template->setVar('ALTERNATING_THREAD', 'thread_alt_0');
						else:
							$template->setVar('ALTERNATING_THREAD', 'thread_alt_1');
						endif;
						$alternating_thread++;
					
						// Set up last updated information
						$last_updated = $forum_call->getThreadLastUpdated($template->getVar('THREAD_ID'));
						foreach ($last_updated[1] as $key => $value):
							$template->setVar(strtoupper($key), $value);
						endforeach;
					
						// Get the pages navigations, add 1 to thread replies to cover for the first post
						$list_pages = list_pages($thread[thread_replies] + 1, $template->getVar('POST_DISPLAY_NUM'), 'thread');
						// Compile the page navigations and set the template variables
						$template->setVar('THREAD_PAGES', '[ ' . $list_pages[links] . ']');
						
						// Display the page
						$template->displayPage('thread');
					endif;
				endif;
			endforeach;
			
			// End the page
			$template->displayPage('threads_end');
		
		else:
			print 'You do not have the authority to view the threads in this forum';
		endif;
		
	endif;

	// If the forum main page (all cats, forums, no threads) is shown, display Who's Online module
	if (!isset($_GET['forum']) && !isset($_GET['category']) && !isset($_GET['thread'])):
		whos_online();
	endif;

	$template->setVar('DB_CONSTRUCTS', $db_constructs);
	$template->setVar('DB_QUERIES', $db_queries);
	$template->setVar('PAGE_TIME_TO_LOAD', microtime()-$time);
	$template->displayPage('footer');
	unset($forum_call);
	unset($db);
?>