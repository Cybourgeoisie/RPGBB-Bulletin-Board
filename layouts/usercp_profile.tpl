        <form action="./usercp.php?mode=profile" method="post" name="profile_info_form">
		<fieldset>
		<legend>Profile Information</legend>
		<div class="form">
        <table>
        
        {PROFILE_INFORMATION_QUESTIONS}
            
        </table>
        </div>
    	</fieldset>
		<br />
    	<input type="hidden" id="member_id" name="member_id" value="{MEMBER_ID}" />
		<input type="submit" name="profile_info_submit" value="Save Changes" /> | 
         <input type="reset" name="profile_info_reset" value="Reset Values" />
		</form>