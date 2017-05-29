<?php
/*
Template Name: Advisor Account
*/

if($_POST){

	$photo_gallery_size = 0;

	/**
	* c means counter
	* @var integer
	*/
	$c = 0;
	foreach($_FILES['photo_gallery']['size'] as $size){
		$photo_gallery_size += $size;
	}

	/**
	* check if photo for photo gallery is empty
	*/
	if($photo_gallery_size > 0){
		$uploaddir = wp_upload_dir()['path'] . '/';

		foreach($_FILES['photo_gallery']['name'] as $key => $value){

			$fn = explode('.', $value);
			$fn[0] .= time();

			$new_file_name = implode('.', $fn);

			$file = $uploaddir . $new_file_name;
			$photo_gallery_url = wp_upload_dir()['url'] . '/' . $new_file_name;

			$meta_data = array();
			$raw_file_name = $_FILES['photo_gallery']['tmp_name'][$key];
			if (move_uploaded_file($_FILES['photo_gallery']['tmp_name'][$key], $file)) {

				$meta_data['ID'] = $GLOBALS['current_user']->ID;
				$meta_data['meta_key'] = 'photo_gallery';
				$meta_data['meta_value'] = $photo_gallery_url;
				$wpdb->insert('advisor_details_meta', $meta_data);

			} else {
				// echo "error";
			}
		}
	}

	if($_POST['video_gallery'] != ''){
		$meta_data['ID'] = $GLOBALS['current_user']->ID;
		$meta_data['meta_key'] = 'video_gallery';
		$meta_data['meta_value'] = $_POST['video_gallery'];
		$wpdb->insert('advisor_details_meta', $meta_data);
	}

	$detail_data = array();

	/**
	* if there is a file
	*/
	if($_FILES['listing_photo']['size'] > 0){
		$uploaddir = wp_upload_dir()['path'] . '/';

		$fn = explode('.', basename($_FILES['listing_photo']['name']));
		$fn[0] .= time();

		$new_file_name = implode('.', $fn);

		$file = $uploaddir . $new_file_name;
		$listing_photo_url = wp_upload_dir()['url'] . '/' . $new_file_name;

		$raw_file_name = $_FILES['listing_photo']['tmp_name'];
		if (move_uploaded_file($_FILES['listing_photo']['tmp_name'], $file)) {
			# TODO: add some kind of confirmation shizzz
		} else {
			// echo "error";
		}
		$detail_data['listing_photo'] = $listing_photo_url;
	}
	$detail_data['ID'] = $GLOBALS['current_user']->ID;
	$detail_data['title'] = $_POST['title'];
	$detail_data['address'] = $_POST['address'];
	$detail_data['contact'] = $_POST['contact'];
	$detail_data['experience'] = $_POST['experience'];
	/*social shits*/
	$detail_data['social_fb'] = $_POST['social_fb'];
	$detail_data['social_gplus'] = $_POST['social_gplus'];
	$detail_data['social_linkedin'] = $_POST['social_linkedin'];
	$detail_data['social_skype'] = $_POST['social_skype'];

	$selected_license = "";

	foreach($_POST['license'] as $selected){
		if($selected == 'Others, pls specify'){
			$license_to_add = $_POST['license_other'];
		}else{
			$license_to_add = $selected;
		}

		if($selected_license == ""){
			$selected_license .= $license_to_add;
		}else{
			$selected_license .= ",".$license_to_add;
		}
	}

	$detail_data['license'] = $selected_license;

	$selected_expertise = "";

	foreach($_POST['expertise'] as $selected){
		if($selected == 'Others, pls specify'){
			$expertise_to_add = $_POST['expert_other'];
		}else{
			$expertise_to_add = $selected;
		}

		if($selected_expertise == ""){
			$selected_expertise .= $expertise_to_add;
		}else{
			$selected_expertise .= ",".$expertise_to_add;
		}
	}

	$detail_data['expertise'] = $selected_expertise;
	$detail_data['award'] = $_POST['award'];
	$detail_data['rates'] = $_POST['rates'];

	$details_exists = $wpdb->get_var("SELECT COUNT(*) FROM advisor_details WHERE ID = ".$GLOBALS['current_user']->ID);
	if($details_exists == 0){
		$wpdb->insert('advisor_details', $detail_data);
		// Print last SQL query string
		// echo $wpdb->last_query;
		// Print last SQL query result
		// echo $wpdb->last_result;
		// Print last SQL query Error
		// echo $wpdb->last_error;
		// die();
	}else{
		$wpdb->update('advisor_details', $detail_data, array('ID' => $GLOBALS['current_user']->ID));
	}

	header('Location: ' . site_url('advisor-account?sc=1#'));
	die();
}

