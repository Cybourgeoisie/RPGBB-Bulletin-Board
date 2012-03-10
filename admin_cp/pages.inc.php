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
/admin_cp/pages.inc.php
******************************************
To grab and display the forum settings

Contents:

	edit_pages_submit:
		edit the page info
		
	create_page_submit:
		create new page
		
	pages:
		display pages and info
******************************************/

	if (!defined('SCROLLIO')): 
		die('You are unauthorised to view this file.'); 
	endif;

	if (!member_is_admin($_SESSION['member_id'])):
		die('You are unauthorised to view this file.');
	endif;
	
function edit_pages_submit($name, $desc) {
	global $dbc_calls;
	
	if (trim($name) == ''):
		return 'Please enter a name for your group';
	endif;
	
	return 'finish this function';
}

function create_page_submit($name) {
	global $dbc_calls;
	
	if (($name = trim($name)) == ''):
		return 'Please enter a name for your page';
	elseif (!preg_match('/^[a-zA-Z0-9\s]+$/', $name)):
		return 'The name for your page can only contain numbers, letters, and spaces';
	endif;
	
	$insert = array('page_name'=>$name);
	
	if ($dbc_calls->insert('pages', $insert)):
		return 'Your page, ' . $name . ', has been created';
	else:
		return 'An error has occurred';
	endif;
}
	
function pages() {
	global $template, $dbc_calls;
	
	if (!isset($dbc_calls)):
		$dbc_calls = new dbc_calls();
	endif;
	
	// Grab all of the custom pages
	if ($pages = $dbc_calls->select('SELECT page_id, page_name FROM ' . DB_PREFIX . '_pages')):
		foreach($pages as $page):
			$pages_names .= $page[page_name] . "<br />";
			$id = $page[page_id];
			$pages_options .= '&nbsp; <a href="admincp.php?a=pages&edit='.$id.'">edit</a>';
			$pages_options .= ' | <a href="admincp.php?a=pages&permissions='.$id.'">permissions</a>';
			$pages_options .= ' | <a href="admincp.php?a=pages&delete='.$id.'">delete</a><br />';
		endforeach;
		$template->setVar('PAGES_LIST', $pages_names);
		$template->setVar('PAGES_OPTIONS', $pages_options);
	else:
		// No results
		$template->setVar('PAGES_LIST', 'You have not created any custom pages');
	endif;
	
	// Return the page
	return $template->displayPage('admin_pages', true);
}
	
?>