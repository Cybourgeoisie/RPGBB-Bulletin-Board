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
/admin_cp/forums.inc.php
******************************************
To grab and display the forums and categories

Contents:

	change_forums_submit:
		updates settings info
		
	forums:
		displays settings page
******************************************/

	if (!defined('SCROLLIO')): 
		die('You are unauthorised to view this file.'); 
	endif;

	if (!member_is_admin($_SESSION['member_id'])):
		die('You are unauthorised to view this file.');
	endif;
	
function forums() {
	switch($_GET[b]):
		case 'forums':
			return forums_list();
		break;
		case 'permissions':
			if (is_numeric($_GET[cat]) || is_numeric($_GET[forum])):
				return perm_forums_contents();
			endif;
		break;
		case 'edit':
			if (is_numeric($_GET['forum_id']) || is_numeric($_GET['cat_id'])):
				return edit_forums_contents();
			endif;
		break;
		case 'delete':
			if (is_numeric($_GET['forum_id']) || is_numeric($_GET['cat_id'])):
				return delete_move_forums_contents();
			endif;
		break;
		default:
			return forums_list();
		break;
	endswitch;
}

function permissions_forums_submit($forum_id = NULL, $group_id = NULL, $perms_to_children = NULL) {
	// Initiate
		global $dbc_calls;
		// Set up the permission fields
		$perms = array('can_view','can_read','can_post','can_edit','can_delete','can_lock');
	
	// Make sure that the total iterations is numeric and >= 1
	if (!is_numeric($_POST['total_iterations']) || $_POST['total_iterations'] < 1):
		return 'You have no groups to update!';
	endif;
	
	// Determine if we're editing a forum or category, compose the additional where sql, and grab the ID
	if (is_numeric($forum_id) && is_numeric($group_id) && isset($perms_to_children)):
		// Delete any related category IDs
		if (isset($perms_to_children['cat_id'])):
			unset($perms_to_children['cat_id']);
		endif;
		
		// This is for updating a forum in recursion.
		$f_or_c_type = 'forum_id';
		$f_or_c_id = $forum_id;
		$where = 'forum_id = ' . $forum_id;
	elseif (is_numeric($_POST['forum_id'])):
		// For updating the top-level forum
		$f_or_c_type = 'forum_id';
		$f_or_c_id = $_POST['forum_id'];
		$where = 'forum_id = ' . $_POST['forum_id'];
		
		// Get the subforums if necessary, change apply_to_children to 0
		if ($_POST['apply_to_children'] == 1):
			$children = array();
			recursive_is_child($_POST['forum_id'], &$children);
			$_POST['apply_to_children'] = 0;
		endif;
	elseif (is_numeric($_POST['cat_id'])):
		$f_or_c_type = 'cat_id';
		$f_or_c_id = $_POST['cat_id'];
		$where = 'cat_id = ' . $_POST['cat_id'];
		
		// Get the forums and subforums if necessary, change apply_to_children to 0
		if ($_POST['apply_to_children'] == 1):
			$children = array();
			$children = recursive_get_cat_forums($_POST['cat_id']);
			$_POST['apply_to_children'] = 0;
		endif;
	else:
		return 'You must choose a valid forum or category to edit';
	endif;
	
	// Decide if to update a forum from recursion (with function parameters provided), or with POST variables
	if (is_numeric($forum_id) && is_numeric($group_id) && isset($perms_to_children)):
		// Insert if the category is not found in a SQL search. If a prior category is found, update.
		$select = 'SELECT group_id FROM ' . DB_PREFIX . '_group_permissions WHERE group_id = ' . $group_id . ' AND ' . $where . ' LIMIT 1';
			
		// Check if the result returns to determine if necessary to insert or update
		if ($dbc_calls->select($select) == FALSE):
			// Add group and forum / cat id
			$perms_to_children['group_id'] = $group_id;
			$perms_to_children[$f_or_c_type] = $f_or_c_id;
			// Insert
			if (!$dbc_calls->insert('group_permissions', $perms_to_children)):
				$result['error'] = 'An error has occurred in inserting the group permissions';
			endif;
		else:
			// Update
			if (!$dbc_calls->update('group_permissions', $perms_to_children, 'group_id = ' . $group_id . ' AND ' . $where)):
				$result['error'] = 'An error has occurred in updating the group permissions';
			endif;
		endif;
	else:
		// Run through each iteration
		for ($i = 1; $i <= $_POST['total_iterations']; $i++):
			$group_id = $_POST['group_iteration_' . $i];
		
			// Reset $insert_update for SQL
			$insert_update = array();
		
			// For each kind of permission, see if it equates to 1, or set to 0
			foreach ($perms as $perm):
				if ($_POST[$perm . '_' . $i] == 1):
					$insert_update[$perm] = 1;
				else:
					$insert_update[$perm] = 0;
				endif;
			endforeach;
					
			// Insert if the category is not found in a SQL search. If a prior category is found, update.
			$select = 'SELECT group_id FROM ' . DB_PREFIX . '_group_permissions WHERE group_id = ' . $group_id . ' AND ' . $where . ' LIMIT 1';
			
			// Check if the result returns to determine if necessary to insert or update
			if ($dbc_calls->select($select) == FALSE):
				// Add group and forum / cat id
				$insert_update['group_id'] = $group_id;
				$insert_update[$f_or_c_type] = $f_or_c_id;
				// Insert
				if (!$dbc_calls->insert('group_permissions', $insert_update)):
					$result['error'] = 'An error has occurred in inserting the group permissions';
				endif;
			else:
				// Add group and forum / cat id
				// Update
				if (!$dbc_calls->update('group_permissions', $insert_update, 'group_id = ' . $group_id . ' AND ' . $where)):
					$result['error'] = 'An error has occurred in updating the group permissions';
				endif;
			endif;
			
			// If there are children, provided if the apply_to_children checkbox is checked
			if (isset($children)):
				foreach ($children as $child):
					$result['children'] .= permissions_forums_submit($child, $group_id, $insert_update);
				endforeach;
			endif;
		endfor;
	endif;
		
	if (isset($result['error'])):
		return $result['error'];
	elseif (is_array($result['children'])):
		if (array_key_exists('error', $result['children'])):
			return $result['children']['error'];
		endif;
	else:
		return 'Your fields have been updated';
	endif;
}

