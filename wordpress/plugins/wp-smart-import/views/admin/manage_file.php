<div class="wsi-wrap">
    <div class="wsi-body-header-title">
        <h2 class="plugin-title"> <?php wpsi_helper::_e("Wp Smart Import"); ?> </h2>
        <h1 class="page-title"> <?php wpsi_helper::_e("Manage Files"); ?> </h1>
    </div>
    <div class="wpsi-body">
<?php if (isset($_SESSION['res'])): ?>
    	<div id="message" class="updated notice is-dismissible">
			<p><?php wpsi_helper::_e($_SESSION['res']['msg']); ?></p>
			<button type="button" class="notice-dismiss">
                <span class="screen-reader-text">
                    <?php wpsi_helper::_e("Dismiss this notice."); ?>
                </span>
            </button>
		</div>
<?php session_destroy();  
    endif;
    $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
    echo wpSmartImportView::load(sanitize_text_field($_GET['page']), $action);
?>
    </div>
    <div id="ajax-wait"></div>
</div>