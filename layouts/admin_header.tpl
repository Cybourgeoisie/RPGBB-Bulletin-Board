<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

	<!-- HTML, CSS, and jQuery includes -->
	<link rel="stylesheet" type="text/css" href="{PATH_TO_TEMPLATE}/original_fluid.css" />
	<link rel="stylesheet" type="text/css" href="./layouts/bbcode.css" />
	<script type="text/javascript" src="functions/jquery.inc.js"></script>
    <script type="text/javascript" src="functions/jquery_scrollio.inc.js"></script>

	<title>{FORUM_NAME} {TITLE_SEPARATOR} {PAGE_NAME}</title>

</head>
<body>
<div class="admin_body">
	<div class="admin_menu">
    	<div class="top_col">
        	<a href="?a=main">Main</a><a href="?a=forums">Forums</a><a href="?a=groups">Groups</a><a href="?a=members">Members</a><!--<a href="?a=pages">Pages</a><a href="?a=template">Templates</a>-->
       	</div>
        <div class="submenu">
        	{SUBMENU_ITEMS}
        </div>
        {ADMIN_PROCESS_HEADER}