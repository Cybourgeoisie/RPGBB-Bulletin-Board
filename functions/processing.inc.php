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
/functions/processing.inc.php
******************************************
Contains the functions for the following:
deleting
editing
login
posting
registration

To delete posts, and threads if necessary.
******************************************/

if (!defined('SCROLLIO')): 
	die('You are unauthorised to view this file.'); 
endif;


// DELETE FUNCTION

function deleting($delete_info) {
	global $dbc_calls;
	
	// Check to make sure that the post is numeric
	if (!is_numeric($delete_info[p])):
		$result['process_error'] = 'An error has occurred; please try deleting again';
		return $result;
	endif;
	
	// Initialize
	$result = array();
	if (!isset($dbc_calls)):
		$dbc_calls = new dbc_calls;
	endif;
	
	// Grab the author ID
	$author = $dbc_calls->select('SELECT post_author FROM ' . DB_PREFIX . '_posts WHERE post_id='.$delete_info[p]);
	$author_id = $author[1][post_author];
	
	// Grab the current reply count - if it is 0, then delete the thread as well
	$thread = $dbc_calls->select('SELECT forum_id,thread_replies FROM ' . DB_PREFIX . '_threads WHERE thread_id='.$delete_info[thread]);
	$thread_posts = $thread[1][thread_replies];
	$forum_id = $thread[1][forum_id];
	
	// Delete the thread if necessary
	if ($thread_posts == 0):
		$delete_result .= $dbc_calls->query('DELETE FROM ' . DB_PREFIX . '_threads WHERE thread_id='.$delete_info[thread].' LIMIT 1');
		$delete_mode = 'thread';
	elseif ($thread_posts != 0):
		$delete_mode = 'post';
	endif;
	
	// Delete the post
	$delete_result .= $dbc_calls->query('DELETE FROM ' . DB_PREFIX . '_posts WHERE post_id='.$delete_info[p].' LIMIT 1');
	
	// Decrement member's post count, number of posts in the forum, number of posts in the thread, and number of threads in total (if appl.)
	$decrement_all = new post_counts();
	$decrement_all->update_all('-', $author_id, $forum_id, $delete_info[thread], $delete_mode);
	unset($decrement_all);
	
	// Return the results
	if ($delete_result):
		$result['process_success']['header'] = 'Success';
		$result['process_success'][] = 'The post was successfully deleted.';
		if ($delete_mode == 'post'):
			$result['process_success'][] = '<br /><a href="./thread.php?id=' . $delete_info['thread'] . '">Return to the thread.</a>';
		endif;
	else:
		$result['process_oddity'] = 'An error has occurred; please try deleting again';
	endif;
		
	return $result;
}



// EDIT POSTS

function editing($post_info) {
	global $dbc_calls;
	
	// Initialize. Set up dbc_calls and forms classes.
	$result = array();
	$post = TRUE;
	if (!isset($dbc_calls)):
		$dbc_calls = new dbc_calls;
	endif;
	$check = new forms();
	
	// Because we are editing a post, a post and thread must exist. The parent is the thread_id.
	$thread_id = $post_info['post_parent_id'];
	
	// Check if the thread's first post is being edited
	$post = $dbc_calls->select('SELECT post_id FROM ' . DB_PREFIX . '_posts WHERE thread_id = '.$thread_id.' ORDER BY post_id ASC LIMIT 1');
	$first_post = $post[1][post_id];
	
	// Run the checks
		// Make sure the post values are numeric
		if (!is_numeric($_POST[post_id]) || !is_numeric($_POST[thread_id])):
			$result['process_error'] = 'An error has occurred; please try deleting again';
			return $result;
		endif;
		
		// Clean up the posts and title
		$post_body = $check->text_filter($post_info['post_body']);
		$post_title = $check->text_filter($post_info['post_title']);
		
		// If there is no post body, flag
		if ($post_body == ''):
			$result['process_error'][] = 'Please enter content for your post';
			$post = FALSE;
		endif;
	
		// If the mode is thread and there is no post title, flag
		if ($first_post == $_POST[post_id] && ($post_title == '' || $post_title == ' ')):
			$result['process_error'][] = 'Please enter a title for your thread';
			$post = FALSE;
		endif;
	// End checks
	
	// If all of the checks cleared, then insert the user's information
	if ($post):
		
		// Update the post
		$edit_query = 'UPDATE ' . DB_PREFIX . '_posts SET post_title="' . $post_title . '", post_body="' . $post_body . '"';
		$edit_query .= ' WHERE post_id="' . $_POST[post_id] . '" && thread_id="' . $_POST[thread_id] . '"';
		$edit_result = $dbc_calls->query($edit_query);
		
		// If this is the thread's first post, update the thread title
		if ($first_post == $_POST[post_id]):
			$edit_query = 'UPDATE ' . DB_PREFIX . '_threads SET thread_title="' . $post_title . '" WHERE thread_id="' . $_POST[thread_id] . '"';
			$edit_result .= $dbc_calls->query($edit_query);	
		endif;
		
		// Return the results
		if ($edit_result):
			$result['process_success']['header'] = 'Success';
			$result['process_success'][] = 'The post was successfully edited.';
		else:
			$result['process_oddity'] = 'An error has occurred; please try posting again';
		endif;
	else:
		$result['process_error']['header'] = 'An error has occurred';
	endif;
	
	unset($check);
	
	return $result;
}



