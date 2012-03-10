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
	define('PAGE_NAME', 'Processing');
	$sess_write = TRUE;
	require_once(PATH_TO_FILES . 'common.inc.php');
	require_once(PATH_TO_FILES . 'functions/processing.inc.php');

/******************************************
Figure out what the form is, then process the sucker.
If the form doesn't exist, warn the user.

Processes:
	Posting or Editing Replies / Threads
	Deleting Posts / Threads
	Log Off submission
	Registration submission
	Log In submission
	
******************************************/

// Sending a PM
if ($_POST['pm_submit']):
	$pm_info = array(
		'pm_title' => $_POST['pm_title'], 
		'pm_body' => $_POST['pm_body'], 
		'pm_re' => $_POST['pm_re'],
		'pm_to' => $_POST['pm_to'],
		'pm_from' => $_POST['pm_from']);
		
	$process_result = send_private_message($pm_info);
endif;


// Making or editing a post
if ($_POST['post_submit']):
	$post_info = array(
		'post_title' => $_POST['post_title'], 
		'post_body' => $_POST['post_content'], 
		'post_parent_id' => $_POST['parent_id'],
		'post_mode' => $_POST['post_mode']);
	
	if ($_POST['post_type']=='edit'):
		// Make sure that we've got all of the valid information
		if (is_numeric($_POST[post_id]) && is_numeric($_POST[thread_id])):
			
			// Determine if the user has the authority 
			if (has_authority('edit', array('post_id' => $_POST[post_id]))):
				// Run the editing function
				$process_result = editing($post_info);
			else:
				$process_result['process_error'][] = 'You do not have the authority to edit this post.';
			endif;
		else:
			$process_result['process_error'][] = 'You can only edit a valid post.';
		endif;
	else:
		// Run the posting function
		$process_result = posting($post_info);
	endif;
	
	$process_post = true;
endif;

// Deleting a post
if (isset($_GET['delete'])):
	if (is_numeric($_GET[p]) && is_numeric($_GET[thread])):
		
		// Determine if the user has the authority to delete the thread
		if (has_authority('delete', array('thread_id'=>$_GET[thread],'post_id'=>$_GET[p]))):
			// Run the editing function
			$process_result = deleting($delete_info);
			$process_delete = true;
		else:
			$process_result['process_error'][] = 'You do not have the authority to delete this post.';
		endif;
	else:
		$process_result['process_error'][] = 'You can only delete a valid post.';
	endif;
endif;

// logging off
if (isset($_GET['logoff'])):
	// Reset the saved information
	session_destroy();
	setcookie('remember_me');
	// Set a message
	$process_result['process_success']['header'] = "You're now logged off";
	$process_result['process_success'][] = "Have a fine day";
	$process_logoff = true;
endif;

// Registering or Logging On
if ($_SESSION['member_status'] == 0 || $_SESSION['member_status'] == NULL):
	// Registering
	if (isset($_POST['reg_submit'])):
		$reg_info = array(
			'member_name' => $_POST['reg_member_name'], 
			'member_password' => $_POST['reg_member_password'],
			'member_password_check' => $_POST['reg_member_password_check'], 
			'member_email' => $_POST['reg_member_email']);
			
		// Run the registration check, grab the outcome
		$process_result = registration_check($reg_info);
		$process_registration = true;

	elseif (isset($_POST['log_submit'])):
		$log_info = array(
			'member_name' => $_POST['log_member_name'], 
			'member_password' => $_POST['log_member_password']);

		// Run the login check, receive the outcome
		$process_result = login_check($log_info);
		$process_login = true;

	endif;
endif;


// Moved the session_write_close to avoid header issues
session_write_close();
require_once(PATH_TO_FILES . 'functions/template_defines.inc.php');
$template->displayPage('header');
	
	// Grab all results
	if ($process_result): 
	 	foreach($process_result as $key => $value):
	 		// Handle Oddity
			if (is_numeric($key)):
				$template->setVar('PROCESS_ODDITY', $value);
			else:
				// Handle Success, Error
				foreach($value as $key_2 => $value_2):
					if (is_numeric($key_2)):
						if ($template->getVar(strtoupper($key)) == ''):
							$template->setVar(strtoupper($key), $value_2);
						else:
							$template->setVar(strtoupper($key), $template->getVar(strtoupper($key)) . '<br />' . $value_2);
						endif;
					else:
						$template->setVar('PROCESS_HEADER', '<h3>' . $value_2 . '</h3>');
					endif;
				endforeach;
			endif;
		endforeach; 
	 endif;
	
	// If the process was registration, display the login screen
	if ($process_registration == true):
		if (isset($process_result['process_success'])):
			$full_login = full_login();
			$template->setVar('FULL_LOGIN', $full_login);
		endif;
	// If the process was adding or editing a post, allow the user to jump to it.
	elseif ($process_post == true && !isset($process_result['process_error']['header'])):
		
		// Display most recent post if this were a new post
		if (!is_numeric($_POST[post_id])):
			
			if (!isset($dbc_calls)):
				$dbc_calls = new dbc_calls();
			endif;
			
			// Set the location of the most recent post by the member
			$post_loc = $dbc_calls->select(
			'SELECT thread_id, post_id FROM ' . DB_PREFIX . '_posts WHERE post_author = "'.$_SESSION['member_id'].'" ORDER BY post_id DESC LIMIT 1');
			
			// Determine the page
			$start = $dbc_calls->select('SELECT thread_replies FROM ' . DB_PREFIX . '_threads WHERE thread_id = "' . $post_loc[1][thread_id] . '"');
			$start = $start[1][thread_replies] + 1;
			if ($start%POST_DISPLAY_NUM == 0):
				$start = $start - POST_DISPLAY_NUM;
			else:
				$start = ((int)($start/POST_DISPLAY_NUM))*POST_DISPLAY_NUM;
			endif;
		
		// Display the proper post of this were an edit	
		elseif (is_numeric($_POST[thread_id]) && is_numeric($_POST[post_id])):
			$post_loc = array (1 => array ('thread_id' => $_POST[thread_id], 'post_id' => $_POST[post_id]));
			$start = 0;
		endif;
		
		if (isset($post_loc)):
			$link = '<a href="./thread.php?id=' . $post_loc[1]['thread_id'] . '&start=' . $start . '#' . $post_loc[1]['post_id'] . '">Click here to view your post.</a>';
			$template->setVar('LINK_TO_POST', '<br /><br />' . $link);
		endif;
	endif;
	
	// Set the template variable for the message if necessary
	if (isset($process_delete) || isset($process_login) || isset($process_logoff)):
		$template->setVar('SUCCESS_MESSAGE', '<a href="./forum.php">Return to the forum</a>');
	endif;
	
	// Display the page
	$template->displayPage('process_start');
	$template->displayPage('process');	 
	$template->displayPage('process_end');
	 
	$template->setVar('DB_CONSTRUCTS', $db_constructs);
	$template->setVar('DB_QUERIES', $db_queries);
	$template->setVar('PAGE_TIME_TO_LOAD', microtime()-$time);
	$template->displayPage('footer');
	unset($forum_call);
	unset($db);
	
?>