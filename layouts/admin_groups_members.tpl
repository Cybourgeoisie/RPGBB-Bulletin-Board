	<div id="admin_form">
		<div class="form">

        <fieldset>
		<legend>{GROUP_NAME} Members</legend>
		<table>
        {GROUP_MEMBERS}
        </table>
    	</fieldset>
		<br />
        
        <form action="./admincp.php?a=groups&members={GROUP_ID}" method="post">
        <fieldset>
		<legend>Add Member</legend>
		<table><tr>
        <td class="short">Member Name</td>
        <td>{MEMBERS_OPTIONS}</td>
        </tr></table>
    	</fieldset>
		<br />
		<input type="hidden" name="group_id" value="{GROUP_ID}" />
        <input type="submit" name="add_group_member" value="Add Member" />
		</form>
        </div>
	</div>