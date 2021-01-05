<p class="help-message">
	<?php _e("Ranking rules are built-in rules that ensure relevancy in search results. Ranking rules are applied in a default order which can be changed in the settings. You can add or remove rules and change their order of importance.", MP_TD) ?>
	<a href="https://docs.meilisearch.com/references/ranking_rules.html" target="_blank">
		<?php _e("Read more on MeiliSearch docs.") ?>
	</a>
</p>

<mp-ranking-rules v-model="form.data.ranking_rules" :index-form="form.data">

</mp-ranking-rules>