get_header();
global $style;

/**
* /classes folder are loaded inside the header
*/

$blog_style = get_theme_mod( 'content-blog-style', 'default' );
$style = 'grid-standard' == $blog_style ? 'standard' : 'cover';

if($GLOBALS['current_user']->roles[0] == 'vendor' || $GLOBALS['current_user']->roles[0] == 'pending_vendor'){
	$advisor_details = $wpdb->get_row('SELECT * FROM advisor_details WHERE ID = '.$GLOBALS['current_user']->ID, ARRAY_A);
	$advisor_details_meta = $wpdb->get_results('SELECT * FROM advisor_details_meta WHERE ID = '.$GLOBALS['current_user']->ID, ARRAY_A);
	$advisor_reviews = $wpdb->get_results('SELECT * FROM advisor_reviews WHERE advisor_id = '. $GLOBALS['current_user']->ID, ARRAY_A);
}elseif($GLOBALS['current_user']->roles[0] == 'customer'){
	// dont show this to end users
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
	get_footer();
	die();
}else{ # Same here show 404 template ?>
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
	get_footer();
	die();
}

$details_exists = $wpdb->get_var("SELECT COUNT(*) FROM advisor_details WHERE ID = ".$GLOBALS['current_user']->ID);

/**
* can be edited in profile
* @var string
*/
$first_name = get_user_meta($GLOBALS['current_user']->ID, 'first_name', true);
$last_name = get_user_meta($GLOBALS['current_user']->ID, 'last_name', true);
$name = $first_name . " " . $last_name;



