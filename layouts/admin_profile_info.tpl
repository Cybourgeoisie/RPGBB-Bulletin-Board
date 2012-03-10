	<div id="admin_form">
		<div class="form">
    	<form action="./admincp.php?a=members&b=profile" method="post">
        <fieldset>
		<legend>Edit Profile Information Questions</legend>
		<table class="trio">
        <tr><td>
			<strong>Information Field Title</strong>
        </td><td>
        	<strong>Information Field Type</strong>
        </td><td>
        	<strong>Information Image Address</strong>
        </td></tr>
        	{PROFILE_INFORMATION_QUESTIONS}
        </table>
    	</fieldset>
		
        <br />
        <input type="hidden" name="total_iterations" value="{ITERATION}" />
		<input type="submit" name="edit_profile_info_submit" value="Save Changes" /> | 
         <input type="reset" value="Reset Values" />
		</form>
        </div>
	</div>