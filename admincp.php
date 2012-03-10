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
	define('PAGE_NAME', 'Admin Control Panel');
	require_once(PATH_TO_FILES . 'common.inc.php');
	
	// Make the admin log into the page?
	
	require_once(PATH_TO_FILES . 'admin_cp/groups.inc.php');
	require_once(PATH_TO_FILES . 'admin_cp/members.inc.php');
	require_once(PATH_TO_FILES . 'admin_cp/settings.inc.php');
	require_once(PATH_TO_FILES . 'admin_cp/pages.inc.php');
	require_once(PATH_TO_FILES . 'admin_cp/template.inc.php');
	require_once(PATH_TO_FILES . 'admin_cp/forums.inc.php');
	
	// Make sure that only admins can get in
	if (!member_is_admin($_SESSION['member_id'])):
		die('You are unauthorised to view this file.');
	endif;
	
	// Initialize template vars
	$template->setVar('ADMIN_PROCESS_HEADER', '');
	
	// Get the specific page, then run functions for post values and displaying the page
	switch ($_GET['a']):
		case ('forums'):
			if (isset($_POST['create_forum_submit'])):
				$template->setVar('ADMIN_PROCESS_HEADER', create_forum_submit($_POST['forum_name'], $_POST['forum_desc'], $_POST['parent_id'], $_POST['cat_id']));
			elseif (isset($_POST['create_cat_submit'])):
				$template->setVar('ADMIN_PROCESS_HEADER', create_cat_submit($_POST['cat_name'], $_POST['cat_desc']));
			elseif (isset($_POST['edit_submit'])):
				$template->setVar('ADMIN_PROCESS_HEADER', edit_forums_submit());
			elseif (isset($_POST['delete_submit'])):
				$template->setVar('ADMIN_PROCESS_HEADER', delete_forums_submit());
			elseif (isset($_POST['change_submit'])):
				$template->setVar('ADMIN_PROCESS_HEADER', change_forums_submit());
			elseif (isset($_POST['permissions_forums_submit'])):
				$template->setVar('ADMIN_PROCESS_HEADER', permissions_forums_submit());
			endif;
			$template->setVar('ADMIN_PAGE', forums());
			$template->setVar('SUBMENU_ITEMS', '<a href="./admincp.php?a=forums&b=forums">Edit Forums</a>');
		break;
		
		case ('groups'):
			if (isset($_POST['create_group_submit'])):
				$template->setVar('ADMIN_PROCESS_HEADER', create_group_submit($_POST['group_name'], $_POST['group_desc']));
			elseif (isset($_POST['edit_group_submit'])):
				$template->setVar('ADMIN_PROCESS_HEADER', edit_group_submit());
			elseif (isset($_POST['add_group_member'])):
				$template->setVar('ADMIN_PROCESS_HEADER', members_group_add($_POST['group_id'], $_POST['member_id'], 0));
			elseif (is_numeric($_GET['remove']) && is_numeric($_GET['member'])):
				$template->setVar('ADMIN_PROCESS_HEADER', members_group_remove($_GET['remove'], $_GET['member']));
			elseif (is_numeric($_GET['delete'])):
				$template->setVar('ADMIN_PROCESS_HEADER', delete_group_submit());
			endif;
			$template->setVar('ADMIN_PAGE', groups());
			$template->setVar('SUBMENU_ITEMS', '<a href="./admincp.php?a=groups&b=add">Edit Groups</a>');
		break;
		
		case ('members'):
			if (isset($_POST['edit_profile_info_submit'])):
				$template->setVar('ADMIN_PROCESS_HEADER', edit_profile_info_submit());
			endif;
			$template->setVar('ADMIN_PAGE', members());
			$template->setVar('SUBMENU_ITEMS', '<a href="./admincp.php?a=members&b=profile">Edit Profile Information</a>');
		break;
		
		case ('settings' || 'main'):
			if (isset($_POST['change_settings_submit'])):
				$template->setVar('ADMIN_PROCESS_HEADER', change_settings_submit());
			endif;
			$template->setVar('ADMIN_PAGE', settings());
		break;
	/*
	
	 CURRENTLY UNDER CONSTRUCTION
		
		case ('pages'):
			if (isset($_POST['edit_pages_submit'])):
				$template->setVar('ADMIN_PROCESS_HEADER', edit_pages_submit());
			elseif (isset($_POST['create_page_submit'])):
				$template->setVar('ADMIN_PROCESS_HEADER', create_page_submit($_POST['page_name']));
			endif;
			$template->setVar('ADMIN_PAGE', pages());
		break;
		
		case ('template'):
			if (isset($_POST['edit_template_submit'])):
				$template->setVar('ADMIN_PROCESS_HEADER', edit_template_submit());
			endif;
			$template->setVar('ADMIN_PAGE', template());
		break;
	*/	
		default:
			$template->setVar('ADMIN_PAGE', settings());
		break;
	endswitch;
	
	$template->displayPage('admin_header');
	$template->displayPage('admin');
	$template->displayPage('admin_footer');
?>