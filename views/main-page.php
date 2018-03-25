<?php
/**
 * Log record main page
 * 
 * @package log-record/views
 */

 
$trans_key = 'log_record';
$img_url   = plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/ninethree.png';

$records = Log::get_records();

$gravatar_args = array(
    96,
    'retro',
);
$current_url = get_admin_url() . 'admin.php';
?>
<header class="log-section">
    <h2 class="log-page-title">
        <?php _e( 'WP Login Record', $trans_key ); ?>
    </h2>
</header>
<section class="log-section">
    <div class="log-form-row">
        <div class="log-form-col--half">
            <h3 class="log-form-title">
                <span class="dashicons dashicons-chart-bar log-form-title-icon"></span>
                <?php echo _e( 'Summary', $trans_key ); ?>
            </h3>
        </div>
    </div>
    <div class="log-form-row">
        <div class="log-form-col">
            <div id="summary-chart" class="log-chart-wrapper"></div>
        </div>
    </div>
    <div class="log-form-row">
        <div class="log-form-col">
            <div id="summary-roles-chart" class="log-chart-wrapper"></div>
        </div>
    </div>
    <div class="log-form-row">
        <div class="log-form-col">
            <div id="summary-logins-chart" class="log-chart-wrapper"></div>
        </div>
    </div>
</section>
<section class="log-section">
    <div class="log-form-row">
        <div class="log-form-col--half">
            <h3 class="log-form-title">
                <span class="dashicons dashicons-editor-alignleft log-form-title-icon"></span>
                <?php echo _e( 'Records', $trans_key ); ?>
            </h3>
        </div>
    </div>
    <div class="log-form-row">
        <?php if ( is_array( $records ) && ! empty( $records ) ) : ?>
            <table class="record-list" cellspacing="0">
                <?php foreach ( $records as $item ) :
                    $user_id        = (int) $item->user_id;
                    $gravatar_url   = get_avatar_url( $user_id, array( 96, 'retro' ) );
                    $page_args      = array(
                        'page'      => 'log-user-page',
                        'user-id'   => $user_id,
                    );
                    $user_page_url = add_query_arg( $page_args, $current_url );
                ?>
                <tr>
                    <td>
                        <img src="<?php echo $gravatar_url; ?>" alt="" class="record-list__profile-img">
                    </td>
                    <td>
                        <span class="record-list__data-item">
                            <?php echo esc_html( ucfirst( $item->user_name ) ); ?>
                        </span>
                    </td>
                    <td>
                        <span class="record-list__data-item">
                            <?php echo date( 'd-m-Y H:i:s', strtotime( $item->last_session ) ); ?>
                        </span>
                    </td>
                    <td>
                        <span class="record-list__data-item">
                            <?php echo esc_html( ucfirst( $item->user_role ) ); ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?php echo esc_url( $user_page_url ); ?>" class="record-list__user-data-link">See user records -></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
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