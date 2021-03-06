<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package Catch Themes
 * @subpackage Adventurous
 * @since Adventurous 1.0
 */
?>
<!DOCTYPE html>
<!--[if IE 6]>
<html id="ie6" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html id="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
    <title><?php wp_title( '|', true, 'right' ); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/js/html5.min.js"></script>
	<![endif]-->
	<link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/favicon.ico" />
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php 
/** 
 * adventurous_before hook
 */
do_action( 'adventurous_before' ); ?>

<div id="page" class="hfeed site">
	<?php 
    /** 
     * adventurous_before_header hook
	 *
	 * HOOKED_FUNCTION_NAME PRIORITY
	 *
	 * adventurous_header_top 10
     */
    do_action( 'adventurous_before_header' ); ?>
    
	<header id="masthead">


    	<div id="hgroup-wrap" class="container">
        
       		<?php 
			/** 
			 * adventurous_hgroup_wrap hook
			 *
			 * HOOKED_FUNCTION_NAME PRIORITY
			 *
			 * adventurous_header_image 10
			 * adventurous_header_right 20
			 */
			do_action( 'adventurous_hgroup_wrap' ); ?>
            
        </div><!-- #hgroup-wrap -->
        
        <?php 
		/** 
		 * adventurous_after_hgroup_wrap hook
		 *
		 * HOOKED_FUNCTION_NAME PRIORITY
		 *
		 * adventurous_homepage_featured_position 10
		 */
		do_action( 'adventurous_after_hgroup_wrap' ); ?>
        
	</header><!-- #masthead .site-header -->

	<?php 
    /** 
     * adventurous_after_header hook
     */
    do_action( 'adventurous_after_header' ); ?>
    
<div class="translate"><?php echo do_shortcode('[google-translator]'); ?></div>

    
<?php if(is_page(39)): ?>
<div class="boletosdebussearch"><ul>
<li><h1>01.</h1><h2>&iquest;Hacia donde te dirijes?</h2></li>
<li><h1>02.</h1><h2>&iquest;Desde d&oacute;nde viajas?</h2></li>
<li><h1>03.</h1><h2>&iquest;Cu&aacute;ndo?</h2></li>
<li><h1>04.</h1><h2>&iquest;Quienes viajar&aacute;n?</h2></li>
</ul>

<?php echo do_shortcode( '[search-form id="1"]' ) ?>


<!--
<form  method="post" action="<?php //bloginfo('url');?>/listing-search-results/"> 
<?php  /**$taxonomies = get_object_taxonomies('boletos-bus');
    foreach($taxonomies as $tax){
        echo buildSelect($tax);
    }**/
?>
<input type="submit"/>	
</form>
	
<form role="search" method="get" id="searchform" class="searchform" action="<?php esc_url( home_url( '/' ) ); ?>">
<div>
	<label class="screen-reader-text" for="s"><?php _x( 'Search for:', 'label' ); ?></label>
	<input type="text" value="<?php echo get_search_query(); ?>" name="s" id="s" />
	<input type="submit" id="searchsubmit" value="<?php esc_attr_x( 'Search', 'submit button' ); ?>" />
</div>
</form>-->
	
<?php /**echo do_shortcode( '[acps id="33"]' ) */?>
</div>
<?php endif; ?> 
    
    <div id="main-wrapper">
		<?php 
        /** 
         * adventurous_before_main hook
         *
         * HOOKED_FUNCTION_NAME PRIORITY
         *
	 	 * adventurous_featured_overall_image value after header 5
		 * adventurous_secondary_menu 10
		 * content_sidebar_check 20
         * adventurous_slider_display 40
         * adventurous_homepage_headline value after slider 60
         * adventurous_homepage_featured_display 80
         */
        do_action( 'adventurous_before_main' ); ?>

		<?php 
        /** 
         * adventurous_main hook
         *
         * HOOKED_FUNCTION_NAME PRIORITY
         *
         * adventurous_main_wrapper 10
         */
        do_action( 'adventurous_main' ); ?> 
                
			<?php 
            /** 
             * adventurous_content_sidebar hook
             *
             * HOOKED_FUNCTION_NAME PRIORITY
             *
             * adventurous_content_sidebar_wrapper 10
			 * adventurous_breadcrumb_display 20
             */
            do_action( 'adventurous_content_sidebar' ); ?>
	    
    
	    
	    

	    