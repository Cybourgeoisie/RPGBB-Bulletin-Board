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
/admin_cp/members.inc.php
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

function members() {
	switch($_GET[b]):
		case 'profile':
			return profile_info();
		break;
		default:
			return profile_info();
		break;
	endswitch;	
}

function edit_profile_info_submit() {
	
	// Initialize
		global $dbc_calls, $check;
	
		if (!isset($check)):
			$check = new forms;
		endif;
	
	// For each collection of profile information data, update and iterate
	for ($i=0; $i<=$_POST[total_iterations]; $i++):
		if (is_numeric($_POST[info_id_.$i])):
			// update
			if (($title = trim($_POST[info_title_.$i])) == ''):
				$dbc_calls->query('DELETE FROM ' . DB_PREFIX . '_members_information WHERE info_id = ' . $_POST[info_id_.$i]);
			elseif (preg_match('/^([[:alnum:]]|\s|\?|\.|\,)+$/', $title)):
				if (trim($_POST[info_img_.$i]) != '' && $check->url_validate($_POST[info_img_.$i])):
					$img = $check->url_filter($_POST[info_img_.$i]);
				else:
					$img = '';
				endif;
				$update = array('info_title'=>$title,'info_type'=>$_POST[info_type_.$i],'info_img'=>$img,'info_status'=>1);
				$dbc_calls->update('members_information', $update, 'info_id = ' . $_POST[info_id_.$i]);
			else:
				return 'Your profile information field title can only contain numbers, letters, spaces, and punctuation';
			endif;
		elseif ($_POST[info_id_.$i]=='new'):
			// insert
			if (($title = trim($_POST[info_title_.$i])) == ''):
				break;
			elseif (preg_match('/^([[:alnum:]]|\s|\?|\.|\,)+$/', $title)):
				$insert = array('info_title'=>$title,'info_type'=>$_POST[info_type_.$i],'info_status'=>1);
				if ($dbc_calls->insert('members_information', $insert)):
					// leave a message???
				endif;
			else:
				return 'Your profile information field title can only contain numbers, letters, spaces, and punctuation';
			endif;
		endif;
	endfor;
	
	return 'Your fields have been updated';
}

function profile_info () {
	global $template, $dbc_calls, $check;
	
	if (!isset($check)):
		$check = new forms;
	endif;
	
	if (!isset($dbc_calls)):
		$dbc_calls = new dbc_calls();
	endif;
	
	$ques = $dbc_calls->select('SELECT * FROM ' . DB_PREFIX . '_members_information');
	
	$i=0;
	foreach ($ques as $question):
		$template->setVar('ITERATION', $i++);
		$template->setVar('INFO_ID', $question[info_id]);
		$template->setVar('INFO_TITLE', $question[info_title]);
		$template->setVar('INFO_TYPE', $question[info_type]);
		$template->setVar('INFO_IMG', $check->url_prepare($question[info_img]));
		$template->setVar('INFO_TYPE_IS_0', '');
		$template->setVar('INFO_TYPE_IS_1', '');
		
		// Set TEXT_FIELD to input text or textarea
		if ($question[info_type]==0):
			$template->setVar('INFO_TYPE_IS_0', 'checked="checked"');
		else:
			$template->setVar('INFO_TYPE_IS_1', 'checked="checked"');
		endif;
		
		$page .= $template->displayPage('admincp_profile_info_td', true);
	endforeach;
	
	// Once more for an empty row, for new entries
	$template->setVar('ITERATION', $i++);
	$template->setVar('INFO_ID', 'new');
	$template->setVar('INFO_TITLE', '');
	$template->setVar('INFO_TYPE_IS_0', 'checked="checked"');
	$template->setVar('INFO_TYPE_IS_1', '');
	$page .= $template->displayPage('admincp_profile_info_td', true);
	
	$template->setVar('PROFILE_INFORMATION_QUESTIONS', $page);
	
	// Return the page
	return $template->displayPage('admin_profile_info', true);
}

?>