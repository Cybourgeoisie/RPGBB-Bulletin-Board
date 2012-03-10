	<div id="admin_form">
		<div class="form">
    	<form action="./admincp.php?a=forums&b=permissions&{LC_FORUM_OR_CAT}={THIS_FORUM_OR_CAT_ID}" method="post">
        <fieldset>
		<legend>Edit Permissions for {THIS_FORUM_OR_CAT_NAME}</legend>        	
         <table>
         <tr>
         <td class="short"><strong>Group Name</strong></td>
         <td><strong>View</strong></td>
         <td><strong>Read</strong></td>
         <td><strong>Post</strong></td>
         <td><strong>Edit</strong></td>
         <td><strong>Delete</strong></td>
         <td><strong>Lock</strong></td>
         </tr>
            {PERMISSIONS_TD}         
         </table>
    	</fieldset>
        <br />
        <input type="hidden" name="total_iterations" value="{ITERATION}" />
        <input type="hidden" name="{LC_FORUM_OR_CAT}_id" value="{THIS_FORUM_OR_CAT_ID}" />
        Apply to all children (Forums, Subforums) <input type="checkbox" name="apply_to_children" value="1" /><br /><br />
		<input type="submit" name="permissions_forums_submit" value="Save Changes" /> | 
         <input type="reset" value="Reset Values" />
		</form>
        </div>
	</div>