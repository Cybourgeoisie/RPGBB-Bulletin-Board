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
/functions/private_messages.inc.php
******************************************
Displays the private messages page of the UCP

Contents:
	
	private_messages:
		Determines the page to display
		
	get_private_messages:
		Displays the private messages in the inbox, outbox, or sentbox
	
	read_private_message:
		Displays the information for a single private message, including a string of responses
	
	write_private_messae:
		Displays the write new private message page
	
******************************************/

if (!defined('SCROLLIO')): 
	die('You are unauthorised to view this file.'); 
endif;

function private_messages($user_id) {
	
	// Initialize
		global $dbc_calls, $template;
		
		if (!isset($dbc_calls)):
			$dbc_calls = new dbc_calls();
		endif;
	
	// Find out what the user wants to view; determined by GET 'type'
	switch ($_GET['type']):
		case 'in':
			$page = get_private_messages('WHERE pm_to = ' . $user_id, 'from');
		break;
		
		case 'out':
			$page = get_private_messages('WHERE pm_received = 1 AND pm_from = ' . $user_id, 'to');
		break;
		
		case 'sent':
			$page = get_private_messages('WHERE pm_received = 0 AND pm_from = ' . $user_id, 'to');
		break;
		
		case 'write':
			$page = write_private_message($user_id);
		break;
		
		case 'read':
			// Make sure that the user has the ability to view the private message.
			if (is_numeric($_GET[id]) && is_numeric($_GET[to]) && is_numeric($_GET[from])):
				$page = read_private_message($_GET[id], $_SESSION[member_id]);
			else:
				$page = 'Invalid private message values provided';
			endif;
		break;
		
		default:
			return 'Invalid message type specified';
		break;
		
	endswitch;
	
	return $page;
}

function get_private_messages($where, $from_or_to = 'from') {
	
	// Initialize
		global $dbc_calls, $template;
		
		if (!isset($dbc_calls)):
			$dbc_calls = new dbc_calls();
		endif;
	
	// Grab all messages, if none exist, bypass the following
	if ($messages = $dbc_calls->select('SELECT *, DATE_FORMAT(pm_time + INTERVAL ' . HOUR_DIFF_FROM_GMT . ' HOUR, "%a, %b %e, %Y at %l:%i %p") as pm_time FROM ' . DB_PREFIX . '_private_messages ' . $where . ' ORDER BY pm_id DESC')):
		
		// The Title for the Addressed or the Addresser
		if ($from_or_to == 'from'):
			$add = 'Addressee';
		else:
			$add = 'Addresser';
		endif;
		$template->setVar('ADDRESSED', $add);
		$template->setVar('FROM_OR_TO', $from_or_to);
		
		// Grab all the messages, line them up
		foreach ($messages as $message):
			if ($from_or_to == 'from'):
				$name = $dbc_calls->select('SELECT member_name FROM ' . DB_PREFIX . '_members WHERE member_id = ' . $message[pm_from]);
			else:
				$name = $dbc_calls->select('SELECT member_name FROM ' . DB_PREFIX . '_members WHERE member_id = ' . $message[pm_to]);
			endif;
			$template->setVar('PM_MEMBER_NAME', $name[1][member_name]);
			
			// Set the Template variables for each of the items in message
			foreach($message as $key => $value):
				$template->setVar(strtoupper($key), $value);
			endforeach;
			
			// If the PM is not read and this letter is in the inbox, add a NEW icon
			$template->setVar('NEW_OR_READ', '');
			if ($message[pm_received] == 0 && $from_or_to == 'from'):
				$template->setVar('NEW_OR_READ', 'NEW!');
			endif;
			
			// Collect the PM information for the row
			$list .= $template->displayPage('usercp_private_messages_listing', true);
		endforeach;
		
		// Collect the rows of PM information, display the page
		$template->setVar('PRIVATE_MESSAGES_LISTING', $list);
		return $template->displayPage('usercp_private_messages', true);
	endif;
	
	// Only is met if no messages are found
	return 'You have no messages in this folder';
}

