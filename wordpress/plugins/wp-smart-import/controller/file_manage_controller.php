<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
if (!class_exists('wpsi_manage_file_controller')) {
    class wpsi_manage_file_controller extends WP_List_Table {
        
        function __construct() {
            global $status, $page;
            //Set parent defaults
            parent::__construct(array(
                'singular'  => 'file',     //singular name of the listed records
                'plural'    => 'files',    //plural name of the listed records
                'ajax'      => false        //does this table support ajax?
            ));
        }

        function column_default($item, $column_name) {
            switch ($column_name){
                case 'id':
                case 'file_name':
                    return $item[$column_name];
                case 'last_activity':
                    $last_activity = date("d M Y  h:i:s A ", strtotime($item['updated_at']));
                    return  "$last_activity" ;
                default:
                    break;
                    /*return print_r($item,true);*/ //Show the whole array for troubleshooting purposes
            }
        }

        function column_name($item) {
            //Build row actions
            $actions = array(
                'delete' => sprintf('<a href="?page=%s&action=%s&id=%s&_nonce=%s">Delete</a>', $_REQUEST['page'], 'delete', $item['id'], wp_create_nonce('wpsi_nonce')),
            );
            $upload_dir = wp_upload_dir();
            $url = $upload_dir['baseurl'].'/'.$item['file_path'];
             //Return the title contents
            return sprintf('%1$s <br/> <a href="%3$s" download> %2$s </a> %4$s',
                /*$1%s*/ $item['name'],
                /*$2%s*/ $item['file_path'],
                /*$2%s*/ $url,
                /*$3%s*/ $this->row_actions($actions)
            );
        }

        function column_cb($item) {
            return sprintf('<input type="checkbox" name="ids[]" value="%1$s" />', $item['id']);
        }

        function get_columns() {
            $columns = array(
                'cb'            => '<input type="checkbox" />', //Render a checkbox instead of text
                'id'            => 'ID',
                'name'          => 'Name',
                'file_name'     => 'File Name',
                'last_activity' => 'Last Activity',
            );
            return $columns;
        }

        function get_sortable_columns() {
            $sortable_columns = array(
                'id'            => array('id', false),
                'name'          => array('name', false),     //true means it's already sorted
                'file_name'     => array('file_name', false),
            );
            return $sortable_columns;
        }

        function get_bulk_actions() {
            $actions = array(
                'delete' => 'Delete'
            );
            return $actions;
        }

        function process_bulk_action() {
            //Detect when a bulk action is being triggered...
            if ('delete'=== $this->current_action()) {
                $request = wpsi_helper::recursive_sanitize_text_field($_POST);
                if (!array_key_exists('ids', $request)) return false;
                
                $ids = implode(',', $request['ids']);
                $pages = wpSmartImport::getVar('pages');
                wpSmartImport::wpsi_redirect(array(
                    'page' => $pages[2], 
                    'action' => 'delete', 
                    'id' => $ids,
                    '_nonce' => wp_create_nonce('wpsi_nonce')
                ));
                exit();
            }
        }

        function prepare_items($search = '') {
            global $wpdb; //This is used only if making any database queries
            $per_page = 5;
            $columns = $this->get_columns();
            $hidden = array();
            $sortable = $this->get_sortable_columns();
            $this->_column_headers = array($columns, $hidden, $sortable);
            $this->process_bulk_action();
            
            /*************** SEARCH ACTION ****************/
            $table = $wpdb->prefix."wpsi_files";
            $querystr = "SELECT * FROM $table";
            if (!empty($search)) {
                $search  = trim( esc_sql( $wpdb->esc_like($search)));
                $querystr .= " WHERE `id` LIKE '%{$search}%' OR `name` LIKE '%{$search}%' OR `file_name` LIKE '%{$search}%' OR `updated_at` LIKE '%{$search}%' ";
            } 
            $querystr .= " ORDER BY $table.id DESC";
            $data = $wpdb->get_results($querystr, ARRAY_A);
            /***********************************************/

            function usort_reorder($a,$b) {
                $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'id'; //If no sort, default to id
                $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc'; //If no order, default to asc
                $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
                return ($order==='desc') ? $result : -$result; //Send final sort direction to usort
            }
            usort($data, array(&$this, 'usort_reorder'));
            $current_page = $this->get_pagenum();
            $total_items = count($data);
            $data = array_slice($data, (($current_page-1)*$per_page), $per_page);
            $this->items = $data;
            $this->set_pagination_args(array(
                'total_items' => $total_items,    //WE have to calculate the total number of items
                'per_page'    => $per_page,       //WE have to determine how many items to show on a page
                'total_pages' => ceil($total_items/$per_page) //WE have to calculate the total number of pages
            ));
        }
    }
    new wpsi_manage_file_controller();
}