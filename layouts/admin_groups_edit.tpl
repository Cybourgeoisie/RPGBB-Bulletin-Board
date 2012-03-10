	<div id="admin_form">
		<div class="form">
		<form action="./admincp.php?a=groups" method="post">
        <fieldset>
		<legend>Edit Group</legend>
		<table><tr>
        <td>Group Name</td>
   		 	<td>
        	<input type="text" name="group_name" size="50" value="{GROUP_NAME}" />
        	</td>
        </tr><tr>
        <td class="short">Group Description</td>
   		 	<td>
        	<input type="text" name="group_desc" size="50" value="{GROUP_DESC}" />
        	</td>
        </tr>
        </table>
    	</fieldset>
		<br />
		<input type="hidden" name="group_id" value="{GROUP_ID}" />
        <input type="submit" name="edit_group_submit" value="Edit Group" />
		</form>
        </div>
	</div>