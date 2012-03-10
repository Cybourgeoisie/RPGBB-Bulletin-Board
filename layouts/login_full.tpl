	<div id="login_form">
    	<div class="form">
    	<form action="./process.php" method="post">
    	<fieldset>
    	<legend>Log In</legend>
		<table><tr>
        	<td class="short">Username</td>
            <td><input type="text" name="log_member_name" /></td>
            </tr><tr>
			<td class="short">Password</td>
            <td><input type="password" name="log_member_password" /></td>
    	</tr></table>
        </fieldset><br />
		<input type="checkbox" name="set_remember_me" /> Remember Me | 
    	<input type="submit" name="log_submit" value="Log In" />
    	</form>
    	</div>
    </div>