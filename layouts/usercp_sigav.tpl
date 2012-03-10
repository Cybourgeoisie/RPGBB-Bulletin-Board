		<form action="./usercp.php?mode=sigav" method="post">
		<fieldset>
		<legend>Signature and Avatar</legend>
		<div class="form">
        <table><tr>
        	<td>Off-Site Avatar URL</td>
   		 	<td>
        	<input type="text" name="member_avatar" size="60" value="{AVATAR}" />
        	</td>
        </tr><tr>
        	<td>Signature</td>
    		<td>
       		<textarea name="member_signature" cols="60" rows="6">{SIGNATURE}</textarea>
        	</td>
        </tr></table>
    	</div>
        </fieldset>
		<br />
    	<input type="hidden" name="member_id" value="{MEMBER_ID}" />
		<input type="submit" name="profile_sigav_submit" value="Save Changes" /> | 
         <input type="reset" value="Reset Values" />
		</form>