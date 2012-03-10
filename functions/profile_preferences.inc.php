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
/functions/profile_preferences.inc.php
******************************************
Displays the user information for the signature and avatar

Contents:
	profile_preferences:
		grabs the information, displays the preferences page
	
	profile_pref_check:
		updates the preferences
	
******************************************/

if (!defined('SCROLLIO')): 
	die('You are unauthorised to view this file.'); 
endif;

function profile_preferences($user_id) {
	
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
	
	// Set the template variables
	$template->setVar('TIMEZONE_SELECTION', list_timezones(HOUR_DIFF_FROM_GMT));
	$template->setVar('MEMBER_ID', $user_id);
	
	// Return the page
	return $template->displayPage('usercp_preferences', true);
}

function profile_pref_check() {

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
		// If the timezone is not numeric or a valid integer, flag
		if (!is_numeric($_POST[member_timezone]) || !($_POST[member_timezone] >= -12 && $_POST[member_timezone] <= 13)):
			$update = false;
		endif;
	// End checks
	
	// If all of the checks cleared, then insert the user's information
	if ($update):
		// Compile the update query and run it, then grab the result
		$update_query = 'UPDATE ' . DB_PREFIX . '_members SET member_timezone = "' . $_POST[member_timezone] . '"';
		$update_query .= ' WHERE member_id='. $_SESSION[member_id];
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