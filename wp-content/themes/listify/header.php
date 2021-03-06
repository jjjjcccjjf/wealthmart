<?php
/**
* The Header for our theme.
*
* Displays all of the <head> section and everything up till <div id="content">
*
* @package Listify
*/

/**
* load our custom classes. Order is important!
* Advisor inherits from User
* Customer inherits from User
* @author: @jjjjcccjjf
*/
include 'classes/User.php';
include 'classes/Advisor.php';
include 'classes/Customer.php';

$GLOBALS['current_user'] = wp_get_current_user();

define("theme_url", get_template_directory_uri()."/");
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="profile" href="http://gmpg.org/xfn/11">
  <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

  <link rel="stylesheet" href="<?php echo theme_url; ?>css/styles-optimind.css">
  <link rel="stylesheet" href="<?php echo theme_url; ?>css/responsive-optimind.css">
  <link rel="stylesheet" href="<?php echo theme_url; ?>css/flickity.css">
  <link rel="stylesheet" href="<?php echo theme_url; ?>css/magnific-popup.css">
  <script src="<?php echo theme_url; ?>js/tinymce.min.js"></script>
  <script>tinymce.init({ selector:'textarea' });</script>

  <?php if($GLOBALS['current_user']->ID > 0){
    $is_logged_in = true;
  }else{
    $is_logged_in = false;
  } ?>
  
  <?php if(!$is_logged_in){?>
    <style>
    .fn-recent-purchases{
      display: none !important;
    }
    </style>
    <?php } ?>

    <?php wp_head(); ?>
  </head>

  <body <?php body_class(); ?>>

    <div id="page" class="hfeed site">

      <header id="masthead" class="site-header<?php if ( is_front_page() ) :?> site-header--<?php echo get_theme_mod( 'home-header-style', 'default' ); ?><?php endif; ?>">
        <div class="primary-header">
          <div class="container">
            <div class="primary-header-inner">
              <div class="site-branding">
                <?php echo listify_partial_site_branding(); ?>
              </div>

              <div class="primary nav-menu">
                <?php echo listify_partial_primary_nav_menu(); ?>
              </div>
            </div>

            <?php if ( get_theme_mod( 'nav-search', true ) ) : ?>
              <div id="search-header" class="search-overlay">
                <div class="container">
                  <?php locate_template( array( 'searchform-header.php', 'searchform.php' ), true, false ); ?>
                  <a href="#search-header" data-toggle="#search-header" class="ion-close search-overlay-toggle"></a>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <nav id="site-navigation" class="main-navigation<?php if ( is_front_page() ) : ?> main-navigation--<?php echo get_theme_mod( 'home-header-style', 'default' ); ?><?php endif; ?>">
          <div class="container">
            <a href="#" class="navigation-bar-toggle">
              <i class="ion-navicon-round"></i>
              <span class="mobile-nav-menu-label"><?php echo listify_get_theme_menu_name( 'primary' ); ?></span>
            </a>

            <div class="navigation-bar-wrapper">
              <?php
              wp_nav_menu( array(
                'theme_location' => 'primary',
                'container_class' => 'primary nav-menu',
                'menu_class' => 'primary nav-menu'
              ) );

              if ( listify_theme_mod( 'nav-secondary', true ) ) {
                wp_nav_menu( array(
                  'theme_location' => 'secondary',
                  'container_class' => 'secondary nav-menu',
                  'menu_class' => 'secondary nav-menu'
                ) );
              }
              ?>
            </div>

            <?php if ( get_theme_mod( 'nav-search', true ) ) : ?>
              <a href="#search-navigation" data-toggle="#search-navigation" class="ion-search search-overlay-toggle"></a>

              <div id="search-navigation" class="search-overlay">
                <?php locate_template( array( 'searchform-header.php', 'searchform.php' ), true, false ); ?>

                <a href="#search-navigation" data-toggle="#search-navigation" class="ion-close search-overlay-toggle"></a>
              </div>
            <?php endif; ?>
          </div>
        </nav><!-- #site-navigation -->
      </header><!-- #masthead -->

      <?php do_action( 'listify_content_before' ); ?>

      <div id="content" class="site-content">
