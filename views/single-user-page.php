<?php
/**
 * Single user page template
 * 
 * @package log-record/views
 */

 //Base::debug();

$admin_url = get_admin_url() . 'admin.php';
$trans_key = 'log_record';

// redirect user to main plugin page if user id does not exists
if ( ! isset( $_GET['user-id'] ) || empty( $_GET['user-id'] ) ) {
    echo "Error : User ID not available. Data not loaded.";
    exit;
} else {
    $user_id = (int) $_GET['user-id'];
}

$user_data = Action::get_user_data( $_GET['user-id'] );
var_dump( $user_data );
die( 'herethere' );

?>

<header class="log-section">
    <h2 class="log-page-title">
        <?php _e( 'WP Login Record', $trans_key ); ?>
    </h2>
</header>