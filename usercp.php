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
	define('PAGE_NAME', 'Index');
	require_once(PATH_TO_FILES . 'common.inc.php');
	$template->displayPage('header');
	include_once(PATH_TO_FILES . 'functions/profile_signature_avatar.inc.php');
	include_once(PATH_TO_FILES . 'functions/profile_information.inc.php');
	include_once(PATH_TO_FILES . 'functions/private_messages.inc.php');
	include_once(PATH_TO_FILES . 'functions/profile_preferences.inc.php');

// Make sure that the user is logged in
if (isset($_SESSION['member_status'])):
	// Initialize
	$message = '';
	
	// If there are POST values, determine what the user is trying to do, then run the function
	if (isset($_POST[profile_sigav_submit])):
		// Signature and Avatar updates
		$profile_sigav = array(
			'member_id' => $_POST['member_id'], 
			'member_avatar' => $_POST['member_avatar'], 
			'member_signature' => $_POST['member_signature']);
		$result = profile_sigav_check($profile_sigav);
	elseif (isset($_POST[profile_info_submit])):
		// Profile information updates
		foreach ($_POST as $key => $value):
			$information[$key] = $value;
		endforeach;
		$result = profile_information_check($information);
	elseif (isset($_POST[profile_pref_submit])):
		// Preference updates
		$result = profile_pref_check();
	endif;
	
	// If POST variables were run, report the results
	if ($result):
		foreach($result as $result_message):
			$message = "<br />$result_message";
		endforeach;
	endif;
	
	// Run the correct function for the page GET mode
	switch($_GET[mode]):
		case 'sigav':
			$page = profile_signature_avatar($_SESSION['member_id']);
		break;
		
		case 'profile':
			$page = profile_information($_SESSION['member_id']);
		break;
		
		case 'preferences':
			$page = profile_preferences($_SESSION['member_id']);
		break;
		
		case 'messages':
			$page = private_messages($_SESSION['member_id']);
		break;
		
		default:
			$page = profile_signature_avatar($_SESSION['member_id']);
		break;
	endswitch;
	
	// Set up the template variables, then run the templates
	$template->setVar('RESULT_MESSAGE', $message);
	$template->setVar('USERCP_PAGE', $page);

	$template->displayPage('usercp_start');
	$template->displayPage('usercp');
	$template->displayPage('usercp_end');
	
else:
	// If the user is not logged in or active, block
	print "<div class=\"user_menu\">Please log in or register to use the User Control Panel.</div>";
endif;

	$template->setVar('DB_CONSTRUCTS', $db_constructs);
	$template->setVar('DB_QUERIES', $db_queries);
	$template->setVar('PAGE_TIME_TO_LOAD', microtime()-$time);
	$template->displayPage('footer');
	unset($db);
?>