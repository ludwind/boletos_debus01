function sf_adjust_elements_waitimg(){
	jQuery( '.sf-result' ).find( 'img' ).load( function(){
		sf_adjust_elements();
	});
}

function sf_adjust_elements(){
	var resultlist = jQuery( '.sf-result > li' );
	var i = 1;
	var h = 0;
	var elements = [];
	resultlist.each( function(){
		if( h < jQuery( this ).outerHeight() )
			h = jQuery( this ).outerHeight();
		if( i <= sf_columns )
			elements.push( this );
		if( i == sf_columns ){
			jQuery( elements ).each( function(){
				jQuery( this ).css({height:h+'px'});
			});
			elements = [];
			h = 0;
			i = 0;
		} else {
		
		}
		i++;
	});
}

function collect_data( wrapper ){

		var data = {};
		wrapper.find('select').each( function(){
			if( jQuery( this ).attr( 'name' ) != 'orderby' || jQuery( this ).val() != null ){				
				if( jQuery( this ).val() != '' ){
					data[ jQuery( this ).attr( 'name' ) ] = jQuery( this ).val() ;
				}
			}
		});
		
		wrapper.find('input').each( function(){
			if( typeof( jQuery( this ).attr( 'name' ) ) != 'undefined' && ( typeof jQuery( this ).attr( 'disabled' ) == 'undefined' || jQuery( this ).attr( 'disabled' ) == false ) ){
				if( jQuery( this ).hasClass( 'sf-date' ) || jQuery( this ).attr( 'type' ) == 'hidden' || jQuery( this ).attr( 'name' ).substr( jQuery( this ).attr( 'name' ).length - 2, 2 ) != '[]' ){
					if( jQuery( this ).val() != '' ){
						if( jQuery( this ).attr( 'type' ) != 'radio' || jQuery( this ).prop( 'checked' ) ){
							if( jQuery( this ).attr( 'name' ).substr( jQuery( this ).attr( 'name' ).length - 2, 2 ) != '[]' ){
								data[ jQuery( this ).attr( 'name' ) ] = jQuery( this ).val() ;
							} else {
								var data_name = jQuery( this ).attr( 'name' ).substr( 0, jQuery( this ).attr( 'name' ).length - 2 )
								if( typeof( data[ data_name ] ) == 'undefined' )
									data[ data_name ] = [];
								data[ data_name ].push( jQuery( this ).val() );
							}
						}
					}
				} else{
					var n = jQuery( this ).attr( 'name' ).substr( 0, jQuery( this ).attr( 'name' ).length - 2 );
				
					if( jQuery( this ).prop( 'checked' ) ){
						if( typeof data[n] == 'undefined' )
							data[n] = [];					
						data[n].push( jQuery( this ).val() );
					}
				}
			}
		});
		return data;
	}

function get_filter_results( start, $form ){

		var wrapper = jQuery( '.sf-wrapper' );
		var data = {
				action	:	'sf-search',
				data	:	collect_data( wrapper )
		};
		
		
		if( typeof start == 'undefined' ){
			location.href = '#sf-' + JSON.stringify( data.data );
		} else {
			if( typeof $form != 'undefined' ){
				var url = $form.attr( 'action' );
				url += '#sf-' + JSON.stringify( collect_data( $form ) );
				location.href = url;
				return;
			}
		}
		wrapper.css({opacity:.1});
		search_data = data.data;
		jQuery.post(
					sf_ajax_root,
					data,
					function( response ){
						response = JSON.parse( response );
						if( JSON.stringify( search_data ) != JSON.stringify( response.post ) )
							return;
						wrapper.css({opacity:1});
						
						var txt = '';
						if( response.result.length > 0 ){
							for( var i = 0; i < response.result.length; i++ ){
								txt += response.result[i];
							}
						} else {
							txt = '<li class="no-result">Keine Ergebnisse gefunden</li>';
						}
						jQuery( wrapper ).find( '.sf-result' ).html( txt );
						if( response.result.length > 0 ){
							sf_adjust_elements_waitimg();
						}
						
						var txt = '';
						if( response.nav.length > 0 ){
							for( var i = 0; i < response.nav.length; i++ ){
								txt += response.nav[i];
							}
						}
						jQuery( wrapper ).find('ul.sf-nav').html( txt );
						if( typeof( response.head ) != 'undefined' )
							jQuery( wrapper ).find('.sf-result-head').html( response.head );
					}
					);
	}

