<?php

/*
Template Name: BDB-Inicio
*/

/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Catch Themes
 * @subpackage Adventurous
 * @since Adventurous 1.0
 */
get_header(); ?>



<!-- ----------------- ofertas especiales home -------------------------- -->
<div class="ofertasespeciales">
	<h1>Ofertas especiales</h1><div class="linea-horizontal"></div>
<ul><?php
$args = array( 'posts_per_page' => 4, 'offset'=> 0, 'category' => 10 );
$myposts = get_posts( $args );
foreach ( $myposts as $post ) : setup_postdata( $post ); ?>
<li><a href="<?php the_permalink(); ?>">
<?php the_post_thumbnail( 'bdb-ofertasimg', array( 'class' => 'featured-img' ) ); ?>
<article><h2><?php the_title(); ?></h2>
<?php global $more; $more = 0; ?>
<?php the_content(''); ?>
</article>
<div class="viajarahora">Viajar ahora<span>></span></div>
</a></li>
<?php endforeach; wp_reset_postdata();?>
</ul>
</div>

<div class="espaciadorhome"></div>
<!-- ----------------- fin ofertas especiales home -------------------- -->

<!-- ----------------- Los mejores hoteles -------------------------- -->
<div class="mejoreshoteles">
	<h1>Los mejores hoteles</h1><div class="linea-horizontal"></div>
<ul><?php
$args = array( 'posts_per_page' => 3, 'offset'=> 0, 'category' => 11 );
$myposts = get_posts( $args );
foreach ( $myposts as $post ) : setup_postdata( $post ); ?>
<li><a href="<?php the_permalink(); ?>">
<?php the_post_thumbnail( 'bdb-mejoreshotelesimg', array( 'class' => 'featured-img' ) ); ?>
<article><h2><?php the_title(); ?></h2>
<?php global $more; $more = 0; ?>
<?php the_content(''); ?>
</article>
</a></li>
<?php endforeach; wp_reset_postdata();?>
</ul>
<a href="#" class="vertodos">ver todos los hoteles</a>
</div>
<!-- ----------------- Fin Los mejores hoteles ---------------------- -->

<!-- ----------------- Los mejores hoteles -------------------------- -->
<div class="top-sitiosturisticos">
	<h1>Los mejores hoteles</h1><div class="linea-horizontal"></div>
<ul><?php $args = array( 'posts_per_page' => 7, 'offset'=> 0, 'category' => 12 );
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
<a href="#" class="vertodos2">ver todos los sitios turisticos</a>
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



		<!--<div id="primary" class="content-area">
			<div id="content" class="site-content" role="main">

		
			</div><!-- #content .site-content
		</div> #primary .content-area -->

<?php get_footer(); ?>