<?php
/**
 * The template for displaying Archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Catch Themes
 * @subpackage Adventurous
 * @since Adventurous 1.0
 */

get_header(); ?>

		<div class="container" id="content-sidebar">	    


			<?php if ( have_posts() ) : ?>

<div class="todosloshoteles">
	<h1><?php
							if ( is_category() ) {
								printf( __( '%s', 'adventurous' ), '<span>' . single_cat_title( '', false ) . '</span>' );

							} elseif ( is_tag() ) {
								printf( __( 'Tag Archives: %s', 'adventurous' ), '<span>' . single_tag_title( '', false ) . '</span>' );

							} elseif ( is_author() ) {
								/* Queue the first post, that way we know
								 * what author we're dealing with (if that is the case).
								*/
								the_post();
								printf( __( 'Author Archives: %s', 'adventurous' ), '<span class="vcard"><a class="url fn n" href="' . get_author_posts_url( get_the_author_meta( "ID" ) ) . '" title="' . esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a></span>' );
								/* Since we called the_post() above, we need to
								 * rewind the loop back to the beginning that way
								 * we can run the loop properly, in full.
								 */
								rewind_posts();

							} elseif ( is_day() ) {
								printf( __( 'Daily Archives: %s', 'adventurous' ), '<span>' . get_the_date() . '</span>' );

							} elseif ( is_month() ) {
								printf( __( 'Monthly Archives: %s', 'adventurous' ), '<span>' . get_the_date( 'F Y' ) . '</span>' );

							} elseif ( is_year() ) {
								printf( __( 'Yearly Archives: %s', 'adventurous' ), '<span>' . get_the_date( 'Y' ) . '</span>' );

							} else {
								_e( 'Archives', 'adventurous' );

							}
						?>
					</h1>
					<?php
						if ( is_category() ) {
							// show an optional category description
							$category_description = category_description();
							if ( ! empty( $category_description ) )
								echo apply_filters( 'category_archive_meta', '<div class="taxonomy-description">' . $category_description . '</div>' );

						} elseif ( is_tag() ) {
							// show an optional tag description
							$tag_description = tag_description();
							if ( ! empty( $tag_description ) )
								echo apply_filters( 'tag_archive_meta', '<div class="taxonomy-description">' . $tag_description . '</div>' );
						}
					?>
			<!-- .page-header -->

				<?php adventurous_content_nav( 'nav-above' ); ?>

				
</h1><div class="linea-horizontal"></div>
<ul><?php /* Start the Loop */ ?><?php while ( have_posts() ) : the_post(); ?>
<?php get_template_part( 'content-hoteles-cat', get_post_format() ); ?>
				<?php endwhile; ?>

				<?php adventurous_content_nav( 'nav-below' ); ?>

			<?php else : ?>

				<?php get_template_part( 'no-results', 'archive' ); ?>

			<?php endif; ?>
			
			</div><!-- #content .site-content -->
		</section><!-- #primary .content-area -->


<div class="espaciadorhome"></div>

<!-- ----------------- autos para tu viaje -------------------------- -->
<div class="mejoreshoteles">
	<h1>Autos en Guatemala</h1><div class="linea-horizontal"></div>
<ul><?php
$args = array( 'posts_per_page' => 3, 'offset'=> 0, 'category' => 26 );
$myposts = get_posts( $args );
foreach ( $myposts as $post ) : setup_postdata( $post ); ?>
<li><a href="<?php the_permalink(); ?>">
<?php the_post_thumbnail( 'bdb-mejoreshotelesimg', array( 'class' => 'featured-img' ) ); ?>
<article><h2><?php the_title(); ?></h2>
<h3>desde $<?php the_field('precio_desde'); ?> / d&iacute;a</h3>	
<?php global $more; $more = 0; ?>
<?php the_content(''); ?>
</article>
</a></li>
<?php endforeach; wp_reset_postdata();?>
</ul>
<a href="?cat=26" class="vertodos">ver todos los autos</a>
</div>
<!-- ----------------- Fin Los mejores hoteles ---------------------- -->

<!-- ----------------- Top sitios turisticos -------------------------- -->
<div class="top-sitiosturisticos">
	<h1>Top sitio tur&iacute;sticos</h1><div class="linea-horizontal"></div>
<ul><?php $args = array( 'posts_per_page' => 7, 'offset'=> 0, 'category' => 17 );
$myposts = get_posts( $args );
foreach ( $myposts as $post ) : setup_postdata( $post ); ?>
<li><a href="<?php the_permalink(); ?>">
<article><h2>> <?php the_title(); ?></h2>
<?php global $more; $more = 0; ?>
<?php the_content(''); ?>
</article>
</a></li>
<div class="linea-horizontal lineatopsitios"></div>
<?php endforeach; wp_reset_postdata();?>
</ul>
<a href="?cat=17" class="vertodos2">ver todos los sitios turisticos</a>
</div>
<!-- ----------------- Fin Los mejores hoteles ---------------------- -->
<div class="espaciadorhome"></div>

<!-- ----------------- Suscribete -------------------------- -->

<div class="suscribete"><ul>
	<li><h1>Suscr&iacute;bete para obtener ofertas especiales</h1><Br>
	<h2>Obten cupones especiales, ofertas y promociones con s&oacute;lo escribir tu correo electr&oacute;nico</h2></li>
	<li><input class="suscribeteimput"></input></li>
	<li><a class="suscribetesend">enviar</a></li>
</ul></div>
<!-- ----------------- Suscribete -------------------------- -->


<?php get_footer(); ?>