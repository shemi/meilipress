<?php
/**
 * @var \Shemi\Core\View\Template $this
 */

$this->extend('admin.base');
?>

<?php $this->block('header') ?>

<?php $this->include('admin.partials.header', ['title' => $pageTitle]) ?>

<?php $this->endblock() ?>

<mp-settings-view :form-data='<?php echo json_encode($data, JSON_UNESCAPED_UNICODE); ?>' inline-template>
    <div class="meilipress-settings-view">
        <form action="post" @submit.prevent="submit">
            <h2><?php _e("Connection", MP_TD) ?></h2>

            <mp-field label="<?php _e("URL", MP_TD) ?>" :error="errors.instance.url || ''">
                <input type="text" class="regular-text" id="connection_url" v-model="form.instance.url">
            </mp-field>

            <mp-field label="<?php _e("Master Key", MP_TD) ?>">
                <input type="text" class="regular-text" id="connection_master_key" v-model="form.instance.masterKey">
            </mp-field>

            <mp-field label="<?php _e("Index Prefix", MP_TD) ?>"
                      message="<?php _e("in case you have multiple application on the same MeiliSearch instance, this will prevent index override", MP_TD) ?>">
                <input type="text" class="regular-text" id="connection_index_prefix" v-model="form.instance.indexPrefix">
            </mp-field>

            <div class="submit-row inline">
                <mp-button type="button"
                           :loading="testing"
                           @click.native.prevent="testConnection"
                           :large="false" :primary="false">
                    <?php _e("Test connection", MP_TD) ?>
                </mp-button>
            </div>

            <h2><?php _e("Sync", MP_TD) ?></h2>

            <mp-field label="<?php _e("Documents per cycle", MP_TD) ?>"
                      message="<?php _e("The number of documents (posts) per sync cycle", MP_TD) ?>"
                      :error="errors.sync.documents_per_sync || ''">
                <input type="number"
                       v-model="form.sync.documents_per_sync"
                       id="sync_docs_per_cycle">
            </mp-field>

            <mp-field label="<?php _e("Sync type", MP_TD) ?>"
                      :error="errors.sync.type || ''">
                <select type="number" class="regular-text"
                        v-model="form.sync.type"
                        id="sync_type">
                    <option value="cron"><?php _e("cron sync", MP_TD) ?></option>
                    <option value="ajax"><?php _e("ajax sync", MP_TD) ?></option>
                </select>

                <p slot="message">
                    <?php
                    _e("Select the type of sync, you have 2 options:<br>
                            1. cron - sync all documents in the background (recommended only if the cron is called using the CLI)<br>
                            2. ajax - using ajax requests you will have to stay on the page until all documents synced.")
                    ?>
                </p>
            </mp-field>

            <div class="submit-row inline">
                <mp-button :loading="saving">
					<?php _e("Save settings", MP_TD) ?>
                </mp-button>
            </div>

        </form>

    </div>
</mp-settings-view>


