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
/functions/list_pages.inc.php
******************************************
This file compiles the page display lists for 
forums or threads.
******************************************/


if (!defined('SCROLLIO')): 
	die('You are unauthorised to view this file.'); 
endif;

	function list_pages($items, $display_num, $type) {
		global $template;
		
		// Get the pages navigations
		for ($i = 0; $i < $items+1; $i+=$display_num):
			
			// Determine the page number
			$page_number = $i/$display_num + 1;
			
			// If the current starting post is equal to the GET value, then choose selected='selected' over class='class'
			if ($_GET[start] == $i):
				$template->setVar('PAGE_CLASS', 'selected');
			else:
				$template->setVar('PAGE_CLASS', 'class');
			endif;
			
			// If this displays forum pages, set the proper information
			if ($type == 'forum'):
				$id = $template->getVar('FORUM_ID');
				$get_type = 'forum';
			// If this displays a thread, set the proper information
			elseif ($type == 'thread'):
				$id = $template->getVar('THREAD_ID');
				$get_type = 'id';
			// Default the information, which shouldn't be necessary
			else:
				$type = 'forum';
				$id = 'null';
				$get_type = 'null';
			endif;
			
			// Add to the two types of displays, one with a hyperlink, the other with an option
			$links .= '<a href="./'.$type.'.php?'.$get_type.'=' . $id . '&start=' . $i . '" class="' . $template->getVar('PAGE_CLASS') . '">' . $page_number . '</a> ';
			$options .= '<option value="' . $i . '" ' . $template->getVar('PAGE_CLASS') . '="' . $template->getVar('PAGE_CLASS') . '">' . $page_number . '</option> ';
		endfor;
		
		// Return an array with the two kinds of displays
		return array('links'=>$links,'options'=>$options);
	}
?>