function perm_forums_contents() {
	// Initialize
		global $dbc_calls, $template;
		
		if (!isset($template)):
			$template = new template;
		endif;
		
		// Need to be defaulted. Iteration will start at 1 after initial ++
		$group_ids = array();
		$iteration = 0;
		
	// Make sure one of the two are provided and numeric, and provide all information from them
	if (is_numeric($_GET['forum'])):
		if ($info = $dbc_calls->select('SELECT forum_id, forum_name FROM ' . DB_PREFIX . '_forums WHERE forum_id = ' . $_GET['forum'])):
			// Set up the select query and template variables
			$select = 'SELECT * FROM ' . DB_PREFIX . '_group_permissions WHERE forum_id = ' . $_GET['forum'] . ' ORDER BY group_id';
			$template->setVar('LC_FORUM_OR_CAT', 'forum');
			$template->setVar('THIS_FORUM_OR_CAT_ID', $info[1]['forum_id']);
			$template->setVar('THIS_FORUM_OR_CAT_NAME', $info[1]['forum_name']);
		endif;
	elseif (is_numeric($_GET['cat'])):
		if ($info = $dbc_calls->select('SELECT cat_id, cat_name FROM ' . DB_PREFIX . '_categories WHERE cat_id = ' . $_GET['cat'])):
			// Set up the select query and template variables
			$select = 'SELECT * FROM ' . DB_PREFIX . '_group_permissions WHERE cat_id = ' . $_GET['cat'] . ' ORDER BY group_id';
			$template->setVar('LC_FORUM_OR_CAT', 'cat');
			$template->setVar('THIS_FORUM_OR_CAT_ID', $info[1]['cat_id']);
			$template->setVar('THIS_FORUM_OR_CAT_NAME', $info[1]['cat_name']);
		endif;
	else:
		return 'Please provide a valid forum or cat id';
	endif;
	
	// For each group that is assigned to the forum, grab and display
	if ($perms = $dbc_calls->select($select)):
		foreach ($perms as $perm):
			// Compile a list of all groups that already have permissions set up - all others will be presented secondly
			$group_ids[] = $perm['group_id'];
			// Increase the iteration number
			$template->setVar('ITERATION', ++$iteration);
			
			// Get all the values
			foreach ($perm as $key => $value):
				if ($key != 'group_id' && $key != 'forum_id' && $key != 'cat_id'):
					// Grab the value, determine if checked or not
					if ($value == 1):
						$checked = 'checked="checked"';
					else:
						$checked = '';
					endif;
					
					// Display values
					$template->setVar(strtoupper($key), '<input type="checkbox" name="' . $key . '_' . $iteration . '" value="1" ' . $checked . ' />');
				endif;
			endforeach;
			
			// Get the group name
			$name = $dbc_calls->select('SELECT group_name FROM ' . DB_PREFIX . '_groups WHERE group_id = ' . $perm['group_id']);
			$template->setVar('GROUP_NAME', $name[1]['group_name']);
			$template->setVar('GROUP_ID', $perm['group_id']);
			
			$perm_td .= $template->displayPage('admin_forums_permissions_td', true);
		endforeach;
	endif;
	
	// For each group not assigned, grab and display
	$select = 'SELECT group_id, group_name FROM ' . DB_PREFIX . '_groups';
	foreach ($dbc_calls->select($select) as $group):
		// If the group ID is not in the group IDs list, display
		if (!in_array($group['group_id'], $group_ids)):
			// Increase the iteration number
			$template->setVar('ITERATION', ++$iteration);
			
			// Set all values to 0
			$template->setVar('CAN_VIEW', '<input type="checkbox" name="can_view_' . $iteration . '" value="1" />');
			$template->setVar('CAN_READ', '<input type="checkbox" name="can_read_' . $iteration . '" value="1" />');
			$template->setVar('CAN_POST', '<input type="checkbox" name="can_post_' . $iteration . '" value="1" />');
			$template->setVar('CAN_EDIT', '<input type="checkbox" name="can_edit_' . $iteration . '" value="1" />');
			$template->setVar('CAN_DELETE', '<input type="checkbox" name="can_delete_' . $iteration . '" value="1" />');
			$template->setVar('CAN_LOCK', '<input type="checkbox" name="can_lock_' . $iteration . '" value="1" />');
			
			// Get the group name and ID
			$template->setVar('GROUP_NAME', $group['group_name']);
			$template->setVar('GROUP_ID', $group['group_id']);
			
			// Collect output
			$perm_td .= $template->displayPage('admin_forums_permissions_td', true);
		endif;
	endforeach;
	
	$template->setVar('PERMISSIONS_TD', $perm_td);
	
	return $template->displayPage('admin_forums_permissions', true);
}

