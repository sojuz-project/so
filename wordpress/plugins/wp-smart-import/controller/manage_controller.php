<?php  
if (!defined('ABSPATH')) { exit; } // Exit if accessed directly
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}
if (!class_exists('wpsi_manage_controller')) {
    class wpsi_manage_controller extends WP_List_Table {

        /** ************************************************************************
         * REQUIRED. Set up a constructor that references the parent constructor. We 
         * use the parent reference to set some default configs.
         ***************************************************************************/
        function __construct() {
            global $status, $page;
            //Set parent defaults
            parent::__construct(array(
                'singular'  => 'import',     //singular name of the listed records
                'plural'    => 'imports',    //plural name of the listed records
                'ajax'      => false        //does this table support ajax?
            ));
          
        }

        /** ************************************************************************
         * Recommended. This method is called when the parent class can't find a method
         * specifically build for a given column. Generally, it's recommended to include
         * one method for each column you want to render, keeping your package class
         * neat and organized. For example, if the class needs to process a column
         * named 'title', it would first see if a method named $this->column_title() 
         * exists - if it does, that method will be used. If it doesn't, this one will
         * be used. Generally, you should try to use custom column methods as much as 
         * possible. 
         * 
         * Since we have defined a column_title() method later on, this method doesn't
         * need to concern itself with any column with a name of 'title'. Instead, it
         * needs to handle everything else.
         * 
         * For more detailed insight into how columns are handled, take a look at 
         * WP_List_Table::single_row_columns()
         * 
         * @param array $item A singular item (one full row's worth of data)
         * @param array $column_name The name/slug of the column to be processed
         * @return string Text or HTML to be placed inside the column <td>
         **************************************************************************/
        function column_default($item, $column_name) {
            switch($column_name) {
                case 'id':
                case 'post_type':
                    return $item[$column_name];
                case 'action':
                    return sprintf("<h4><a class='import-action' href='?page=%s&action=%s&id=%s'>Run Import</h4></a>", $_REQUEST['page'], 'update', $item['id']);
                case 'summary':
                    $last_activity = date("d M Y  h:i:s A", strtotime($item['last_activity']));
                    return  "Last Activity : $last_activity <br/> $item[created] $item[post_type] created <br/> $item[updated]  Updated  $item[failed] Failed" ;
                default:
                    return print_r($item, true); //Show the whole array for troubleshooting purposes
            }
        }

        /** ************************************************************************
         * Recommended. This is a custom column method and is responsible for what
         * is rendered in any column with a name/slug of 'title'. Every time the class
         * needs to render a column, it first looks for a method named 
         * column_{$column_title} - if it exists, that method is run. If it doesn't
         * exist, column_default() is called instead.
         * 
         * This example also illustrates how to implement rollover actions. Actions
         * should be an associative array formatted as 'slug'=>'link html' - and you
         * will need to generate the URLs yourself. You could even ensure the links
         * 
         * 
         * @see WP_List_Table::::single_row_columns()
         * @param array $item A singular item (one full row's worth of data)
         * @return string Text to be placed inside the column <td> (movie title only)
         **************************************************************************/
        function column_name($item) {
            //Build row actions
            $actions = array(
                'edit'      => sprintf('<a href="?page=%s&action=%s&id=%s">Edit Import</a>', $_REQUEST['page'], 'edit', $item['id']),
                'delete'  => sprintf('<a href="?page=%s&action=%s&id=%s&_nonce=%s">Delete</a>', $_REQUEST['page'], 'delete', $item['id'], wp_create_nonce('wpsi_nonce')),
            );
            $upload_dir = wp_upload_dir();
            $url = wp_make_link_relative($upload_dir['baseurl'].'/'.$item['file_path']);
             //Return the title contents
            return sprintf('%1$s <br/> <a href="%3$s" download> %2$s </a> %4$s',
                /*$1%s*/ $item['name'],
                /*$2%s*/ $item['file_path'],
                /*$2%s*/ $url,
                /*$3%s*/ $this->row_actions($actions)
            );
        }

        /** ************************************************************************
         * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
         * is given special treatment when columns are processed. It ALWAYS needs to
         * have it's own method.
         * 
         * @see WP_List_Table::::single_row_columns()
         * @param array $item A singular item (one full row's worth of data)
         * @return string Text to be placed inside the column <td> (movie title only)
         **************************************************************************/
        function column_cb($item) {
            return sprintf('<input type="checkbox" name="ids[]" value="%1$s" />', $item['id']);
        }

        /** ************************************************************************
         * REQUIRED! This method dictates the table's columns and titles. This should
         * return an array where the key is the column slug (and class) and the value 
         * is the column's title text. If you need a checkbox for bulk actions, refer
         * to the $columns array below.
         * 
         * The 'cb' column is treated differently than the rest. If including a checkbox
         * column in your table you must create a column_cb() method. If you don't need
         * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
         * 
         * @see WP_List_Table::::single_row_columns()
         * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
         **************************************************************************/
        function get_columns() {
            $columns = array(
                'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
                'id'        => 'ID',
                'name'      => 'Files',
                'post_type' => 'Post Type',
                'summary'   => 'Summary',
                'action'    => 'Action',
            );
            return $columns;
        }

        /** ************************************************************************
         * Optional. If you want one or more columns to be sortable (ASC/DESC toggle), 
         * you will need to register it here. This should return an array where the 
         * key is the column that needs to be sortable, and the value is db column to 
         * sort by. Often, the key and value will be the same, but this is not always
         * the case (as the value is a column name from the database, not the list table).
         * 
         * This method merely defines which columns should be sortable and makes them
         * clickable - it does not handle the actual sorting. You still need to detect
         * the ORDERBY and ORDER querystring variables within prepare_items() and sort
         * your data accordingly (usually by modifying your query).
         * 
         * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
         **************************************************************************/
        function get_sortable_columns() {
            $sortable_columns = array(
                'id'        => array('id', false),
                'name'      => array('name', false),     //true means it's already sorted
                'post_type' => array('post_type', false),
            );
            return $sortable_columns;
        }

        /** ************************************************************************
         * Optional. If you need to include bulk actions in your list table, this is
         * the place to define them. Bulk actions are an associative array in the format
         * 'slug'=>'Visible Title'
         * 
         * If this method returns an empty value, no bulk action will be rendered. If
         * you specify any bulk actions, the bulk actions box will be rendered with
         * the table automatically on display().
         * 
         * Also note that list tables are not automatically wrapped in <form> elements,
         * so you will need to create those manually in order for bulk actions to function.
         * 
         * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
         **************************************************************************/
        function get_bulk_actions() {
            return array('delete' => 'Delete');
        }

        /** ************************************************************************
         * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
         * For this example package, we will handle it in the class to keep things
         * clean and organized.
         * 
         * @see $this->prepare_items()
         **************************************************************************/
        function process_bulk_action() {
            //Detect when a bulk action is being triggered...
            if ('delete'=== $this->current_action()) {
                $request = wpsi_helper::recursive_sanitize_text_field($_POST);
                if (!array_key_exists('ids', $request))
                    return false;
                
                $id = implode(',', $request['ids']);
                $pages = wpSmartImport::getVar('pages');
                wpSmartImport::wpsi_redirect(array(
                    'page' => $pages[1],
                    'action' => 'delete',
                    'id' => $id,
                    '_nonce' => wp_create_nonce('wpsi_nonce')
                ));
                exit();
            }
        }

        /** ************************************************************************
         * REQUIRED! This is where you prepare your data for display. This method will
         * usually be used to query the database, sort and filter the data, and generally
         * get it ready to be displayed. At a minimum, we should set $this->items and
         * $this->set_pagination_args(), although the following properties and methods
         * are frequently interacted with here...
         * 
         * @global WPDB $wpdb
         * @uses $this->_column_headers
         * @uses $this->items
         * @uses $this->get_columns()
         * @uses $this->get_sortable_columns()
         * @uses $this->get_pagenum()
         * @uses $this->set_pagination_args()
         **************************************************************************/
        function prepare_items($search ='') {
            global $wpdb; //This is used only if making any database queries

            /**
             * First, lets decide how many records per page to show
             */
            $per_page = 5;
            
            /**
             * REQUIRED. Now we need to define our column headers. This includes a complete
             * array of columns to be displayed (slugs & titles), a list of columns
             * to keep hidden, and a list of columns that are sortable. Each of these
             * can be defined in another method (as we've done here) before being
             * used to build the value for our _column_headers property.
             */
            $columns = $this->get_columns();
            $hidden = array();
            $sortable = $this->get_sortable_columns();
            
            /**
             * REQUIRED. Finally, we build an array to be used by the class for column 
             * headers. The $this->_column_headers property takes an array which contains
             * 3 other arrays. One for all columns, one for hidden columns, and one
             * for sortable columns.
             */
            $this->_column_headers = array($columns, $hidden, $sortable);
            
            /**
             * Optional. You can handle your bulk actions however you see fit. In this
             * case, we'll handle them within our package just to keep things clean.
             */
            $this->process_bulk_action();
            
            /**
             * Instead of querying a database, we're going to fetch the example data
             * property we created for use in this plugin. This makes this example 
             * package slightly different than one you might build on your own. In 
             * this example, we'll be using array manipulation to sort and paginate 
             * our data. In a real-world implementation, you will probably want to 
             * use sort and pagination data to build a custom query instead, as you'll
             * be able to use your precisely-queried data immediately.
             */
            $table = $wpdb->prefix."wpsi_imports";
            $querystr = "SELECT * FROM $table";
            if (!empty($search)) {
                $search = trim($search);
                $querystr .= " WHERE `id` LIKE '%{$search}%' OR `name` LIKE '%{$search}%' OR `file_path` LIKE '%{$search}%' OR `post_type` LIKE '%{$search}%' ";
            } 
            $querystr .= " ORDER BY $table.id DESC";
            $data = $wpdb->get_results($querystr, ARRAY_A);
            
            /**
             * This checks for sorting input and sorts the data in our array accordingly.
             * 
             * In a real-world situation involving a database, you would probably want 
             * to handle sorting by passing the 'orderby' and 'order' values directly 
             * to a custom query. The returned data will be pre-sorted, and this array
             * sorting technique would be unnecessary.
             */
            function usort_reorder($a,$b) {
                $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'id'; //If no sort, default to id
                $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc'; //If no order, default to asc
                $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
                return ($order==='desc') ? $result : -$result; //Send final sort direction to usort
            }
            usort($data,array( &$this, 'usort_reorder'));
            
            /***********************************************************************
             * ---------------------------------------------------------------------
             * 
             * In a real-world situation, this is where you would place your query.
             *
             * For information on making queries in WordPress, see this Codex entry:
             * http://codex.wordpress.org/Class_Reference/wpdb
             * 
             * ---------------------------------------------------------------------
             **********************************************************************/
                    
            /**
             * REQUIRED for pagination. Let's figure out what page the user is currently 
             * looking at. We'll need this later, so you should always include it in 
             * your own package classes.
             */
            $current_page = $this->get_pagenum();
            
            /**
             * REQUIRED for pagination. Let's check how many items are in our data array. 
             * In real-world use, this would be the total number of items in your database, 
             * without filtering. We'll need this later, so you should always include it 
             * in your own package classes.
             */
            $total_items = count($data);
            
            /**
             * The WP_List_Table class does not handle pagination for us, so we need
             * to ensure that the data is trimmed to only the current page. We can use
             * array_slice() to 
             */
            $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
            
            /**
             * REQUIRED. Now we can add our *sorted* data to the items property, where 
             * it can be used by the rest of the class.
             */
            $this->items = $data;
            
            /**
             * REQUIRED. We also have to register our pagination options & calculations.
             */
            $this->set_pagination_args(array(
                'total_items' => $total_items,                  //WE have to calculate the total number of items
                'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
                'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
            ) );
        }
    }
    new wpsi_manage_controller();
}