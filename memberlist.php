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

// Grab all the members
if (!isset($dbc_calls)):
	$dbc_calls = new dbc_calls;
endif;
$members = $dbc_calls->getMemberInfo();

// Compile information for each member
foreach($members as $member):
	// Set all variables based on member information
	foreach($member as $key => $value):
		$template->setVar(strtoupper($key), $value);
	endforeach;
	
	// Input the information in the row, save the row for the memberlist page
	$members_list .= $template->displayPage('memberlist_td', true);
endforeach;

// Make the members_list a template variable, then display the memberlist
$template->setVar('MEMBERS_LIST', $members_list);
$template->displayPage('memberlist');

// Print the Who's Online module
whos_online();

	$template->setVar('DB_CONSTRUCTS', $db_constructs);
	$template->setVar('DB_QUERIES', $db_queries);
	$template->setVar('PAGE_TIME_TO_LOAD', microtime()-$time);
	$template->displayPage('footer');
	unset($db);
?>