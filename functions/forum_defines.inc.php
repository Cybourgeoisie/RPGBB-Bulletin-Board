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
/functions/forum_defines.inc.php
******************************************
This file retrieves all of the forum settings
from the database and sets them as php definitions.
******************************************/


if (!defined('SCROLLIO')): 
	die('You are unauthorised to view this file.'); 
endif;

if (!isset($dbc_calls)):
	$dbc_calls = new dbc_calls();
endif;

$results = $dbc_calls->selectNum('SELECT * FROM ' . DB_PREFIX . '_forum_settings');

foreach ($results as $value):
	if ($value[2] == 1):
		define($value[0],$value[1],true);
	endif;
endforeach;

unset($results);
unset($value);

// Stick in fundamental values for unset definitions
// Paths, directions
if (!defined('PATH_TO_FILES')):
	define(PATH_TO_FILES, './');
endif;

if (!defined('PATH_TO_TEMPLATE')):
	define(PATH_TO_TEMPLATE, './layouts/templates/original/');
endif;

if (!defined('PATH_TO_LAYOUT')):
	define(PATH_TO_LAYOUT, PATH_TO_FILES . 'layouts/');
endif;

// Forum information
if (!defined('FORUM_NAME')):
	define(FORUM_NAME,'Scrollio Forum');
endif;

if (!defined('FORUM_DESC')):
	define(FORUM_DESC,'This forum is powered by Scrollio Forum');
endif;

if (!defined('TITLE_SEPARATOR')):
	define(TITLE_SEPARATOR, ":");
endif;

if (!defined('BREADCRUMBS')):
	define(BREADCRUMBS,":");
endif;

if (!defined('FORUM_LOGO_URL')):
	define(FORUM_LOGO,"");
else:
	define(FORUM_LOGO,"<img src=\"" . FORUM_LOGO_URL . "\" />");
endif;

if (!defined('FORUM_BANNER_URL')):
	define(FORUM_BANNER,"");
else:
	define(FORUM_BANNER,"<img src=\"" . FORUM_BANNER_URL . "\" />");
endif;

if (!defined('FORUM_NAME')):
	define(FORUM_NAME,"Scrollio Forum");
endif;

if (!defined('FORUM_GREETING')):
	define(FORUM_GREETING,'Hello, ' . $_SESSION[member_name] . ', and welcome to <a href="./forum.php">' . FORUM_NAME . '</a>');
endif;

if (!defined('TOP_LINKS')):
	define(TOP_LINKS,"<a href=\"./index.php\">Portal</a> " . BREADCRUMBS . " <a href=\"./forum.php\">Forum</a>");
endif;

if (!defined('NAV_LINKS')):
	define(NAV_LINKS, "<a href=\"./\">FAQ</a> " . BREADCRUMBS . " <a href=\"./\">Rules of " . FORUM_NAME . "</a> " 
		. BREADCRUMBS . " <a href=\"./memberlist.php\">Memberlist</a> " . BREADCRUMBS . " <a href=\"./\">Characters</a>");
endif;

if (!defined('POST_DISPLAY_NUM')):
	define(POST_DISPLAY_NUM,20);
endif;

if (!defined('NEW_POST_BUTTON')):
	define(NEW_POST_BUTTON,'Add Post');
endif;

if (!defined('NEW_THREAD_BUTTON')):
	define(NEW_THREAD_BUTTON,'Add Thread');
endif;

if (!defined('QUOTE_BUTTON')):
	define(QUOTE_BUTTON,'Quote');
endif;

if (!defined('EDIT_BUTTON')):
	define(EDIT_BUTTON,'Edit');
endif;

if (!defined('PM_BUTTON')):
	define(PM_BUTTON,'Send PM');
endif;

if (!defined('DELETE_BUTTON')):
	define(DELETE_BUTTON,'Delete');
endif;

if (!defined('SELECTED_LANGUAGE_PACK')):
	define(SELECTED_LANGUAGE_PACK,"english");
endif;

// Grab Member's default settings
$member_default = $dbc_calls->getMemberInfo($_SESSION['member_id']);
if (!defined('HOUR_DIFF_FROM_GMT')):
	if (is_numeric($member_default[1][member_timezone])):
		// If the timezone was changed via post, modify the definition as such
		if (is_numeric($_POST[member_timezone]) && ($_POST[member_timezone] >= -12 && $_POST[member_timezone] <= 13)):
			define(HOUR_DIFF_FROM_GMT, $_POST[member_timezone]);
		else:
			define(HOUR_DIFF_FROM_GMT, $member_default[1][member_timezone]);
		endif;
	else:
		define(HOUR_DIFF_FROM_GMT, 0);
	endif;
endif;

?>