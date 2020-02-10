<?php 
 
// wp-csv-hooks.php
 
namespace Smackcoders\FCSV;

if ( ! defined( 'ABSPATH' ) ) {
        die;
}

$plugin_ajax_hooks = [

    'database_optimization_process',
    'security_performance',
    'settings_options',
    'send_login_credentials_to_users',
    'get_options',
    'support_mail',
    'send_subscribe_email',
    'parse_data',
    'total_records',
    'get_post_types',
    'get_taxonomies',
    'get_authors',
    'mappingfields',
    'getfields',
    'get_export_fields',
    'templateinfo',
    'search_template',
    'zip_ngg_upload',
    'display_log',
    'download_log',
    'get_desktop',
    'get_ftp_url',
    'get_parse_xml',
    'LineChart',
    'PieChart',
    'BarChart',
    'checkExtensions',
    'listuploads',
    'locklist',
    'displayCSV',
    'preview',
    'updatefields',
    'rollback_now',
    'clear_rollback',
    'zip_upload',
    'image_options',
    'delete_image',
    'media_report',
    'saveMappedFields',
    'StartImport',
    'GetProgress',
    'ImportState',
    'ImportStop',
    'checkmain_mode',
    'disable_main_mode',
    'csv_options',
    'check_export'
];  




