	<div id="admin_form">
		<div class="form">
		<form action="./admincp.php?a=groups" method="post">
        <fieldset>
		<legend>Create New Group</legend>
		<table><tr>
        <td class="short">Group Name</td>
   		 	<td>
        	<input type="text" name="group_name" size="50" value="" />
        	</td>
            </tr><tr>
    	<td class="short">Group Description</td>
    		<td>
       		<textarea name="group_desc" cols="50" rows="5"></textarea>
        	</td>
    	</tr></table>
    	</fieldset>
		<br />
		<input type="submit" name="create_group_submit" value="Add New Group" /> | 
         <input type="reset" value="Reset Values" />
		</form>
        
        <fieldset>
		<legend>Manage Groups</legend>
        	<table>
			{GROUPS_LIST}
    		</table>
        </fieldset>
    	</div>
	</div>