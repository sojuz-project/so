<div class="wpsi-portlet" id="sec_options">
    <div class="wpsi-portlet-title">
        <div class="caption">
            <span class="caption-subject">
                <?php wpsi_helper::_e(ucfirst($post_type).' Options'); ?>
            </span>
        </div>
    </div>
    <div class="wpsi-portlet-body">
        <div class="wpsi-portlet-single-block">
            <h4 class="block-title"> <?php wpsi_helper::_e('Set Post Password'); ?> :</h4>
            <div class="wpsi-single-block-content">
                <div class="get_post_types_list input-group ">
                    <input type="password" name="post_password" class="wpsi-form-control drop-target" placeholder="Enter Post Password here.." 
                        value="<?php echo esc_attr(wpsi_helper::_d($post_data, 'post_password')); ?>" >
                </div>
            </div>
        </div>
        <div class="wpsi-portlet-single-block">
            <?php wpsi_helper::_dref($post_data, 'comment_status', 'open'); ?>
            <h4 class="block-title"><?php wpsi_helper::_e('Comment Status  :'); ?></h4>
            <div class="wpsi-single-block-content">
                <label>
                    <input type="radio" name="comment_status" value="open" <?php checked($post_data['comment_status'], 'open'); ?> ><?php wpsi_helper::_e('Open'); ?>
                </label>
                <label>
                    <input type="radio" name="comment_status" value="closed" <?php checked($post_data['comment_status'], 'closed'); ?> > <?php wpsi_helper::_e("Closed"); ?>
                </label>
            </div>
        </div>
        <div class="wpsi-portlet-single-block">
            <?php wpsi_helper::_dref($post_data, 'date_type', 'auto'); ?>
            <div class="wpsi-portlet-single-title">
                <div class="caption">
                    <span><?php wpsi_helper::_e('Set Date  :'); ?></span>
                </div>
            </div>
            <div class="wpsi-single-block-content">
                <div class="wpsi-date-container">
                    <label>
                        <input type="radio" name="date_type" value="auto" class="show_hide_radio" <?php checked($post_data['date_type'], 'auto'); ?> ><?php wpsi_helper::_e('Auto'); ?>
                    </label>
                    <label>
                        <input type="radio" name="date_type" value="specific" class="show_hide_radio" <?php checked($post_data['date_type'], 'specific'); ?> ><?php wpsi_helper::_e('As specified'); ?>
                    </label>
                    <div class="slidingDiv wpsi-inner-block">
                        <div class="inner-content">
                            <input type="text" name="post_date" class="wpsi-form-control drop-target wpsi-cat-list date-picker" value="<?php echo esc_attr(wpsi_helper::_d($post_data,'post_date')); ?>" />
                        </div>
                    </div>
                    <label>
                        <input type="radio" name="date_type" value="random" class="show_hide_radio" <?php checked($post_data['date_type'], 'random'); ?> > 
                        <?php wpsi_helper::_e('Random Date'); ?>
                    </label>
                    <div class="slidingDiv wpsi-inner-block">
                        <div class="inner-content">
                            <div class="wpsi-col-6">
                                <input type="text" name="post_date_start" class="wpsi-form-control drop-target wpsi-cat-list from-date" placeholder="Start Date" value="<?php echo esc_attr(wpsi_helper::_d($post_data, 'post_date_start')); ?>" />
                            </div>
                            <div class="wpsi-col-6">
                                <input type="text" name="post_date_end" class="wpsi-form-control wpsi-cat-list to-date" placeholder="End Date" value="<?php echo esc_attr( wpsi_helper::_d($post_data, 'post_date_end')); ?>"/>
                            </div>
                            <div class="wpsi-clear"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="wpsi-portlet-single-block">
            <h4 class="block-title">
                <?php wpsi_helper::_e('Menu Order :'); ?>
            </h4>
            <div class="wpsi-single-block-content">
                <div class="get_post_types_list input-group ">
                    <input type="text" name="menu_order" class="wpsi-form-control drop-target" value="<?php echo esc_attr(wpsi_helper::_d($post_data, 'menu_order',0)); ?>" />
                </div>
            </div>
        </div>
        <div class="wpsi-portlet-single-block">
            <?php wpsi_helper::_dref($post_data, 'ping_status','open'); ?>
            <h4 class="block-title"><?php wpsi_helper::_e('Trackbacks and Pingbacks : '); ?></h4>
            <div class="wpsi-single-block-content">
                <label>
                    <input type="radio" name="ping_status" value="open" <?php checked($post_data['ping_status'] , 'open'); ?> /><?php wpsi_helper::_e('Open'); ?>
                </label>
                <label>
                    <input type="radio" name="ping_status" value="closed" <?php checked($post_data['ping_status'], 'closed'); ?> /><?php wpsi_helper::_e("Closed"); ?>
                </label>
            </div>
        </div>
        <div class="wpsi-portlet-single-block">
            <h4 class="block-title" title="<?php wpsi_helper::_e('Default slug is the sanitized post title when creating a new post.') ?>"><?php wpsi_helper::_e('Slug : '); ?></h4>
            <div class="wpsi-single-block-content">
                <div class="get_post_types_list input-group ">
                    <input type="text" name="slug" class="wpsi-form-control drop-target" value="<?php  echo wpsi_helper::_d( $post_data, 'slug') ?>"  >
                </div>
            </div>
        </div>
    </div>
    <!-- wpsi-portlet-body END -->
</div>
<!-- wpsi-portlet END -->