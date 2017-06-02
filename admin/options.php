<div class="wrap">
	<h1>Настройки VK Community Messages</h1>
	<form action="options.php" method="post">
		<?php settings_fields( mihdan_vk_community_messages()->get_slug() ); ?>
		<?php do_settings_sections( mihdan_vk_community_messages()->get_slug() ); ?>
		<?php submit_button( 'Сохранить' ); ?>
	</form>
</div>