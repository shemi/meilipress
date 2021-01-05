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
        'title' => $page->title(),
        'actions' => [['url' => $plugin->getPageUrl('meilipress-index'), 'title' => __('New Index', MP_TD)]]
    ]);

$this->endblock();
?>

<mp-indexes-view inline-template>
    <form class="mp-indexes-view">
        <p class="search-box">
            <label class="screen-reader-text" for="post-search-input">Search Pages:</label>
            <input type="search" id="post-search-input" name="s" value="">
            <input type="submit" id="search-submit" class="button" value="Search Pages">
        </p>

        <list-table :columns="columns"
                    action-column="name"
                    :not-found="searchTerm ? '<?php _e('No indexes matching your search.', MP_TD) ?>' : '<?php _e('You didn\\\'t create indexes yet. create your first index by clicking the New Index button above.', MP_TD) ?>'"
                    :actions="actions"
                    :bulk-actions="bulkActions"
                    @action:click="onActionClick"
                    @bulk:click="onBulkAction"
                    :rows="indexes">

        </list-table>

    </form>
</mp-indexes-view>