// For a single Private Message to be displayed
function read_private_message($id, $user) {

	// Initialize
		global $dbc_calls, $template, $check;
		
		if (!isset($dbc_calls)):
			$dbc_calls = new dbc_calls();
		endif;
	
		if (!isset($check)):
			$check = new forms();
		endif;
	
	// Grab the single private messae if the exists and if the user can view it
	$select = 'SELECT *, DATE_FORMAT(pm_time + INTERVAL ' . HOUR_DIFF_FROM_GMT . ' HOUR, "%a, %b %e, %Y at %l:%i %p") as pm_time FROM ' . DB_PREFIX . '_private_messages WHERE pm_id="' . $id . '" AND (pm_to="'.$user.'" OR pm_from="'.$user.'")';
	if ($message = $dbc_calls->select($select)):
		
		// Update the thread if necessary
		if ($message[1][pm_received]==0 && $message[1][pm_to] == $user):
			$dbc_calls->query('UPDATE ' . DB_PREFIX . '_private_messages SET pm_received = 1 WHERE pm_id = ' . $message[1][pm_id]);
		endif;
		
		// Grab the member names from the from and to information
		$from = $dbc_calls->select('SELECT * FROM ' . DB_PREFIX . '_members WHERE member_id = "' . $message[1][pm_from] . '"');
		$to = $dbc_calls->select('SELECT * FROM ' . DB_PREFIX . '_members WHERE member_id = "' . $message[1][pm_to] . '"');
		
		// Set the variables
		foreach ($message[1] as $key => $value):
			$template->setVar(strtoupper($key), $value);
		endforeach;
		
		// Set or modify values
		$template->setVar('TO_NAME', $to[1][member_name]);
		$template->setVar('FROM_NAME', $from[1][member_name]);
		
		// Append the img src to the Avatar if it exists
		if ($from[1][member_avatar]):
			$avatar = "<img src=\"" . $check->url_prepare($from[1][member_avatar]) . "\" class=\"post_avatar\" /><br />";
			$template->setVar('AVATAR', $avatar);
		else:
			$template->setVar('AVATAR', '');
		endif;
		
		// Edit the PM to allow for new line breaks and BBCode
		$pm_body = $check->long_prepare($template->getVar('PM_BODY'));
		$template->setVar('PM_BODY',$pm_body);
		
		// Run the page, collect the output
		$page .= $template->displayPage('usercp_read_pm', 1);
		
		// If this PM is part of a chain of responses, run this same function again
		if ($message[1][pm_re] != 0):
			$page .= '<br /><br /><hr /><strong>Previous Message:</strong><br /><br />';
			$page .= read_private_message($message[1]['pm_re'], $user);
		endif;
		
		// Return the information
		return $page;
	endif;
	
	return 'You are unauthorized to view this message';
}

function write_private_message($user) {
	
	// Initialize
		global $dbc_calls, $template, $check;
		
		if (!isset($dbc_calls)):
			$dbc_calls = new dbc_calls();
		endif;
	
		if (!isset($check)):
			$check = new forms();
		endif;
	
	// Grab the to if it exists
	if (is_numeric($_GET['to'])):
		if ($dbc_calls->select('SELECT member_name FROM ' . DB_PREFIX . '_members WHERE member_id =' . $_GET[to])):
			$pm_to = $_GET['to'];
		else:
			$pm_to = NULL;
		endif;
	endif;
	
	// Set user id of recipient
	$template->setVar('USER_ID', $user);
	
	// Set up the User options
	// Temporary until AJAX fill-in is made
	$members = $dbc_calls->select('SELECT member_id, member_name FROM ' . DB_PREFIX . '_members ORDER BY member_name ASC');
	foreach ($members as $member):
		if ($pm_to == $member[member_id]):
			$members_options .= '<option value="' . $member[member_id] . '" selected="selected">' . $member[member_name] . '</option>';
		else:
			$members_options .= '<option value="' . $member[member_id] . '">' . $member[member_name] . '</option>';
		endif;
	endforeach;
	$template->setVar('MEMBERS_OPTIONS', $members_options);
	
	// Set up the default title if necessary
	if ($_GET['title']):
		$template->setVar('PM_TITLE', $check->url_prepare($_GET['title']));
	else:
		$template->setVar('PM_TITLE', '');
	endif;
	
	// If this page is a response, set this template variable
	if (is_numeric($_GET['re'])):
		$template->setVar('PM_RE', $_GET['re']);
	else:
		$template->setVar('PM_RE', '0');
	endif;
	
	return $template->displayPage('usercp_write_pm' ,1);
}

?>