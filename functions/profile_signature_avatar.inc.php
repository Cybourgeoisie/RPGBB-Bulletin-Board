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
/functions/profile_signature_avatar.inc.php
******************************************
Displays the user information for the signature and avatar

Contents:
	profile_signature_avatar:
		grabs the information, displays the sigav page
	
	profile_sigav_check:
		updates the signature and avatar information
	
******************************************/

if (!defined('SCROLLIO')): 
	die('You are unauthorised to view this file.'); 
endif;

function profile_signature_avatar($user_id) {
	
	// Initialize
		global $template, $check;
	
		if (!isset($dbc_calls)):
			$dbc_calls = new dbc_calls();
		endif;
		
		if (!isset($check)):
			$check = new forms();
		endif;
	
	// Grab signature and avatar information
	$member_info = $dbc_calls->getMemberInfo($user_id);
	$member_avatar = $check->url_prepare($member_info[1][member_avatar]);
	$member_signature = $check->text_prepare($member_info[1][member_signature]);
	
	// Set the template variables
	$template->setVar('AVATAR', $member_avatar);
	$template->setVar('SIGNATURE', $member_signature);
	$template->setVar('MEMBER_ID', $user_id);
	
	// Return the page
	return $template->displayPage('usercp_sigav', true);
}

function profile_sigav_check($profile_info) {

	// Initialize
		$result = array();
		$update = TRUE;
		global $template, $check;
	
		if (!isset($dbc_calls)):
			$dbc_calls = new dbc_calls();
		endif;
		
		if (!isset($check)):
			$check = new forms();
		endif;
	
	// Run the checks
		// Clean up the posts and title
		$member_avatar = $check->url_filter($profile_info['member_avatar']);
		$member_signature = $check->text_filter($profile_info['member_signature']);
	// End checks
	
	// If all of the checks cleared, then insert the user's information
	if ($update):
		// Compile the update query and run it, then grab the result
		$update_query = 'UPDATE ' . DB_PREFIX . '_members SET member_avatar = "' . $member_avatar . '", member_signature = "' . $member_signature . '"';
		$update_query .= ' WHERE member_id='. $profile_info[member_id];
		$update_result = $dbc_calls->query($update_query);		
		
		// Determine the message by the result
		if ($update_result):
			$result['update_success'] = "Your changes have been saved.";
		else:
			$result[] = 'An error has occurred; please try again';
		endif;
	endif;
		
	return $result;
}

?>