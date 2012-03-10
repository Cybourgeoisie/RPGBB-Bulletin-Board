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
/admin_cp/template.inc.php
******************************************
To grab and display the template information

Contents:

	edit_template_submit:
		updates template information
		
	template:
		displays template page and text boxes if necessary 
******************************************/

	if (!defined('SCROLLIO')): 
		die('You are unauthorised to view this file.'); 
	endif;

	if (!member_is_admin($_SESSION['member_id'])):
		die('You are unauthorised to view this file.');
	endif;
	
function create_group_submit($name, $desc) {
	global $dbc_calls;
	
	if (($name = trim($name)) == ''):
		return 'Please enter a name for your group';
	endif;
	
	$values = array('group_name' => $name, 'group_desc' => $desc);
	if ($dbc_calls->insert('groups', $values)):
		return 'Your group, ' . $name . ', has been created';
	else:
		return 'An error has occurred';
	endif;
}

function delete_group_submit() {
	global $dbc_calls;
	
	if (!is_numeric($_GET['delete'])):
		return 'Please specify the proper group ID';
	endif;
	
	// Make sure that the user can't delete the default groups
	if ($_GET['delete'] <= 3):
		return 'The default groups (Members, Awaiting Approval, Banned) can not be deleted.';
	endif;
	
	// Delete the group
	if ($dbc_calls->query('DELETE FROM ' . DB_PREFIX . '_groups WHERE group_id = ' . $_GET['delete'])):
		// Delete the associated group_members fields
		if ($dbc_calls->query('DELETE FROM ' . DB_PREFIX . '_group_members WHERE group_id = ' . $_GET['delete'])):
			// Delete the associated group_permissions fields
			if ($dbc_calls->query('DELETE FROM ' . DB_PREFIX . '_group_permissions WHERE group_id = ' . $_GET['delete'])):
				return 'Your group has been deleted';
			endif;
		endif;
	else:
		return 'An error has occurred';
	endif;
}

function edit_group_contents() {
	// Initialize
		global $dbc_calls, $template;
		
		if (!isset($template)):
			$template = new template;
		endif;
	
	// As long as one of the two are specified and numeric, we can carry out the experiment
	if (!is_numeric($_GET['edit'])):
		return 'Specify a numeric group ID';
	endif;
	
	// Make sure that the user can't edit the default groups
	if ($_GET['edit'] <= 3):
		return 'The default groups (Members, Awaiting Approval, Banned) can not be edited.';
	endif;
	
	// Get the group information, run the check
	$group = $dbc_calls->select('SELECT group_id, group_name, group_desc FROM ' . DB_PREFIX . '_groups WHERE group_id = ' . $_GET['edit']);
	$template->setVar('GROUP_NAME', $group[1]['group_name']);
	$template->setVar('GROUP_DESC', $group[1]['group_desc']);
	$template->setVar('GROUP_ID', $group[1]['group_id']);
	
	return $template->displayPage('admin_groups_edit', true);
}

function edit_group_submit() {
	global $dbc_calls;
	
	// Make sure that the forum name is valid
	if (($group_name = trim($_POST['group_name'])) == ''):
		return 'Please enter a name for your forum';
	elseif (!preg_match('/^([[:alnum:]]|\s|[[:punct:]])+$/', $group_name)):
		return 'Your group name can only contain numbers, letters, spaces, and punctuation';
	endif;
	
	// Make sure that the forum description is valid
	if (trim($_POST['group_desc'])!='' && !preg_match('/^([[:alnum:]]|\s|[[:punct:]])+$/', $_POST['group_desc'])):
		return 'Your group description can only contain numbers, letters, spaces, and punctuation';
	endif;
	
	if (!is_numeric($_POST['group_id'])):
		return 'Don\'t mess with the post variables';
	endif;
	
	// Make sure that the user can't edit the default groups
	if ($_POST['edit'] <= 3):
		return 'The default groups (Members, Awaiting Approval, Banned) can not be edited.';
	endif;
	
	if ($dbc_calls->update('groups', array('group_name'=>$group_name,'group_desc'=>$_POST['group_desc']), 'group_id = ' . $_POST['group_id'])):
		return 'Your group has been edited';
	endif;
}

