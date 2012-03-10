	<div id="admin_form">
		<div class="form">
		<form action="./admincp.php?a=forums" method="post">
        <fieldset>
		<legend>Edit {FORUM_OR_CATEGORY}</legend>
		<table><tr>
        <td>{FORUM_OR_CATEGORY} Name</td>
   		 	<td>
        	<input type="text" name="{LC_FORUM_OR_CATEORY}_name" size="50" value="{FORUM_OR_CAT_NAME}" />
        	</td>
        </tr><tr>
        <td class="short">{FORUM_OR_CATEGORY} Description</td>
   		 	<td>
        	<input type="text" name="{LC_FORUM_OR_CATEORY}_desc" size="50" value="{FORUM_OR_CAT_DESC}" />
        	</td>
        </tr><tr>
        <td class="short">{PARENT}</td>
   		 	<td>
        	{OPTIONS}
        	</td>
        </tr>
        </table>
    	</fieldset>
		<br />
		{FORUM_OR_CAT_ID_INPUT}
        <input type="submit" name="edit_submit" value="Edit {FORUM_OR_CATEGORY}" />
		</form>
        </div>
	</div>