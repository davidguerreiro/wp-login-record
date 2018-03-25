<?php
/**
 * Plugin main class
 * 
 * @package log-record/inc
 */

class Log {

    private static $initiated           = false;
    private static $sessions_table_name = 'log_sessions';
    private static $sessions_action_table_name = 'log_actions';
    // private static $total_table_name    = 'log_total';

    /**
     * Main init function
     * 
     * @return void
     */
    public static function init() {
        if ( ! self::$initiated ) {
            self::init_hooks();
        }
    }

    /**
     * Initialise WordPress hooks
     * 
     * @return void
     */
    public static function init_hooks() {
        self::$initiated = true;

        add_action( 'wp_login', array( 'Log', 'register_login' ), 10, 2 );

        add_action( 'admin_menu', array( 'Log', 'add_menu_page_option' ) );

        add_action( 'admin_init', array( 'Log', 'process_settings_form' ) );

        add_action( 'admin_enqueue_scripts', array( 'Log', 'enqueue_js_scripts' ) );
        
        add_action( 'admin_enqueue_scripts', array( 'Log', 'enqueue_css_files' ) );

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
        // load css only in plugin pages.
        if ( isset( $_GET['page'] ) && ( $_GET['page'] == 'log-record-page') || $_GET['page'] == 'log-settings' ) {
            $plugin_dir_path = plugin_dir_url(  dirname( __FILE__ ) );
            wp_enqueue_style( 'log-records-css', $plugin_dir_path . 'css/style.css' );
        }   
    }

    /**
     * Register user login on database
     * 
     * @static
     * @global $wpdb Object WordPress Database handler
     * @return void
     */
    public static function register_login( $user_login, $user ) {
        global $wpdb;
        
        if ( ! empty( $user ) ) {
            // log init session.
            $table_name = $wpdb->prefix . self::$sessions_table_name;
            $data       = array(
                'user_id'       => $user->data->ID,
                'user_name'     => $user_login,
                'user_email'    => $user->data->user_email,
                'user_role'     => $user->roles[0],
                'last_session'  => date( 'Y-m-d H:i:s' ),
            );
            $wpdb->insert( $table_name, $data );
        }
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
            array( 'Log', 'display_main_page' ), 
            'dashicons-desktop' 
        );

        // submenu settings page
        add_submenu_page(
            $parent_slug,
            'Settings',
            'Settings',
            'administrator',
            'log-settings',
            array( 'Log', 'display_settings_page' )
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
     * Displays plugin settings page
     * 
     * @static
     * @return void
     */
    public static function display_settings_page() {
        require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'views/settings-page.php' );
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
     * Set user notification.
     * 
     * @param  String ( required ) $not_id Notification ID.
     * @return Array $not_data Contais id as key and text as value
     */
    public static function get_not_data( $not_id ) {
        $not_text = 'Something went wrong ! Please try again.';
        $type     = 'error';
        switch ( $not_id ) {
            case 'invalid-nonce' :
                $not_text   = 'Please do not cheat with the forms';
                $type       = 'error';
                break;
            case 'settings-updated' :
                $not_text   = 'Settings successfully updated';
                $type       = 'success';
                break;
            default :
                $not_text   = 'Something went wrong ! Please try again.';
                $type       = 'error';
                break;
        }

        return array(
            'content'   => $not_text,
            'type'      => $type,
        );
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
     * Get login records
     * 
     * @param String $type Default to 'total'.
     * @param int    $limit Default to 20.
     * @return Array $data.
     */
    /*
    public static function get_records( $type = 'total', $limit = 20 ) {
        global $wpdb;
        $table_name = $wpdb->prefix . self::$sessions_table_name;
        $data       = array();

        $query = "SELECT * FROM " . $table_name . " LIMIT " . $limit;
        $data  = $wpdb->get_results( $query );

        if ( empty( $data ) || ! $data ) {
            return false;
        }
        return $data;
    }
    */
}