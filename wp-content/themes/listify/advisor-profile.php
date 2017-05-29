<?php
/*
Template Name: Advisor Profile
*/

global $style;

$blog_style = get_theme_mod( 'content-blog-style', 'default' );
$style = 'grid-standard' == $blog_style ? 'standard' : 'cover';

if(isset($_POST['testimonial'])){
  $data['advisor_id'] = $_GET['a_id'];
  $data['reviewer_id'] = $GLOBALS['current_user']->ID;
  $data['rating'] = $_POST['rating'];
  $data['testimonial'] = $_POST['testimonial'];
  $wpdb->insert('advisor_reviews', $data);
  // Print last SQL query string
  // echo $wpdb->last_query;
  // Print last SQL query result
  // echo $wpdb->last_result;
  // Print last SQL query Error
  // echo $wpdb->last_error;
  // die();
  header('Location: ' . site_url('view-profile?a_id=' . $_GET['a_id']. "#"));
  die();
}

if(isset($_POST['a_msg'])){
  $data['receiver_id'] = $_GET['a_id'];
  $data['sender_id'] = $GLOBALS['current_user']->ID;
  $data['message'] = $_POST['a_msg'];
  $wpdb->insert('advisor_inbox', $data);

  header('Location: ' . site_url('view-profile?sc=1&a_id=' . $_GET['a_id']. "#"));
  die();

}

if(isset($_POST['apt_btn'])){
  $apt_datetime = date("l, F d Y g:iA", strtotime($_POST['apt_dt']));
  $apt_date = date("F d, Y", strtotime($_POST['apt_dt']));
  $apt_date_dbsafe = date("Y-m-d h:i:s", strtotime($_POST['apt_dt']));

  $advisor_name = $_POST['advisor_name'];
  $cust_name = $_POST['cust_name'];

  $product_meta = get_post_meta($_POST['product_id']);
  $product = get_post($_POST['product_id']);

  $advisor_msg = "
  <p>Hello, <strong>$advisor_name!</strong> You are receiving this message because
  <strong>$cust_name</strong> would like to avail your product/service entitled
  <em>$product->post_title</em> on <strong>$apt_datetime</strong>.</p>
  <p>You will receive an email notification once the payment has been verified.</p>
  <p><sub>Notice: This is a system generated message. Replying to this message will message <strong>$cust_name</strong></sub></p>
  ";


  # advisor stuffs
  $advisor_data['receiver_id'] = $_GET['a_id']; # Advisor to have an appointment with
  $advisor_data['sender_id'] = $GLOBALS['current_user']->ID;

  $advisor_data['message'] = $advisor_msg;
  $advisor_data['type'] = 1; # 1 = appointment
  $wpdb->insert('advisor_inbox', $advisor_data);
  # / advisor stuffs

  # customer stuffs
  $customer_data['receiver_id'] = $GLOBALS['current_user']->ID; # Receive this yourself
  $customer_data['sender_id'] = $_GET['a_id']; # Store advisor have an appointment with

  $customer_msg = "
  <p>Hello, <strong>$cust_name!</strong> You are receiving this message because
  you tried to avail a product/service entitled
  <em>$product->post_title</em> on <strong>$apt_datetime</strong>. from <strong>$advisor_name</strong></p>
  <p>Please check your cart to checkout and complete your purchase.</p>
  <p><sub>Notice: This is a system generated message. Replying to this message will message <strong>$advisor_name</strong></sub></p>
  ";

  $customer_data['message'] = $customer_msg;
  $customer_data['type'] = 1; # 1 = appointment
  $wpdb->insert('advisor_inbox', $customer_data);
  # /customer stuffs
  WC()->cart->add_to_cart($_POST['product_id']);

  header('Location: ' . site_url('view-profile?sc=2&a_id=' . $_GET['a_id']. "#"));
  die();

}

/**
* all $GLOBALs in header
* /classes folder are loaded inside the header
*/
get_header();

if($GLOBALS['current_user']->ID > 0){
  $is_logged_in = true;
}else{
  $is_logged_in = false;
}

$advisor_role = get_userdata($_GET['a_id'])->roles[0];

if($advisor_role != 'vendor'): # Show 404 if not vendor

