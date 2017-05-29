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

<div style="margin:27px"><h2>Advisor Listings</h2></div>
<div class="row">
  <?php
  foreach($vendor_ids as $vendor_id){
    $advisor_details = $wpdb->get_row('SELECT * FROM advisor_details WHERE ID = '. $vendor_id['ID'] , ARRAY_A);
    $advisor_details_meta = $wpdb->get_results('SELECT * FROM advisor_details_meta WHERE ID = '. $vendor_id['ID'] , ARRAY_A);

    $first_name = get_user_meta($vendor_id['ID'], 'first_name', true);
    $last_name = get_user_meta($vendor_id['ID'], 'last_name', true);
    $name = $first_name . " " . $last_name;

    ?>

    <div class="col-sm-4 card-div">
      <a href="<?php echo site_url('view-profile') . "?a_id=" . $vendor_id['ID'] ?>">
        <label class="pure-toggle card-user" for="pure-toggle">
          <div class="header">
          </div>
          <div class="avatar">
            <!-- <img src="<?php #echo get_avatar_url($vendor_id['ID'])?>" alt="..."> -->
            <?php $listing_photo = ($advisor_details['listing_photo'] != "") ? $advisor_details['listing_photo'] : 'https://developersushant.files.wordpress.com/2015/02/no-image-available.png'; ?>
            <img src="<?php echo $listing_photo ?>" alt="...">
          </div>
          <div class="text">
            <h3><?php echo $name ?></h3>
          </div>
        </a>
      </label>
    </div> <!-- end card div -->

    <?php }

    ?>
  </div>

  <?php get_footer();
