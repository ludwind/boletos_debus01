<?php
/**
 * The Template for displaying all single posts.
 *
 * @package Catch Themes
 * @subpackage Adventurous
 * @since Adventurous 1.0
 WP Post Template: Destino: Guatemala 
 */

get_header(); ?>


			<?php while ( have_posts() ) : the_post(); ?>

				<?php adventurous_content_nav( 'nav-above' ); ?>

				<?php
                    /* Include the Post-Format-specific template for the content.
                     * If you want to overload this in a child theme then include a file
                     * called content-___.php (where ___ is the Post Format name) and that will be used instead.
                     */
                    //get_template_part( 'content', get_post_format() );
					$postformat = ( get_post_format() ? get_post_format() : 'single' );
					get_template_part( 'content_bb-guatemala', $postformat );
                ?>



			<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>