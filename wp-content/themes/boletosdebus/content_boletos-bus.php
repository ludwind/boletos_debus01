<?php
/**
 * The default template for displaying content
 *
 * @package Catch Themes
 * @subpackage Adventurous
 * @since Adventurous 1.0
 */
 
//Getting data from Theme Options Panel and Meta Box 
global $adventurous_options_settings;
$options = $adventurous_options_settings;

//Content Layout
$current_content_layout = $options['content_layout'];

//More Tag
$moretag = $options[ 'more_tag_text' ];
?>


</div><div class="greycontent"><div class="container contentboletos">

<h1><?php the_title(); ?></h1><div class="underlinetittle"></div>
<div class="traveldetail">
	<ul>
		<li><h1>informaci&oacute;n</h1>
			<?php  $image = get_field('logotipo'); if( !empty($image) ): ?>
			<img class="imglogoempresabus" src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
			<?php endif; ?>
			<article><?php the_field('descripcion_corta'); ?></article>
		</li>
		<li><h1>servicios</h1>
			<article><?php the_field('servicios'); ?></article>
		</li>
		<li><h1>contacto</h1>
			<?php $location = get_field('mapa');
				if( !empty($location) ):?>
				<div class="acf-map"><div class="marker" data-lat="<?php echo $location['lat']; ?>" data-lng="<?php echo $location['lng']; ?>"></div></div>
				<?php endif; ?>
			<?php the_field('contacto'); ?>
		</li>
	</ul>
</div>

<a class="botonboleto" href="<?php the_field('enlace'); ?>" target="_blank">comprar mi boleto<span>&gt;</span></a>
	
</div></div>

<div class="container">
	
	
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

<!-- ----------------- Top sitios turisticos -------------------------- -->
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
	
	
	
	
	
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if ( is_sticky() && is_home() && ! is_paged() ) : ?>
        <div class="featured-sticky"><?php _e( 'Featured post', 'adventurous' ); ?></div>
    <?php endif; ?>
    
    <?php if ( function_exists( 'adventurous_content_image' ) ) : adventurous_content_image(); endif; ?>
    
    <div class="entry-container">
    
		<header class="entry-header">

			<?php if ( 'post' == get_post_type() ) : ?>
                <div class="entry-meta">
                    <?php adventurous_header_meta(); ?>
                </div><!-- .entry-meta -->
			<?php endif; ?>
		</header><!-- .entry-header -->

		<?php 
		//Get Excerpt
		$adventurous_excerpt = get_the_excerpt();
		
		if ( is_search() || ( !is_single() && $current_content_layout=='excerpt' && !empty( $adventurous_excerpt ) ) ) : ?>
            <div class="entry-summary">
                <?php the_excerpt(); ?>
            </div><!-- .entry-summary -->     
		<?php else : ?>
            <div class="entry-content">
                <?php the_content( $moretag ); ?>
                <?php wp_link_pages( array( 
					'before'		=> '<div class="page-link"><span class="pages">' . __( 'Pages:', 'adventurous' ) . '</span>',
					'after'			=> '</div>',
					'link_before' 	=> '<span>',
					'link_after'   	=> '</span>',
				) ); 
				?>
            </div><!-- .entry-content -->
        <?php endif; ?>

        <footer class="entry-meta">
        	<?php adventurous_footer_meta(); ?>
			<?php if ( comments_open() && ! post_password_required() ) : ?>
                <span class="sep"> | </span>
                <span class="comments-link">
                    <?php comments_popup_link(__('Leave a reply', 'adventurous'), __('1 Reply', 'adventurous'), __('% Replies', 'adventurous')); ?>
                </span>
            <?php endif; ?>            
            <?php edit_post_link( __( 'Edit', 'adventurous' ), '<span class="sep"> | </span><span class="edit-link">', '</span>' ); ?>
			<?php if ( is_singular() && get_the_author_meta( 'description' ) && is_multi_author() ) : // If a user has filled out their description and this is a multi-author blog, show a bio on their entries. ?>
				<div class="author-info">
					<div class="author-avatar">
						<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'adventurous_author_bio_avatar_size', 68 ) ); ?>
					</div><!-- .author-avatar -->
					<div class="author-description">
						<h2><?php printf( __( 'About %s', 'adventurous' ), get_the_author() ); ?></h2>
						<p><?php the_author_meta( 'description' ); ?></p>
						<div class="author-link">
							<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
								<?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', 'adventurous' ), get_the_author() ); ?>
							</a>
						</div><!-- .author-link	-->
					</div><!-- .author-description -->
				</div><!-- .author-info -->
			<?php endif; ?>            
        </footer><!-- .entry-meta -->
        
  	</div><!-- .entry-container -->
    
</article><!-- #post-<?php the_ID(); ?> -->



<!--------------------------------Google maps ----------------------------->

<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
<script type="text/javascript">
(function($) {

/*
*  render_map
*
*  This function will render a Google Map onto the selected jQuery element
*
*  @type	function
*  @date	8/11/2013
*  @since	4.3.0
*
*  @param	$el (jQuery element)
*  @return	n/a
*/

function render_map( $el ) {

	// var
	var $markers = $el.find('.marker');

	// vars
	var args = {
		zoom		: 16,
		center		: new google.maps.LatLng(0, 0),
		mapTypeId	: google.maps.MapTypeId.ROADMAP
	};

	// create map	        	
	var map = new google.maps.Map( $el[0], args);

	// add a markers reference
	map.markers = [];

	// add markers
	$markers.each(function(){

    	add_marker( $(this), map );

	});

	// center map
	center_map( map );

}

/*
*  add_marker
*
*  This function will add a marker to the selected Google Map
*
*  @type	function
*  @date	8/11/2013
*  @since	4.3.0
*
*  @param	$marker (jQuery element)
*  @param	map (Google Map object)
*  @return	n/a
*/

function add_marker( $marker, map ) {

	// var
	var latlng = new google.maps.LatLng( $marker.attr('data-lat'), $marker.attr('data-lng') );

	// create marker
	var marker = new google.maps.Marker({
		position	: latlng,
		map			: map
	});

	// add to array
	map.markers.push( marker );

	// if marker contains HTML, add it to an infoWindow
	if( $marker.html() )
	{
		// create info window
		var infowindow = new google.maps.InfoWindow({
			content		: $marker.html()
		});

		// show info window when marker is clicked
		google.maps.event.addListener(marker, 'click', function() {

			infowindow.open( map, marker );

		});
	}

}

/*
*  center_map
*
*  This function will center the map, showing all markers attached to this map
*
*  @type	function
*  @date	8/11/2013
*  @since	4.3.0
*
*  @param	map (Google Map object)
*  @return	n/a
*/

function center_map( map ) {

	// vars
	var bounds = new google.maps.LatLngBounds();

	// loop through all markers and create bounds
	$.each( map.markers, function( i, marker ){

		var latlng = new google.maps.LatLng( marker.position.lat(), marker.position.lng() );

		bounds.extend( latlng );

	});

	// only 1 marker?
	if( map.markers.length == 1 )
	{
		// set center of map
	    map.setCenter( bounds.getCenter() );
	    map.setZoom( 16 );
	}
	else
	{
		// fit to bounds
		map.fitBounds( bounds );
	}

}

/*
*  document ready
*
*  This function will render each map when the document is ready (page has loaded)
*
*  @type	function
*  @date	8/11/2013
*  @since	5.0.0
*
*  @param	n/a
*  @return	n/a
*/

$(document).ready(function(){

	$('.acf-map').each(function(){

		render_map( $(this) );

	});

});

})(jQuery);
</script>

<!--------------------------------Google maps ----------------------------->