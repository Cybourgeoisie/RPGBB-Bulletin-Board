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
/classes/override.class.php
******************************************
Produces and displays forum information.

	Contents:
	__construct: 
		connects to the database and produces the
		array of forum layout information.
			
	layout_or_template: 
		determines if a template exists of the file
		before including the file itself

******************************************/
	
if (!defined('SCROLLIO')): 
	die('You are unauthorised to view this file.'); 
endif;

class override {
	
	function __construct($method = 0, $file = '') {
		// Allow shorter class calls
		if ($method == 1):
			$this->layout_or_template($file);
		endif;
	}

	function layout_or_template($file) {
		if (file_exists(PATH_TO_TEMPLATE . $file . '.tpl')):
			return PATH_TO_TEMPLATE . $file . '.tpl';
		endif;
		
		return PATH_TO_LAYOUT . $file . '.tpl';
	}
	
}

?>