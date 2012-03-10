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
/functions/login_displays.inc.php
******************************************
Displays the login html for both basic and full logins

Contents:
	
	basic_login:
		Displays minimized login
		
	full_login:
		Displays full login
		
******************************************/

if (!defined('SCROLLIO')): 
	die('You are unauthorised to view this file.'); 
endif;

function basic_login($hidden = TRUE) {	
	
	// Initialize
		global $template;
	
		if (!isset($template)):
			$template = new template;
		endif;
	
	// Determine if the 'return' link should exist
	if ($hidden):
		$template->setVar('RETURN', '( <a href="#" id="basic_login_return">Return</a> )');
	endif;
	
	// Return the page's output
	return $template->displayPage('login_basic', true);
}

function full_login() {
	
	// Initialize
		global $template;
		
		if (!isset($template)):
			$template = new template;
		endif;
	
	// Return the page's output
	return $template->displayPage('login_full', true);
}

?>