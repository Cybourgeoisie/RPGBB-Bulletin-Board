	<div id="admin_form">
		<div class="form">
    	<form action="./admincp.php?a=pages" method="post">
        <fieldset>
		<legend>Edit Pages</legend>
        	<table><tr>
            <td class="long">{PAGES_LIST}</td>
            	<td>{PAGES_OPTIONS}</td>
            </tr></table>
    	</fieldset>
        <br />
        <input type="hidden" name="member_id" value="{MEMBER_ID}" />
		<input type="submit" name="profile_info_submit" value="Save Changes" /> | 
         <input type="reset" value="Reset Values" />
		</form>
    
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
		<input type="submit" name="create_page_submit" value="Add New Page" /> | 
         <input type="reset" value="Reset Value" />
		</form>
        </div>
	</div>