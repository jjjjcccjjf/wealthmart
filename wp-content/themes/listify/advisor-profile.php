<?php
/*
Template Name: View Advisor Profile
*/

global $style;

$blog_style = get_theme_mod( 'content-blog-style', 'default' );
$style = 'grid-standard' == $blog_style ? 'standard' : 'cover';

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

/**
* check if $_GET['a_id'] (Advisor ID) exists in the DB
* @var int
*/
$advisor_exists = $wpdb->get_var("SELECT COUNT(*) FROM advisor_details WHERE ID = ". $_GET['a_id'] ." AND is_approved = 1");

if($advisor_exists != 1):

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
  $first_name = get_user_meta($GLOBALS['current_user']->ID, 'first_name', true);
  $last_name = get_user_meta($GLOBALS['current_user']->ID, 'last_name', true);
  $name = $first_name . " " . $last_name;

  $advisor_details = $wpdb->get_row('SELECT * FROM advisor_details WHERE ID = '. $_GET['a_id'], ARRAY_A);
  $advisor_details_meta = $wpdb->get_results('SELECT * FROM advisor_details_meta WHERE ID = '.$GLOBALS['current_user']->ID, ARRAY_A);

  $advisor_info = get_userdata($_GET['a_id'])->data;
  // var_dump($advisor_info);
  // die();
  ?>
  <div class="container">
    <section class="listing-banner" style="background: url(<?php echo theme_url; ?>images/bannerimg.jpg) no-repeat center center; background-size: cover;">
      <aside>
        <h1><?php echo $name; ?>
          <?php if($advisor_details['is_verified']):?>
            <span>Verified <i class="fa fa-check" aria-hidden="true"></i></span>
          <?php endif;?>
        </h1>
        <h4><?php echo $advisor_details['address'] ?></h4>
        <p><?php if($advisor_details){ echo str_replace(',', ', ', $advisor_details['expertise']); } ?></p>
        <fieldset class="rating">
          <input type="radio" id="star5" name="rating" value="5" /><label class = "full" for="star5" title="Awesome - 5 stars"></label>
          <input type="radio" id="star4half" name="rating" value="4 and a half" /><label class="half" for="star4half" title="Pretty good - 4.5 stars"></label>
          <input type="radio" id="star4" name="rating" value="4" /><label class = "full" for="star4" title="Pretty good - 4 stars"></label>
          <input type="radio" id="star3half" name="rating" value="3 and a half" /><label class="half" for="star3half" title="Meh - 3.5 stars"></label>
          <input type="radio" id="star3" name="rating" value="3" /><label class = "full" for="star3" title="Meh - 3 stars"></label>
          <input type="radio" id="star2half" name="rating" value="2 and a half" /><label class="half" for="star2half" title="Kinda bad - 2.5 stars"></label>
          <input type="radio" id="star2" name="rating" value="2" /><label class = "full" for="star2" title="Kinda bad - 2 stars"></label>
          <input type="radio" id="star1half" name="rating" value="1 and a half" /><label class="half" for="star1half" title="Meh - 1.5 stars"></label>
          <input type="radio" id="star1" name="rating" value="1" /><label class = "full" for="star1" title="Sucks big time - 1 star"></label>
          <input type="radio" id="starhalf" name="rating" value="half" /><label class="half" for="starhalf" title="Sucks big time - 0.5 stars"></label>

        </fieldset>
        <span>1 Review(s)</span>
      </aside>
    </section>
    <section class="listing-details">
      <aside class="sidebar">
        <div class="profile-pic">
          <img src="<?php echo $advisor_details['listing_photo']?>">
        </div>
        <div class="agent-details">
          <h4><?php if($advisor_details){ echo $advisor_details['position']; } ?></h4>
          <?php if($is_logged_in): ?>
            <ul>
              <li><i class="fa fa-map-marker" aria-hidden="true"></i>
                <?php if($advisor_details){ echo $advisor_details['address']; } ?>
              </li>
              <li><i class="fa fa-phone" aria-hidden="true"></i> <?php if($advisor_details){ echo $advisor_details['contact']; } ?>
              </li>
              <li><i class="fa fa-envelope" aria-hidden="true"></i><?php echo $advisor_info->user_email; ?>
              </li>
            </ul>
            <p class="btnGreen">
              <a href="#">Contact this Advisor</a>
            </p>
            <p class="btnGreen">
              <a href="#">Refer this Advisor</a>
            </p>
          <?php endif; ?>
        </div>
      </aside>
      <article class="agent-main">
        <?php if($is_logged_in): ?>
          <div class="btndiv">
            <p class="btnGreen">
              <a href="#">Consult Now</a>
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

              <div class="carousel-cell">
                <div class="video-container">
                  <iframe width="560" height="315" src="https://www.youtube.com/embed/L6CKuz5a65I" frameborder="0" allowfullscreen></iframe>
                </div>
              </div>
              <div class="carousel-cell">
                <div class="video-container">
                  <iframe width="560" height="315" src="https://www.youtube.com/embed/ZNebSeFVPNc" frameborder="0" allowfullscreen></iframe>
                </div>
              </div>
              <div class="carousel-cell">
                <div class="video-container">
                  <iframe width="560" height="315" src="https://www.youtube.com/embed/vpYkz5WU1Vg" frameborder="0" allowfullscreen></iframe>
                </div>
              </div>

            </div>
          </div>


          <hr>

          <h3>Rates per Consultation</h3>
          <section class="agent-content">
            <?php if($advisor_details){ echo $advisor_details['rates']; } ?>
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
                <a href="#"><i class="fa fa-facebook" aria-hidden="true"></i>
                </a>
              </li>
              <li class="gp">
                <a href="#"><i class="fa fa-google-plus" aria-hidden="true"></i>
                </a>
              </li>
              <li class="in">
                <a href="#"><i class="fa fa-linkedin" aria-hidden="true"></i>
                </a>
              </li>
              <li class="sk">
                <a href="#"><i class="fa fa-skype" aria-hidden="true"></i>
                </a>
              </li>
            </ul>
          <?php endif; ?>
          <section class="agent-review">
            <h3>1 Review(s)</h3>
            <div class="review-box">
              <aside class="overall-rating">
                <p>Average Rating</p>
                <h1>3.0</h1>
                <fieldset class="rating2">
                  <input type="radio" id="star5" name="rating" value="5" /><label class = "full" for="star5" title="Awesome - 5 stars"></label>
                  <input type="radio" id="star4half" name="rating" value="4 and a half" /><label class="half" for="star4half" title="Pretty good - 4.5 stars"></label>
                  <input type="radio" id="star4" name="rating" value="4" /><label class = "full" for="star4" title="Pretty good - 4 stars"></label>
                  <input type="radio" id="star3half" name="rating" value="3 and a half" /><label class="half" for="star3half" title="Meh - 3.5 stars"></label>
                  <input type="radio" id="star3" name="rating" value="3" /><label class = "full" for="star3" title="Meh - 3 stars"></label>
                  <input type="radio" id="star2half" name="rating" value="2 and a half" /><label class="half" for="star2half" title="Kinda bad - 2.5 stars"></label>
                  <input type="radio" id="star2" name="rating" value="2" /><label class = "full" for="star2" title="Kinda bad - 2 stars"></label>
                  <input type="radio" id="star1half" name="rating" value="1 and a half" /><label class="half" for="star1half" title="Meh - 1.5 stars"></label>
                  <input type="radio" id="star1" name="rating" value="1" /><label class = "full" for="star1" title="Sucks big time - 1 star"></label>
                  <input type="radio" id="starhalf" name="rating" value="half" /><label class="half" for="starhalf" title="Sucks big time - 0.5 stars"></label>
                </aside>
                <div class="ratings-summary">
                  <ul>
                    <li><label>1 Star</label>
                      <div class="ratingbar">
                        <div style="width: 0%; background: #48c062;"></div>
                      </div>
                      <div class="ratingnum">0%</div>
                    </li>
                    <li><label>2 Star</label>
                      <div class="ratingbar">
                        <div style="width: 0%; background: #48c062; height: 10px"></div>
                      </div>
                      <div class="ratingnum">0%</div>
                    </li>
                    <li><label>3 Star</label>
                      <div class="ratingbar">
                        <div style="width: 60%; background: #48c062; height: 10px"></div>
                      </div>
                      <div class="ratingnum">60%</div>
                    </li>
                    <li><label>4 Star</label>
                      <div class="ratingbar">
                        <div style="width: 0%; background: #48c062"></div>
                      </div>
                      <div class="ratingnum">0%</div>
                    </li>
                    <li><label>5 Star</label>
                      <div class="ratingbar">
                        <div style="width: 0%; background: #48c062"></div>
                      </div>
                      <div class="ratingnum">0%</div>
                    </li>
                  </ul>
                </div>
              </div>

              <article class="ratings-list">
                <div class="customer-rating">
                  <div class="customer-pic"><img src="images/customerpic.jpg"></div>
                  <div class="customer-review">
                    <h5>Angelo Juan
                      <span class="fright">
                        <fieldset class="rating">
                          <input type="radio" id="star5" name="rating" value="5" /><label class = "full" for="star5" title="Awesome - 5 stars"></label>
                          <input type="radio" id="star4half" name="rating" value="4 and a half" /><label class="half" for="star4half" title="Pretty good - 4.5 stars"></label>
                          <input type="radio" id="star4" name="rating" value="4" /><label class = "full" for="star4" title="Pretty good - 4 stars"></label>
                          <input type="radio" id="star3half" name="rating" value="3 and a half" /><label class="half" for="star3half" title="Meh - 3.5 stars"></label>
                          <input type="radio" id="star3" name="rating" value="3" /><label class = "full" for="star3" title="Meh - 3 stars"></label>
                          <input type="radio" id="star2half" name="rating" value="2 and a half" /><label class="half" for="star2half" title="Kinda bad - 2.5 stars"></label>
                          <input type="radio" id="star2" name="rating" value="2" /><label class = "full" for="star2" title="Kinda bad - 2 stars"></label>
                          <input type="radio" id="star1half" name="rating" value="1 and a half" /><label class="half" for="star1half" title="Meh - 1.5 stars"></label>
                          <input type="radio" id="star1" name="rating" value="1" /><label class = "full" for="star1" title="Sucks big time - 1 star"></label>
                          <input type="radio" id="starhalf" name="rating" value="half" /><label class="half" for="starhalf" title="Sucks big time - 0.5 stars"></label>
                        </span>
                      </h5>
                      <p>Review in text</p>
                    </div>
                  </div>

                  <div class="customer-rating">
                    <div class="customer-pic"><img src="images/customerpic.jpg"></div>
                    <div class="customer-review">
                      <h5>Angelo Juan
                        <span class="fright">
                          <fieldset class="rating">
                            <input type="radio" id="star5" name="rating" value="5" /><label class = "full" for="star5" title="Awesome - 5 stars"></label>
                            <input type="radio" id="star4half" name="rating" value="4 and a half" /><label class="half" for="star4half" title="Pretty good - 4.5 stars"></label>
                            <input type="radio" id="star4" name="rating" value="4" /><label class = "full" for="star4" title="Pretty good - 4 stars"></label>
                            <input type="radio" id="star3half" name="rating" value="3 and a half" /><label class="half" for="star3half" title="Meh - 3.5 stars"></label>
                            <input type="radio" id="star3" name="rating" value="3" /><label class = "full" for="star3" title="Meh - 3 stars"></label>
                            <input type="radio" id="star2half" name="rating" value="2 and a half" /><label class="half" for="star2half" title="Kinda bad - 2.5 stars"></label>
                            <input type="radio" id="star2" name="rating" value="2" /><label class = "full" for="star2" title="Kinda bad - 2 stars"></label>
                            <input type="radio" id="star1half" name="rating" value="1 and a half" /><label class="half" for="star1half" title="Meh - 1.5 stars"></label>
                            <input type="radio" id="star1" name="rating" value="1" /><label class = "full" for="star1" title="Sucks big time - 1 star"></label>
                            <input type="radio" id="starhalf" name="rating" value="half" /><label class="half" for="starhalf" title="Sucks big time - 0.5 stars"></label>
                          </span>
                        </h5>
                        <p>Review in text</p>
                      </div>
                    </div>
                  </article>

                  <h3>Leave Your Review</h3>
                  <p class="btnGreen fleft">
                    <a href="#">Log in to add review</a>
                  </p>
                </section>
              </article>


            </section>

          </div>


          <?php
        endif;
        get_footer(); ?>
