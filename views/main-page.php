<?php
/**
 * Log record main page
 * 
 * @package log-record/views
 */

 // Base::debug();
 // set urls used in this page.
 $admin_url = get_admin_url() . 'admin.php';

 
$trans_key = 'log_record';
$img_url   = plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/ninethree.png';

/**
 * Check if filters are being used.
 * They are sent by GET.
 */
$date = null;
if ( isset( $_GET['record-day'] ) && isset( $_GET['record-month'] ) && isset( $_GET['record-year'] ) ) {
    $date = date( 'Y-m-d', strtotime( $_GET['record-year'] . '-' . $_GET['record-month'] . '-' . $_GET['record-day'] ) );
}

$records = Base::get_records( $date );

$gravatar_args = array(
    96,
    'retro',
);
$current_url = get_admin_url() . 'admin.php';

$current_day        = ( ! is_null( $date ) ) ? date( 'j', strtotime( $date ) ) : date( 'j' );
$current_month      = ( ! is_null( $date ) ) ? date( 'n', strtotime( $date ) ) : date( 'n' );
$current_year       = ( ! is_null( $date ) ) ? date( 'Y', strtotime( $date ) ) : date( 'Y' );
$plugin_start_year  = 2018;      // no records can be found before the year 2018.
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
<section class="log-section" id="record-filters">
    <div class="log-form-row">
        <div class="log-form-col--half">
            <h3 class="log-form-title">
                <span class="dashicons dashicons-editor-alignleft log-form-title-icon"></span>
                <?php echo _e( 'Records', $trans_key ); ?>
            </h3>
        </div>
    </div>
    <div class="log-form-row">
        <form action="<?php echo $admin_url; ?>#record-filters" method="get" class="date-filters">
            <input type="hidden" name="page" value="log-record-page">
            <ul>
                <li>
                    <label for="record-day">Day:</label>
                    <select name="record-day" id="record-day">
                        <?php for ( $i = 1; $i <= 31; $i++ ) :
                        $current = '';
                        if ( $i == $current_day ) {
                            $current = 'selected';
                        } ?>
                            <option value="<?php echo $i; ?>" <?php echo $current; ?>>
                                <?php echo $i; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </li>
                <li>
                    <label for="record-month">Month:</label>
                    <select name="record-month" id="record-month">
                        <?php for ( $i = 1; $i <= 12; $i++ ) :
                            $current = '';
                            if ( $i == $current_month ) {
                                $current = 'selected';
                            } ?>
                            <option value="<?php echo $i; ?>" <?php echo $current; ?>>
                                <?php echo $i; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </li>
                <li>
                    <label for="record-year">Year:</label>
                    <select name="record-year" id="record-year">
                        <?php while( $plugin_start_year <= $current_year ) : 
                            $current = '';
                            if ( $plugin_start_year == $current_year ) {
                                $current = 'selected';
                            }   
                        ?>
                            <option value="<?php echo $plugin_start_year; ?>" <?php echo $current; ?>>
                                <?php echo $plugin_start_year; ?>
                            </option>
                        <?php 
                        $plugin_start_year++;
                        endwhile ?>
                    </select>
                </li>
                <li>
                    <input type="submit" value="Filter" class="button button-primary">
                </li>
            </ul>
        </form>
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
        <?php else : ?>
            <div class="log-no-records">
                <p class="log-no-records--text">
                    <span class="log-no-records--icon dashicons dashicons-backup"></span>
                    There are no records so far for today
                </p>
                <p class="log-no-records--caption">If you are looking at your current day and you do not see any record then your role might not be included. Check the <a href="<?php echo $admin_url . '?page=log-settings'; ?>">Settings</a> page for more information.</p>
            </div>
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