jQuery.fn.sfrange = function() {
	var element = this;
	var source = element.attr( 'data-source' );
	var min = parseInt( element.attr( 'data-start' ) );
	var max = parseInt( element.attr( 'data-end' ) );
	var title = element.attr( 'data-title' );
	var unit = element.attr( 'data-unit' );
	var sf_step = parseFloat( element.attr( 'data-step' ) );
	var unit_front = element.attr( 'data-unitfront' );
	
	if( typeof element.attr( 'data-posmin' ) != 'undefined' )
		var curr_min = parseInt( element.attr( 'data-posmin' ) );
	else
		var curr_min = min;
	
	if( typeof element.attr( 'data-posmax' ) != 'undefined' )
		var curr_max = parseInt( element.attr( 'data-posmax' ) );
	else
		var curr_max = max;
	
	var txt = '<input type="hidden" class="sf-source-max" name="'+source+'|max" value="' + curr_max + '" /><input class="sf-source-min" type="hidden" name="'+source+'|min" value="' + curr_min + '" /><div class="sf-range"></div><p>' + title + ' <span class="sf-write">';
	if( unit_front == 1 )
		txt += unit + '' + curr_min + ' - ' + unit + '' + curr_max;
	else
		txt += curr_min + '' + unit + ' - ' + curr_max + '' + unit;
		
	txt += '</span></p>';
	jQuery( txt ).appendTo( element );
	element.find( '.sf-range' ).slider({
		range: true,
		step: sf_step,
		min: min,
		max: max,
		values: [ curr_min, curr_max ],
		slide: function( event, ui ) {
			if( unit_front == 1 )
				element.find( '.sf-write' ).text( unit + '' + ui.values[ 0 ] + " - " + unit + '' +ui.values[ 1 ] );
			else
				element.find( '.sf-write' ).text( ui.values[ 0 ] + '' + unit + " - " + ui.values[ 1 ] + '' + unit );
		},
		stop: function( event, ui ){
			element.find('input.sf-source-min' ).val( ui.values[0] );
			element.find('input.sf-source-max' ).val( ui.values[1] );
			if( !element.hasClass( 'sf-widget-range-wrapper' ) ){
				if( jQuery( '.sf-wrapper' ).find( '.sf-button-btnsearch' ).length == 0 ){
					jQuery( '.sf-wrapper' ).find( 'input[name="page"]' ).remove();
					get_filter_results();
				}
			} else {
				var $form = element.parent().parent().parent();
				if( $form.attr( 'data-autoload' ) == '1' && $form.attr( 'data-withfiler' ) != '1' ){
					$form.submit();
				} else {
					if( $form.attr( 'data-autoload' ) == '1' && $form.attr( 'data-withfiler' ) == '1' )
						get_filter_results( true, $form );
				}
			}
		}
	});
	return this;
}

var search_rect = {start:null,end:null,draw:0};
var mode = 0;

