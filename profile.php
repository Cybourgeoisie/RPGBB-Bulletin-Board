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
	define('PAGE_NAME', 'Memberlist');
	require_once(PATH_TO_FILES . 'common.inc.php');
	$template->displayPage('header');

// Ensure that the $_GET[m] is numeric before querying
if (is_numeric($_GET[m])):
	// Initialize
	if (!isset($dbc_calls)):
		$dbc_calls = new dbc_calls;
	endif;
	if (!isset($check)):
		$check = new forms;
	endif;
	if (!isset($template)):
		$template = new template;
	endif;
	
	// If the information exists, display the profile
	if ($member = $dbc_calls->getMemberInfo($_GET[m])):
		// Set the template variables
		foreach($member[1] as $key => $info):
			$template->setVar(strtoupper($key), $info);
		endforeach;
		
		// Grab the user's info
		$member_profile_info = $dbc_calls->getMemberProfileInfo($_GET[m]);
		if ($member_profile_info):
			foreach($member_profile_info[$member[1][member_id]] as $info_id => $info):
				// If the user has provided a response, then display
				if (trim($info[info_response]) != '' && trim($info[info_title]) != ''):
					$list .= '<strong>' . $info[info_title] . '</strong>: ' . $info[info_response] . '<br />';
				endif;
			endforeach;
		else:
			$list = 'This member has not provided any information';
		endif;
		
		// Set the template variables for display
		$template->setVar('MEMBER_PROFILE_INFORMATION_LIST', $list);
		$avatar = $check->url_prepare($template->getVar('MEMBER_AVATAR'));
		$avatar = '<img src="' . $avatar . '" class="member_profile_avatar" />';
		$template->setVar('MEMBER_AVATAR', $avatar);
		$template->setVar('MEMBER_SIGNATURE', $check->long_prepare($template->getVar('MEMBER_SIGNATURE')));
		
		$template->displayPage('profile');
	else:
		print 'The member id provided is invalid';
	endif;
else:
	print 'The member id provided is invalid';
endif;


	$template->setVar('DB_CONSTRUCTS', $db_constructs);
	$template->setVar('DB_QUERIES', $db_queries);
	$template->setVar('PAGE_TIME_TO_LOAD', microtime()-$time);
	$template->displayPage('footer');
	unset($db);
	
?>