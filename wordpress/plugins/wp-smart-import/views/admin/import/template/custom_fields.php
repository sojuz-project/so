<div class="wpsi-portlet" id="sec_custom_fields">
    <div class="wpsi-portlet-title">
        <div class="caption">
            <span class="caption-subject"><?php wpsi_helper::_e('Custom Fields'); ?></span>
        </div>
    </div>
    <div class="wpsi-portlet-body">
        <table class="wpsi-table wpsi-custom-field-tab">
            <thead>
                <tr>
                    <th> <?php wpsi_helper::_e('Key'); ?> </th>
                    <th> <?php wpsi_helper::_e('Value'); ?> </th>
                    <th> <?php wpsi_helper::_e('Action'); ?> </th>
                </tr>
            </thead>
            <tbody>
            <?php 
                if (isset($post_data['custom_field_name'])) {
                	$post_data['custom_field_name'] = array_values(array_filter($post_data['custom_field_name']));
                }
            if (isset($post_data['custom_field_name']) && !empty($post_data['custom_field_name'])):   
            	foreach ($post_data['custom_field_name'] as $idx => $value) : ?>
                    <tr>
                        <td>
                            <input type="text" name="custom_field_name[]" class="wpsi-form-control drop-target" value="<?php echo esc_attr($value); ?>" >
                        </td>
                        <td>
                            <input type="text" name="custom_field_value[]" class="wpsi-form-control drop-target" value="<?php echo esc_attr($post_data['custom_field_value'][$idx]); ?>" >
                        </td>
                        <td> 
                            <a class="dashicons dashicons-trash remove-field"></a> 
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td>
                        <input type="text" name="custom_field_name[]" class="wpsi-form-control" >
                    </td>
                    <td>
                        <input type="text" name="custom_field_value[]" class="wpsi-form-control" >
                    </td>
                    <td>
                        <a class="dashicons dashicons-trash remove-field"></a>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
        <div class="add-new-block">
            <button class="add_custom_field wpsi-button wpsi-button-block" type="button" > 
                <span class="dashicons dashicons-plus"></span>
                <?php wpsi_helper::_e('Add Custom Field'); ?> 
            </button>
        </div>
    </div>
    <!-- wpsi-portlet-body END -->
</div>
<!-- wpsi-portlet END -->