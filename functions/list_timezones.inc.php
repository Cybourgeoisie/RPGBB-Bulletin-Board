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
/functions/list_timezones.inc.php
******************************************
This file compiles the possible options for
timezones and prints out a select list
******************************************/

if (!defined('SCROLLIO')): 
	die('You are unauthorised to view this file.'); 
endif;

	function list_timezones($selection) {
		// Print the timezone hour options
		for ($i = -12; $i <= 13; $i++):
			// If $i is positive, append + to the number
			if ($i >= 0):
				$append = '+';
			endif;
			
			// If the selection is the same as $i, select
			if ($i == $selection):
				$options .= '<option value="' . $i . '" selected="selected">GMT ' . $append . $i . ' Hours</option>';
			else:
				$options .= '<option value="' . $i . '">GMT ' .$append . $i . ' Hours</option>';
			endif;
		endfor;
		
		// Return the select
		return '<select name="member_timezone">' . $options . '</select>';
	}
?>