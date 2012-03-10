	<h2>{PROCESS_HEADER}</h2>
    <h4>{PROCESS_SUCCESS}</h4>
    <p>Want your very own Scrollio forum but don't have a place to put it? Let us host you for free!</p>
    <p>If you want to test drive Scrollio, you can do so without registering for a forum. <a href="./forums/test/">Go here to test drive.</a></p>
    	<div class="form" style="width:450px;">
        <form action="./signup.php" method="post">
		<fieldset>
		<legend>Sign Up for a Scrollio Forum</legend>
		{PROCESS_ERROR}
        <table><tr>
        		<td>Forum Name</td>
            	<td><input type="text" name="signup_forum_name" value="{FORUM_NAME}" /></td>
            </tr><tr>
        		<td>Forum URL<br />(scrollio.com/forums/[your-forum-url])</td>
            	<td><input type="text" name="signup_forum_url" value="{FORUM_URL}" /></td>
            </tr><tr>
        		<td>E-mail Address</td>
            	<td><input type="text" name="signup_member_email" value="{ADMIN_EMAIL}" /></td>
            </tr><tr>
   	 			<td>Admin Username</td>
            	<td><input type="text" name="signup_member_name" value="{ADMIN_NAME}" /></td>
   			</tr><tr>
            	<td>Admin Password</td>
            	<td><input type="password" name="signup_member_password" /></td>
    		</tr><tr>
            	<td>Re-enter Password</td>
            	<td><input type="password" name="signup_member_password_check" /></td>
		</tr></table>
        	<input type="submit" name="signup_submit" value="Get My Forum" /> | <input type="reset" value="Clear Form" />
		</fieldset>
		</form>
        </div>