function change_forums_submit() {
	global $dbc_calls;
	
	if (is_numeric($_POST['total_iterations'])):
		for ($i = 1; $i <= $_POST['total_iterations']; $i++):
			// If the number is associated with a category, update that cateory
			if (is_numeric($_POST['cat_'.$i]) && is_numeric($_POST['cat_order_'.$i])):
				$order = $_POST['cat_order_'.$i];
				$dbc_calls->update('categories', array('cat_order'=>$order), 'cat_id = ' . $_POST['cat_'.$i]);
			
			// If the number is associated with a forum, update that forum
			elseif (is_numeric($_POST['forum_'.$i]) && is_numeric($_POST['forum_order_'.$i])):
				$order = $_POST['forum_order_'.$i];
				$dbc_calls->update('forums', array('forum_order'=>$order), 'forum_id = ' . $_POST['forum_'.$i]);
				
			endif;
		endfor;
		return 'Your changes have been saved';
	else:
		return 'Please don\'t mess with the post values';
	endif;
}

function edit_forums_contents() {
	// Initialize
		global $dbc_calls, $template;
		
		if (!isset($template)):
			$template = new template;
		endif;
	
	// As long as one of the two are specified and numeric, we can carry out the experiment
	if (is_numeric($_GET['cat_id'])):
		$template->setVar('FORUM_OR_CATEGORY', 'Category');
		$template->setVar('LC_FORUM_OR_CATEORY', 'cat');
		if ($category = $dbc_calls->select('SELECT cat_id, cat_name, cat_desc FROM ' . DB_PREFIX  . '_categories WHERE cat_id = ' . $_GET['cat_id'])):
			$template->setVar('FORUM_OR_CAT_NAME', $category[1]['cat_name']);
			$template->setVar('FORUM_OR_CAT_DESC', $category[1]['cat_desc']);
			$template->setVar('FORUM_OR_CAT_ID_INPUT', '<input type="hidden" name="cat_id" value="' . $category[1]['cat_id'] . '" />');
		endif;
	elseif (is_numeric($_GET['forum_id'])):
		$template->setVar('FORUM_OR_CATEGORY', 'Forum');
		$template->setVar('LC_FORUM_OR_CATEORY', 'forum');
		if ($forum = $dbc_calls->select('SELECT forum_id, forum_name, forum_desc FROM ' . DB_PREFIX  . '_forums WHERE forum_id = ' . $_GET['forum_id'])):
			$template->setVar('FORUM_OR_CAT_NAME', $forum[1]['forum_name']);
			$template->setVar('FORUM_OR_CAT_DESC', $forum[1]['forum_desc']);
			$template->setVar('FORUM_OR_CAT_ID_INPUT', '<input type="hidden" name="forum_id" value="' . $forum[1]['forum_id'] . '" />');
			$template->setVar('PARENT', 'Parent');
			
			// Grab the forum's children
			$children = array();
			recursive_is_child($_GET['forum_id'], &$children);
			
			// Provide the 'none' parent option, and grab the current parent
			$options .= '<option value="0">None</option>';
			$parent = $dbc_calls->select('SELECT parent_id FROM ' . DB_PREFIX  . '_forums WHERE forum_id = ' . $_GET['forum_id']);
			$parent_id = $parent[1]['parent_id'];
			
			// Get all other options
			if ($select = $dbc_calls->select('SELECT forum_id, forum_name, parent_id FROM ' . DB_PREFIX  . '_forums WHERE forum_id != ' . $_GET['forum_id'])):
				foreach ($select as $forum):
					if ($forum['forum_id'] == $parent_id):
						$options .= '<option value="' . $forum['forum_id'] . '" selected="selected">' . $forum['forum_name'] . '</option>';
					elseif (!in_array($forum['forum_id'],$children)):
						$options .= '<option value="' . $forum['forum_id'] . '">' . $forum['forum_name'] . '</option>';
					endif;
				endforeach;
			endif;
			$template->setVar('OPTIONS', '<select name="parent_id">' . $options . '</select>');
		endif;
	else:
		return 'Specify a numeric category or forum ID';
	endif;
	
	return $template->displayPage('admin_forums_edit', true);
}

