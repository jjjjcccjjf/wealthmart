<?php
/*
Template Name: Advisor Account
*/

global $style;


if($_POST){
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
	$detail_data['position'] = $_POST['position'];
	$detail_data['address'] = $_POST['address'];
	$detail_data['contact'] = $_POST['contact'];
	$detail_data['experience'] = $_POST['experience'];

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
	}else{
		$wpdb->update('advisor_details', $detail_data, array('ID' => $GLOBALS['current_user']->ID));
	}


}

$blog_style = get_theme_mod( 'content-blog-style', 'default' );
$style = 'grid-standard' == $blog_style ? 'standard' : 'cover';

/**
* /classes folder are loaded inside the header
* @var [type]
*/
get_header();

if($GLOBALS['current_user']->roles[0] == 'pending_vendor'){
	$advisor_details = $wpdb->get_row('SELECT * FROM advisor_details WHERE ID = '.$GLOBALS['current_user']->ID, ARRAY_A);
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
	<?php die();
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
		<section class="listing-banner" style="background: url(<?php echo theme_url; ?>images/bannerimg.jpg) no-repeat center center; background-size: cover;">
			<aside>
				<h1><?php echo $name ?>
					&nbsp;
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
					<?php if(isset($advisor_details['listing_photo'])){
						?><img src="<?php echo $advisor_details['listing_photo']; ?>" >
						<?php } ?>
						<input type="file" name="listing_photo">
					</div>
					<div class="agent-details">
						<ul style="margin-top:80px;">
							<li><i class="fa fa-user" aria-hidden="true"></i>
								<input type="text" name="position" placeholder="Position" <?php if($advisor_details){ ?>value="<?php echo $advisor_details['position']; ?>"<?php } ?>>
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
			<button>
				<i class="fa fa-pencil-square" aria-hidden="true"></i> Add
			</button>
			<button>
				<i class="fa fa-minus-square" aria-hidden="true"></i> Delete
			</button>
		</h3>
		<div class="agent-pgallery">
			<div class="carousel" data-flickity='{ "wrapAround": true, "groupCells": true, "autoPlay": true }'>
				<div class="carousel-cell"><img src="images/thumb1.jpg"></div>
				<div class="carousel-cell"><img src="images/thumb2.jpg"></div>
				<div class="carousel-cell"><img src="images/thumb1.jpg"></div>
				<div class="carousel-cell"><img src="images/thumb2.jpg"></div>
				<div class="carousel-cell"><img src="images/thumb1.jpg"></div>
				<div class="carousel-cell"><img src="images/thumb2.jpg"></div>
				<div class="carousel-cell"><img src="images/thumb1.jpg"></div>
				<div class="carousel-cell"><img src="images/thumb2.jpg"></div>
			</div>
		</div>

		<hr>


		<h3 class="video-label">
			Video Gallery
			<button>
				<i class="fa fa-pencil-square" aria-hidden="true"></i> Add
			</button>
			<button>
				<i class="fa fa-minus-square" aria-hidden="true"></i> Delete
			</button>
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

		<h3>Rates per Consultation
			<!--<button>
			<i class="fa fa-pencil-square" aria-hidden="true"></i> Add
		</button>
		<button>
		<i class="fa fa-plus-square" aria-hidden="true"></i> Edit
	</button>-->
</h3>
<section class="agent-content">
	<textarea name="rates"><?php if($advisor_details){ echo $advisor_details['rates']; } ?></textarea>
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

	<input type="submit" value="Save Changes">
</article>


</section>

</div>
</form>

<?php get_footer(); ?>
