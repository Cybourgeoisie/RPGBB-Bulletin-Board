<?php
	/*
	* phpBBCode
	* Revised, edited, and appended to
	* by Ben Heidorn for Scrollio
	*
	* @website   www.swaziboy.com
	* @author    Duncan Mundell
	* @updated   03/2003
	* @version   1.0a
	*/
	
	if (!defined('SCROLLIO')): 
		die('You are unauthorised to view this file.'); 
	endif;
	
	function bbcode($text) {
		
		// Set up the parameters for a URL search string
		$URLSearchString = " a-zA-Z0-9\:\/\-\?\&\.\=\_\~\#\'";
		// Set up the parameters for a MAIL search string
		$MAILSearchString = $URLSearchString . " a-zA-Z0-9\.@";
		
		// Perform URL Search
		$text = preg_replace("/\[url\]([$URLSearchString]*)\[\/url\]/", '<a href="$1" target="_blank">$1</a>', $text);
		$text = preg_replace("(\[url\=([$URLSearchString]*)\](.+?)\[/url\])", '<a href="$1" target="_blank">$2</a>', $text);
		
		// Perform MAIL Search
		$text = preg_replace("(\[mail\]([$MAILSearchString]*)\[/mail\])", '<a href="mailto:$1">$1</a>', $text);
		$text = preg_replace("/\[mail\=([$MAILSearchString]*)\](.+?)\[\/mail\]/", '<a href="mailto:$1">$2</a>', $text);
		
		// Check for bold text
		$text = preg_replace("(\[b\](.+?)\[\/b])is",'<span class="bbcode_bold">$1</span>',$text);
		
		// Check for Italics text
		$text = preg_replace("(\[i\](.+?)\[\/i\])is",'<span class="bbcode_italics">$1</span>',$text);
		
		// Check for Underline text
		$text = preg_replace("(\[u\](.+?)\[\/u\])is",'<span class="bbcode_underline">$1</span>',$text);
		
		// Check for strike-through text
		$text = preg_replace("(\[s\](.+?)\[\/s\])is",'<span class="bbcode_strikethrough">$1</span>',$text);
		
		// Check for over-line text
		$text = preg_replace("(\[o\](.+?)\[\/o\])is",'<span class="bbcode_overline">$1</span>',$text);
		
		// Check for colored text
		$text = preg_replace("(\[color=(.+?)\](.+?)\[\/color\])is","<span style=\"color: $1\">$2</span>",$text);
		
		// Check for sized text
		$text = preg_replace("(\[size=(.+?)\](.+?)\[\/size\])is","<span style=\"font-size: $1px\">$2</span>",$text);
		
		// Check for list text
		$text = preg_replace("/\[list\](.+?)\[\/list\]/is", '<ul class="bbcode_listbullet">$1</ul>' ,$text);
		$text = preg_replace("/\[list=1\](.+?)\[\/list\]/is", '<ul class="bbcode_listdecimal">$1</ul>' ,$text);
		$text = preg_replace("/\[list=i\](.+?)\[\/list\]/s", '<ul class="bbcode_listlowerroman">$1</ul>' ,$text);
		$text = preg_replace("/\[list=I\](.+?)\[\/list\]/s", '<ul class="bbcode_listupperroman">$1</ul>' ,$text);
		$text = preg_replace("/\[list=a\](.+?)\[\/list\]/s", '<ul class="bbcode_listloweralpha">$1</ul>' ,$text);
		$text = preg_replace("/\[list=A\](.+?)\[\/list\]/s", '<ul class="bbcode_listupperalpha">$1</ul>' ,$text);
		$text = str_replace("[*]", "<li>", $text);
		
		// Check for font change text
		$text = preg_replace("(\[font=(.+?)\](.+?)\[\/font\])","<span style=\"font-family: $1;\">$2</span>",$text);
		
		// Declare the format for [code] layout
		$CodeLayout = '<div class="bbcode_code"><div class="bbcode_header"><div class="bbcode_quotecodeheader"> Code:</div></div><div class="bbcode_box"><div class="bbcode_codebody">$1</div></div></div>';
		// Check for [code] text
		$text = preg_replace("/\[code\](.+?)\[\/code\]/is","$CodeLayout", $text);
		
		// Declare the format for [quote] layout
		$QuoteLayout = '<div class="bbcode_quote"><div class="bbcode_header"><div class="bbcode_quotecodeheader"> Quote:</div></div><div class="bbcode_box"><div class="bbcode_quotebody">$1</div></div></div>';
				 
		// Check for [code] text
		$text = preg_replace("/\[quote\](.+?)\[\/quote\]/is","$QuoteLayout", $text);
		
		// Images
		// [img]pathtoimage[/img]
		$text = preg_replace("/\[img\](.+?)\[\/img\]/", '<img src="$1">', $text);
		
		// [img=widthxheight]image source[/img]
		$text = preg_replace("/\[img\=([0-9]*)x([0-9]*)\](.+?)\[\/img\]/", '<img src="$3" height="$2" width="$1">', $text);
		
		return $text;
	}
?>