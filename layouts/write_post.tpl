	{POST_HEADER}
	<div class="posting_body">
		<form action="./process.php" method="post">
		<div class="posting_title">
			Title: <input type="text" name="post_title" size="50" value="{POST_TITLE}">
   		</div>
		<div class="posting_content">
   	 		<textarea name="post_content" cols="70" rows="10">{POST_BODY}</textarea>
    	</div>
        <input type="hidden" name="parent_id" value="{PARENT_ID}">
        <input type="hidden" name="post_mode" value="{MODE}">
        <input type="hidden" name="post_type" value="{POST_TYPE}">
        <input type="hidden" name="post_id" value="{POST_ID}">
        <input type="hidden" name="thread_id" value="{THREAD_ID}">
        <input type="hidden" name="author_id" value="{AUTHOR_ID}">
    	<div class="posting_options">
    		<input type="submit" name="post_submit" value="Post">
    	</div>
    	</form>
	</div>