// LOG-IN FUNCTION

function login_check($log_info) {
	global $dbc_calls;
	
	// Initialize. Set classes.
	$result = array();
	$login = TRUE;
	if (!isset($dbc_calls)):
		$dbc_calls = new dbc_calls;
	endif;
	$check = new forms();
	
	// Run the checks
		// Clean and Validate the username
		$member_name = $check->text_filter($log_info['member_name']);
		$member_name_exists = $dbc_calls->exists($member_name,'member_name',DB_PREFIX.'_members');
		
		// Get the e-mail address for the salt, then salt and validate the password
		$member_email = $dbc_calls->get_value($member_name,'member_email','member_name',DB_PREFIX.'_members');
		$member_password_input = $check->hash($log_info['member_password']);
		$member_password_actual = $dbc_calls->get_value($member_name,'member_password','member_name',DB_PREFIX.'_members');
		$member_password_exists = $check->compare($member_password_input, $member_password_actual);
	
		// If the user is already logged in, fail
		if (isset($_SESSION['member_status'])):
			$result['process_error'][] = "You are already logged in as " . $_SESSION['member_name'];
			$login = FALSE;
		endif;
	
		// If the member name doesn't exist, feed warning
		if (!$member_name_exists):
			$result['process_error'][] = "The username was not found";
			$login = FALSE;
		endif;
		
		// If the password doesn't exist, flag
		if (!$member_password_exists):
			$result['process_error'][] = "The password entered does not match the password on file";
			$login = FALSE;
		endif;
	// End checks
	
	// If all of the checks cleared, then insert the user's information
	if ($login):
		// Grab the user's information
		$member_info = $dbc_calls->select('SELECT * FROM ' . DB_PREFIX . '_members WHERE member_name="' . $member_name . '"');
	
		// Set the session data and the cookie (if opted) for one month (30 days, really)
		if (isset($_POST['set_remember_me'])):
			setcookie('remember_me', session_id(), time()+60*60*24*30, '/', NULL, NULL, TRUE);
		endif;
		$_SESSION['member_name'] = $member_info[1]['member_name'];
		$_SESSION['member_id'] = $member_info[1]['member_id'];
		$_SESSION['member_status'] = $member_info[1]['member_status'];
		
		// Return results				
		if (!$result[0]):
			$result['process_success']['header'] = 'You are now logged in';
			$result['process_success'][] = 'Welcome back to ' . FORUM_NAME;
		else:
			$result['process_oddity'] = 'An error has occurred; please try logging in again';
		endif;
	else:
		$result['process_error']['header'] = 'An error has occurred';
	endif;
	
	unset($check);
	
	return $result;
}



// POSTING FUNCTION

