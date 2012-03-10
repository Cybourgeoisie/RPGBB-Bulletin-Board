	<div id="admin_form">
    	<div class="form">
        <fieldset>
		<legend>Edit Pages</legend>
        	{PAGES_OPTIONS}
    	</fieldset>
        <br />
    
		<form action="./admincp.php?a=pages" method="post">
        <fieldset>
		<legend>Create Page</legend>
		<table><tr>
        	<td class="short">Page Name</td>
   		 	<td>
        	<input type="text" name="page_name" size="50" value="" />
        	</td>
        </tr></table>
    	</fieldset>
		<br />
		<input type="submit" name="profile_info_submit" value="Save Changes" /> | 
         <input type="reset" value="Reset Values" />
		</form>
        </div>
	</div>