function edit_forums_submit() {
	global $dbc_calls;
	
	if (is_numeric($_POST['forum_id'])):
		// Make sure that the forum name is valid
		if (($forum_name = trim($_POST['forum_name'])) == ''):
			return 'Please enter a name for your forum';
		elseif (!preg_match('/^([[:alnum:]]|\s|[[:punct:]])+$/', $forum_name)):
			return 'Your forum name can only contain numbers, letters, spaces, and punctuation';
		endif;
		
		// Make sure that the forum description is valid
		if (trim($_POST['forum_desc'])!='' && !preg_match('/^([[:alnum:]]|\s|[[:punct:]])+$/', $_POST['forum_desc'])):
			return 'Your forum description can only contain numbers, letters, spaces, and punctuation';
		endif;
		
		if (!is_numeric($_POST['parent_id'])):
			return 'Your parent ID must be numeric';
		endif;
		
		// Format the settings for the update function
		$settings = array('forum_name'=>$forum_name,'forum_desc'=>$_POST['forum_desc'],'parent_id'=>$_POST['parent_id']);
		
		// Edit the forum
		if ($dbc_calls->update('forums', $settings, 'forum_id = ' . $_POST[forum_id])):
			return 'Your forum has been edited';
		else:
			return 'An error has occurred';
		endif;
	elseif (is_numeric($_POST['cat_id'])):
		
		// Make sure that the category name is valid
		if (($cat_name = trim($_POST['cat_name'])) == ''):
			return 'Please enter a name for your category';
		elseif (!preg_match('/^([[:alnum:]]|\s|[[:punct:]])+$/', $cat_name)):
			return 'Your forum name can only contain numbers, letters, spaces, and punctuation';
		endif;
		
		// Make sure that the forum description is valid
		if (trim($_POST['cat_desc'])!='' && !preg_match('/^([[:alnum:]]|\s|[[:punct:]])+$/', $_POST['cat_desc'])):
			return 'Your forum description can only contain numbers, letters, spaces, and punctuation';
		endif;
		
		// Format the settings for the update function
		$settings = array('cat_name'=>$cat_name, 'cat_desc'=>$_POST['cat_desc']);
		
		// Edit the forum
		if ($dbc_calls->update('categories', $settings, 'cat_id = ' . $_POST['cat_id'])):
			return 'Your category has been edited';
		else:
			return 'An error has occurred';
		endif;
	else:
		return 'The POST values were incorrect';
	endif;
}

