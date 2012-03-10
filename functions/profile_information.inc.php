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
/functions/profile_information.inc.php
******************************************
Contents:

	profile_information: 
		grabs all profile information
	profile_information_check: 
		updates or inserts profile information to members_to_info

******************************************/

if (!defined('SCROLLIO')): 
	die('You are unauthorised to view this file.'); 
endif;

function profile_information($user_id) {
	// Initialize
	global $template, $check;
	if (!isset($dbc_calls)):
		$dbc_calls = new dbc_calls();
	endif;
	if (!isset($check)):
		$check = new forms();
	endif;
	
	// Grab all profile questions and user responses
	$ques = $dbc_calls->select('SELECT * FROM ' . DB_PREFIX . '_members_information WHERE info_status = 1');
	$info = $dbc_calls->getMemberProfileInfo($_SESSION[member_id]);
	
	// Initialize and display all questions and user responses
	$i = 0;
	$template->setVar('MEMBER_ID', $_SESSION['member_id']);
	foreach ($ques as $question):
		$template->setVar('ITERATION', $i);
		$template->setVar('INFO_ID', $question[info_id]);
		$template->setVar('INFO_TITLE', $question[info_title]);
		$template->setVar('INFO_RESPONSE', $info[$_SESSION[member_id]][$question[info_id]][info_response]);
		
		// Set TEXT_FIELD to input text or textarea
		if ($question[info_type]==0):
			$field = '<input type="text" id="info_response_'.$i.'" name="info_response_'.$i.'" size="60" value="'.$template->getVar('INFO_RESPONSE').'" />';
		else:
			$field = '<textarea id="info_response_'.$i.'" name="info_response_'.$i.'" cols="60" rows="4">'.$template->getVar('INFO_RESPONSE').'</textarea>';
		endif;
		$template->setVar('TEXT_FIELD', $field);
		
		// Run the page through the template engine, grab output; increment $i
		$page .= $template->displayPage('usercp_info_td', true);
		$i++;
	endforeach;
	
	// Set the template variable for the profile information questions and return the page
	$template->setVar('PROFILE_INFORMATION_QUESTIONS', $page);
	return $template->displayPage('usercp_profile', true);
}

function profile_information_check($info) {
	// Initialize
	global $dbc_calls, $check;
	$result = array();
	$update = FALSE;
	if (!isset($dbc_calls)):
		$dbc_calls = new dbc_calls();
	endif;
	if (!isset($check)):
		$check = new forms();
	endif;
	
	// Run through the profile information values, clean them up, and insert or update them
		// Clean up the posts and title
		for($j = 0; $j < (count($info)-2)/2; $j++):
			$info_response = $check->text_filter($info[info_response_.$j]);
			$info_id = $info[info_id_.$j];
			
			$where = ' WHERE (member_id = "' . $info[member_id] . '" AND info_id = "' . $info_id . '")';
			$select = 'SELECT * FROM ' . DB_PREFIX . '_members_to_info' . $where;
			
			if ($dbc_calls->select($select, 1)):
				$query = 'UPDATE ' . DB_PREFIX . '_members_to_info SET info_response = "' . $info_response . '"' . $where;
			else:
				$query = 'INSERT INTO ' . DB_PREFIX . '_members_to_info  (info_id, member_id, info_response) VALUES ("';
				$query .= $info_id . '", "' . $info[member_id] . '", "' . $info_response . '")';
			endif;
						
			$info_result = $dbc_calls->query($query);
		endfor;
	// End run through
	
	// If all of the inserts and updates cleared, then insert the user's information
	if ($info_result):
		$result['update_success'] = "Your changes have been saved.";
	else:
		$result[] = 'An error has occurred; please try again';
	endif;
		
	return $result;
	
}

?>