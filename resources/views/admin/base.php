<?php
/**
 * @var \Shemi\Core\View\Template $this
 */
?>

<div class="wrap meilipress">

    <?php echo $this['header'] ?? '' ?>

	<div id="meilipress-app">
        <div v-for="(message, id) in messages"
             :key="id"
             :class="{['notice-'+message.type]: true, 'is-dismissible': message.dismissible}"
             class="notice">
            <p>
                <b>MeiliPress:</b>
                {{ message.content }}
                <a :href="message.action.url" v-if="message.action">
                    {{ message.action.label }}
                </a>
            </p>
        </div>

		<?php echo $this['content'] ?? '' ?>

        <mp-ajax-sync-popup ref="syncPopup"></mp-ajax-sync-popup>
	</div>

</div>