function members_group_contents() {
	// Initialize
		global $dbc_calls, $template;
		
		if (!isset($template)):
			$template = new template;
		endif;
	
	// As long as one of the two are specified and numeric, we can carry out the experiment
	if (!is_numeric($_GET['members'])):
		return 'Specify a numeric group ID';
	endif;
	
	// Make sure that the user can't edit the default groups
	if ($_GET['members'] <= 3):
		return 'The default groups (Members, Awaiting Approval, Banned) can not be edited.';
	endif;
	
	// Get the group information
	$group = $dbc_calls->select('SELECT group_id, group_name, group_desc FROM ' . DB_PREFIX . '_groups WHERE group_id = ' . $_GET['members']);
	$template->setVar('GROUP_NAME', $group[1]['group_name']);
	$template->setVar('GROUP_ID', $group[1]['group_id']);
	
	$members_in_group[] = 0;
	
	if ($members = $dbc_calls->select('SELECT member_id, member_is_mod FROM ' . DB_PREFIX . '_group_members WHERE group_id = ' . $_GET['members'])):
		foreach ($members as $member):
			$template->setVar('MEMBER_ID', $member['member_id']);
			$template->setVar('MEMBER_IS_MOD', $member['member_is_mod']);
			
			if ($member_name = $dbc_calls->select('SELECT member_name FROM ' . DB_PREFIX . '_members WHERE member_id = ' . $member['member_id'])):
				$template->setVar('MEMBER_NAME', $member_name[1]['member_name']);
			endif;
			
			$members_in_group[] = $member['member_id'];
			$group_members .= $template->displayPage('admin_groups_members_td', true);
		endforeach;
	
		$template->setVar('GROUP_MEMBERS', $group_members);
	else:
		$template->setVar('GROUP_MEMBERS', 'You have no members in this group');
	endif;
	
	
	// Set up the add new member information
	$members = $dbc_calls->select('SELECT member_id, member_name FROM ' . DB_PREFIX . '_members ORDER BY member_name ASC');
	foreach ($members as $member):
		// Leave out all members that are already in the group
		if (!in_array($member[member_id], $members_in_group)):
			$members_options .= '<option value="' . $member[member_id] . '">' . $member[member_name] . '</option>';
		endif;
	endforeach;
	$template->setVar('MEMBERS_OPTIONS', '<select name="member_id" size="5">' . $members_options . '</select>');
	
	return $template->displayPage('admin_groups_members', true);
}

function members_group_add($group_id, $member_id, $member_is_mod = 0) {
	global $dbc_calls;
	
	if (!is_numeric($group_id) || !is_numeric($member_id)):
		return 'Your group id and member id must be numeric';
	endif;
	
	// Make sure that the user can't edit the default groups
	if ($group_id <= 3):
		return 'The default groups (Members, Awaiting Approval, Banned) can not be edited.';
	endif;
	
	$add = array('group_id'=>$group_id, 'member_id'=>$member_id, 'member_is_mod' => $member_is_mod);
	
	if ($dbc_calls->insert('group_members', $add)):
		return 'A member has been added to your group';
	endif;
}

function members_group_remove($group_id, $member_id = 'NULL') {
	// Initialize
		global $dbc_calls;
		
		$member_delete = '';
	
	if (!is_numeric($group_id)):
		return 'Your group id must be numeric';
	elseif (is_numeric($member_id)):
		$member_delete = ' AND member_id = ' . $member_id;
	endif;
	
	// Make sure that the user can't edit the default groups
	if ($group_id <= 3):
		return 'The default groups (Members, Awaiting Approval, Banned) can not be edited.';
	endif;
	
	if ($dbc_calls->query('DELETE FROM ' . DB_PREFIX . '_group_members WHERE group_id = ' . $group_id . $member_delete)):
		return 'A member has been removed from your group';
	endif;
}

function members_group_mod($group_id, $member_id, $member_is_mod) {
	global $dbc_calls;
	
	if (!is_numeric($group_id) || !is_numeric($member_id) || !is_numeric($member_is_mod)):
		return 'Your group id, member id, and selection must be numeric';
	endif;
	
	// Make sure that the user can't edit the default groups
	if ($group_id <= 3):
		return 'The default groups (Members, Awaiting Approval, Banned) can not be edited.';
	endif;
	
	$mod = array('member_is_mod' => $member_is_mod);
	
	if ($dbc_calls->update('group_members', $mod, 'WHERE group_id = ' . $group_id . ' AND member_id = ' . $member_id)):
		return 'Your group member\'s status has been modified';
	endif;
}

function groups() {
	// Initialize
		global $template, $dbc_calls;
	
		if (!isset($dbc_calls)):
			$dbc_calls = new dbc_calls();
		endif;
	
	if (is_numeric($_GET['edit'])):
		return edit_group_contents();
	elseif (is_numeric($_GET['members'])):
		return members_group_contents();
	endif;
	
	$groups = $dbc_calls->select('SELECT group_id, group_name, group_desc FROM ' . DB_PREFIX . '_groups WHERE group_id > 3 ORDER BY group_id');
	
	if ($groups != false):
		foreach ($groups as $value):
			$template->setVar('GROUP_NAME', $value['group_name']);
			$template->setVar('GROUP_DESC', $value['group_desc']);
			$template->setVar('GROUP_ID', $value['group_id']);
			
			// add the output to the groups_list
			$groups_list .= $template->displayPage('admin_groups_td', true);
		endforeach;
		// Set the Groups
		$template->setVar('GROUPS_LIST', $groups_list);
	else:
		$template->setVar('GROUPS_LIST', 'You have no groups');
	endif;
	
	// Return the page
	return $template->displayPage('admin_groups', true);
}
	
?>