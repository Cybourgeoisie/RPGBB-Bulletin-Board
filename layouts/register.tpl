	<h3>Welcome to {FORUM_NAME}. Take the time to register with us.</h3>
	
    <div id="register_form">
		<div class="form">
        <form action="./process.php" method="post">
		<fieldset>
		<legend>Registration Information</legend>
		<table><tr>
        		<td>E-mail Address</td>
            	<td><input type="text" name="reg_member_email" /></td>
            </tr><tr>
   	 			<td>Username</td>
            	<td><input type="text" name="reg_member_name" /></td>
   			</tr><tr>
            	<td>Password</td>
            	<td><input type="password" name="reg_member_password" /></td>
    		</tr><tr>
            	<td>Re-enter Password</td>
            	<td><input type="password" name="reg_member_password_check" /></td>
		</tr></table>
        	<input type="submit" name="reg_submit" value="Register" /> | <input type="reset" value="Clear Form" />
		</fieldset>
		</form>
        </div>
		<br />
	</div>
	<br />