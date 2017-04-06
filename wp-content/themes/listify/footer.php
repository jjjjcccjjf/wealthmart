<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Listify
 */
?>

	</div><!-- #content -->

</div><!-- #page -->

<div class="footer-wrapper">

	<?php if ( ! listify_is_job_manager_archive() ) : ?>

		<?php get_template_part( 'content', 'aso' ); ?>

		<?php if ( is_active_sidebar( 'widget-area-footer-1' ) || is_active_sidebar( 'widget-area-footer-2' ) || is_active_sidebar( 'widget-area-footer-3' ) ) : ?>

			<footer class="site-footer-widgets">
				<div class="container">
					<div class="row">

						<div class="footer-widget-column col-xs-12 col-sm-12 col-lg-5">
							<?php dynamic_sidebar( 'widget-area-footer-1' ); ?>
						</div>

						<div class="footer-widget-column col-xs-12 col-sm-6 col-lg-3 col-lg-offset-1">
							<?php dynamic_sidebar( 'widget-area-footer-2' ); ?>
						</div>

						<div class="footer-widget-column col-xs-12 col-sm-6 col-lg-3">
							<?php dynamic_sidebar( 'widget-area-footer-3' ); ?>
						</div>

					</div>
				</div>
			</footer>

		<?php endif; ?>

	<?php endif; ?>

	<footer id="colophon" class="site-footer">
		<div class="container">

			<div class="site-info">
				<?php echo listify_theme_mod( 'copyright-text', sprintf( __( 'Copyright %s &copy; %s. All Rights Reserved', 'listify' ), get_bloginfo( 'name' ), date( 'Y' ) ) ); ?>
			</div><!-- .site-info -->

			<div class="site-social">
				<?php wp_nav_menu( array(
					'theme_location' => 'social',
					'menu_class' => 'nav-menu-social',
					'fallback_cb' => '',
					'depth' => 1
				) ); ?>
			</div>

		</div>
	</footer><!-- #colophon -->

</div>

<div id="ajax-response"></div>

	<script src="https://use.fontawesome.com/108c9331e1.js"></script>
    <script src="<?php echo theme_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo theme_url; ?>js/flickity.pkgd.min.js"></script>
    
    <script src="j<?php echo theme_url; ?>js/jquery.magnific-popup.js"></script>
    
    <script>
      jQuery(document).ready(function($) {
        
        $('#open-popup').magnificPopup({
              type:'image',
              closeBtnInside: true
            });
      });
    </script>

<?php wp_footer(); ?>

</body>
</html>
