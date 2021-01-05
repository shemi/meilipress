<p class="help-message">
	<?php _e("Query the database and build the Index documents.", MP_TD) ?><br>
    <?php _e("A document is an object composed of one or more fields. Each field consists of an attribute and its associated value.", MP_TD) ?>
    <a href="https://docs.meilisearch.com/guides/main_concepts/documents.html#documents" target="_blank">
		<?php _e("Read more on MeiliSearch docs.") ?>
    </a>
</p>

<h2><?php _e("Type & Status", MP_TD); ?></h2>

<mp-field label="<?php _e("Post Type", MP_TD) ?>" :error="errors.data.query.post_type || ''">
    <v-select :options="postTypes"
              :reduce="postType => postType.id"
              v-model="form.data.query.post_type"
              placeholder="<?php _e("Any", MP_TD) ?>"
              class="regular-text"
              taggable
              multiple>
    </v-select>
    <p slot="message" class="message"><?php esc_attr(printf(__('Index posts by <a href="%1$s" target="_blank">post type</a>.', MP_TD), 'https://wordpress.org/support/article/post-types')); ?></p>
</mp-field>

<mp-field label="<?php _e("Post Status", MP_TD) ?>" :error="errors.data.query.post_status || ''">
    <v-select :options="statuses"
              :reduce="status => status.id"
              v-model="form.data.query.post_status"
              placeholder="<?php _e("Any", MP_TD) ?>"
              class="regular-text"
              taggable
              multiple>
    </v-select>
    <p slot="message" class="message"><?php esc_attr(printf(__('Index posts by <a href="%1$s" target="_blank">post status</a>.', MP_TD), 'https://wordpress.org/support/article/post-status')); ?></p>
</mp-field>

<hr>

<h2><?php _e("Post & Page", MP_TD); ?></h2>

<mp-field label="<?php _e("Post Parent", MP_TD) ?>" :error="errors.data.query.post_parent || ''">
    <input type="text"
           placeholder="<?php _e("None", MP_TD) ?>"
           id="query_post_parent"
           class="regular-text"
           v-model="form.data.query.post_parent">
    <p slot="message" class="message"><?php _e("Index only child-posts of a parent-post id.", MP_TD) ?></p>
</mp-field>

<hr>

<h2><?php _e("Author", MP_TD); ?></h2>

<mp-field label="<?php _e("Author ID", MP_TD) ?>" :error="errors.data.query.author || ''">
    <input type="text"
           placeholder="<?php _e("None", MP_TD) ?>"
           id="query_post_parent"
           class="regular-text"
           v-model="form.data.query.author">
    <p slot="message" class="message"><?php _e("Index only posts by author ID.", MP_TD) ?></p>
</mp-field>

<mp-field label="<?php _e("Author Name", MP_TD) ?>" :error="errors.data.query.author_name || ''">
    <input type="text"
           placeholder="<?php _e("None", MP_TD) ?>"
           id="query_post_parent"
           class="regular-text"
           v-model="form.data.query.author_name">
    <p slot="message" class="message"><?php _e("Index only posts by author 'user_nicename'.", MP_TD) ?></p>
</mp-field>

<hr>

<h2><?php _e("Date", MP_TD); ?></h2>

<mp-field label="<?php _e("Date Query", MP_TD) ?>" :error="errors.data.query.date_query || ''">
    <mp-date-query-field v-model="form.data.query.date_query"></mp-date-query-field>
</mp-field>

<hr>

<h2><?php _e("Taxonomy", MP_TD); ?></h2>

<mp-field label="<?php _e("Taxonomy Query", MP_TD) ?>" :error="errors.data.query.tax_query || ''">
    <mp-tax-query-field v-model="form.data.query.tax_query"></mp-tax-query-field>
</mp-field>

<hr>

<h2><?php _e("Meta", MP_TD); ?></h2>

<mp-field label="<?php _e("Meta Query", MP_TD) ?>" :error="errors.data.query.meta_query || ''">
    <mp-meta-query-field v-model="form.data.query.meta_query"></mp-meta-query-field>
</mp-field>


