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
/admin_cp/settings.inc.php
******************************************
To grab and display the forum settings

Contents:

	change_settings_submit:
		updates settings info
		
	settings:
		displays settings page
******************************************/

	if (!defined('SCROLLIO')): 
		die('You are unauthorised to view this file.'); 
	endif;

	if (!member_is_admin($_SESSION['member_id'])):
		die('You are unauthorised to view this file.');
	endif;
	
function change_settings_submit() {
	global $dbc_calls;
	
	// For each setting, update the proper row in the forum_settings table
	foreach ($_POST as $key => $value):
		if ($key != 'change_settings_submit'):
			
			$key = strtoupper($key); 
			$values = array('setting' => $value);
			
			// If the update works, return success
			if ($dbc_calls->update('forum_settings', $values, 'setting_name = "' . $key . '"')):
				$success = 1;
			else:
				$error = 1;
			endif;
			
		endif;
	endforeach;
	
	// If any errors have occurred, display notice. Otherwise, success notice.
	if ($error == 1):
		return 'An error has occurred';
	elseif ($success == 1):
		return 'Your settings have been saved';
	endif;

	return false;
}

function settings() {
	
	// Initialize
		global $template, $dbc_calls;
	
		if (!isset($dbc_calls)):
			$dbc_calls = new dbc_calls();
		endif;
	
	// Grab the forum settings under MYSQLI_NUM
	$results = $dbc_calls->selectNum('SELECT * FROM ' . DB_PREFIX . '_forum_settings');
	
	// Set template variable for each result
	foreach ($results as $value):
		if ($value[2] == 1):
			$template->setVar($value[0],$value[1]);
		endif;
	endforeach;
	
	// Compile options for some items
	for ($i=1; $i <= 25; $i++):
	
		// Select the current value in the HTML
		if ($i == $template->getVar('POST_DISPLAY_NUM')):
			$num_post_options .= '<option value="' . $i . '" selected="selected">' . $i . '</option>';
		else:
			$num_post_options .= '<option value="' . $i . '">' . $i . '</option>';
		endif;
		
		// Select the current value in the HTML
		if ($i == $template->getVar('THREAD_DISPLAY_NUM')):
			$num_thread_options .= '<option value="' . $i . '" selected="selected">' . $i . '</option>';
		else:
			$num_thread_options .= '<option value="' . $i . '">' . $i . '</option>';
		endif;
	endfor;
	
	// Finish up with template variable
	$template->setVar('ADMIN_NUM_POSTS_OPTIONS', '<select id="post_display_num" name="post_display_num">' . $num_post_options . '</select>');
	$template->setVar('ADMIN_NUM_THREADS_OPTIONS', '<select id="thread_display_num" name="thread_display_num">' . $num_thread_options . '</select>');
	
	// Return the page
	return $template->displayPage('admin_settings', true);
}
	
?>