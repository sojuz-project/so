
<div class="tool-box">
    <h3 class="title"><?php _e('Import Products in CSV Format:', 'product-import-export-for-woo'); ?></h3>
    <p><?php _e('Import products in CSV format ( works for simple products)  from different sources', 'product-import-export-for-woo'); ?></p>
    <p class="submit">
        <?php
        $merge_url = admin_url('admin.php?import=woocommerce_csv&merge=1');
        $import_url = admin_url('admin.php?import=woocommerce_csv');
        ?>
        <a class="button button-primary" id="mylink" href="<?php echo admin_url('admin.php?import=woocommerce_csv'); ?>"><?php _e('Import Products', 'product-import-export-for-woo'); ?></a>
        &nbsp;
        <input type="checkbox" id="merge" value="0"><?php _e('Merge products if exists', 'product-import-export-for-woo'); ?> <br>
    </p>
</div>
<script type="text/javascript">
    jQuery('#merge').click(function () {
        if (this.checked) {
            jQuery("#mylink").attr("href", '<?php echo $merge_url ?>');
        } else {
            jQuery("#mylink").attr("href", '<?php echo $import_url ?>');
        }
    });
</script>