?>
	<form method="POST" enctype="multipart/form-data">
		<div class="container">
			<?php if(@$_GET['sc'] == 1){ ?>
				<div class="sc-msg">Profile updated.</div>
				<?php } ?>
			<section class="listing-banner" style="background: url(<?php echo theme_url; ?>images/bannerimg.jpg) no-repeat center center; background-size: cover;">
				<aside>
					<h1><?php echo $name ?>
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
					<span><?php echo $rating_count?> Review(s)</span><br>
				</aside>
				<aside>
					<a style="    color: whitesmoke;
					text-decoration: underline;
					font-weight: bold;
					font-size: 17px;" href="<?php echo site_url('view-profile') . "?a_id=" . $GLOBALS['current_user']->ID ?>">View profile</a>
				</aside>
			</section>
			<section class="listing-details">
				<aside class="sidebar">
					<div class="profile-pic">
						<?php if(isset($advisor_details['listing_photo'])){
							?><img src="<?php echo $advisor_details['listing_photo']; ?>" >
							<?php } ?>
							<input type="file" name="listing_photo">
						</div>
						<div class="agent-details">
							<ul style="margin-top:80px;">
								<li><i class="fa fa-user" aria-hidden="true"></i>
									<input type="text" name="title" placeholder="Title" <?php if($advisor_details){ ?>value="<?php echo $advisor_details['title']; ?>"<?php } ?>>
								</li>
								<li><i class="fa fa-map-marker" aria-hidden="true"></i>
									<input type="text" placeholder="Address" name="address" <?php if($advisor_details){ ?>value="<?php echo $advisor_details['address']; ?>"<?php } ?> >
								</li>
								<li><i class="fa fa-phone" aria-hidden="true"></i>
									<input type="text" placeholder="Contact Number" name="contact" <?php if($advisor_details){ ?>value="<?php echo $advisor_details['contact']; ?>"<?php } ?>>
								</li>
								<li><i class="fa fa-envelope" aria-hidden="true"></i>
									<?php echo $GLOBALS['current_user']->user_email; ?>
								</li>
							</ul>

						</div>
					</aside>

					<article class="agent-main">

						<h3 class="experience-label">
							Experience
							<!-- <button>
							<i class="fa fa-pencil-square" aria-hidden="true"></i> Add
						</button>
						<button>
						<i class="fa fa-plus-square" aria-hidden="true"></i> Edit
					</button> -->
				</h3>
				<section class="agent-content">
					<textarea name="experience"><?php if($advisor_details){ echo $advisor_details['experience']; } ?></textarea>
				</section>

				<hr>

				<h3 class="licenses-label">
					Licenses, Certifications and Affiliations
					<!--<button>
					<i class="fa fa-pencil-square" aria-hidden="true"></i> Add
				</button>
				<button>
				<i class="fa fa-plus-square" aria-hidden="true"></i> Edit
			</button> -->
		</h3>

		<section class="agent-content">
			<?php
			$licenses = array('Licensed Insurance Advisor', 'Certified Investment Solicitor (CIS)', 'Registered Financial Planner (RFP)', 'Registered Financial Consultant (RFC)', 'Certified Wealth Planner (CWP)', 'Registered Estate Planner (REP)', 'Chartered Wealth Manager (CWM)', 'Accredited Financial Analyst (AFA)', 'Chartered Trust and Estate Planner (CTEP)', 'Certified Securities Representative (CSR)', 'Certified Treasuries Professional (CTP)', 'Life Underwriters Training Council Fellow (LUTCF)', 'Chartered Financial Planner (ChFP)', 'Chartered Financial Analyst (CFA)', 'Certified Public Accountant (CPA)', 'International Accredited Accountant (IAA)', 'Others, pls specify');

			$other_license = "";
			$user_licenses_arr = array();
			if($advisor_details){
				$user_licenses = explode(",", $advisor_details['license']);
				foreach($user_licenses as $user_license){
					if(in_array($user_license, $licenses)){
						$user_licenses_arr[] = $user_license;
					}else{
						if(!in_array("Others", $user_licenses_arr)){
							$user_licenses_arr[] = "Others";
						}

						if(empty($other_license)){
							$other_license = $user_license;
						}else{
							$other_license .= ",".$user_license;
						}
					}
				}
			}
			?>
			<ul class="listbox">
				<?php
				foreach($licenses as $license){
					?>
					<li>
						<input type="checkbox" name="license[]" value="<?php echo $license; ?>" <?php if(($license == 'Others, pls specify' && in_array("Others", $user_licenses_arr)) || in_array($license, $user_licenses_arr)){ echo "checked"; } ?>> <?php echo $license; ?> <?php if($license == 'Others, pls specify'){ ?><input type="text" name="license_other" value="<?php echo $other_license; ?>"><?php } ?></li>

						<?php } ?>
					</ul>
				</section>
				<hr>

				<h3 class="expertise-label">
					Expertise
					<!-- <button>
					<i class="fa fa-pencil-square" aria-hidden="true"></i> Add
				</button>
				<button>
				<i class="fa fa-plus-square" aria-hidden="true"></i> Edit
			</button> -->
		</h3>
		<section class="agent-content">
			<?php
			$expertise = array('Personal Finance Planning Consultation','Corporate / In-house Training','Investment Planning','Insurance Planning','Retirement Planning','Trust and Estate Planning','Education Planning', 'Others, pls specify');

			$other_expertise = "";
			$user_expertise_arr = array();
			if($advisor_details){
				$user_expertise = explode(",", $advisor_details['expertise']);
				foreach($user_expertise as $user_field){
					if(in_array($user_field, $expertise)){
						$user_expertise_arr[] = $user_field;
					}else{
						if(!in_array("Others", $user_expertise_arr)){
							$user_expertise_arr[] = "Others";
						}

						if(empty($other_expertise)){
							$other_expertise = $user_field;
						}else{
							$other_expertise .= ",".$user_field;
						}
					}
				}
			}
			?>
			<ul class="listbox">
				<?php
				foreach($expertise as $field){
					?>
					<li>
						<input type="checkbox" name="expertise[]" value="<?php echo $field; ?>"
						<?php if(
							($field == 'Others, pls specify' && in_array("Others", $user_expertise_arr)) ||
							in_array($field, $user_expertise_arr)
						){ echo "checked"; } ?>>
						<?php echo $field; ?>
						<?php if($field == 'Others, pls specify'){
							?><input type="text" name="expert_other" value="<?php echo $other_expertise; ?>">
							<?php } ?>
						</li>

						<?php } ?>
					</section>
					<hr>

					<h3 class="awards-label">
						Awards and Recognitions
						<!--<button>
						<i class="fa fa-pencil-square" aria-hidden="true"></i> Add
					</button>
					<button>
					<i class="fa fa-plus-square" aria-hidden="true"></i> Edit
				</button>-->
			</h3>
			<section class="agent-content">
				<textarea name="award"><?php if($advisor_details){ echo $advisor_details['award']; } ?></textarea>
			</section>
			<hr>

			<h3 class="gallery-label">
				Photo Gallery
				<!-- <button> -->
				<input type="file" multiple="multiple" name="photo_gallery[]" >
				<!-- </button> -->
				<!-- <button>
				<i class="fa fa-minus-square" aria-hidden="true"></i> Delete
			</button> -->
		</h3>
		<div class="agent-pgallery">
			<div class="carousel" data-flickity='{ "wrapAround": true, "groupCells": true, "autoPlay": true }'>

				<!-- PHOTO GALLERY  -->
				<?php foreach($advisor_details_meta as $meta):
					if($meta['meta_key'] == 'photo_gallery'):
						?>
						<div class="carousel-cell" id="photo_gallery-<?php echo $meta['meta_id']?>">
							<center><a href="javascript:void(0);" onclick="ajaxDeleteMeta(<?php echo $meta['meta_id']?>, 'photo_gallery')"><i class="fa fa-minus-square" aria-hidden="true"></i> Delete</a></center>
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
			<!-- <button>
			<i class="fa fa-pencil-square" aria-hidden="true"></i> Add
		</button>
		<button>
		<i class="fa fa-minus-square" aria-hidden="true"></i> Delete
	</button> -->
