<?php
/**
 * @var \Shemi\Core\View\Template $this
 * @var \Shemi\MeiliPress\MeiliPress $plugin
 * @var \Shemi\Core\Foundation\Pages\Page $page
 */

$this->extend('admin.base');
?>

<?php
$this->block('header');

    $this->include('admin.partials.header', [
        'title' => $page->title()
    ]);

$this->endblock();
?>

<mp-index-view inline-template>
    <form class="mp-index-view" @submit.prevent="save">
        <main class="index-settings">

            <title-input v-model="form.name"
                         label="<?php _e("Index Name", MP_TD); ?>"
                         :error="errors.name"
                         prefix="<?php echo $plugin->settings('instance.indexPrefix') ?>">
                <span slot="message"><?php _e("Once you save, the index name cannot be changed!", MP_TD); ?></span>
            </title-input>

            <mp-tabs>

                <mp-tab id="query" label="<?php _e("Query", MP_TD); ?>">
                    <?php $this->include('admin.index-tabs.query') ?>
                </mp-tab>

                <mp-tab id="fields" label="<?php _e("Fields", MP_TD); ?>">
					<?php $this->include('admin.index-tabs.fields') ?>
                </mp-tab>

                <mp-tab id="synonyms" label="<?php _e("Synonyms", MP_TD); ?>">
					<?php $this->include('admin.index-tabs.synonyms') ?>
                </mp-tab>

                <mp-tab id="stop-words" label="<?php _e("Stop words", MP_TD); ?>">
					<?php $this->include('admin.index-tabs.stop-words') ?>
                </mp-tab>

                <mp-tab id="ranking" label="<?php _e("Ranking rules", MP_TD); ?>">
					<?php $this->include('admin.index-tabs.ranking') ?>
                </mp-tab>

            </mp-tabs>

        </main>

        <aside class="index-meta">
            <mp-meta-box title="<?php _e("Publish & State", MP_TD) ?>">
                <div class="index-state">
                    <p class="index-status">
                        <span class="dashicons dashicons-post-status"></span>
                        <?php _e("Status:", MP_TD) ?> <b>{{ `index_sync_state_${form.sync_state}` | __ }}</b>
                        <a href="#" class="disable-button"><?php _e("Disable", MP_TD) ?></a>
                    </p>
                    <p class="index-status">
                        <span class="dashicons dashicons-index-card"></span>
                        <?php _e("Index State:", MP_TD) ?>
                        <b v-if="! form.index_stats">{{ 'New' | __ }}</b>
                        <b v-else-if="form.index_stats.isIndexing">{{ 'Indexing...' | __ }}</b>
                        <b v-else>{{ 'Idle' | __ }}</b>
                    </p>

                    <p class="created-at">
                        <span class="dashicons dashicons-calendar-alt"></span>
						<?php _e("Created At:", MP_TD) ?>
                        <b>{{ form.created_at }}</b>
                    </p>

                    <p class="updated-at">
                        <span class="dashicons dashicons-calendar-alt"></span>
						<?php _e("Updated At:", MP_TD) ?>
                        <b>{{ form.updated_at }}</b>
                    </p>

                    <p class="posts-count">
                        <span class="dashicons dashicons-admin-post"></span>
						<?php _e("Posts Count:", MP_TD) ?> <b>{{ form.posts_count || 'N/A' }}</b>
                    </p>

                    <p class="index-count">
                        <span class="dashicons dashicons-media-document"></span>
						<?php _e("Documents Count:", MP_TD) ?>
                        <b>{{ form.index_stats ? form.index_stats.numberOfDocuments : 'N/A' }}</b>
                    </p>
                </div>

                <template slot="footer">
                    <mp-button :loading="loading" large>
                        <?php _e("Save", MP_TD) ?>
                    </mp-button>

                    <mp-button type="button"
                               @click.native.prevent="reindex"
                               v-if="form.id" :primary="false">
                        <span v-if="form.last_index">
                            <?php _e('Reindex', MP_TD); ?>
                        </span>
                        <span v-else>
                            <?php _e('Sync', MP_TD); ?>
                        </span>
                    </mp-button>
                </template>

            </mp-meta-box>

            <mp-search-meta-box v-if="form.id"
                                :index-id="form.id">
            </mp-search-meta-box>

        </aside>
    </form>
</mp-index-view>





