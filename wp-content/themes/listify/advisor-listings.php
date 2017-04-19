<?php
/*
Template Name: Advisor Listings
*/

$users = get_users(
  array(
    'role' => 'vendor',
  )
);

$vendor_ids = $wpdb->get_results('SELECT ID FROM advisor_details WHERE 1', ARRAY_A);


// $first_name = get_user_meta($vendor_id, 'first_name', true);
// var_dump($vendor_id); die();

global $style;

$blog_style = get_theme_mod( 'content-blog-style', 'default' );
$style = 'grid-standard' == $blog_style ? 'standard' : 'cover';

/**
* all $GLOBALs in header
* /classes folder are loaded inside the header
*/
get_header();

?>

<ul>
  <?php
    foreach($vendor_ids as $vendor_id){
      $advisor_details = $wpdb->get_row('SELECT * FROM advisor_details WHERE ID = '. $vendor_id['ID'] , ARRAY_A);
      $advisor_details_meta = $wpdb->get_results('SELECT * FROM advisor_details_meta WHERE ID = '. $vendor_id['ID'] , ARRAY_A);

      $first_name = get_user_meta($vendor_id['ID'], 'first_name', true);
      $last_name = get_user_meta($vendor_id['ID'], 'last_name', true);
      $name = $first_name . " " . $last_name;

      ?>

      <li><a href="<?php echo site_url('view-profile') . "?a_id=" . $vendor_id['ID'] ?>"><?php echo $name ?></a></li>

    <?php }

  ?>
</ul>

<?php get_footer(); ?>
