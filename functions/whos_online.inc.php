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
/functions/whos_online.inc.php
******************************************
Prints up the Who's Online information
******************************************/


if (!defined('SCROLLIO')): 
	die('You are unauthorised to view this file.'); 
endif;


function whos_online() {
	// Initialize
		global $dbc_calls, $template;
		
		if (!isset($dbc_calls)):
			$dbc_calls = new dbc_calls;
		endif;
	
	// Find out how many people have been online in the last % minutes
	$whos_online = $dbc_calls->getWhosOnline();
	$member_count = $dbc_calls->getNumberOfMembers();
	
	if ($whos_online[session]):
		$i=1;
		foreach($whos_online[session] as $data):
			// Grab the member name
			$member = explode('"',$data['session_data']);
			// Print the name, add a comma if necessary
			$online_members .= $member[1];
				if ($i < $whos_online[number]):
					$online_members .= ', ';
				endif;
			$i++;
		endforeach;
	endif;
	
	$template->setVar('MEMBER_COUNT', $member_count);
	$template->setVar('ONLINE_MEMBER_COUNT', $i-1);
	$template->setVar('ONLINE_MEMBERS', $online_members);
	
	$template->displayPage('whos_online');
	
}

?>