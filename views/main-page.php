<?php
/**
 * Log record main page
 * 
 * @package log-record/views
 */

 
$trans_key = 'log_record';
$img_url   = plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/ninethree.png';

// $records = Log::get_records();
$temp_user_id = get_current_user_id();
$gravatar_url = get_avatar_url( $temp_user_id, array( 96, 'retro' ) );

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
        <table class="record-list" cellspacing="0">
            <tr>
                <td>
                    <img src="<?php echo $gravatar_url; ?>" alt="" class="record-list__profile-img">
                </td>
                <td>
                    <span class="record-list__data-item">David</span>
                </td>
                <td>
                    <span class="record-list__data-item"><?php echo date( 'd-m-Y H:i:s' ); ?></span>
                </td>
                <td>
                    <span class="record-list__data-item">Administrator</span>
                </td>
                <td>
                    <a href="#" class="record-list__user-data-link">See user records -></a>
                </td>
            </tr>
            <tr>
                <td>
                    <img src="<?php echo $gravatar_url; ?>" alt="" class="record-list__profile-img">
                </td>
                <td>
                    <span class="record-list__data-item">David</span>
                </td>
                <td>
                    <span class="record-list__data-item"><?php echo date( 'd-m-Y H:i:s' ); ?></span>
                </td>
                <td>
                    <span class="record-list__data-item">Administrator</span>
                </td>
                <td>
                    <a href="#" class="record-list__user-data-link">See user records -></a>
                </td>
            </tr>
        </table>
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