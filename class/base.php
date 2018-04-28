<?php
/**
 * Plugin main class
 * 
 * This class inits the plugin and has all the methods to create
 * pages, tables and enqueue scripts, along with some others
 * methods related with general working. The method init
 * is called when the plugin is enabled.
 * 
 * @package log-record/class
 */

class Base {

    protected static $initiated           = false;
    protected static $sessions_table_name = 'log_sessions';
    protected static $sessions_action_table_name = 'log_actions';
    // private static $total_table_name    = 'log_total';

    /**
     * Debug funciton -- remove after development
     * 
     * @return void
     */
    public static function debug() {
        ini_set('display_errors', 1 );
        ini_set('display_startup_errors', 1 );
        error_reporting(E_ALL);
    }

    /**
     * Initialise WordPress hooks
     * 
     * @return void
     */
    public static function base_init() {

        add_action( 'admin_menu', array( 'Base', 'add_menu_page_option' ) );

        add_action( 'admin_init', array( 'Base', 'process_settings_form' ) );

        add_action( 'admin_enqueue_scripts', array( 'Base', 'enqueue_js_scripts' ) );
        
        add_action( 'admin_enqueue_scripts', array( 'Base', 'enqueue_css_files' ) );

        add_filter( 'user_row_actions', array( 'Base', 'add_single_user_log_profile_link' ), 10, 2 );

    }

    /**
     * Attached to activate_{ plugin basename( __FILES__ ) } by register_activation_hook()
     * 
     * @static
     * @global Object $wpdb WordPress Database handler
     * @return void
     */
    public static function plugin_activation() {
        
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // create table to log every session.
        $table_name = $wpdb->prefix . self::$sessions_table_name;
        $query = "CREATE TABLE " . $table_name . " (
            id BIGINT( 11 ) NOT NULL AUTO_INCREMENT,
            user_id BIGINT( 11 ) NOT NULL DEFAULT 0,
            user_name TEXT NOT NULL DEFAULT '',
            user_email TEXT NOT NULL DEFAULT '',
            user_role TEXT NOT NULL DEFAULT '',
            last_session DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
            PRIMARY KEY ( id )
        ) " . $charset_collate . ";";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $query );

