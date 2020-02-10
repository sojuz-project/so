<?php
  function zero_settings_options_page()
    {
      ?>
        <div>
          <h2>Checklist</h2>
          <p>.Create Home page / home-page</p>
          <p>.Menu [NavItem, DropdownItem, ParentItem]</p>
          <p>.Check permalinks</p>
        </div>
      <?php
    }

  /** 
   * Register options page with menu
   */
  add_action( 'admin_init', function() {
    add_option( 'zero_settings_option_name', 'This is my option value.');
    register_setting( 'zero_settings_options_group', 'zero_settings_option_name', 'zero_settings_callback' );
  } );

  add_action('admin_menu', function() {
    add_options_page('Page Title', 'Zero theme checklist', 'manage_options', 'zero_settings', 'zero_settings_options_page');
  });

  