?>
<div <?php echo apply_filters( 'listify_cover', 'page-cover' ); ?>>
  <div class="cover-wrapper">
    <h1 class="page-title"><?php _e( 'Oops! That page can&rsquo;t be found.', 'listify' ); ?></h1>
  </div>
</div>
<div id="primary" class="container">
  <div class="row content-area">
    <main id="main" class="site-main col-md-10 col-md-offset-1 col-xs-12" role="main">
      <?php get_template_part( 'content', 'none' ); ?>
    </main>
  </div>
</div>
<?php
else:

  /**
  * can be edited in profile
  * @var string
  */
  $first_name = get_user_meta($_GET['a_id'], 'first_name', true);
  $last_name = get_user_meta($_GET['a_id'], 'last_name', true);
  $name = $first_name . " " . $last_name;

  # For currently logged-in user
  $c_first_name = get_user_meta($GLOBALS['current_user']->ID, 'first_name', true);
  $c_last_name = get_user_meta($GLOBALS['current_user']->ID, 'last_name', true);
  $c_name = $c_first_name . " " . $c_last_name;



  $advisor_details = $wpdb->get_row('SELECT * FROM advisor_details WHERE ID = '. $_GET['a_id'], ARRAY_A);
  $advisor_details_meta = $wpdb->get_results('SELECT * FROM advisor_details_meta WHERE ID = '. $_GET['a_id'], ARRAY_A);
  $advisor_reviews = $wpdb->get_results('SELECT * FROM advisor_reviews WHERE advisor_id = '. $_GET['a_id'], ARRAY_A);

  // die(var_dump(array_unique(array_column($advisor_reviews, 'rating'))));
  $advisor_info = get_user_meta($_GET['a_id']);
  $advisor_data = get_userdata($_GET['a_id'])->data;
  // var_dump();
  // die();

  # Products for contact advisor block
  $args = array(
    'post_type' => 'product',
    'posts_per_page' => -1,
    'author' => $_GET['a_id']
  );
  $query = new WP_Query( $args );
  $product_dd = $query->posts;
  # /Products for contact advisor block

  ?>
  <div class="container">
    <?php
    if(@$_GET['sc'] == 1){ ?>
      <div class="sc-msg">Message sent</div>
      <?php }else if(@$_GET['sc'] == 2){ ?>
        <div class="sc-msg">Appointment booked successfully. Please check your <a href="<?php echo site_url('cart')?>">cart</a> to complete your payment.</div>
        <?php }
        ?>
        <section class="listing-banner" style="background: url(<?php echo theme_url; ?>images/bannerimg.jpg) no-repeat center center; background-size: cover;">
          <aside>
            <h1><?php echo $name; ?>
              <?php if($advisor_details['is_verified']):?>
                <span>Verified <i class="fa fa-check" aria-hidden="true"></i></span>
              <?php endif;?>
            </h1>
            <h4><?php echo $advisor_details['address'] ?></h4>
            <p><?php if($advisor_details){ echo str_replace(',', ', ', $advisor_details['expertise']); } ?></p>
            <?php
            $rating_count = count($advisor_reviews);
            $all_ratings = array_column($advisor_reviews, 'rating');
            /**
            * get the average of all ratings
            */
            $avg_rating = number_format(array_sum($all_ratings) / $rating_count, 1);
            ?>
            <fieldset class="rating2">
              <input type="radio"  value="5" <?php echo ($avg_rating <= 5.0 && $avg_rating > 4.5) ? 'checked' : '';?>/><label class = "full" for="star5" title="Awesome - 5 stars"></label>
              <input type="radio"  value="4 and a half" <?php echo ($avg_rating <= 4.5 && $avg_rating > 4.0) ? 'checked' : '';?>/><label class="half" for="star4half" title="Pretty good - 4.5 stars"></label>
              <input type="radio"  value="4" <?php echo ($avg_rating <= 4.0 && $avg_rating > 3.5) ? 'checked' : '';?>/><label class = "full" for="star4" title="Pretty good - 4 stars"></label>
              <input type="radio"  value="3 and a half" <?php echo ($avg_rating <= 3.5 && $avg_rating > 3.0) ? 'checked' : '';?>/><label class="half" for="star3half" title="Meh - 3.5 stars"></label>
              <input type="radio"  value="3" <?php echo ($avg_rating <= 3.0 && $avg_rating > 2.5) ? 'checked' : '';?>/><label class = "full" for="star3" title="Meh - 3 stars"></label>
              <input type="radio"  value="2 and a half" <?php echo ($avg_rating <= 2.5 && $avg_rating > 2.0 ) ? 'checked' : '';?>/><label class="half" for="star2half" title="Kinda bad - 2.5 stars"></label>
              <input type="radio"  value="2" <?php echo ($avg_rating <= 2.0 && $avg_rating > 1.5 ) ? 'checked' : '';?>/><label class = "full" for="star2" title="Kinda bad - 2 stars"></label>
              <input type="radio"  value="1 and a half" <?php echo ($avg_rating <= 1.5 && $avg_rating > 1.0 ) ? 'checked' : '';?>/><label class="half" for="star1half" title="Meh - 1.5 stars"></label>
              <input type="radio"  value="1" <?php echo ($avg_rating <= 1.0 && $avg_rating > 0.5) ? 'checked' : '';?>/><label class = "full" for="star1" title="Sucks big time - 1 star"></label>
              <input type="radio"  value="half" <?php echo ($avg_rating <= 0.5) ? 'checked' : '';?>/><label class="half" for="starhalf" title="Sucks big time - 0.5 stars"></label>
            </fieldset>
            <span><?php echo $rating_count?> Review(s)</span>
          </aside>
        </section>
        <!-- Modal Contact Advisor -->
        <div class="modal" id="modal-contact-advisor" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-header">
              <h2>Send a message</h2>
              <a href="#" class="btn-close" aria-hidden="true">×</a>
            </div>
            <div class="modal-body">
              <p>Say something nice to <?php echo $name ?></p>
              <form method="post">
                <textarea name="a_msg" rows="8" cols="80" placeholder="Your message"></textarea>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn-submit">Send</button>
              </form>
            </div>
          </div>
        </div>
        <!-- /Modal Contact Advisor -->
        <!-- Modal Appointment -->
        <div class="modal" id="modal-appointment" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-header">
              <h2>Book an appointment with <?php echo $name ?></h2>
              <a href="#" class="btn-close" aria-hidden="true">×</a>
            </div>
            <div class="modal-body">
              <form method="post">
                <p>Choose your preferred date and time to meet with <?php echo $name ?></p>
                <input type="datetime-local" name="apt_dt" value="" required="required">
                <input type="hidden" name="advisor_name" value="<?php echo $name ?>">
                <input type="hidden" name="cust_name" value="<?php echo $c_name ?>">
                <br>
                <br>
                <p>Choose the product/service you want to avail</p>
                <select name="product_id" required="required">

                  <?php
                  foreach($product_dd as $post) {
                    $price = get_post_meta($post->ID, '_regular_price');
                    ?>
                    <option value="<?php echo $post->ID ?>"><?php
                    echo $post->post_title
                    ?> - <?php echo $price[0] . " " .  get_option('woocommerce_currency')?>
                    </option>
                    <?php }

                    wp_reset_postdata();
                    ?>
                  </select>
                </div>
                <div class="modal-footer">
                  <button type="submit" name="apt_btn" class="btn-submit">Book Appointment</button>
                </form>
              </div>
            </div>
          </div>
          <!-- /Modal Appointment -->
          <section class="listing-details">
            <aside class="sidebar">
              <div class="profile-pic">
                <img src="<?php echo $advisor_details['listing_photo']?>">
              </div>
              <div class="agent-details">
                <h4><?php if($advisor_details){ echo $advisor_details['title']; } ?></h4>
                <?php if($is_logged_in): ?>
                  <ul>
                    <li><i class="fa fa-map-marker" aria-hidden="true"></i>
                      <?php if($advisor_details){ echo $advisor_details['address']; } ?>
                    </li>
                    <li><i class="fa fa-phone" aria-hidden="true"></i> <?php if($advisor_details){ echo $advisor_details['contact']; } ?>
                    </li>
                    <li>
                    <a href="mailto:<?php echo $advisor_data->user_email; ?>">
                      <i class="fa fa-envelope" aria-hidden="true"></i><?php echo $advisor_data->user_email; ?>
                    </a>
                    </li>
                  </ul>
                  <p class="btnGreen">
                    <a href="#modal-contact-advisor">Contact this Advisor</a>
                  </p>
                <?php endif; ?>
              </div>
            </aside>
            <article class="agent-main">
              <?php if($is_logged_in && count($product_dd) > 0): ?>
                <div class="btndiv">
                  <p class="btnGreen">
                    <a href="#modal-appointment">Book an Appointment</a>
                  </p>
                </div>
                <h3 class="experience-label">
                  Experience
                </h3>
                <section class="agent-content">
                  <?php if($advisor_details){ echo $advisor_details['experience']; } ?>
                </section>

              <?php endif; ?>

              <hr>

              <h3 class="licenses-label">
                Licenses, Certifications and Affiliations
              </h3>
              <ul class="agents">
                <?php
                if($advisor_details){
                  $user_licenses = explode(",", $advisor_details['license']);
                  foreach($user_licenses as $user_license){
                    ?>
                    <li><?php echo $user_license; ?></li>
                    <?php
                  }
                }
                ?>
              </ul>

              <hr>

              <h3 class="expertise-label">
                Expertise
              </h3>
              <ul class="expertise-list">
                <?php
                if($advisor_details){
                  $user_expertise = explode(",", $advisor_details['expertise']);
                  foreach($user_expertise as $user_field){
                    ?>
                    <li><?php echo $user_field; ?></li>
                    <?php
                  }
                }
                ?>
              </ul>

              <hr>
              <?php if($is_logged_in):?>

                <h3 class="awards-label">
                  Awards and Recognitions
                </h3>
                <section class="agent-content">
                  <?php if($advisor_details){ echo $advisor_details['award']; } ?>
                </section>

                <hr>

                <h3 class="gallery-label">
                  Photo Gallery
                </h3>
                <div class="agent-pgallery">
                  <div class="carousel" data-flickity='{ "wrapAround": true, "groupCells": true, "autoPlay": true }'>

                    <!-- PHOTO GALLERY  -->
                    <?php foreach($advisor_details_meta as $meta):
                      if($meta['meta_key'] == 'photo_gallery'):
                        ?>
                        <div class="carousel-cell">
                          <img src="<?php echo $meta['meta_value']?>">
                        </div>
                        <?php
                      endif;
                    endforeach;?>
                    <!-- / PHOTO GALLERY  -->

                  </div>
                </div>

                <hr>


                <h3 class="video-label">
                  Video Gallery
                </h3>
                <div class="agent-vgallery">
                  <div class="videocarousel" data-flickity='{ "wrapAround": true }'>
                    <!-- VIDEO GALLERY  -->
                    <?php foreach($advisor_details_meta as $meta):
                      if($meta['meta_key'] == 'video_gallery'):
                        ?>
                        <div class="carousel-cell" id="video_gallery-<?php echo $meta['meta_id']?>">
                          <center><a href="javascript:void(0);" onclick="ajaxDeleteMeta(<?php echo $meta['meta_id']?>, 'video_gallery')"><i class="fa fa-minus-square" aria-hidden="true"></i> Delete</a></center>
                          <div class="video-container">
                            <iframe width="560" height="315" src="<?php echo $meta['meta_value']?>" frameborder="0" allowfullscreen></iframe>
                          </div>
                        </div>
                        <?php
                      endif;
                    endforeach;?>
                    <!-- / VIDEO GALLERY  -->
                  </div>
                </div>


                <hr>

                <h3>Rate per Consultation</h3>
                <section class="agent-content">
                  <?php
                  $args = array(
                    'post_type' => 'product',
                    'posts_per_page' => -1,
                    'author' => $_GET['a_id']
                  );
                  $query = new WP_Query( $args );
                  $posts = $query->posts;

                  foreach($posts as $post) {
                    $price = get_post_meta($post->ID, '_regular_price');
                    ?>
                    <p><?php
                    echo $post->post_title
                    ?> - <?php echo $price[0] . " " .  get_option('woocommerce_currency')?></p>
                    <?php }

                    wp_reset_postdata();
                    ?>
                  </section>
                  <hr>

                  <h3>Blog</h3>
                  <ul class="agents">
                    <?php
                    $blogs = get_posts(array('order' => 'DESC', 'orderby' => 'date', 'posts_per_page' => 2));

                    foreach($blogs as $blog){
                      ?>
                      <li>
                        <h5><?php echo $blog->post_title; ?></h5>
                        <p><?php echo $blog->post_excerpt; ?></p>
                        <p><a href="<?php echo get_permalink($blog->ID); ?>" class="readmore">Read More</a></p>
                      </li>
                      <?php } ?>
                    </ul>

                    <hr>

                    <ul class="social-links">
                      <li class="fb">
                        <a href="<?php echo $advisor_details['social_fb'] ?>"><i class="fa fa-facebook" aria-hidden="true"></i>
                        </a>
                      </li>
                      <li class="gp">
                        <a href="<?php echo $advisor_details['social_gplus'] ?>"><i class="fa fa-google-plus" aria-hidden="true"></i>
                        </a>
                      </li>
                      <li class="in">
                        <a href="<?php echo $advisor_details['social_linkedin'] ?>"><i class="fa fa-linkedin" aria-hidden="true"></i>
                        </a>
                      </li>
                      <li class="sk">
                        <a href="<?php echo $advisor_details['social_skype'] ?>"><i class="fa fa-skype" aria-hidden="true"></i>
                        </a>
                      </li>
                    </ul>
                  <?php endif; ?>
                  <section class="agent-review">
                    <h3><?php echo $rating_count ?> Review(s)</h3>
                    <div class="review-box">
                      <aside class="overall-rating">
                        <p>Average Rating</p>
                        <h1><?php echo $avg_rating; ?></h1>
                        <fieldset class="rating2">
                          <input type="radio"  value="5" <?php echo ($avg_rating <= 5.0 && $avg_rating > 4.5) ? 'checked' : '';?>/><label class = "full" for="star5" title="Awesome - 5 stars"></label>
                          <input type="radio"  value="4 and a half" <?php echo ($avg_rating <= 4.5 && $avg_rating > 4.0) ? 'checked' : '';?>/><label class="half" for="star4half" title="Pretty good - 4.5 stars"></label>
                          <input type="radio"  value="4" <?php echo ($avg_rating <= 4.0 && $avg_rating > 3.5) ? 'checked' : '';?>/><label class = "full" for="star4" title="Pretty good - 4 stars"></label>
                          <input type="radio"  value="3 and a half" <?php echo ($avg_rating <= 3.5 && $avg_rating > 3.0) ? 'checked' : '';?>/><label class="half" for="star3half" title="Meh - 3.5 stars"></label>
                          <input type="radio"  value="3" <?php echo ($avg_rating <= 3.0 && $avg_rating > 2.5) ? 'checked' : '';?>/><label class = "full" for="star3" title="Meh - 3 stars"></label>
                          <input type="radio"  value="2 and a half" <?php echo ($avg_rating <= 2.5 && $avg_rating > 2.0 ) ? 'checked' : '';?>/><label class="half" for="star2half" title="Kinda bad - 2.5 stars"></label>
                          <input type="radio"  value="2" <?php echo ($avg_rating <= 2.0 && $avg_rating > 1.5 ) ? 'checked' : '';?>/><label class = "full" for="star2" title="Kinda bad - 2 stars"></label>
                          <input type="radio"  value="1 and a half" <?php echo ($avg_rating <= 1.5 && $avg_rating > 1.0 ) ? 'checked' : '';?>/><label class="half" for="star1half" title="Meh - 1.5 stars"></label>
                          <input type="radio"  value="1" <?php echo ($avg_rating <= 1.0 && $avg_rating > 0.5) ? 'checked' : '';?>/><label class = "full" for="star1" title="Sucks big time - 1 star"></label>
                          <input type="radio"  value="half" <?php echo ($avg_rating <= 0.5) ? 'checked' : '';?>/><label class="half" for="starhalf" title="Sucks big time - 0.5 stars"></label>
                        </fieldset>
                      </aside>
                      <div class="ratings-summary">

                        <?php
                        $unique_ratings = array_unique(array_column($advisor_reviews, 'rating'));
                        $u_rating_count = array(
                          '5.00' => 0,
                          '4.50' => 0,
                          '4.00' => 0,
                          '3.50' => 0,
                          '3.00' => 0,
                          '2.50' => 0,
                          '2.00' => 0,
                          '1.50' => 0,
                          '1.00' => 0,
                          '0.50' => 0,
                        );

                        foreach($u_rating_count as $key => $val){
                          $u_rating_count[$key] = count(array_keys($all_ratings, $key));
                        }

                        ?>
                        <ul>
                          <li><label>1 Star</label>
                            <div class="ratingbar">
                              <div style="width: <?php
                              echo $ratingbar1 = round((($u_rating_count['0.50'] + $u_rating_count['1.00']) / $rating_count) * 100);
                              ?>%; background: #48c062;  height: 10px"></div>
                            </div>
                            <div class="ratingnum">
                              <?php echo $ratingbar1 ?>%
                            </div>
                          </li>
                          <li><label>2 Star</label>
                            <div class="ratingbar">
                              <div style="width: <?php
                              echo $ratingbar2 = round((($u_rating_count['1.50'] + $u_rating_count['2.00']) / $rating_count) * 100);
                              ?>%; background: #48c062; height: 10px"></div>
                            </div>
                            <div class="ratingnum">
                              <?php echo $ratingbar2 ?>%
                            </div>
                          </li>
                          <li><label>3 Star</label>
                            <div class="ratingbar">
                              <div style="width: <?php
                              echo $ratingbar3 = round((($u_rating_count['2.50'] + $u_rating_count['3.00']) / $rating_count) * 100);
                              ?>%; background: #48c062; height: 10px"></div>
                            </div>
                            <div class="ratingnum">
                              <?php echo $ratingbar3 ?>%
                            </div>
                          </li>
                          <li><label>4 Star</label>
                            <div class="ratingbar">
                              <div style="width: <?php
                              echo $ratingbar4 = round((($u_rating_count['3.50'] + $u_rating_count['4.00']) / $rating_count) * 100);
                              ?>%; background: #48c062; height: 10px"></div>
                            </div>
                            <div class="ratingnum">
                              <?php echo $ratingbar4 ?>%</div>
                            </li>
                            <li><label>5 Star</label>
                              <div class="ratingbar">
                                <div style="width: <?php
                                echo $ratingbar5 = round((($u_rating_count['4.50'] + $u_rating_count['5.00']) / $rating_count) * 100);
                                ?>%; background: #48c062; height: 10px"></div>
                              </div>
                              <div class="ratingnum">
                                <?php echo $ratingbar5 ?>%
                              </div>
                            </li>
                          </ul>
                        </div>
                      </div>

                      <article class="ratings-list">

                        <!-- REVIEW COMMENTS  -->
                        <?php foreach($advisor_reviews as $row):
                          ?>
                          <div class="customer-rating">
                            <div class="customer-pic"><img src="<?php echo get_avatar_url($row['reviewer_id'])?>"></div>
                            <div class="customer-review">
                              <h5>
                                <?php echo get_user_meta($row['reviewer_id'], 'first_name', true)
                                . ' ' . get_user_meta($row['reviewer_id'], 'last_name', true)?>
                                <span class="fright">
                                  <fieldset class="rating">
                                    <input type="radio"  value="5" <?php echo ($row['rating'] == 5.0) ? 'checked' : '';?>/><label class = "full" for="star5" title="Awesome - 5 stars"></label>
                                    <input type="radio"  value="4 and a half" <?php echo ($row['rating'] == 4.5) ? 'checked' : '';?>/><label class="half" for="star4half" title="Pretty good - 4.5 stars"></label>
                                    <input type="radio"  value="4" <?php echo ($row['rating'] == 4.0) ? 'checked' : '';?>/><label class = "full" for="star4" title="Pretty good - 4 stars"></label>
                                    <input type="radio"  value="3 and a half" <?php echo ($row['rating'] == 3.5) ? 'checked' : '';?>/><label class="half" for="star3half" title="Meh - 3.5 stars"></label>
                                    <input type="radio"  value="3" <?php echo ($row['rating'] == 3.0) ? 'checked' : '';?>/><label class = "full" for="star3" title="Meh - 3 stars"></label>
                                    <input type="radio"  value="2 and a half" <?php echo ($row['rating'] == 2.5 ) ? 'checked' : '';?>/><label class="half" for="star2half" title="Kinda bad - 2.5 stars"></label>
                                    <input type="radio"  value="2" <?php echo ($row['rating'] == 2.0 ) ? 'checked' : '';?>/><label class = "full" for="star2" title="Kinda bad - 2 stars"></label>
                                    <input type="radio"  value="1 and a half" <?php echo ($row['rating'] == 1.5) ? 'checked' : '';?>/><label class="half" for="star1half" title="Meh - 1.5 stars"></label>
                                    <input type="radio"  value="1" <?php echo ($row['rating'] == 1.0) ? 'checked' : '';?>/><label class = "full" for="star1" title="Sucks big time - 1 star"></label>
                                    <input type="radio"  value="half" <?php echo ($row['rating'] == 0.5) ? 'checked' : '';?>/><label class="half" for="starhalf" title="Sucks big time - 0.5 stars"></label>
                                  </fieldset>
                                </span>
                              </h5>
                              <p><?php echo $row['testimonial'] ?></p>
                            </div>
                          </div>
                          <?php
                        endforeach;?>
                        <!-- / REVIEW COMMENTS  -->

                        <form method="post">
                          <h3>Leave Your Review
                            <span class="fright">
                              <fieldset class="rating" >
                                <input type="radio" id="star5" name="rating" value="5" /><label class = "full" for="star5" title="Awesome - 5 stars"></label>
                                <input type="radio" id="star4_5" name="rating" value="4.5" /><label onclick="$('#star4_5').prop('checked', true);" class="half" for="star4half" title="Pretty good - 4.5 stars"></label>
                                <input type="radio" id="star4" name="rating" value="4" /><label class = "full" for="star4" title="Pretty good - 4 stars"></label>
                                <input type="radio" id="star3_5" name="rating" value="3.5" /><label onclick="$('#star3_5').prop('checked', true);" class="half" for="star3half" title="Meh - 3.5 stars"></label>
                                <input type="radio" id="star3" name="rating" value="3" /><label class = "full" for="star3" title="Meh - 3 stars"></label>
                                <input type="radio" id="star2_5" name="rating" value="2.5" /><label onclick="$('#star2_5').prop('checked', true);" class="half" for="star2half" title="Kinda bad - 2.5 stars"></label>
                                <input type="radio" id="star2" name="rating" value="2" /><label class = "full" for="star2" title="Kinda bad - 2 stars"></label>
                                <input type="radio" id="star1_5" name="rating" value="1.5" /><label onclick="$('#star1_5').prop('checked', true);" class="half" for="star1half" title="Meh - 1.5 stars"></label>
                                <input type="radio" id="star1" name="rating" value="1" /><label class = "full" for="star1" title="Sucks big time - 1 star"></label>
                                <input type="radio" id="star0_5" name="rating" value="0.5" /><label onclick="$('#star0_5').prop('checked', true);" class="half" for="starhalf" title="Sucks big time - 0.5 stars"></label>
                              </fieldset>
                            </span></h3>
                            <?php if(!$is_logged_in): ?>
                              <p class="btnGreen fleft">
                                <a href="<?php echo site_url('myaccount')?>">Log in to add review</a>
                              </p>
                            <?php endif; ?>

                            <?php if($is_logged_in): ?>
                              <div class="customer-rating">
                                <div class="customer-review">
                                  <h5>

                                  </h5>
                                </div>
                                <textarea name="testimonial"></textarea>
                                <input type="submit" name="" value="Submit" style="margin-top:20px;">
                              </div>
                            </form>
                          <?php endif; ?>
                        </section>
                      </article>


                    </section>

                  </div>


                  <?php
                endif;
                get_footer(); ?>

                <script type="text/javascript">
                $(document).ready(function(){

                  triggerStar = function(id){
                    $('#' + id).prop('checked', true);
                    alert('Checked ' + id)
                  }

                });
                </script>
