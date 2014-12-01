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
<div class="hoteldetail"><ul>
		
<li><h1>fotograf&iacute;as</h1>
<article>
<?php the_field('galeria_de_fotos'); ?>
</article>
</li>

<li><h1>ub&iacute;canos</h1>
<?php $location = get_field('mapa-hoteles');
if( !empty($location) ):?>
<div class="acf-map"><div class="marker" data-lat="<?php echo $location['lat']; ?>" data-lng="<?php echo $location['lng']; ?>"></div></div>
<?php endif; ?>
<?php the_field('contact-info'); ?>
</li>

</ul></div>

<div class="descripcionhotel">
<h1>informaci&oacute;n</h1>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php if ( is_sticky() && is_home() && ! is_paged() ) : ?>
<div class="featured-sticky"><?php _e( 'Featured post', 'adventurous' ); ?></div><?php endif; ?>
<?php /*if ( function_exists( 'adventurous_content_image' ) ) : adventurous_content_image(); endif; */?>  
<div class="entry-container"><?php $adventurous_excerpt = get_the_excerpt();		
if ( is_search() || ( !is_single() && $current_content_layout=='excerpt' && !empty( $adventurous_excerpt ) ) ) : ?>
<div class="entry-summary"><?php the_excerpt(); ?></div><!-- .entry-summary -->     
<?php else : ?><div class="entry-content"><?php the_content( $moretag ); ?></div><!-- .entry-content -->
<?php endif; ?>				
</div>	</div>


<a class="botonboleto" href="<?php the_field('enlace_para_reservaciones'); ?>" target="_blank">hacer una reservaci&oacute;n<span>&gt;</span></a>
	
</div></div>

<div class="container">
	
	
<!-- ----------------- Los mejores hoteles -------------------------- -->
<div class="mejoreshoteles">
	<h1>Otros hoteles en Nicaragua</h1><div class="linea-horizontal"></div>
<ul><?php
$args = array( 'posts_per_page' => 3, 'offset'=> 0, 'category' => 28 );
$myposts = get_posts( $args );
foreach ( $myposts as $post ) : setup_postdata( $post ); ?>
<li><a href="<?php the_permalink(); ?>">
<?php the_post_thumbnail( 'bdb-mejoreshotelesimg', array( 'class' => 'featured-img' ) ); ?>
<article><h2><?php the_title(); ?></h2>
<h3>desde $<?php the_field('precio_desde'); ?> / noche</h3>
<?php global $more; $more = 0; ?>
<?php the_content(''); ?>
</article>
</a></li>
<?php endforeach; wp_reset_postdata();?>
</ul>
<a href="?cat=34" class="vertodos">ver todos los hoteles</a>
</div>
<!-- ----------------- Fin Los mejores hoteles ---------------------- -->

<!-- ----------------- Top sitios turisticos -------------------------- -->
<div class="top-sitiosturisticos">
	<h1>Turismo en El Salvador</h1><div class="linea-horizontal"></div>
<ul><?php $args = array( 'posts_per_page' => 6, 'offset'=> 0, 'category' => 34 );
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


        <footer class="entry-meta">
        	
			      
            
			
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