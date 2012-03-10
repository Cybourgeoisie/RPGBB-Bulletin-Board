<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="Description" content="{FORUM_DESC}">

	<!-- HTML, CSS, and jQuery includes -->
	<link rel="stylesheet" type="text/css" href="{PATH_TO_TEMPLATE}/original_fluid.css" />
	<link rel="stylesheet" type="text/css" href="{PATH_TO_FILES}layouts/bbcode.css" />
	<script type="text/javascript" src="{PATH_TO_FILES}functions/jquery.inc.js"></script>
	<script type="text/javascript" src="{PATH_TO_FILES}functions/jquery_scrollio.inc.js"></script>

	<title>{FORUM_NAME} {TITLE_SEPARATOR} {PAGE_NAME}</title>

</head>
<body>

<!-- Header Navigation -->
<div class="header" id="header_navbar">
	<div id="top_links"> 
		{TOP_LINKS}
	</div>
        
    <div id="banner">
		<div style="background-image:url('{FORUM_BANNER_URL}');">
        	{FORUM_LOGO}
        </div>
	</div>
    
    <div id="user_links"> 
		<a href="./usercp.php?mode=profile">User CP</a> {BREADCRUMBS} 
        <a href="./usercp.php?mode=messages&type=in">Private Messages {NEW_PM}</a>
    </div>
    
    <div id="nav_links"> 
		{NAV_LINKS}
    </div>    
</div>

<div class="forum_body">