<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Post_Scripting
 * @subpackage Post_Scripting/includes
 */

/*
 * Copy of Plugin Name: Paulund WP List Table Example
 * Description: An example of how to use the WP_List_Table class to display data in your WordPress Admin area
 * Plugin URI: http://www.paulund.co.uk
 * Author: Paul Underwood
 * Author URI: http://www.paulund.co.uk
 * Version: 1.0
 * License: GPL2
 */
 
if( is_admin() ) {
    new Paulund_Wp_List_Table_Copy();
}
 
/**
 * Paulund_Wp_List_Table class will create the page to load the table
 */
class Paulund_Wp_List_Table_Copy {
    /**
     * Constructor will create the menu item
     */
    public function __construct() {
        

        add_action( 'admin_menu', array($this, 'add_menu_example_list_table_page' ));


    }
 
    /**
     * Menu item will allow us to load the page to display the table
     */
    public function add_menu_example_list_table_page()
    {
        add_menu_page( 'XXX List Table Copy', 'XXX List Table Copy', 'manage_options', 'example-list-table.php', array($this, 'list_table_page') );
    }
 
    /**
     * Display the list table page
     *
     * @return Void
     */
    public function list_table_page()
    {
        $exampleListTable = new Pser_Scripts_Table();
        $exampleListTable->prepare_items();
        ?>
            <div class="wrap">

                <h1>XXX List Table Page</h1>
                <?php // psing_settings_page(); ?>
                <form id="psing-scripts-filter" method="get">
                    <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                    <!-- Now we can render the completed list table -->
                    <?php $exampleListTable->display(); ?>
                </form>
            </div>
        <?php
    }
}
 
// WP_List_Table is not loaded automatically so we need to load it in our application
if( ! class_exists( 'WP_List_Table' ) ) {
    // require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if( ! class_exists( 'Psing_List_Table' ) ){
    require_once( plugin_dir_path( __FILE__ ) . 'class-psing-list-table.php' );
}
 
/**
 * Creates new table class using Psing_List_Table (a copy of WP_List_Table).
 */
class Pser_Scripts_Table extends Psing_List_Table {

