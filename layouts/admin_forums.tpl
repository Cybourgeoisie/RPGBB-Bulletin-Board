	<div id="admin_form">
		<div class="form">
    	<form action="./admincp.php?a=forums&b=forums" method="post">
        <fieldset>
		<legend>Edit Forums</legend>        	
            {FORUMS_LISTING}
    	</fieldset>
        <br />
        <input type="hidden" name="total_iterations" value="{ITERATION}" />
		<input type="submit" name="change_submit" value="Save Changes" /> | 
         <input type="reset" value="Reset Values" />
		</form>
    	
        <form action="./admincp.php?a=forums" method="post">
        <fieldset>
		<legend>Add Category</legend>
		<table><tr>
        <td>Category Name</td>
   		 	<td>
        	<input type="text" name="cat_name" size="50" value="" />
        	</td>
        </tr><tr>
        <td>Category Description</td>
   		 	<td>
        	<input type="text" name="cat_desc" size="50" value="" />
        	</td>
        </tr></table>
    	</fieldset>
		<br />
		<input type="submit" name="create_cat_submit" value="Add Category" /> | 
         <input type="reset" value="Reset Values" />
		</form>
        
		<form action="./admincp.php?a=forums" method="post">
        <fieldset>
		<legend>Add Forum</legend>
		<table><tr>
        <td>Forum Name</td>
   		 	<td>
        	<input type="text" name="forum_name" size="50" value="" />
        	</td>
        </tr><tr>
        <td>Forum Description</td>
   		 	<td>
        	<input type="text" name="forum_desc" size="50" value="" />
        	</td>
        </tr><tr>
        <td>Forum Parent</td>
   		 	<td>
        	{FORUMS_OPTIONS}
        	</td>
        </tr><tr>
        <td>Category</td>
   		 	<td>
        	{CATEGORIES_OPTIONS}
        	</td>
        </tr></table>
    	</fieldset>
		<br />
		<input type="submit" name="create_forum_submit" value="Add Forum" /> | 
         <input type="reset" value="Reset Values" />
		</form>
        </div>
	</div>