function delete_move_forums_contents() {
	// Initialize
		global $dbc_calls, $template;
		
		if (!isset($template)):
			$template = new template;
		endif;
	
	// As long as one of the two are specified and numeric, we can carry out the experiment
	if (is_numeric($_GET['cat_id'])):
		$template->setVar('FORUM_OR_CATEGORY', 'Category');
		// For each category add to options except for the forum in question of deletion 
		foreach($dbc_calls->select('SELECT cat_id, cat_name FROM ' . DB_PREFIX . '_categories') as $cat):
			if ($_GET['cat_id'] != $cat[cat_id]):
				$options .= '<option value="' . $cat['cat_id'] . '">' . $cat['cat_name'] . '</option>';
			else:
				$template->setVar('FORUM_OR_CAT_NAME', $cat['cat_name']);
				$template->setVar('FORUM_OR_CAT_ID_INPUT', '<input type="hidden" name="cat_id" value="' . $cat['cat_id'] . '" />');
			endif;
		endforeach;
		
		// Set the name to 'to_cat_id' for categories
		$template->setVar('OPTIONS', '<select name="to_cat_id">' . $options . '</select>');
	elseif (is_numeric($_GET['forum_id'])):
		$template->setVar('FORUM_OR_CATEGORY', 'Forum');
		
		// Grab the forum's children
		$children = array();
		recursive_is_child($_GET['forum_id'], &$children);
		
		// For each forum add to options except for the forum in question of deletion 
		foreach($dbc_calls->select('SELECT forum_id, forum_name, parent_id FROM ' . DB_PREFIX . '_forums') as $forum):
			if ($_GET['forum_id'] != $forum[forum_id] && !in_array($forum['forum_id'], $children)):
				$options .= '<option value="' . $forum['forum_id'] . '">' . $forum['forum_name'] . '</option>';
			elseif ($_GET['forum_id'] == $forum[forum_id]):
				$template->setVar('FORUM_OR_CAT_NAME', $forum['forum_name']);
				$template->setVar('FORUM_OR_CAT_ID_INPUT', '<input type="hidden" name="forum_id" value="' . $forum['forum_id'] . '" />');
			endif;
		endforeach;
		
		// Set the name to 'to_forum_id' for forums
		$template->setVar('OPTIONS', '<select name="to_forum_id">' . $options . '</select>');
	else:
		return 'Specify a numeric category or forum ID';
	endif;
	
	return $template->displayPage('admin_forums_delete_move', true);
}