        // create table to log actions.
        $table_name = $wpdb->prefix . self::$sessions_action_table_name;
        $query = "CREATE TABLE " . $table_name . " (
            id BIGINT( 11 ) NOT NULL AUTO_INCREMENT,
            user_id BIGINT ( 11 ) NOT NULL DEFAULT 0,
            action TEXT NOT NULL DEFAULT '',
            date_time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
            PRIMARY KEY ( id )
        ) " .  $charset_collate . ";";
        dbDelta( $query );

        // create table to log total user sessions.
        /*
        $table_name = $wpdb->prefix . self::$total_table_name;
        $query = "CREATE TABLE " . $table_name . " (
            id BIGINT( 11 ) NOT NULL AUTO_INCREMENT,
            user_id BIGINT ( 11 ) NOT NULL DEFAULT 0,
            session_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
            PRIMARY KEY ( id )
        ) " . $charset_collate . ";";
        dbDelta( $query );
        */

        // set options.

        // highlight one or several roles.
        $featured_roles = get_option( 'log_featured_roles' );
        if ( ! $featured_roles ) {
            update_option( 'log_featured_roles', '' );
        }

        // display metabox with extra info on the dashboard.
        $display_dashboard_metabox = get_option( 'log_display_dashboard_metabox' );
        if ( ! $display_dashboard_metabox ) {
            update_option( 'log_display_dashboard_metabox', 'yes' );
        }

        // enable / disable email notifications.
        $send_admin_email_notification = get_option( 'log_send_admin_email_notification' );
        if ( ! $display_dashboard_metabox ) {
            update_option( 'log_send_admin_email_notification', 'no' );
        }
    }

    /**
     * Add JS and required external libraries
     * 
     * @return void.
     */
    public static function enqueue_js_scripts() {
        // load scripts only in plugin main page.
        if ( isset( $_GET['page'] ) && $_GET['page'] == 'log-record-page' ) {

            $summary_data = self::get_summary_data();

            $plugin_dir_path = plugin_dir_url(  dirname( __FILE__ ) );
            // load google charts library from local.
            wp_enqueue_script( 'google_charts_local', $plugin_dir_path . 'js/googlecharts.min.js', array(), true );

            // load custom charts js.
            wp_enqueue_script( 'charts_js', $plugin_dir_path . 'js/drawcharts.js', array(), true );

            // pass summary data to charts.js.
            wp_localize_script( 'charts_js', 'summary_data', $summary_data );
        }
    }

    /**
     * Enqueue css style
     * 
     * @return void
     */
    public static function enqueue_css_files() {
        // TODO: Add single user page on the array.
        /*
        $pages = array(
            'log-record-page',
            'log-settings',
        );
        // load css only in plugin pages.
        if ( isset( $_GET['page'] ) && in_array( $_GET['page'], $pages ) ) {
            $plugin_dir_path = plugin_dir_url(  dirname( __FILE__ ) );
            wp_enqueue_style( 'log-records-css', $plugin_dir_path . 'css/style.css' );
        }  
        */
        $plugin_dir_path = plugin_dir_url(  dirname( __FILE__ ) );
        wp_enqueue_style( 'log-records-css', $plugin_dir_path . 'css/style.css' );
    }

    /**
     * Add menu page option
     * 
     * @static
     * @return void
     */
    public static function add_menu_page_option() {
        $parent_slug = 'log-record-page';

        // main menu page
        add_menu_page( 
            'Log Record', 
            'Log Record', 
            'administrator', 
            $parent_slug, 
            array( 'Base', 'display_main_page' ), 
            'dashicons-desktop' 
        );

        // submenu settings page
        add_submenu_page(
            $parent_slug,
            'Settings',
            'Settings',
            'administrator',
            'log-settings',
            array( 'Base', 'display_settings_page' )
        );

        // submenu settings page
        add_submenu_page(
            $parent_slug,
            'Single User Page',
            'Single User Page',
            'administrator',
            'log-single-user-page',
            array( 'Base', 'display_single_user_page' )
        );
    }

    /**
     * Displays plugin main page
     * 
     * @static
     * @return void
     */
    public static function display_main_page() {
        require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'views/main-page.php' );
    }

    /**
     * Display plugin listing users page
     * 
     * @static
     * @return void
     */
    public static function display_listing_users_page() {
        require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'views/user-listing-page.php' );
    }

    /**
     * Displays plugin settings page
     * 
     * @static
     * @return void
     */
    public static function display_settings_page() {
        require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'views/settings-page.php' );
    }

    /**
     * Displays plugin single user page
     * 
     * @static
     * @return void
     */
    public static function display_single_user_page() {
        require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'views/single-user-page.php' );
    }

    /**
     * Process settings form
     * 
     * @return void
     */
    public static function process_settings_form() {
        if ( ! isset( $_POST['log_form'] ) || $_POST['log_form'] !== 'settings' ) {
            return;
        }

        if ( ! isset( $_POST['log_nonce'] ) || empty( $_POST['log_nonce'] ) || ! wp_verify_nonce( $_POST['log_nonce'], 'log_settings_form' ) ) {
            // get user notification data.
            $not_data = array(
                'status' => 'invalid-nonce',
            );
            //redirect user to settings form.
            self::redirect_user( 'settings', $not_data );
        }
        
        // featured administrators.
        if ( isset( $_POST['log_roles'] ) && is_array( $_POST['log_roles'] ) || ! empty( $_POST['log_roles'] ) ) {
            update_option( 'log_featured_roles', serialize( $_POST['log_roles'] ) );
        }

        // display metabox
        if ( isset( $_POST['log_display_metabox'] ) && ! empty( $_POST['log_display_metabox'] ) ) {
            update_option( 'log_display_dashboard_metabox', sanitize_text_field( $_POST['log_display_metabox'] ) );
        }

        // send admin notification.
        if ( isset( $_POST['log_send_admin_notification'] ) && ! empty( $_POST['log_send_admin_notification'] ) ) {
            update_option( 'log_send_admin_email_notification', sanitize_text_field( $_POST['log_send_admin_notification'] ) );
        }

        $not_data = array(
            'status' => 'settings-updated',
        );
        self::redirect_user( 'settings', $not_data );
    }

    /**
     * Redirect user after process a form
     * 
     * @param String ( required ) $location Page where the user is redirected.
     * @param Array               $not_data Optional notification data
     */
    public static function redirect_user( $location, $not_data = array() ) {
        // set page redirection.
        switch( $location ) {
            case 'main' :
                $path = 'admin.php?page=log-record-page';
                break;
            case 'settings' :
                $path = 'admin.php?page=log-settings';
                break;
            default :
                $path = 'admin.php?page=log-record-page';
                break;
        }
        $base_url = get_admin_url( null, $path );

        // add user notification data.
        if ( is_array( $not_data ) && ! empty( $not_data ) ) {
            $base_url = add_query_arg( $not_data, $base_url );
        }
        
        wp_safe_redirect( $base_url );
        exit;
    }

    /**
     * Get summary data
     * 
     * @return Array $data
     */
    public static function get_summary_data() {
        global $wpdb;
        $table_name     = $wpdb->prefix . self::$sessions_table_name;
        $data           = array();
        $current_roles  = get_editable_roles();
        $i              = 0;
        $total          = count( $current_roles );

        // get total users data and number of users by role.
        $data['user_data'] = count_users();
        
        // get total logins.
        $query = "SELECT COUNT(*) FROM " . $table_name;
        $data['total_logins'] = (int) $wpdb->get_var( $query );

        // get logins by role.
        $query = "SELECT";
        foreach ( $current_roles as $key => $role ) {
            $query .= " ( SELECT COUNT(*) FROM " . $table_name . " 
                        WHERE user_role = '" . sanitize_text_field( $key ) . "' ) 
                        AS '" . $key . "'";
            $i++;
            if ( $i < $total ) {
                $query .= ',';
            }
        }
        $data['logins_per_role'] = $wpdb->get_row( $query );
        return $data;
    }

    /**
     * Add single user page link in users.php
     * 
     * @param array $actions
     * @param object $user_object
     * @return array $users
     */
    public static function add_single_user_log_profile_link( $actions, $user_object ) {
        if ( current_user_can( 'administrator' ) ) {
            $admin_url = get_admin_url() . 'admin.php';
            $args = [
                'page'      => 'log-single-user-page',
                'user-id'   => $user_object->ID,
            ];
            $admin_url = add_query_arg( $args, $admin_url );
            $actions['view_log_profile'] = "<a href='" . esc_url( $admin_url ) . "' class='log-wpusers-link'>Log profile</a>";
        }
        return $actions;
    }

}