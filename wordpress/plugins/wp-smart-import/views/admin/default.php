<?php if (!defined('ABSPATH')) { exit; } ?>
<div class="wsi-wrap">
	<div class="wsi-body-header-title">
		<h2 class="plugin-title"><?php wpsi_helper::_e("Wp Smart Import"); ?></h2>
		<h1 class="page-title"><?php wpsi_helper::_e("Import XML"); ?></h1>
	</div>
	<div class="wpsi-body">
		<?php $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
    		echo wpSmartImportView::load(sanitize_text_field($_GET['page']), $action); ?>
		<div id="ajax-wait"></div>
	</div>
</div>