function delete_forums_submit() {
	global $dbc_calls;
	
	if (is_numeric($_POST['forum_id']) && is_numeric($_POST['to_forum_id'])):
		// Delete the forum
		if ($dbc_calls->query('DELETE FROM ' . DB_PREFIX . '_forums WHERE forum_id = ' . $_POST[forum_id])):
			// Move all threads to the to_forum_id
			$dbc_calls->update('threads', array('forum_id'=>$_POST['to_forum_id']), 'forum_id = ' . $_POST[forum_id]);
			// Move all posts to the to_forum_id
			$dbc_calls->update('posts', array('forum_id'=>$_POST['to_forum_id']), 'forum_id = ' . $_POST[forum_id]);
			// Move all subforums to the to_forum_id
			$dbc_calls->update('forums', array('parent_id'=>$_POST['to_forum_id']), 'parent_id = ' . $_POST[forum_id]);
			// Save the message
			return 'Your forum has been deleted';
		else:
			return 'An error has occurred';
		endif;
	elseif (is_numeric($_POST['cat_id']) && is_numeric($_POST['to_cat_id'])):
		if ($dbc_calls->query('DELETE FROM ' . DB_PREFIX . '_categories WHERE cat_id = ' . $_POST[cat_id])):
			// Move all subforums to the to_forum_id
			$dbc_calls->update('forums', array('cat_id'=>$_POST['to_cat_id']), 'cat_id = ' . $_POST[cat_id]);
			// Save the message
			return 'Your category has been deleted';
		else:
			return 'An error has occurred';
		endif;
	else:
		return 'The POST values were incorrect';
	endif;
}

function create_forum_submit($forum_name, $forum_desc, $parent_id, $cat_id) {
	global $dbc_calls;
	
	// Make sure that the forum name is valid
	if (($forum_name = trim($forum_name)) == ''):
		return 'Please enter a name for your forum';
	elseif (!preg_match('/^([[:alnum:]]|\s|[[:punct:]])+$/', $forum_name)):
		return 'Your forum name can only contain numbers, letters, spaces, and punctuation';
	endif;
	
	// Make sure that the forum description is valid
	if (trim($forum_desc)!='' && !preg_match('/^([[:alnum:]]|\s|[[:punct:]])+$/', $forum_desc)):
		return 'Your forum description can only contain numbers, letters, spaces, and punctuation';
	endif;
	
	// Make sure that the IDs are numeric
	if (!is_numeric($parent_id) || !is_numeric($cat_id)):
		return 'You must select parent and category ids';
	endif;
	
	// Make sure that the parent forum exists in the category if the forum is not 0
	if ($parent_id != 0):
		if ($dbc_calls->select('SELECT forum_id FROM ' . DB_PREFIX . '_forums WHERE forum_id="'.$parent_id.'" AND cat_id="'.$cat_id.'"') == false):
			return 'The parent of your forum must exist in the same category';
		endif;
	endif;
	
	$insert = array('forum_name'=>$forum_name,'forum_desc'=>$forum_desc,'parent_id'=>$parent_id,'cat_id'=>$cat_id);
	
	if ($dbc_calls->insert('forums', $insert)):
		return 'Your forum has been created';
	else:
		return 'An error has occurred';
	endif;
}