function posting($post_info) {
	global $dbc_calls;
	
	// Initialize. Set the classes.
	$result = array();
	$post = TRUE;
	if (!isset($dbc_calls)):
		$dbc_calls = new dbc_calls;
	endif;
	$check = new forms();
	
	// Run the checks
		// Clean up the posts and title
		$post_body = $check->text_filter($post_info['post_body']);
		$post_title = $check->text_filter($post_info['post_title']);
		
		// If there is no post body, flag
		if ($post_body == ''):
			$result['process_error'][] = 'Please enter content for your post';
			$post = FALSE;
		endif;
	
		// If the mode is thread and there is no post title, flag
		if ($post_info['post_mode'] == 'thread' && ($post_title == '' || $post_title == ' ')):
			$result['process_error'][] = 'Please enter a title for your thread';
			$post = FALSE;
		endif;
	// End checks
	
	// Because the insert function handles the text filter, remove the filtering
	$post_body = $post_info['post_body'];
	$post_title = $post_info['post_title'];
	
	// If all of the checks cleared, then insert the information
	if ($post):
	
		// If the mode is 'post', set the thread_id to the parent, and grab the forum from the thread
		if ($post_info['post_mode'] == 'post'):
			// Give the parent ID to the thread_id, get the forum_id
			$thread_id = $post_info['post_parent_id'];
			$result_forum = $dbc_calls->select('SELECT forum_id FROM ' . DB_PREFIX . '_threads WHERE thread_id = "' . $thread_id . '"');
			
			// If the forum ID is found, grab it; if the forum_id is not found, flag it
			if ($result_forum != false):
				$forum_id = $result_forum[1]['forum_id'];
			else:
				$result['process_error'][] = "The forum you are attempting to post this in does not exist";
				$post = FALSE;
			endif;
			
		// If the mode is 'thread', insert the information to the forum, then grab the new thread ID
		elseif ($post_info['post_mode'] == 'thread'):
			// Give the parent id to the forum_id
			$forum_id = $post_info['post_parent_id'];
			
			// Prepare and insert values as a new thread
			$values = array(forum_id=>$forum_id, thread_title=>$post_title, thread_time=>'UTC_TIMESTAMP()', thread_author=>$_SESSION[member_id]);
			$new_topic_result = $dbc_calls->insert('threads', $values);
			
			// We need to grab the thread
			$result_thread = $dbc_calls->select(
				'SELECT thread_id FROM ' . DB_PREFIX . '_threads WHERE thread_author = "'.$_SESSION[member_id].'" ORDER BY thread_id DESC LIMIT 1');
			
			// If the thread ID is found, grab it; if the thread_id is not found, flag it
			if ($result_thread != false):
				$thread_id = $result_thread[1]['thread_id'];
			else:
				$result['process_error'][] = "The database was unable to create your topic";
				$post = FALSE;
			endif;
		endif;
		
		// Prepare and send the information to the database
		$values = array(thread_id=>$thread_id, forum_id=>$forum_id, post_title=>$post_title, 
						post_body=>$post_body, post_author=>$_SESSION[member_id], post_time=>'UTC_TIMESTAMP()');
		$insert_result = $dbc_calls->insert('posts', $values);
		
		// Increment user's post count, number of posts in the forum, number of posts in the thread, and number of threads in total (if appl.)
		$increment_all = new post_counts();
		$increment_all->update_all('+', $_SESSION['member_id'], $forum_id, $thread_id, $post_info['post_mode']);
		unset($increment_all);
		
		// Return the results
		if ($insert_result):
			$result['process_success']['header'] = 'Success';
			$result['process_success'][] = 'Your post has been added.';
		else:
			$result['process_oddity'] = 'An error has occurred; please try posting again';
		endif;
	else:
		$result['process_error']['header'] = 'An error has occurred';
	endif;
	
	unset($check);
	
	return $result;
}



// REGISTER FUNCTION

