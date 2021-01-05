<?php
/**
 * @var \Shemi\Core\View\Template $this
 */

$this->extend('admin.base');
?>

<?php $this->block('header') ?>

<?php $this->include('admin.partials.header', ['title' => __("MeiliPress - Dashboard")]) ?>

<?php $this->endblock() ?>

this is the dashboard content!


