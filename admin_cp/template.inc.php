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
	
function edit_template_submit($name, $desc) {
	global $dbc_calls;
	
	if (trim($name) == ''):
		return 'Please enter a name for your group';
	endif;
	
	return 'finish this function';
}
	
function template() {
	
	// initialize
		global $template, $dbc_calls;
		
		if (!isset($dbc_calls)):
			$dbc_calls = new dbc_calls();
		endif;
	
	// Grab the template array
	$template_array = array(1=>'Main Page', 'Forums', 'Threads', 'Memberlist');
	
	// Make links out of 'em all
	foreach($template_array as $id => $name):
		$options .= '<a class="admin_jq_link" id="'.$id.'" href="admincp.php?a=template&edit='.$id.'">'.$name.'</a> &nbsp; ';
	endforeach;
	
	$template->setVar('PAGES_OPTIONS', $options);
	
	// Return the page
	return $template->displayPage('admin_template', true);
}
	
?>