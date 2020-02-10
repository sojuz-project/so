<div class="wpsi-portlet" id="sec_unique_key" >
    <div class="wpsi-portlet-title">
        <div class="caption">
            <span class="caption-subject">
                <?php wpsi_helper::_e('Unique key'); ?>
                <span class="required"> * </span>
            </span>
        </div>
        <div class="actions">
            <abbr title="<?php wpsi_helper::_e('Unique key field is use to find post for retrive, update or delete operation and identify uniquely. In this field xPath will not work'); ?>" rel="tooltip" class="dashicons dashicons-editor-help"></abbr>
        </div>
    </div>
    <div class="wpsi-portlet-body">
        <input type="text" name="unique_key" class="wpsi-form-control" required="" placeholder="write Unique key here" value="<?php echo esc_attr(wpsi_helper::_d($post_data,'unique_key')); ?>"
         <?php echo isset($id) && !empty($id) ? 'readonly' :''?> /><br>
        <div class="wpsi-portlet-single-block">
            <div class="wpsi-single-block-content" style="float: left;">
                <label>
                    <input type="checkbox" name="update_post" value="yes" <?php checked(wpsi_helper::_d($post_data, 'update_post'), 'yes'); ?> />
                    <?php wpsi_helper::_e('Update Exiting "'. ucfirst($post_type) .'" if title are match
                    '); ?>
                </label>
            </div>
            <div class="actions" style="float: right;">
                <abbr title='<?php wpsi_helper::_e("If Checked it it will update existing \"".ucfirst($post_type)."\" <br/> if \"".ucfirst($post_type). "\" title are match."); ?>' rel="tooltip" class="dashicons dashicons-editor-help"></abbr>
            </div>
            <div class="wpsi-clear"></div>
        </div>
    </div>
    <!-- wpsi-portlet-body END -->
</div>
<!-- wpsi-portlet END -->