function create_cat_submit($cat_name, $cat_desc) {
	global $dbc_calls;
	
	// Make sure that the forum name is valid
	if (($cat_name = trim($cat_name)) == ''):
		return 'Please enter a name for your forum';
	elseif (!preg_match('/^([[:alnum:]]|\s|[[:punct:]])+$/', $cat_name)):
		return 'Your category name can only contain numbers, letters, spaces, and punctuation (? , .)';
	endif;
	
	// Make sure that the forum description is valid
	if (trim($cat_desc)!='' && !preg_match('/^([[:alnum:]]|\s|[[:punct:]])+$/', $cat_desc)):
		return 'Your category description can only contain numbers, letters, spaces, and punctuation (? , .)';
	endif;
	
	$insert = array('cat_name'=>$cat_name,'cat_desc'=>$cat_desc);
	
	if ($dbc_calls->insert('categories', $insert)):
		return 'Your category has been created';
	else:
		return 'An error has occurred';
	endif;
}

function forums_list() {
	// Initialize
		global $template;
		
		if (!isset($template)):
			$template = new template;
		endif;
		
		$forum_call = new forum();
		// Initialize at 0, but starts at 1
		$list_iteration = 0;
	
	// Grab the forums
	$categories = $forum_call->getCategoryList();
	
	// For each category, set the name and ID, get the forums
	foreach ($categories as $category):
		// Increase the number of iterations, necessary for saving changes on all forums
		$list_iteration++;
		
		// Get the information
		$template->setVar('THIS_CAT_NAME', $category['cat_name']);
		$template->setVar('THIS_CAT_DESC', $category['cat_desc']);
		$template->setVar('THIS_CAT_ID', $category['cat_id']);
		$template->setVar('THIS_CAT_ORDER', order_options(count($categories), $category['cat_order'], 'cat', $list_iteration));
		$template->setVar('ITERATION', $list_iteration);
		
		// Set up the options and save the output
		$options = '&nbsp; <a href="admincp.php?a=forums&b=edit&cat_id='.$category['cat_id'].'">edit</a>';
		$options .= ' | <a href="admincp.php?a=forums&b=permissions&cat='.$category['cat_id'].'">permissions</a>';
		$options .= ' | <a href="admincp.php?a=forums&b=delete&cat_id='.$category['cat_id'].'">delete</a><br />';
		$template->setVar('OPTIONS', $options);
		$forums_listing .= $template->displayPage('admin_forums_start', true);
		
		// Add to total categories options
		$categories_options .= '<option value="' . $category['cat_id'] . '">' . $category['cat_name'] . '</option>';
		
		// If there are forums in the category, then display them
		if (isset($category[0])):
			// For each non-sub forum, set the name and ID
			foreach ($category[0] as $forum_id => $forum):
				// Increase the number of iterations, necessary for saving changes on all forums
				$list_iteration++;
				
				$template->setVar('PREFIX', '');
				$template->setVar('THIS_FORUM_NAME', $forum['forum_name']);
				$template->setVar('THIS_FORUM_DESC', $forum['forum_desc']);
				$template->setVar('THIS_FORUM_ID', $forum['forum_id']);
				$template->setVar('THIS_FORUM_ORDER', order_options(count($category[0]), $forum['forum_order'], 'forum', $list_iteration));
				$template->setVar('ITERATION', $list_iteration);
				
				// Set up the options and save the output
				$options = '&nbsp; <a href="admincp.php?a=forums&b=edit&forum_id='.$forum['forum_id'].'">edit</a>';
				$options .= ' | <a href="admincp.php?a=forums&b=permissions&forum='.$forum['forum_id'].'">permissions</a>';
				$options .= ' | <a href="admincp.php?a=forums&b=delete&forum_id='.$forum['forum_id'].'">delete</a><br />';
				$template->setVar('OPTIONS', $options);	
				$forums_listing .= $template->displayPage('admin_forum', true);
				
				// Add to total forum options
				$forums_options .= '<option value="' . $forum['forum_id'] . '">' . $forum['forum_name'] . '</option>';
				
				// For each sub forum, set the name and ID
				if (isset($category[$forum['forum_id']])):
					recursive_subforums($category, $forum_id, &$forums_listing, &$forums_options, &$list_iteration);
				endif;
			endforeach;
		endif;
		
		// End forums listing
		$forums_listing .= $template->displayPage('admin_forums_end', true);
	endforeach;
	
	// Set template variables and display the page
	$template->setVar('FORUMS_LISTING', $forums_listing);
	$template->setVar('CATEGORIES_OPTIONS', '<select name="cat_id">' . $categories_options . '</select>');
	$template->setVar('FORUMS_OPTIONS', '<select name="parent_id"><option value="0">None</option>' . $forums_options . '</select>');
	
	// Return the page
	return $template->displayPage('admin_forums', true);
}
	
