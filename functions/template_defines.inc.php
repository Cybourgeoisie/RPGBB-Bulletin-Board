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
/functions/template_defines.inc.php
******************************************
This file retrieves all of the default template settings
and sets them as template variables
******************************************/


if (!defined('SCROLLIO')): 
	die('You are unauthorised to view this file.'); 
endif;

$template->setVar('PATH_TO_FILES', PATH_TO_FILES);

$admin_panel = '<a href="./admincp.php">Go to Admin Panel</a> | ';
$template->setVar('ADMIN_PANEL', $admin_panel);

$admin_panel = '';
$template->setVar('NOTHING', '');

$footer = 'Welcome, ' . $_SESSION['member_name'] . ' | <a href="./forum.php">To the Forum</a> | <a href="./process.php?logoff">Log Off</a>';
$template->setVar('LOGGED_IN_FOOTER_NAV', $footer);

$footer = '<div class="guest_footer">Hello, Guest.';
$footer .= ' Would you like to <a href="#" id="login_toggle">log in</a> or <a href="./register.php">register</a>?</div>';
$footer .= basic_login();
$template->setVar('GUEST_FOOTER_NAV', $footer);
	
?>