jQuery.fn.sfmap = function( classname, index ) {
	var element = this;	
	var mapOptions = {
          zoom: parseInt( element.parent().attr( 'data-zoom' ) ),
		  center: new google.maps.LatLng( parseFloat( element.parent().attr( 'data-lat' ) ), parseFloat( element.parent().attr( 'data-lon' ) ) ),
          mapTypeId: google.maps.MapTypeId[ element.parent().attr( 'data-style' ) ]
    };
	
	var map = new google.maps.Map( document.getElementsByClassName( classname )[index], mapOptions);

	
	google.maps.event.addListener( map, 'click', function( event ) {
		if( mode == 0 ){
			search_rect.start = event.latLng;
			if( search_rect.draw == 0 ){			
				search_rect.draw = new google.maps.Rectangle({
					strokeColor: '#FF0000',
					strokeOpacity: 0.8,
					strokeWeight: 2,
					fillColor: '#FF0000',
					fillOpacity: 0.35,
					map: map,
					editable: true,
					draggable: true,
					bounds: new google.maps.LatLngBounds(
						new google.maps.LatLng( search_rect.start.lat() - 1,search_rect.start.lng() - 1 ),
						new google.maps.LatLng( search_rect.start.lat() + 1,search_rect.start.lng() + 1 ) )
				});
				
				google.maps.event.addListener(search_rect.draw, 'bounds_changed', function( event ){
					element.parent().find( 'input.google_start' ).val( search_rect.draw.getBounds().getNorthEast().toString() );
					element.parent().find( 'input.google_end' ).val( search_rect.draw.getBounds().getSouthWest().toString() );
					if( !element.parent().hasClass( 'sf-widget-map-wrapper' ) ){
						jQuery( '.sf-wrapper' ).find( 'input[name="page"]' ).remove();
						if( jQuery( '.sf-wrapper' ).find( '.sf-button-btnsearch' ).length == 0 )
							get_filter_results();
					}
				});
			}
			mode = 1;
		}
	});
	
	return map;
};

function sf_loadmap(){	
	jQuery( document ).ready( function(){
		var i = 0;
		jQuery( '.sf-map-wrapper, .sf-widget-map-wrapper' ).each( function(){
			var range_max = '';
			var range_min = '';
			
			jQuery( '<input type="hidden" name="' + jQuery( this ).attr( 'data-index' ) + '|min" value="' + range_min + '" class="google_end" /><input type="hidden" name="' + jQuery( this ).attr( 'data-index' ) + '|max" value="' + range_max + '" class="google_start" /><div class="sf-map"></div>' ).appendTo( this );
			var map = jQuery( this ).find( '.sf-map' ).sfmap( 'sf-map', i );
			i++;
			
			if( jQuery( this ).hasClass( 'sf-widget-map-wrapper' ) && jQuery( this ).attr( 'data-rect-max' ) != '' && jQuery( this ).attr( 'data-rect-min' ) != '' ){
					var element = jQuery( this );
					var range_min = jQuery( this ).attr( 'data-rect-max'  );
					var range_max = jQuery( this ).attr( 'data-rect-min'  );
					element.find( 'input[name="' + element.attr( 'data-index' ) + '|min"]' ).val( range_min );
					element.find( 'input[name="' + element.attr( 'data-index' ) + '|max"]' ).val( range_max );
					
					range_min = range_min.match(/\d+/g);
					range_max = range_max.match(/\d+/g);
					range_min = new google.maps.LatLng(parseFloat(range_min[0] +'.'+range_min[1]),parseFloat(range_min[2] +'.'+range_min[3]));
					range_max = new google.maps.LatLng(parseFloat(range_max[0] +'.'+range_max[1]),parseFloat(range_max[2] +'.'+range_max[3]));
					search_rect = {start:range_min,end:range_max,draw:0};
					search_rect.draw = new google.maps.Rectangle({
						strokeColor: '#FF0000',
						strokeOpacity: 0.8,
						strokeWeight: 2,
						fillColor: '#FF0000',
						fillOpacity: 0.35,
						map: map,
						editable: true,
						draggable: true,
						bounds: new google.maps.LatLngBounds(
							range_max,
							range_min
						)
					});
				
					google.maps.event.addListener(search_rect.draw, 'bounds_changed', function( event ){
						element.find( 'input.google_start' ).val( search_rect.draw.getBounds().getNorthEast().toString() );
						element.find( 'input.google_end' ).val( search_rect.draw.getBounds().getSouthWest().toString() );
						jQuery( '.sf-wrapper' ).find( 'input[name="page"]' ).remove();
						if( jQuery( '.sf-wrapper' ).find( '.sf-button-btnsearch' ).length == 0 )
							get_filter_results();
					});
					
					var bounds = new google.maps.LatLngBounds();
					bounds.extend(range_min);
					bounds.extend(range_max);
					map.fitBounds(bounds);
			}
			
			if( location.hash.substr( 0, 4 ) == '#sf-' ){
				var	hash = JSON.parse( location.hash.substr( 4 ) );
				if( typeof( hash[ jQuery( this ).attr( 'data-index' ) + '|min' ] ) != 'undefined' ){
					var element = jQuery( this );
					var range_min = hash[ jQuery( this ).attr( 'data-index'  ) +'|min'];
					var range_max = hash[ jQuery( this ).attr( 'data-index'  ) +'|max'];
					element.find( 'input[name="' + element.attr( 'data-index' ) + '|min"]' ).val( range_min );
					element.find( 'input[name="' + element.attr( 'data-index' ) + '|max"]' ).val( range_max );
					
					range_min = range_min.match(/[-]?\d+/g);
					range_max = range_max.match(/[-]?\d+/g);
					range_min = new google.maps.LatLng(parseFloat(range_min[0] +'.'+range_min[1]),parseFloat(range_min[2] +'.'+range_min[3]));
					range_max = new google.maps.LatLng(parseFloat(range_max[0] +'.'+range_max[1]),parseFloat(range_max[2] +'.'+range_max[3]));
					search_rect = {start:range_min,end:range_max,draw:0};
					search_rect.draw = new google.maps.Rectangle({
						strokeColor: '#FF0000',
						strokeOpacity: 0.8,
						strokeWeight: 2,
						fillColor: '#FF0000',
						fillOpacity: 0.35,
						map: map,
						editable: true,
						draggable: true,
						bounds: new google.maps.LatLngBounds(
							range_min,
							range_max
						)
					});
				
					google.maps.event.addListener(search_rect.draw, 'bounds_changed', function( event ){
						element.find( 'input.google_start' ).val( search_rect.draw.getBounds().getNorthEast().toString() );
						element.find( 'input.google_end' ).val( search_rect.draw.getBounds().getSouthWest().toString() );
						jQuery( '.sf-wrapper' ).find( 'input[name="page"]' ).remove();
						
						if( jQuery( '.sf-wrapper' ).find( '.sf-button-btnsearch' ).length == 0 )
							get_filter_results();
					});
					
					var bounds = new google.maps.LatLngBounds();
					
					bounds.extend(range_max);
					bounds.extend(range_min);
					map.fitBounds(bounds);
					get_filter_results( true );
				}
			}
		});
	});
}

