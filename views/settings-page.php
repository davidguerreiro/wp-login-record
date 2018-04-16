<?php
/**
 * Log records settings page template
 * 
 * @package log-record/views
 */

 $featured_roles                = unserialize( get_option( 'log_featured_roles' ) );
 $display_dashboard_metabox     = get_option( 'log_display_dashboard_metabox' );
 $send_admin_email_notification = get_option( 'log_send_admin_email_notification' );
 $wp_nonce                      = wp_create_nonce( 'log_settings_form' );
 $trans_key                     = 'log_record';

 $roles     = get_editable_roles();
 $img_url   = plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/ninethree.png';

 // process notifications.
 if ( isset( $_GET['status'] ) ) {
     $class     = 'notice log-notice';
     $not_data  = Log::get_not_data( $_GET['status'] );
     $class     .= ( $not_data['type'] == 'success' ) ? ' notice-success' : ' notice-error';
 }

?>

<header class="log-section">
    <h2 class="log-page-title">
        <?php _e( 'WP Login Record', $trans_key ); ?>
    </h2>
</header>
<?php if ( isset( $class ) && isset( $not_data ) && ! empty( $not_data ) ) : ?>
    <div class="<?php echo $class; ?>">
        <p aria-live="polite"><?php echo esc_html( $not_data['content'] ); ?></p>
    </div>
<?php endif; ?>
<section class="log-section">
    <form action="" method="post">
        <input type="hidden" name="log_form" value="settings">
        <input type="hidden" name="log_nonce" value="<?php echo $wp_nonce; ?>">
        <div class="log-form-row">
            <div class="log-form-col--half">
            <h3 class="log-form-title">
                <span class="dashicons dashicons-admin-generic log-form-title-icon"></span>
                <?php echo _e( 'Settings', $trans_key ); ?>
            </h3>
            </div>
        </div>
        <div class="log-form-row">
            <div class="log-form-col--half">
                <label for="log_roles" class="log-form-label">
                    <?php echo _e( 'Featured Roles', $trans_key ); ?>
                </label>
                <p class="log-form-caption">
                    <?php echo _e( 'Featured Roles will be highlighted to help tracking', $trans_key ); ?>
                </p>
            </div>
            <div class="log-form-col">
                <ul class="log-form-checkbox-list">
                    <?php foreach ( $roles as $role ) :
                        $checked = '';
                        if ( is_array( $featured_roles ) && ! empty( $featured_roles ) && in_array( strtolower( $role['name'] ), $featured_roles ) ) {
                            $checked = "checked";
                        } else {
                            $checked = '';
                        }
                    ?>
                        <li>
                            <input type="checkbox" 
                            name="log_roles[]" 
                            value="<?php echo strtolower( $role['name'] ); ?>" 
                            id="<?php echo $role['name']; ?>"
                            class="log-form-checkbox" 
                            <?php echo $checked; ?>>
                            <?php echo $role['name']; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="log-form-row">
            <div class="log-form-col--half">
                <label for="log_display_metabox" class="log-form-label">
                    <?php _e( 'Display info on the Dashboard', $trans_key ); ?>
                </label>
                <p class="log-form-caption">
                    <?php echo _e( 'Enable / Disable info summary metabox displayed on the dashboard', $trans_key ); ?>
                </p>
            </div>
                <div class="log-form-col--half">
                    <ul class="log-form-radio-list">
                        <li>
                            <input type="radio" 
                            name="log_display_metabox"
                            value="yes"
                            <?php if ( empty( $display_dashboard_metabox) || $display_dashboard_metabox == 'yes' ) :
                                echo "checked"; 
                            endif; ?>
                            > <?php echo _e( 'Yes', $trans_key ); ?>
                        </li>
                        <li>
                            <input type="radio"
                            name="log_display_metabox"
                            value="no"
                            <?php if( $display_dashboard_metabox == 'no' ) :
                                echo "checked";
                            endif; ?> 
                            > <?php echo _e( 'No', $trans_key ); ?>
                        </li>
                    </ul>
            </div>
        </div>
        <div class="log-form-row">
            <div class="log-form-col--half">
                <label for="log_enbale_email" class="log-form-label">
                    <?php echo _e( 'Send Admin notifications', $trans_key ); ?>
                </label>
                <p class="log-form-caption">
                        <?php echo _e( 'Send a record summary email to admins every week' ); ?>
                </p>
            </div>
            <div class="log-form-col--half">
                <ul class="log-form-radio-list">
                    <li>
                        <input type="radio"
                        name="log_send_admin_notification"
                        value="yes"
                        <?php if ( $send_admin_email_notification == 'yes' ) :
                            echo "checked";
                        endif; ?>
                        > <?php echo _e( 'Yes', $trans_key ); ?>
                    </li>
                    <li>
                        <input type="radio" 
                        name="log_send_admin_notification" 
                        value="no"
                        <?php if ( empty( $send_admin_email_notification ) || $send_admin_email_notification == 'no' ) :
                            echo "checked";
                        endif; ?>
                        > <?php echo _e( 'No', $trans_key ); ?>
                    </li>
                </ul>
            </div>
        </div>
        <div class="log-form-row">
            <div class="log-form-col">
                <input type="submit" value="<?php echo _e( 'Update Settings', $trans_key ); ?>" class="button button-primary log-submit-button">
            </div>
        </div>
    </form>
</section>
<footer class="log-section">
    <div class"log-form-row">
        <div class="log-form-col">
            <p class="footer-text"><?php _e( 'Proudly developed by :', $trans_key ); ?></p>
        </div>
    </div>
    <div class="log-form-col">
        <div class="log-form-col">
            <a href="https://93digital.co.uk" class="footer-image-link" target="_blank">
                <img src="<?php echo $img_url; ?>" alt="" class="footer-image">
            </a>
        </div>
    </div>
</footer>