<?php

/*
Template Name: Buses
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



</div>

<div class="heroimageboletos"><img src="<?php echo get_bloginfo('template_directory');?>/img/hoteles/head.jpg"/></div>

<div class="hotelesearch">
<h1>&#191;en donde nos hospedaremos hoy?</h1>

<FORM name="boletosearchf" class="boletosearch"> 
<SELECT name="boletosearchs"> 
	<option selected>Selecciona un pa&#237;s</option> 
	<option value="?cat=16">Guatemala</option>
	<option value="?cat=20">El Salvador</option>
	<option value="?cat=18">Honduras</option>
	<option value="?cat=28">Nicaragua</option>
	<option value="?cat=29">Costa Rica</option>
	<option value="?cat=30">Panam&aacute;</option>
</SELECT>
<section class="buscar-searchoteles">
<INPUT type="button" name="go" value="ver hoteles" 
       onClick="window.location=document.boletosearchf.boletosearchs.options[document.boletosearchf.boletosearchs.selectedIndex].value">
<span>&gt;</span></section>
</FORM> 

</div>
<div class="container" id="content-sidebar">	    
    



<div class="todosloshoteles">
	<h1>todos los hoteles</h1><div class="linea-horizontal"></div>
<ul>
<?php $cat = array(20,16,18,22,); $showposts = 25; $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;        $args=array(
        'category__in' => $cat, 'showposts' => $showposts, 'paged' => $paged, 'orderby' => 'post_date',
        'order' => 'DESC', 'post_status' => 'publish',  );  $the_query = new WP_Query ( $args ); //the query
        $i = 0;while ($the_query->have_posts() ) : $the_query->the_post(); //start the loop ?>
<?php if($i==0){ //Sets the output for the top post ?>  

<li><a href="<?php the_permalink(); ?>">
<?php the_post_thumbnail( 'bdb-ofertasimg', array( 'class' => 'featured-img' ) ); ?>
<article><h2><?php the_title(); ?></h2>	

<?php foreach((get_the_category()) as $category) { echo '<a href="'.get_category_link( $category->term_id ).
 '" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $category->name ) ) . '">'.$category->cat_name.', </a>';} ?>	
	
<?php $content = get_the_content(); $trimmed_content = wp_trim_words( $content, 15 ); ?>
<p><?php echo $trimmed_content; ?></p>
<h3>desde $<?php the_field('precio_desde'); ?> / noche</h3>	
</article>
<div class="viajarahora">Ver m&aacute;s<span>></span></div>
</a></li>

<?php  $i++; } else { ?>

<li><a href="<?php the_permalink(); ?>">
<?php the_post_thumbnail( 'bdb-ofertasimg', array( 'class' => 'featured-img' ) ); ?>
<article><h2><?php the_title(); ?></h2>

<?php foreach((get_the_category()) as $category) { echo '<a href="'.get_category_link( $category->term_id ).
 '" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $category->name ) ) . '">'.$category->cat_name.', </a>';} ?>
	
<?php $content = get_the_content(); $trimmed_content = wp_trim_words( $content, 15 ); ?>
<p><?php echo $trimmed_content; ?></p>
<h3>desde $<?php the_field('precio_desde'); ?> / noche</h3>	
</article>
<div class="viajarahora">Ver m&aacute;s<span>></span></div>
</a></li>	   
	   
<?php } endwhile; //end of the loop ?><?php wp_reset_postdata(); // reset the query ?>
</ul></div>
<div class="espaciadorhome"></div>


<!-- ----------------- autos para tu viaje -------------------------- -->
<div class="mejoreshoteles">
	<h1>Autos para tu viaje</h1><div class="linea-horizontal"></div>
<ul><?php
$args = array( 'posts_per_page' => 3, 'offset'=> 0, 'category' => 24 );
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
<a href="?cat=11" class="vertodos">ver todos los autos</a>
</div>
<!-- ----------------- Fin Los mejores hoteles ---------------------- -->

<!-- ----------------- Top sitios turisticos -------------------------- -->
<div class="top-sitiosturisticos">
	<h1>Top sitio tur&iacute;sticos</h1><div class="linea-horizontal"></div>
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
<a href="?cat=12" class="vertodos2">ver todos los sitios turisticos</a>
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