jQuery( document ).ready( function(){
	if( jQuery( '.sf-date' ).length > 0 && jQuery( '.sf-date2' ).length == 0 ){
		var sf_dateformat = jQuery( '.sf-date' ).attr( 'data-dateformat' );
		jQuery( '.sf-date' ).datepicker({
			dateFormat: sf_dateformat
		});
	}
	
	if( jQuery( '.sf-date2' ).length > 0 ){
		var sf_dateformat = jQuery( '.sf-date.first' ).attr( 'data-dateformat' );
		jQuery( '.sf-date.first' ).datepicker({
			dateFormat: sf_dateformat,
			 onClose: function( selectedDate ) {
				jQuery( '.sf-date.second' ).datepicker( "option", "minDate", selectedDate );
			}
		});
		jQuery( '.sf-date.second' ).datepicker({
			dateFormat: sf_dateformat,
			 onClose: function( selectedDate ) {
				jQuery( '.sf-date.first' ).datepicker( "option", "maxDate", selectedDate );
			}
		});
	
	}
	
	jQuery( '.sf-wrapper' ).find( '.sf-button-btnsearch' ).click( function(){
		get_filter_results();
	});
	
	jQuery( '.sf-wrapper' ).find( 'input' ).keyup( function( event ){
		if(event.which == 13)
			get_filter_results();
	});
	
	jQuery( '.sf-widget' ).find( '.sf-button-btnsearch' ).click( function( event ){
		event.preventDefault();
		var $form = jQuery( this ).closest( 'form' );
		if( $form.attr( 'data-withfilter' ) == '1' )
			get_filter_results( true, $form );
		else
			$form.submit();
	});
	
	jQuery( '.sf-button-btnreset' ).click( function( event ){
		event.preventDefault();
		
		jQuery( '.sf-wrapper, .sf-widget' ).find( 'input' ).each( function(){
			if( jQuery( this ).attr( 'type' ) != 'hidden' && jQuery( this ).attr( 'type' ) != 'reset' && jQuery( this ).attr( 'type' ) != 'submit' ){
				if( jQuery( this ).attr( 'type' ) == 'checkbox' || jQuery( this ).attr( 'type' ) == 'radio' ){
					jQuery( this ).prop( 'checked', false );
				} else {
					jQuery( this ).val( '' );
				}
			}
		});
		
		jQuery( '.sf-element-hide, .sf-widget-element-hide' ).hide();
		
		jQuery( '.sf-wrapper, .sf-widget' ).find( 'select' ).each( function(){
			jQuery( this ).val( '' );
		});
		
		jQuery( '.sf-wrapper' ).find( '.sf-range-wrapper' ).each( function(){
			jQuery( this ).html( '' );
			jQuery( this ).sfrange();
		});
		jQuery( '.sf-widget' ).find( '.sf-widget-range-wrapper' ).each( function(){
			jQuery( this ).html( '' );
			jQuery( this ).attr( 'data-posmin', jQuery( this ).attr( 'data-start' ) );
			jQuery( this ).attr( 'data-posmax', jQuery( this ).attr( 'data-end' ) );
			jQuery( this ).sfrange();
		});
		
		jQuery( '.google_end, .google_start' ).val( '' );
		
		
		if( search_rect.draw != 0 ){
			search_rect.draw.setMap( null );
			search_rect.draw = 0;
			mode = 0;
		}
		
		jQuery( '.sf-widget' ).find( 'input, select' ).each( function(){
			if( jQuery( this ).prop( 'checked' ) || typeof( jQuery( this ).attr( 'type' ) ) == 'undefined' || jQuery( this ).attr( 'type' ) != 'checkbox' || jQuery( this ).attr( 'type' ) != 'radio' ){
				var val = jQuery( this ).val();
				var name = jQuery( this ).attr( 'name' );
				jQuery( '.sf-filter' ).find( 'input[name="' + name + '"]' ).val( val );
			}
		});
		
		get_filter_results();
	});
	
	jQuery( '.sf-range-wrapper' ).each( function(){
		jQuery( this ).sfrange();
	});
	jQuery( '.sf-widget-range-wrapper' ).each( function(){
		jQuery( this ).sfrange();
	});
	
	jQuery( document ).on( 'change', '.sf-widget-range-wrapper input, .sf-widget-element input, .sf-widget-element select', function(){
		var possible_cond_key = jQuery( this ).parent().attr( 'data-id' );
		var possible_cond_val = jQuery( this ).val();
		jQuery( '.sf-widget-element-hide' ).each( function(){
			if( jQuery( this ).attr( 'data-condkey' ) == possible_cond_key ){
				if( possible_cond_val == jQuery( this ).attr( 'data-condval' ) ){
					jQuery( this ).fadeIn();
					jQuery( this ).addClass( 'sf-widget-element' );
					jQuery( this ).find( 'input, select' ).attr( 'disabled', false );
				}else{
					jQuery( this ).hide();
					jQuery( this ).removeClass( 'sf-widget-element' );
					jQuery( this ).find( 'input, select' ).attr( 'disabled', true );
				}
			}
		});
		
		var $form = jQuery( this ).closest( 'form' );
		if( typeof( $form.attr( 'data-autoload' ) ) != 'undefined' && $form.attr( 'data-autoload' ) == 1 && $form.attr( 'data-withfilter' ) != 1 ){
			jQuery( this ).closest( 'form' ).submit();
		} else {
			if( typeof( $form.attr( 'data-autoload' ) ) != 'undefined' && $form.attr( 'data-autoload' ) == 1 && $form.attr( 'data-withfilter' ) == 1 ){
				get_filter_results( true, $form );
			}
		}
	});
	
	jQuery( document ).on( 'change', '.sf-filter input, .sf-filter select', function(){
		var possible_cond_key = jQuery( this ).closest( '.sf-element' ).attr( 'data-id' );
		var possible_cond_val = jQuery( this ).val();
		if( ( jQuery( this ).attr('type') == 'checkbox' || jQuery( this ).attr('type') == 'radio' ) && !jQuery( this ).prop( 'checked' ) )
			possible_cond_val = -2;
		jQuery( '.sf-element-hide' ).each( function(){
			if( jQuery( this ).attr( 'data-condkey' ) == possible_cond_key ){
				if( possible_cond_val == jQuery( this ).attr( 'data-condval' ) ){
					jQuery( this ).fadeIn();
					jQuery( this ).addClass( 'sf-element' );
					jQuery( this ).find( 'input, select' ).attr( 'disabled', false );
				}else{
					jQuery( this ).hide();
					jQuery( this ).removeClass( 'sf-element' );
					jQuery( this ).find( 'input, select' ).attr( 'disabled', true );
				}
			}
		});
		jQuery( '.sf-wrapper' ).find( 'input[name="page"]' ).remove();
		if( jQuery( '.sf-wrapper' ).find( '.sf-button-btnsearch' ).length == 0 )
			get_filter_results();
	});
	
	jQuery( document ).on( 'click','.sf-nav-click', function( event ){
		event.preventDefault();
		jQuery( '.sf-wrapper' ).find( 'input[name="page"]' ).remove();
		var txt = '<input type="hidden" name="page" value="' + jQuery( this ).attr( 'data-href' ) + '" />';
		jQuery( txt ).appendTo( '.sf-wrapper' );
		get_filter_results();
		jQuery('html, body').animate({ scrollTop: ( jQuery('.sf-wrapper').offset().top - 25 )}, 'slow');
	});
	
	if( location.hash.substr( 0, 4 ) == '#sf-' ){
		var range_max = '';
		var range_min = '';
		var	hash = JSON.parse( location.hash.substr( 4 ) );
		var do_ajax_request = true;
		for ( property in hash ) {
			jQuery( '.sf-element-hide[data-condkey="'+property+'"]' ).each( function(){
				if( jQuery( this ).attr( 'data-condval' ) == hash[property] ){
					jQuery( this ).show();
					jQuery( this ).addClass( 'sf-element' );
				}
			});
				
			if( jQuery( '.sf-filter *[name="' + property + '"]' ).attr( 'type' ) != 'radio' )
				jQuery( '.sf-filter *[name="' + property + '"]' ).val( hash[property] );
			jQuery( '.sf-filter input[name="' + property + '[]"]' ).each( function(){
				if( jQuery( this ).attr( 'type' ) == 'checkbox' ){
					for( var i = 0; i < hash[property].length; i++ )
						if( jQuery( this ).val() == hash[property][i] )
							jQuery( this ).prop( 'checked', true );
				}
			});
			
			var date_index = 0;
			jQuery( '.sf-filter input.sf-date[name="' + property + '[]"]' ).each( function(){
				jQuery( this ).val( hash[property][ date_index ] );
				date_index++;
			});
			
			jQuery( '.sf-filter input[type="radio"][name="' + property + '"][value="' + hash[property] +'"]' ).prop('checked',true);
			if( jQuery( '.sf-filter *[name="' + property + '"]' ).parent().hasClass( 'sf-range-wrapper' ) ){
				var arrange_slider = true;
				jQuery( '.sf-filter *[name="' + property + '"]' ).parent().find( 'input[type="hidden"]' ).each( function(){
					if( jQuery( this ).val() != hash[ jQuery( this ).attr( 'name') ] )
						arrange_slider = false;
				});
				if( arrange_slider ){
					var parent = jQuery( '.sf-filter *[name="' + property + '"]' ).parent()
					parent.find( 'input[type="hidden"]' ).each( function(){						
						if( jQuery( this ).attr( 'name' ).match(/max/i) )
							range_max = parseInt( jQuery( this ).val() );
						else
							range_min = parseInt( jQuery( this ).val() );
					});
					parent.find( '.sf-range' ).slider( "option", "values", [range_min,range_max] );	
					if( parent.attr( 'data-unitfront' ) == 1 )
						var pricetxt = parent.attr( 'data-unit' ) + range_min + ' - ' + parent.attr( 'data-unit' ) + range_max;
					else
						var pricetxt = range_min + parent.attr( 'data-unit' ) + ' - ' + range_max + parent.attr( 'data-unit' );
					parent.find( '.sf-write' ).text( pricetxt );
				}
			}
		}
		if( do_ajax_request )
			get_filter_results( true );
	}
});