</h3>
<div>
	Add new YouTube video <br>
	<sub>NOTE: Embed URL only. (e.g https://www.youtube.com/embed/dQw4w9WgXcQ)</sub> <br><br>
	<input type="url" name="video_gallery" placeholder="YouTube Embed URL">
</div>
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

<h3>Rate per Consultation
	<!--<button>
	<i class="fa fa-pencil-square" aria-hidden="true"></i> Add
</button>
<button>
<i class="fa fa-plus-square" aria-hidden="true"></i> Edit
</button>-->
</h3>
<section class="agent-content">
	<p><sub>Your products are automatically added here.</sub></p>

	<?php
	$args = array(
		'post_type' => 'product',
		'posts_per_page' => 12,
		'author' => $GLOBALS['current_user']->ID
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
				<a href="javascript:void(0);"><i class="fa fa-facebook" aria-hidden="true"></i>
				</a>
				<input type="text" name="social_fb" placeholder="Facebook link" value="<?php echo $advisor_details['social_fb'] ?>">
			</li>
			<li class="gp">
				<a href="javascript:void(0);"><i class="fa fa-google-plus" aria-hidden="true"></i>
				</a>
				<input type="text" name="social_gplus" placeholder="Google+ link" value="<?php echo $advisor_details['social_gplus'] ?>">
			</li>
			<li class="in">
				<a href="javascript:void(0);"><i class="fa fa-linkedin" aria-hidden="true"></i>
				</a>
				<input type="text" name="social_linkedin" placeholder="LinkedIn link" value="<?php echo $advisor_details['social_linkedin'] ?>">
			</li>
			<li class="sk">
				<a href="javascript:void(0);"><i class="fa fa-skype" aria-hidden="true"></i>
				</a>
				<input type="text" name="social_skype" placeholder="Skype link" value="<?php echo $advisor_details['social_skype'] ?>">
			</li>
		</ul>

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
			</section>
			<input type="submit" value="Save Changes" style="margin-top: 20px;">
		</article>


	</section>

</div>
</form>

<?php get_footer(); ?>

<script>
$(document).ready(function(){

	ajaxDeleteMeta = function(meta_id, meta_key){
		$.ajax({
			url:"<?php echo site_url(). "/wp-content/themes/listify/ajax/deleteAdvisorMeta.php"?>",
			type: "POST",
			data: {'meta_id': meta_id},
			success:function(data){
				if(meta_key == 'video_gallery'){
					$("#video_gallery-"+ meta_id).fadeOut(400, function(){
						$("#video_gallery-"+ meta_id).remove()
					});
				}else if(meta_key == 'photo_gallery'){
					$("#photo_gallery-"+ meta_id).fadeOut(400, function(){
						$("#photo_gallery-"+ meta_id).remove()
					});
				}
			} // success end
		}); // ajax end
	} //function body end

});
</script>
