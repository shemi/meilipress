<p class="help-message">
	<?php _e("The stop-words route lets you add a list of words that will be ignored in search queries. So if you add the as a stop word and you make a search on the mask you will only have matching documents with mask.", MP_TD) ?>
	<a href="https://docs.meilisearch.com/references/stop_words.html" target="_blank">
		<?php _e("Read more on MeiliSearch docs.") ?>
	</a>
</p>

<mp-stop-words v-model="form.data.stop_words"></mp-stop-words>