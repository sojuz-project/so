<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="woocommerce">
	<div class="icon32" id="icon-woocommerce-importer"><br></div>
    <h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
        <a href="<?php echo admin_url('admin.php?page=wf_woocommerce_csv_im_ex'); ?>" class="nav-tab <?php echo ('help' != $tab) ? 'nav-tab-active' : ''; ?>"><?php _e('Product Export', 'product-import-export-for-woo'); ?></a>
        <a href="<?php echo admin_url('admin.php?import=xa_woocommerce_csv'); ?>" class="nav-tab"><?php _e('Product Import', 'product-import-export-for-woo'); ?></a>
        <a href="<?php echo admin_url('admin.php?page=wf_woocommerce_csv_im_ex&tab=help'); ?>" class="nav-tab <?php echo ('help' == $tab) ? 'nav-tab-active' : ''; ?>"><?php _e('Help', 'product-import-export-for-woo'); ?></a>
        <a href="https://www.webtoffee.com/product/product-import-export-woocommerce/" target="_blank" class="nav-tab nav-tab-premium"><?php _e('Upgrade to Premium for More Features', 'product-import-export-for-woo'); ?></a>
    
    </h2>
        	<?php
		switch ($tab) {
			case "help" :
				$this->admin_help_page();
			break;
                    
                        case "export":
			default :
				$this->admin_export_page();
			break;
		}
	?>
</div>