<div class="wpsi-portlet" id="sec_content">
   <div class="wpsi-portlet-title">
      <div class="caption">
         <span class="caption-subject"> <?php wpsi_helper::_e("Content"); ?> </span>
      </div>
      <div class="actions">
         <a class="wpsi-go-upload-file" href="<?php echo esc_url(admin_url('admin.php?page=wp_smart_import&action=index')); ?>">
            <?php wpsi_helper::_e("Upload New File"); ?>
         </a>
      </div>
   </div>
   <div class="wpsi-portlet-body">
      <p class="wpsi-note">
         <?php wpsi_helper::_e("NOTE : Drag and Drop node element for input"); ?>
      </p>
      <div class="wpsi-post-field-container wpsi-clear">
         <div class="wpsi-post-field-list-contaner">
            <div class="get_post_types_list input-group input-group-inline">
               <label for="post_title">
                  <?php wpsi_helper::_e('Post title'); ?> 
                  <span class="required"> * </span> 
               </label>
               <input type="text" name="post_title" class="wpsi-form-control drop-target"  required placeholder="<?php wpsi_helper::_e('Enter title here..'); ?>" value="<?php echo esc_attr(wpsi_helper::_d($post_data,'post_title')); ?>" id="title" />
            </div>
            <div class="get_post_types_list input-group input-group-inline">
               <label for="post_des">
                  <?php wpsi_helper::_e('Description'); ?>
                  <span class="required"> * </span>
               </label>
               <textarea name="post_des" id="content" class="wpsi-form-control drop-target" required placeholder="<?php wpsi_helper::_e("Enter description here.."); ?>" rows="5"><?php echo esc_textarea(wpsi_helper::_d($post_data, 'post_des')); ?></textarea>
            </div>
            <div class="get_post_types_list input-group input-group-inline" >
               <?php wpsi_helper::_dref($post_data, 'post_status','publish'); ?> 
               <label for="post_status"><?php wpsi_helper::_e( 'Post Status'); ?></label>
               <select name="post_status" class="wpsi-form-control" >
                  <option value="publish" <?php selected($post_data['post_status'], "publish" ); ?> >
                     <?php wpsi_helper::_e('Published'); ?>
                  </option>
                  <option value="pending" <?php selected($post_data['post_status'], "pending"); ?> >
                     <?php wpsi_helper::_e('Pending review'); ?>
                  </option>
                  <option value="draft" <?php selected($post_data['post_status'], "draft"); ?> >
                     <?php wpsi_helper::_e('Draft'); ?>
                  </option>
               </select>
            </div>
            <div class="get_post_types_list input-group input-group-inline">
               <label for="post_types"> <?php wpsi_helper::_e('Post author'); ?> </label>
               <select name="post_auth" class="wpsi-form-control">
                  <?php $wp_users = get_users(array('role__in' => array('administrator', 'editor', 'author'))); ?>
                  <?php foreach ($wp_users as $wp_user) : ?>
                  <option value="<?php echo $wp_user->ID ?>" 
                     <?php if (wpsi_helper::_d($post_data, 'post_auth', false)) {
                           selected($post_data['post_auth'], $wp_user->ID);
                        } else {
                           selected($wp_user->ID, get_current_user_id());
                        } ?> > 
                        <?php echo $wp_user->user_nicename; ?>
                  </option>
                  <?php endforeach; ?>
               </select>
            </div>
         </div>
      </div>
      <!-- wpsi-post-container END -->
   </div>
   <!-- wpsi-portlet-body END -->
</div>
<!-- wpsi-portlet END -->