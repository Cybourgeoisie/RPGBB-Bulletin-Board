		<form action="./process.php" method="post">
		<div class="posting_title">
			To: <select name="pm_to" id="pm_to">
            <option value="NULL">Pick a Username -</option>
            {MEMBERS_OPTIONS}
            </select>
   		</div>
        <div class="posting_title">
			Title: <input type="text" name="pm_title" id="pm_title" size="50" value="{PM_TITLE}">
   		</div>
		<div class="posting_content">
   	 		<textarea name="pm_body" id="pm_body" cols="70" rows="10"></textarea>
    	</div>
        <input type="hidden" name="pm_from" id="pm_from" value="{USER_ID}">
        <input type="hidden" name="pm_re" id="pm_re" value="{PM_RE}">
        <input type="submit" name="pm_submit" id="pm_submit" value="Send">
    	</form>