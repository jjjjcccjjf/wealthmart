<?php
/**
* Listify child theme.
*/
function listify_child_styles() {
  wp_enqueue_style( 'listify-child', get_stylesheet_uri() );
}
add_action( 'wp_enqueue_scripts', 'listify_child_styles', 999 );

/** Place any new code below this line */

function custom_listify_remove_action_links() {
  global $listify_job_manager;

  remove_action( 'listify_single_job_listing_actions_start', array( $listify_job_manager->gallery, 'add_link' ) );
}
add_action( 'init', 'custom_listify_remove_action_links' );


function listify_hide_packages_from_shop( $q ) {
  if ( ! $q->is_main_query() ) return;
  if ( ! $q->is_post_type_archive() ) return;

  if ( ! is_admin() && is_shop() || is_search() ) {
    $q->set( 'tax_query', array(array(
      'taxonomy' => 'product_cat',
      'field' => 'slug',
      'terms' => array( 'plans-and-pricing' ),
      'operator' => 'NOT IN'
    )));
  }

  remove_action( 'pre_get_posts', 'listify_hide_packages_from_shop' );
}
add_action( 'pre_get_posts', 'listify_hide_packages_from_shop' );


/**
* email user whenever role changed!
* @author: @jjjjcccjjf
* @link http://wpsnipp.com/index.php/functions-php/send-email-notification-when-user-role-changes/
*/
add_action( 'set_user_role', function( $user_id, $new_role, $old_role )
{
  if ($new_role == 'vendor') {
    $site_url = get_bloginfo('wpurl');
    $user_info = get_userdata( $user_id );
    $to = $user_info->user_email;
    $subject = "Role changed: ".$site_url."";
    $message = "Hello " .$user_info->display_name . " your role has changed on ".$site_url.", congratulations you are now a " . $new_role . ".";
    $message .= "\n\nYou can access advisor listings by clicking this link: " . site_url('advisor-listings');
    $message .= "\n\nYou can access your advisor profile by clicking this link: " . site_url('advisor-account');
    wp_mail($to, $subject, $message);
  }

}, 10, 3 );


add_filter('woocommerce_login_redirect', 'login_redirect', 10, 2);
function login_redirect( $redirect_to, $user ) {

  // WCV dashboard -- Uncomment the 3 lines below if using WC Vendors Free instead of WC Vendors Pro
  // if (class_exists('WCV_Vendors') && WCV_Vendors::is_vendor( $user->id ) ) {
  //  $redirect_to = get_permalink(WC_Vendors::$pv_options->get_option( 'vendor_dashboard_page' ));
  // }

  // WCV Pro Dashboard
  if (class_exists('WCV_Vendors') && class_exists('WCVendors_Pro') && WCV_Vendors::is_vendor( $user->id ) ) {
    $redirect_to = get_permalink(WCVendors_Pro::get_option( 'dashboard_page_id' ));
  }
  return $redirect_to;
}

the_content();