function recursive_subforums($category, $subforum_id, $forums_listing, $forums_options, $list_iteration) {
	global $template;
		
	foreach ($category[$subforum_id] as $subforum):
		// Increase the number of iterations, necessary for saving changes on all forums
		$list_iteration++;
	
		$template->setVar('PREFIX', 'sub');
		$template->setVar('THIS_FORUM_NAME', $subforum['forum_name']);
		$template->setVar('THIS_FORUM_DESC', $subforum['forum_desc']);
		$template->setVar('THIS_FORUM_ID', $subforum['forum_id']);
		$template->setVar('THIS_FORUM_ORDER', order_options(count($category[$subforum_id]), $subforum['forum_order'], 'forum', $list_iteration));
		$template->setVar('ITERATION', $list_iteration);
		
		// Set up the options and save the output
		$options = '&nbsp; <a href="admincp.php?a=forums&b=edit&forum_id='.$subforum['forum_id'].'">edit</a>';
		$options .= ' | <a href="admincp.php?a=forums&b=permissions&forum='.$subforum['forum_id'].'">permissions</a>';
		$options .= ' | <a href="admincp.php?a=forums&b=delete&forum_id='.$subforum['forum_id'].'">delete</a><br />';
		$template->setVar('OPTIONS', $options);	
		$forums_listing .= $template->displayPage('admin_forum', true);
						
		// Add to total forum options
		$forums_options .= '<option value="' . $subforum['forum_id'] . '"> - ' . $subforum['forum_name'] . '</option>';
		
		if (isset($category[$subforum['forum_id']])):
			$forum_options .= recursive_subforums($category, $subforum['forum_id'], &$forums_listing, &$forums_options, &$list_iteration);
		endif;
	endforeach;
	
	return $forums_options;
}	

function recursive_get_cat_forums($cat_id) {
	// Continually grab the forums in the category, grab their children, and return an array
	
	global $dbc_calls;
	
	if (is_numeric($cat_id)):
		if ($child_ids = $dbc_calls->select('SELECT forum_id FROM ' . DB_PREFIX . '_forums WHERE cat_id = ' . $cat_id)):
			foreach($child_ids as $child):
				// Grab the ID, add it to the children array, and then run the child function if necessary
				$id = $child['forum_id'];
				$children[] = $id;
				recursive_is_child ($id, &$children);
			endforeach;
		endif;
	endif;
	
	return $children;
}	

function recursive_is_child($forum_id, $children) {
	// Continually grab the forums that have a parent id of $forum_id and supply an array
	
	global $dbc_calls;
	
	if (is_numeric($forum_id)):
		if ($child_ids = $dbc_calls->select('SELECT forum_id FROM ' . DB_PREFIX . '_forums WHERE parent_id = ' . $forum_id)):
			foreach($child_ids as $child):
				// Grab the ID, add it to the children array, and then run this function again if necessary
				$id = $child['forum_id'];
				$children[] = $id;
				recursive_is_child ($id, &$children);
			endforeach;
		endif;
	endif;
}	

function order_options($count, $selected, $f_or_c, $id) {
	// Based on the number of forums or categories, display a valid order listing
	if (is_numeric($count)):
		for ($i = 1; $i <= $count; $i++):
			if ($i == $selected):
				$options .= '<option value="' . $i . '" selected="selected">' . $i . '</option>';
			else:
				$options .= '<option value="' . $i . '">' . $i . '</option>';
			endif;
		endfor;
	endif;
	
	return '<select name="'.$f_or_c . '_order_' . $id .'">' . $options . '</select>';
}

?>