    /** ************************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We 
     * use the parent reference to set some default configs.
     ***************************************************************************/
    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'script',     //singular name of the listed records
            'plural'    => 'scripts',    //plural name of the listed records
            'ajax'      => true        //does this table support ajax?
        ) );
        
    }

    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items() {

        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
 
        $data = $this->table_data();
        usort( $data, array( &$this, 'sort_data' ) );
 
        $perPage = 20;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);
 
        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );
 
        $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);
 
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;

        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();

    }
 
    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns() {
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'handle'    => 'Handle',
            'deps'      => 'Deps',
            'args'      => 'Args',
            'ver'       => 'Ver',
            'src'       => 'Src',
        );
 
        return $columns;
    }


    /** ************************************************************************
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     * 
     * @see Psing_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['handle']                //The value of the checkbox should be the record's id
        );
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
     * @see Psing_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_handle($item){
        
        //Build row actions
        $actions = array(
            'view'      => sprintf( '<a href="%s">View</a>', $this->src_url( $item['src'] ) ),
            'remove'    => sprintf( '<a href="?page=%s&action=%s&script=%s">Remove</a>',$_REQUEST['page'],'remove',$item['handle'] ),
        );
        
        //Return the handle contents
        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ $item['handle'],
            /*$2%s*/ $item['args'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }

    function column_deps($item){
        return implode( ', ', $item['deps'] );
    }


    function src_url( $src ){
        // Make absolute URLs for WP core scripts (from their registered relative 'src' values)
        if ( substr( $src, 0, 13 ) === '/wp-includes/' ) {
            $src = rtrim( includes_url(), '/' ) . $src;
        } elseif ( substr( $src, 0, 10 ) === '/wp-admin/' ) {
            $src = rtrim( admin_url(), '/' ) . $src;
        }
        return $src;
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
        $actions = array(
            'remove'    => 'Remove'
        );
        return $actions;
    }

    /** ************************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     * 
     * @see $this->prepare_items()
     **************************************************************************/
    function process_bulk_action() {
        // Security check.
        if ( ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) ) {
            $nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
            $action = 'bulk-' . $this->_args['plural'];

            if ( ! wp_verify_nonce( $nonce, $action ) && ! wp_verify_nonce( $nonce, 'psing-nonce' ) ) {
                // wp_die( 'Paulund: Not allowed.' );
            }      

            // Setting to remove an item from array of allowed script handles.
            if ( 'remove' === $this->current_action() && isset( $_REQUEST['script'] ) ) {

                $postscript_added_scripts = get_option( 'psing_added_scripts', array() );
                $postscript_remove_scripts = $_REQUEST['script'];
                $postscript_added_scripts = array_diff( $postscript_added_scripts, $postscript_remove_scripts );
                update_option( 'psing_added_scripts', (array) $postscript_added_scripts );

                $message = __( 'Items removed: ' . print_r( $_REQUEST['script'], true ), 'post-scripting' );
                $class = 'updated settings-updated';

                echo "<div class=\"$class\ notice is-dismissible\"><p>$message</p></div>";
            } 
            

        }
    }
    

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns()
    {
        return array();
    }
 
    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns()
    {
        return array( 'handle' => array( 'handle', false ), 'args' => array( 'args', false ) );
    }
 
    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function psing_arr_value_keys( $arr_multi, $field, $value ) {
       foreach( $arr_multi as $key => $arr ) {
          if ( $arr[$field] === $value )
             return $arr;
       }
       return false;
    }

    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data()
    {
        $data = array();
/*
        $data = array(
            array(
                'handle'    => 'accordion',
                'src'       => '/wp-admin/js/accordion.min.js',
                'deps'      => array( 'jquery'),
                'ver'       => '',
                'args'      => '1',
            ),
            array(
                'handle'    => 'admin-comments',
                'src'       => '/wp-admin/js/edit-comments.min.js',
                'deps'      => array( 'wp-lists', 'quicktags', 'jquery-query' ),
                'ver'       => '',
                'args'      => '1',
            ),
            array(
                'handle'    => 'a8c-developer',
                'src'       => 'http://rji.local/wp-content/plugins/developer/developer.js',
                'deps'      => array( 'jquery'),
                'ver'       => '1.2.6',
                'args'      => '',
            ),
            array(
                'handle'    => 'colorpicker',
                'src'       => '/wp-includes/js/colorpicker.min.js',
                'deps'      => array( 'prototype'),
                'ver'       => '3517m',
                'args'      => '',
            ),
        );
*/
        global $wp_scripts, $wp_styles; $psing_added_script_keys;

        $psing_added_scripts = get_option( 'psing_added_scripts' );

        $psing_reg_scripts_arr = psing_object_into_array( $wp_scripts->registered );
        sort( $psing_reg_scripts_arr );

        foreach ( $psing_added_scripts as $handle) {
            $psing_added_script_keys[] = $this->psing_arr_value_keys( $psing_reg_scripts_arr, 'handle', $handle );
        }

        $data = $psing_added_script_keys;
 
        return $data;
    }
 


    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $item, $column_name ) {
        switch( $column_name ) {
            case 'handle':
            case 'src':
            case 'deps':
            case 'ver':
            case 'args':
                return $item[ $column_name ];
 
            default:
                return print_r( $item, true ) ;
        }
    }
 
    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    private function sort_data( $a, $b ) {
        // Set defaults
        $orderby = 'handle';
        $order = 'asc';
 
        // If orderby is set, use this as the sort column
        if(!empty($_GET['orderby']))
        {
            $orderby = $_GET['orderby'];
        }
 
        // If order is set use this as the order
        if(!empty($_GET['order']))
        {
            $order = $_GET['order'];
        }
 
 
        $result = strnatcmp( $a[$orderby], $b[$orderby] );
 
        if($order === 'asc')
        {
            return $result;
        }
 
        return -$result;
    }

}

function pser_admin_notice() {
    if ( isset( $_REQUEST['script'] ) ) {
            $message = __( 'Items to be removedrrr: ' . print_r( $_REQUEST['script'], true ), 'post-scripting' );
            $class = 'updated settings-updated';
    ?>
    <div class="<?php echo $class; ?> is-dismissible"><p><?php echo $message; ?><?php echo $_GET['action']; ?></p></div>
    <?php
    }
}
// add_action( 'admin_notices', 'pser_admin_notice' );