        	<div class="{ALTERNATING_FORUM} {PREFIX}forum" id="forum_{FORUM_ID}">
            	<div class="forum_icon"></div>
				<div class="thread_last_updated">
                {LAST_UPDATED_MESSAGE}
                <a href="./thread.php?id={THREAD_ID}&start={LAST_UPDATED_START}#{POST_ID}">{LAST_UPDATED_AUTHOR}</a>
            	<br />{LAST_UPDATED_TIME}</div>
                <div class="num_posts">{FORUM_POSTS}</div>
				<div class="num_threads">{FORUM_THREADS}</div>
				<div class="{PREFIX}forum_title"><a href="./forum.php?forum={FORUM_ID}">{FORUM_NAME}</a></div>
				<div class="{PREFIX}forum_description">{FORUM_DESC}&nbsp;<!--[Moderators: <a href="#">The Ocarina</a>]--></div>
			</div>