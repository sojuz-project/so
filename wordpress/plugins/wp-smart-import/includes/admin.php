<?php
/**
 * wpSmartImportAdmin Class Doc Comment
 *
 * @category Class
 * @package  wpSmartImportAdmin
 * @author   phxsolution
 */
if (!defined('ABSPATH')) { exit; }
if (!class_exists('wpSmartImportAdmin')) {
    Class wpSmartImportAdmin {

        public function __construct() {
            add_action('admin_init', array(&$this, 'admin_init'));
            add_action('init', array(&$this, 'app_output_buffer'));
            add_action('admin_enqueue_scripts',array(&$this, 'admin_enqueue'));
            $this->wpsi_register_ajax_actions();
            add_action('wp_loaded',array($this,'boot_session'));
            add_filter('style_loader_src', array(&$this, 'remove_wp_ver_css_js'), 9999);
            add_filter('script_loader_src', array(&$this, 'remove_wp_ver_css_js'), 9999);
        }

        function admin_init() {
            $this->register_setting();
        }
        
        function app_output_buffer() {
            if (is_admin()) {
                add_filter( 'admin_body_class', function($classes) {
                    $screen = get_current_screen();
                    if (preg_match('/wp_smart_import/i', $screen->id)) {
                        return  $classes .= ' wpsmartimport-plugin ';
                    }
                });
            }
            ob_start();
        }

        function remove_wp_ver_css_js($src) {
            if (strpos($src, 'ver='))
                $src = remove_query_arg('ver', $src);
            return $src;
        }

        public function boot_session() {
            if (session_status() == PHP_SESSION_NONE || session_id() == '')
            { session_start(); }
            global $session;
            $session = wpSmartImport::getVar('session');
        }

        public function register_setting() {
            register_setting('wp-smart-import-settings', 'wp-smart-import-settings');
        }

        public function wpsi_register_ajax_actions() {
            add_action('wp_ajax_wpsi_file_upload', array('wpSmartImportUpload','wpsi_file_upload'));
            add_action('wp_ajax_wpsi_xml_preview', array('wpsiAjaxController', 'wpsi_xml_preview'));
            add_action('wp_ajax_wpsi_images_preview', array('wpsiAjaxController', 'wpsi_images_preview'));
            add_action('wp_ajax_insert_term', array('wpsiAjaxController', 'insert_term'));
            add_action('wp_ajax_wpsi_file_name_check', array('wpSmartImportQuery', 'wpsi_file_name_check'));
            add_action('wp_ajax_nopriv_wpsi_file_name_check', array('wpSmartImportQuery', 'wpsi_file_name_check'));
            add_action('wp_ajax_wpsi_runImport', array('wpsiAjaxController', 'wpsi_runImport'));
            add_action('wp_ajax_manage_imports', array('wpsiAjaxController', 'manage_imports'));
            add_action('wp_ajax_get_total_batch_for_import', array('wpsiAjaxController', 'get_total_batch_for_import'));
            add_action('wp_ajax_manage_import_files', array('wpsiAjaxController', 'manage_import_files'));
            add_action('wp_ajax_get_total_batch_for_file', array('wpsiAjaxController', 'get_total_batch_for_file'));
        }
        
        function admin_enqueue() {
            if (!is_admin()) return;
            // loading WordPress Default jquery.js file
            wp_enqueue_script('jquery');
            wp_enqueue_style('wpsi-admin-css', wpSmartImport::getVar('css', 'url') . 'style.css');
            wp_enqueue_script('wpsi-custom-js', wpSmartImport::getVar('js', 'url') . 'custom.js');
            wp_enqueue_script('wpsi-custom-ajax-js', wpSmartImport::getVar('js', 'url') . 'custom_ajax.js', array('jquery'));
             
             //Jquery UI js
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_style('wpsi-jquery-ui-css', wpSmartImport::getVar('js', 'url').'jquery-ui-1.12.1/jquery-ui.min.css');
            wp_enqueue_style('wpsi-toastr-css', wpSmartImport::getVar('js', 'url')."toastr-jquery/toastr.min.css");
             wp_enqueue_script('wpsi-toastr-js', wpSmartImport::getVar('js', 'url')."toastr-jquery/toastr.min.js");

            /* Use URL and nonce in JS file by using wp_localize_script() function
                https://codex.wordpress.org/Function_Reference/wp_localize_script*/
            wp_localize_script('wpsi-custom-ajax-js', 'ajaxurl', admin_url('admin-ajax.php'));
            wp_localize_script('wpsi-custom-ajax-js', '_nonce', wp_create_nonce('wpsi_nonce'));
            $data = array('pluginUrl' => wpSmartImport::getVar('home', 'url'),
                        'upload_url' => admin_url('async-upload.php'),
                        'admin_url' => admin_url('admin.php'),
                        'pages' => wpSmartImport::getVar('pages')
                    );
            wp_localize_script('wpsi-custom-ajax-js', 'path', $data);
        }
    }
    new wpSmartImportAdmin;
}