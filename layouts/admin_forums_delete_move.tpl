	<div id="admin_form">
		<div class="form">
		<form action="./admincp.php?a=forums" method="post">
        <fieldset>
		<legend>Delete {FORUM_OR_CATEGORY}</legend>
		<table><tr>
        <td>{FORUM_OR_CATEGORY} Name</td>
   		 	<td>
        	{FORUM_OR_CAT_NAME}
        	</td>
        </tr><tr>
        <td class="long">Move all forums and threads to</td>
   		 	<td>
        	{OPTIONS}
        	</td>
        </tr></table>
    	</fieldset>
		<br />
		{FORUM_OR_CAT_ID_INPUT}
        <input type="submit" name="delete_submit" value="Delete {FORUM_OR_CATEGORY}" />
		</form>
        </div>
	</div>