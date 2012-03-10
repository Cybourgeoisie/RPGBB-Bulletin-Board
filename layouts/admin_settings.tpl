	<div id="admin_form">
		<div class="form">
		<form action="./admincp.php?a=settings" method="post">
        <fieldset>
		<legend>Forum Settings</legend>
		<table><tr>
        <td class="short">Forum Name</td>
   		 	<td>
        	<input type="text" name="forum_name" size="50" value="{FORUM_NAME}" />
        	</td>
    	</tr><tr>
        <td class="short">Forum Description</td>
    		<td>
       		<textarea name="forum_desc" cols="50" rows="3">{FORUM_DESC}</textarea>
        	</td>
    	</tr><tr>
        <td class="short">Forum Logo</td>
    		<td>
       		<input type="text" name="forum_logo_url" size="50" value="{FORUM_LOGO_URL}" />
        	</td>
        </tr><tr>
        <td class="short">Forum Banner</td>
    		<td>
       		<input type="text" name="forum_banner_url" size="50" value="{FORUM_BANNER_URL}" />
        	</td>
        </tr></table>
    	</fieldset>
        <br />
        
        <fieldset>
		<legend>Thread Settings</legend>
        <table><tr>
        <td class="long">Number of threads to display on each forum page</td>
   		 	<td>
 			{ADMIN_NUM_THREADS_OPTIONS}
        	</td>
        </tr><tr>
        <td class="long">Number of posts to display on each thread page</td>
   		 	<td>
 			{ADMIN_NUM_POSTS_OPTIONS}
        	</td>        
        </tr></table>
    	</fieldset>
		<br />
		<input type="submit" name="change_settings_submit" value="Save Settings" /> | 
         <input type="reset" value="Reset to Previous Settings" />
		</form>
        </div>
	</div>
    