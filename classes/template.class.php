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
/classes/template.class.php
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

class template extends override {
	
	protected $override, $variables, $tag_definition;
	
	function __construct() {
		$this->override = new override;
		$this->variables['variablesSet'] = 'isset';
	}
	
	function displayPage($page, $return = false) {
		$page = $this->override->layout_or_template($page);
		
		if (!file_exists($page)):
        	return "This file - " . $page . " - does not exist.<br />\n";
    	endif;
		
		$display = file_get_contents($page);
 		
		// Handle User/Default replacements
		if (preg_match_all("/(\{__USER#([a-zA-Z_]+)\|([a-zA-Z_]+)\})/", $display, $matches)):
			for($i = 0; $i < count($matches); $i++):
				if (isset($_SESSION[member_status])):
					$display = preg_replace("/(\{__USER#".$matches[2][$i]."\|".$matches[3][$i]."\})/", "{".$matches[2][$i]."}", $display) ;
				else:
					$display = preg_replace("/(\{__USER#".$matches[2][$i]."\|".$matches[3][$i]."\})/", "{".$matches[3][$i]."}", $display) ;
				endif;
			endfor;
		endif;
		
		// Handle Admin/Default replacements
		if (preg_match_all("/(\{__ADMIN#([a-zA-Z_]+)\|([a-zA-Z_]+)\})/", $display, $matches)):
			for($i = 0; $i < count($matches); $i++):
				if (member_is_admin($_SESSION['member_id'])):
					$display = preg_replace("/(\{__ADMIN#".$matches[2][$i]."\|".$matches[3][$i]."\})/", "{".$matches[2][$i]."}", $display) ;
				else:
					$display = preg_replace("/(\{__ADMIN#".$matches[2][$i]."\|".$matches[3][$i]."\})/", "{".$matches[3][$i]."}", $display) ;
				endif;
			endfor;
		endif;
		
		// For reference
		// $display = preg_replace("/\{__([a-zA-Z]+)#([a-zA-Z_]+)\|([a-zA-Z_]+){1}\}/", "1:$1 2:$2 3:$3", $display);
		
		// Replace all declared tags
    	if (isset($this->variables['variablesSet'])):
			foreach ($this->variables as $name => $variable):
				// Handle simple replacements
				$tag_definition = '{' . $name . '}';
    	    	$display = str_replace($tag_definition, $variable, $display);
			endforeach;
 		endif;
 		
		// Replace undeclared template tags with an empty string
		$display = preg_replace("/\{([a-zA-Z_0-9]+)\}/", "", $display);
		
		if (!$return):
    		echo $display;
		else:
			return $display;
		endif;
	}
	
	function getVar($name) {
		return $this->variables[$name];
	}
	
	function setVar($name, $variable) {
		$this->variables[$name] = $variable;
	}
	
}

?>