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
/common.php
******************************************
This file sets up all of the PHP configurations
that all other files depend on. For example, all 
HTTP variables are cleaned up, the validity of 
the user is checked, and the permissions of the 
user are identified. Finally, all permanent file 
includes are added here.
******************************************/


if (!defined('SCROLLIO')): 
	die('You are unauthorised to view this file.'); 
endif;

// Check to make sure that the forum is installed
if (!isset($db['type']) || !isset($db['host']) || !isset($db['name']) || !isset($db['pass']) || !isset($db['prefix']) || defined(DB_PREFIX)):
	die('<p>You still need to run the <a href="./install.php">installation file</a>!</p>');
// Make sure that PHP 5 is enabled
elseif ((real)phpversion() < (real)'5.0.0'):
	die('You must have PHP 5 enabled to use Scrollio');
endif;

// Initialize the starting time and DB query variables
$time = microtime();
$db_constructs = 0;
$db_queries = 0;

// Start up the template class instance
$template = new template();

// Set the session id to the cookie
if (isset($_COOKIE['remember_me']) && !isset($_SESSION['member_status']) && !isset($_SESSION['member_name'])):
	session_id($_COOKIE['remember_me']);
endif;

// Require necessary, often used functions and classes
require_once(PATH_TO_FILES . 'classes/db_connections/' . $db['type'] . '.inc.php');
require_once(PATH_TO_FILES . 'functions/db_sessions.inc.php');
require_once(PATH_TO_FILES . 'functions/forum_defines.inc.php');
require_once(PATH_TO_FILES . 'functions/whos_online.inc.php');
require_once(PATH_TO_FILES . 'functions/bbcode.inc.php');
require_once(PATH_TO_FILES . 'functions/post_options.inc.php');
require_once(PATH_TO_FILES . 'functions/has_authority.inc.php');
require_once(PATH_TO_FILES . 'functions/list_pages.inc.php');
require_once(PATH_TO_FILES . 'functions/login_displays.inc.php');
require_once(PATH_TO_FILES . 'functions/list_timezones.inc.php');
require_once(PATH_TO_FILES . 'functions/has_authority.inc.php');

// Choose language pack
$language = 'languages/'.SELECTED_LANGUAGE_PACK.'.language.php';
require_once(PATH_TO_FILES . $language);

// Set autoload
function __autoload($class) {
	require_once(PATH_TO_FILES . 'classes/' . $class . '.class.php');
}

// Boot up the DBC_CALLS if necessary
if (!isset($dbc_calls)):
	$dbc_calls = new dbc_calls();
endif;

// Set default values for session if necessary
if (!isset($_SESSION['member_name'])):
	$_SESSION['member_name']='Guest';
	$_SESSION['member_status']=NULL;
	$_SESSION['member_id']=NULL;
endif;

// Establish all definitions within the templates
$constants = get_defined_constants(true);
$constants = $constants['user'];

foreach($constants as $name => $value):
	$template->setVar($name,$value);
endforeach;

// If we're not manipulating Session data, then write and close here
if (!$sess_write):
	session_write_close();
	require_once(PATH_TO_FILES . 'functions/template_defines.inc.php');
endif;

// Grab new PM notifications if necessary
if (is_numeric($_SESSION['member_id'])):
	$select = 'SELECT pm_id FROM ' . DB_PREFIX . '_private_messages WHERE pm_to = "' . $_SESSION['member_id'] . '" AND pm_received=0';
	if ($new_pm_num = $dbc_calls->select($select, 1)):
		$template->setVar('NEW_PM','(' . $new_pm_num . ' New)');
	endif;
endif;


?>