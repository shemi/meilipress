<p class="help-message">
	<?php _e("Synonyms is an object containing words and their respective synonyms. A synonym in Meilisearch is considered equal to its associated word in a search query.", MP_TD) ?>
	<a href="https://docs.meilisearch.com/references/synonyms.html" target="_blank">
		<?php _e("Read more on MeiliSearch docs.") ?>
	</a>
</p>

<mp-synonyms v-model="form.data.synonyms"></mp-synonyms>