<?php if(isset($title)): ?>
	<h1 class="wp-heading-inline"><?php echo $title ?></h1>
<?php endif; ?>

<?php if(isset($actions) && is_array($actions)): ?>

	<?php foreach ($actions as $action): ?>

		<a href="<?php echo ($action['url'] ?? '#') ?>" class="page-title-action <?php echo ($action['cssClass'] ?? '') ?>">
			<?php echo $action['title']; ?>
		</a>

	<?php endforeach; ?>

<?php endif; ?>

<hr class="wp-header-end">
