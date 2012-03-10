		<form action="./usercp.php?mode=preferences" method="post">
		<fieldset>
		<legend>Preferences</legend>
		<div class="form">
        <table><tr>
        	<td class="short">Timezone difference from GMT</td>
   		 	<td>
        	{TIMEZONE_SELECTION}
        	</td>
        </tr></table>
    	</div>
        </fieldset>
		<br />
    	<input type="hidden" name="member_id" value="{MEMBER_ID}" />
		<input type="submit" name="profile_pref_submit" value="Save Preferences" /> | 
         <input type="reset" value="Reset Values" />
		</form>