function registration_check($reg_info) {
	global $dbc_calls;
	
	// Initialize. Set classes.
	$result = array();
	$register = TRUE;
	if (!isset($dbc_calls)):
		$dbc_calls = new dbc_calls;
	endif;
	$check = new forms();
	
	// Run the checks
		// Filter and check the member's name
		$member_name = $check->text_filter($reg_info['member_name']);
		$member_name_exists = $dbc_calls->exists($member_name,'member_name',DB_PREFIX.'_members');
		
		// Validate and check the e-mail
		$member_email = $reg_info['member_email'];
		$member_email_valid = $check->email_validate($member_email);
		$member_email_exists = $dbc_calls->exists($member_email,'member_email',DB_PREFIX.'_members');
		
		// Compare and hash the password
		$member_password_valid = $check->password_validate($reg_info['member_password']);
		$member_password_comparison = $check->compare($reg_info['member_password'], $reg_info['member_password_check']);
		$member_password = $check->hash($reg_info['member_password']);
	
		// Validate all returned values
		// Check to ensure a member name was entered
		if (!($member_name)):
			$result['process_error'][] = 'Please enter a username';
			$register = FALSE;
		endif;
		
		// Check to ensure the member name was not taken
		if ($member_name_exists):
			$result['process_error'][] = 'This username is already taken';
			$register = FALSE;
		endif;
		
		// Check to ensure the e-mail address is valid (also checks to see if it exists)
		if (!($member_email_valid)):
			$result['process_error'][] = 'Please enter a valid e-mail address';
			$register = FALSE;
		endif;
		
		// Check to ensure the e-mail address is not already used
		if ($member_email_exists):
			$result['process_error'][] = 'This e-mail address is already in use';
			$register = FALSE;
		endif;
		
		// Check to ensure the password is valid
		if (!($member_password_valid)):
			$result['process_error'][] = 'Please enter a password with no white space';
			$register = FALSE;
		endif;
		
		// Check to ensure the password and password check match
		if (!($member_password_comparison)):
			$result['process_error'][] = 'Your password entered does not match the password checked';
			$register = FALSE;
		endif;
	// End checks
	
	// If all of the checks cleared, then insert the user's information
	if ($register):
	
		// Insert the values to the DB. Don't use the 'insert' function because most of this shouldn't be filtered further.
		$query = 'INSERT INTO ' . DB_PREFIX . '_members (member_name, member_password, member_email, member_register_date) VALUES ';
		$query .= ' ("' .$member_name .'", "' . $member_password . '", "' . $member_email .'", UTC_TIMESTAMP())';
		$insert_result = $dbc_calls->query($query);		
		
		// Return the results								
		if ($insert_result):
			$result['process_success']['header'] = 'You are now registered with ' . FORUM_NAME;
			$result['process_success'][] = 'You are now registered with ' . FORUM_NAME;
		else:
			$result['process_oddity'] = 'An error has occurred; please try registering again';
		endif;
	else:
		$result['process_error']['header'] = 'An error has occurred';
	endif;
	
	unset($check);
	
	return $result;
}



// SEND PRIVATE MESSAGE

function send_private_message($info) {
	global $dbc_calls;
	
	// Initialize. Set the classes.
	$result = array();
	$pm = TRUE;
	if (!isset($dbc_calls)):
		$dbc_calls = new dbc_calls;
	endif;
	$check = new forms();
	
	// Run the checks
		// Clean up the posts and title
		$pm_body = $check->text_filter($info['pm_body']);
		$pm_title = $check->text_filter($info['pm_title']);
		
		// If there is no post body, flag
		if ($pm_body == ''):
			$result['process_error'][] = 'Please enter content for your message';
			$pm = FALSE;
		endif;
	
		// If the mode is thread and there is no post title, flag
		if ($pm_title == '' || $pm_title == ' '):
			$result['process_error'][] = 'Please enter a title for your message';
			$pm = FALSE;
		endif;
		
		// If any of the numeric values were messed around, flag
		if (!is_numeric($info[pm_re]) || !is_numeric($info[pm_to]) || !is_numeric($info[pm_from])):
			$result['process_error'][] = 'Please don\'t tamper with the POST variables';
			$pm = FALSE;
		endif;
	// End checks
	
	// Because the insert function handles the text filter, remove the filtering
	$pm_body = $info['pm_body'];
	$pm_title = $info['pm_title'];
	
	// If all of the checks cleared, then insert the information
	if ($pm):
		// Prepare and send the information to the database
		$values = array(pm_re=>$info[pm_re], pm_from=>$info[pm_from], pm_to=>$info[pm_to], pm_received=>'0', 
						pm_time=>'UTC_TIMESTAMP()', pm_title=>$info[pm_title], pm_body=>$info[pm_body]);
		$insert_result = $dbc_calls->insert('private_messages', $values);
		
		// Return the results
		if ($insert_result):
			$result['process_success']['header'] = 'Success';
			$result['process_success'][] = 'Your private message has been sent.';
		else:
			$result['process_oddity'] = 'An error has occurred; please try sending again';
		endif;
	else:
		$result['process_error']['header'] = 'An error has occurred';
	endif;
	
	unset($check);
	
	return $result;
}

?>