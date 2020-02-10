<?php 
if (!defined('ABSPATH')) { exit; }
if (empty($session)) {
    exit(wpsi_helper::_e('<h1> No Data Found </h1>'));
}
$file_path = $session['file_path'];
if (isset($file_path)) {
    $wpsiAjaxController = new wpsiAjaxController;
    $xmlfile_path = wpSmartImportCommon::current_xmlfile_path();
    $pathinfo = pathinfo($xmlfile_path);
    $extension = $pathinfo['extension'];
        if (file_exists($xmlfile_path)) {
            if ($extension == 'xml') {
                $wpsiAjaxController::$xml = simplexml_load_file($xmlfile_path); 
                $xml = $wpsiAjaxController::$xml;
                $parent = $xml->getName();
                $wpsiAjaxController->wpsi_recurse_xml($xml,$parent); 
                //count repeated all keys in XML  file
                $xmlcnt = wpsi_helper::key_count($wpsiAjaxController::$xmlcnt); 
?>
            <div class="wpsi-portlet" >
                <div class="wpsi-portlet-title">
                    <div class="caption">
                        <span class="caption-subject">
                            <?php wpsi_helper::_e("Elements") ?>
                        </span>
                    </div>
                    <div class="actions">
                        <a class="wpsi-go-upload-file" href="<?php echo admin_url('admin.php?page=wp_smart_import&action=index'); ?>">
                            <?php wpsi_helper::_e("Upload New File"); ?>
                        </a>
                    </div>
                </div>
                <div class="wpsi-portlet-body">
                    <form action="" method="POST" class="wpsi_element_form">
                        <div class="wpsi-element-view">
                            <div class="xml_element_list">
                                <h3>
                                    <?php wpsi_helper::_e('Select XML element you want to import'); ?>
                                </h3>
                                <?php foreach ($xmlcnt as $key => $cnt): ?>
                                    <a rel="<?php echo $key ?>" href="javascript:void(0)" class="wpsi-root-element" data-count="<?php echo esc_attr($cnt) ?>">
                                        <span class="elm_name"><?php echo $key; ?></span>
                                        <span class="elm_cnt"><?php echo $cnt; ?></span>
                                    </a> 
                                 <?php endforeach; ?>
                                <input type="hidden" name="wpsi_element[node]" id="input_node" value="">
                                <input type="hidden" name="wpsi_element[node_count]" id="input_nodecount" value="">
                            </div>
                            <div class="wpsi-nodes-preview"></div>
                        </div> 
                        <div class="wpsi-step-next">
                            <div class="upload-button-group">
                                <a class="wpsi-button wpsi-button-big btn-grp btn-next" href="<?php echo esc_url(admin_url('admin.php?page=wp_smart_import&action=index')); ?>" >
                                    <?php wpsi_helper::_e("<< Previous Step 1"); ?>
                                </a>
                                <button name="wpsi_submit" class="wpsi-button wpsi-button-big btn-grp btn-next" type="submit" value="element">
                                    <?php wpsi_helper::_e("Next Step 3 >>"); ?>
                                </button>
                            </div>
                        </div>
                    </form>
                </div> 
            </div>
<?php  }
    } // End file_exists 
} 