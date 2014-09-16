<?php
	add_action('wp_ajax_sf-deleteform', 'sf_ajax_deleteform');
	function sf_ajax_deleteform(){		
		$fields = get_option( 'sf-fields' );
		unset( $fields[ $_POST['id'] ] );
		update_option( 'sf-fields', $fields );
		echo 'OK';
		die();
	}
	
	add_action('wp_ajax_sf-optionsearch', 'sf_ajax_optionsearch');
	function sf_ajax_optionsearch(){
		$data = array();
		$i = 0;
		preg_match_all( '^(.*)\[(.*)\]^', $_POST['val'], $match );
		$data_type = $match[1][0];
		$data_value = $match[2][0];
		if( $data_type == 'meta' ){
			$terms = get_postmeta_values( $data_value );
			if( is_array( $terms ) ):
			foreach( $terms as $term ){
				$data[ $i ]['key'] = $term->meta_value;
				$data[ $i ]['val'] = $term->meta_value;
				$i++;
			}
			endif;
		}elseif( $data_type == 'tax' ){
			$args = array(
						'orderby'       => 'name', 
						'order'         => 'ASC',
						'hide_empty'    => true
					);
			$terms = get_terms( $data_value, $args );
			if( is_array( $terms ) ):
			foreach( $terms as $term ){
				$data[ $i ]['key'] = $term->term_id;
				$data[ $i ]['val'] = $term->name;
				$i++;
			}
			endif;
		}
		
		echo json_encode( $data );
		die();
	}
	
	add_action('wp_ajax_sf-search', 'sf_ajax_search');
	add_action('wp_ajax_nopriv_sf-search', 'sf_ajax_search');
	function sf_ajax_search(){	
		error_reporting( 0 );
		echo json_encode( sf_do_search() );
		die();	
	}
	
	function sf_do_search( $exclude = array() ){
		global $wpdb;
			
		if( !isset( $_POST['data']['page'] ) || $_POST['data']['page'] == 1 )
			$_SESSION['sf'] = $_POST['data'];
		$data['post'] = $_POST['data'];		
		
		if( isset( $_POST['data']['wpml'] ) ):
			global $sitepress;
			$sitepress->switch_lang( $_POST['data']['wpml'], true );
			unset( $_POST['data']['wpml'] );
		endif;
		
		$fulltext = "";
		$fields = get_option( 'sf-fields' );
		$found = false;
		foreach( $fields as $field ):
			if( $field['name'] == $_POST['data']['search-id'] ):
				$found = true;
				break;
			endif;
		endforeach;
		
		if( !$found )
			die( 'Wrong parameter' );
				
		$args = array(
			'post_type'		=> $field['posttype'],
			'post_status'	=> 'publish'
		);
		
		if( isset( $field['posts_per_page'] ) && $field['posts_per_page'] != 'auto' )
			$args['posts_per_page'] = $field['posts_per_page'];
		
		/**
			Standard OrderBy
		*/
		if( isset( $field['order-standard'] ) && !empty( $field['order-standard'] ) ):
			$sorderby = explode( '|', $field['order-standard'] );
			$args['order'] = $sorderby[2];
			if( $sorderby[0] == 'post' ):
				$args['orderby'] = $sorderby[1];
			else:
				$args['orderby'] = 'meta_value meta_value_num';
				$args['meta_key'] = $sorderby[1];
			endif;
		endif;
		
		if( count( $exclude ) > 0 )
			$args['post__not_in'] = $exclude;
			
		$template_file = SF_DIR . 'templates/template-' . $field['name'];
		if( function_exists('is_multisite') && is_multisite() )
			$template_file = SF_DIR . 'templates/template-' . $wpdb->blogid . '-' . $field['name'] ;
		
		
		if( !is_file( $template_file . '.php' ) )
			$template_file = SF_DIR . 'templates/res/template-standard';
			
		$data_tmp = array();
		foreach( $_POST['data'] as $key => $val ):
			if( $val == '' || empty( $val ) )
				continue;
				
			$key = explode( '|', $key );
			if( !isset( $key[1] ) )
				$data_tmp[ $key[0] ]['val'] = $val;
			if( isset( $key[1] ) )
				$data_tmp[ $key[0] ][ $key[1] ] = $val;
		endforeach;		
		$_POST['data'] = $data_tmp;
		
		$operator = array( 'like' => 'LIKE', 'between' => 'BETWEEN', 'equal' => '=', 'bt' => '>', 'st' => '<', 'bte' => '>=', 'ste' => '<=' );
		foreach( $field['fields'] as $key => $val ):
			if( isset( $val['datasource'] ) && !in_array( $val['type'], array( 'map','fulltext' ) ) ):
				preg_match_all( '^(.*)\[(.*)\]^', $val['datasource'], $match );
				$data_type = $match[1][0];
				$data_value = $match[2][0];
			else:
				$data_type = $val['type'];
				$data_value = $val['type'] ;
			endif;
			
			
			/**
			Hidden Fields
			*/
			if( $val['type'] == 'hidden' ):
				if( $data_type == 'tax' ):
					if( !isset( $args['tax_query'] ) ):
						$args['tax_query']['relation'] = 'AND';
					endif;
					$args['tax_query'][] = array( 
							'taxonomy'	=> $data_value, 
							'terms'		=> (int) $val['value'] 
						);
						
				elseif( $data_type == 'meta' ):
					if( !isset( $args['meta_query'] ) )
						$args['meta_query'] = array();
						
					$args['meta_query'][] = array(
									'key'		=>	$data_value,
									'value'		=>	$val['value'],
									'compare'	=>	'EQUAL'
						);
				endif;
			endif;
			
			if( isset( $_POST['data'][ $key ] ) ):
				
				/**
				Others Query
				*/
				if( $data_type == 'others' ):
					if( $data_value == 'author' ):
						if( is_array( $_POST['data'][ $key ]['val'] ) ):
							foreach( $_POST['data'][ $key ]['val'] as $k => $v )
								$_POST['data'][ $key ]['val'][ $k ] = (int) $v;
							$args['author__in'] = $_POST['data'][ $key ]['val'] ;
						else:
							$args['author__in'][] = (int) $_POST['data'][ $key ]['val'] ;
						endif;
					endif;
					
					
					
				/**
				Taxonomy Query
				*/
				elseif( $data_type == 'tax' ):
					if( !isset( $args['tax_query'] ) ):
						$args['tax_query']['relation'] = 'AND';
					endif;
					
					/** Select Field */
					if( $val['type'] == 'select' && $_POST['data'][ $key ]['val']  != "" ):
						$args['tax_query'][] = array( 
							'taxonomy'	=> $data_value, 
							'terms'		=> (int) $_POST['data'][ $key ]['val'] 
						);					
					
					/** Range Fields */					
					elseif( $val['type'] == 'range' ):
						$terms = get_terms( $data_value );
						$term_id = array();
						
						if( !isset( $_POST['data'][ $key ]['min'] ) )
							$_POST['data'][ $key ]['min'] = 0;
							
						foreach( $terms as $t ):
							if( ( $_POST['data'][ $key ]['min'] <= $t->name ) && ( $_POST['data'][ $key ]['max'] >= $t->name ) ):
								$term_id[] = $t->term_id;
							endif;
						endforeach;
						if( count( $term_id ) == 0 ):
							$term_id = -1;
						endif;
						
						$args['tax_query'][] = array( 
							'taxonomy'	=> $data_value, 
							'terms'		=> $term_id 
						);							
					
					/** Input Field */
					elseif( $val['type'] == 'input' ):	
						$terms = get_terms( $data_value );
						$term_id = array();
						foreach( $terms as $t ):
							if( strtolower( substr( $t->name, 0, strlen( $_POST['data'][ $key ]['val'] ) ) ) == strtolower( $_POST['data'][ $key ]['val'] ) )
								$term_id[] = $t->term_id;
						endforeach;
						if( count( $term_id ) == 0 ):
							$term_id = -1;
						endif;
						
						$args['tax_query'][] = array( 
							'taxonomy'	=> $data_value, 
							'terms'		=> $term_id 
						);
						
					/** Input Field */
					elseif( $val['type'] == 'checkbox' ):
						$operator = 'IN';
						$include_children = true;
						if( isset( $val['include_children'] ) && $val['include_children'] == 0 )
							$include_children = false;
						if( isset( $val['operator'] ) )
							$operator = $val['operator'];
							
						$args['tax_query'][] = array( 
							'taxonomy'	=> $data_value, 
							'terms'		=> $_POST['data'][ $key ]['val'],
							'operator'	=> $operator,
							'include_children' => $include_children
						);
						
					/** Input Field */
					elseif( $val['type'] == 'radiobox' ):						
						$args['tax_query'][] = array( 
							'taxonomy'	=> $data_value, 
							'terms'		=> $_POST['data'][ $key ]['val'] 
						);
						
					endif;

				/**
				Postmeta Query				
				*/					
				elseif( $data_type == 'meta' ):
					if( !isset( $args['meta_query'] ) )
						$args['meta_query'] = array();
					
					/** Select Field */
					if( $val['type'] == 'select' ):
						$args['meta_query'][] = array(
									'key'		=>	$data_value,
									'value'		=>	$_POST['data'][ $key ]['val'],
									'compare'	=>	'='
						);
					
					/** Input Field */
					elseif( $val['type'] == 'input' ):
						$val_type = 'CHAR';
						if( in_array( $val['operator'], array( 'bt', 'st', 'bte', 'ste', 'between' ) ) )
							$val_type = 'NUMERIC';
						$args['meta_query'][] = array(
									'key'		=> $data_value,
									'value'		=> $_POST['data'][ $key ]['val'],
									'type' 		=> $val_type,
									'compare'	=> $operator[ $val['operator'] ]
						);
					
					/** Checkbox Field */
					elseif( $val['type'] == 'checkbox' ):
						$args['meta_query'][] = array(
									'key'		=> $data_value,
									'value'		=> $_POST['data'][ $key ]['val'],
									'type' 		=> 'CHAR',
									'compare'	=> 'IN'
						);
					
					/** Checkbox Field */
					elseif( $val['type'] == 'radiobox' ):
						$args['meta_query'][] = array(
									'key'		=> $data_value,
									'value'		=> $_POST['data'][ $key ]['val'],
									'type' 		=> 'CHAR',
									'compare'	=> '='
						);
					
					/** Range Fields */					
					elseif( $val['type'] == 'range' ):
						if( !isset( $_POST['data'][ $key ]['min'] ) )
							$_POST['data'][ $key ]['min'] = 0;
						$args['meta_query'][] = array(
									'key'		=>	$data_value,
									'value'		=>	array( $_POST['data'][ $key ]['min'], $_POST['data'][ $key ]['max'] ),
									'type' => 'NUMERIC',
									'compare'	=>	'BETWEEN'
						);					
					endif;
					
					
				elseif( $val['type'] == 'date' && !empty( $_POST['data'][ $key ]['val'] ) ):
					$val['dateformat'] = preg_replace( '^yy^', 'Y', $val['dateformat'] );
					$val['dateformat'] = preg_replace( '^dd^', 'd', $val['dateformat'] );
					$val['dateformat'] = preg_replace( '^mm^', 'm', $val['dateformat'] );
					
					if( $val['source'] == 'modified' )
						$args['date_query']['column'] = 'post_modified';
					
					$date1 = date_parse_from_format( $val['dateformat'], $_POST['data'][ $key ]['val'][0] );
					if( !$date1 ) $date1 = array( 'year' => 0, 'month' => 0, 'day' => 0 );
					if( count( $_POST['data'][ $key ]['val'] ) == 2 )
						$date2 = date_parse_from_format( $val['dateformat'], $_POST['data'][ $key ]['val'][1] );
					
					$args['date_query']['inclusive'] = true;
					if( $val['searchtype'] == 'from' ):
						$args['date_query']['after']['year'] = $date1['year'];
						$args['date_query']['after']['month'] = $date1['month'];
						$args['date_query']['after']['day'] = $date1['day'];
						$args['date_query']['after']['hour'] = 0;
						$args['date_query']['after']['minute'] = 0;
						$args['date_query']['after']['second'] = 0;
					elseif( $val['searchtype'] == 'till' ):
						$args['date_query']['before']['year'] = $date1['year'];
						$args['date_query']['before']['month'] = $date1['month'];
						$args['date_query']['before']['day'] = $date1['day'];
						$args['date_query']['before']['hour'] = 23;
						$args['date_query']['before']['minute'] = 59;
						$args['date_query']['before']['second'] = 59;
					elseif( $val['searchtype'] == 'between' ):
						if( !$date2 ) $date2 = array( 'year' => date( 'Y', time() ), 'month' => date( 'm', time() ), 'day' => date( 'd', time() ) );
						$args['date_query']['before']['year'] = $date2['year'];
						$args['date_query']['before']['month'] = $date2['month'];
						$args['date_query']['before']['day'] = $date2['day'];
						$args['date_query']['before']['hour'] = 23;
						$args['date_query']['before']['minute'] = 59;
						$args['date_query']['before']['second'] = 59;
						$args['date_query']['after']['year'] = $date1['year'];
						$args['date_query']['after']['month'] = $date1['month'];
						$args['date_query']['after']['day'] = $date1['day'];
						$args['date_query']['after']['hour'] = 0;
						$args['date_query']['after']['minute'] = 0;
						$args['date_query']['after']['second'] = 0;
					endif;
					
				elseif( $val['type'] == 'fulltext' && !empty( $_POST['data'][ $key ]['val'] ) ):
					if( in_array( 'the_title', $val['contents'] ) )
						$args['sf-title'] = $_POST['data'][ $key ]['val'];
					if( in_array( 'the_content', $val['contents'] ) )
						$args['sf-content'] = $_POST['data'][ $key ]['val'];
					if( in_array( 'the_excerpt', $val['contents'] ) )
						$args['sf-excerpt'] = $_POST['data'][ $key ]['val'];
					foreach( $val['contents'] as $v ):
						if( preg_match( '^meta\[(.*)\]^', $v ) ):
							if( !isset( $args['sf-meta'] ) ):
								$args['sf-meta'] = array();
							endif;
							$args['sf-meta'][ $v ] = $_POST['data'][ $key ]['val'];
						endif;
					endforeach;
					add_filter( 'posts_where', 'sf_content_filter', 10, 2 );
					if( isset( $args['sf-meta'] ) )
						add_filter( 'posts_join_paged', 'sf_content_filter_join', 10, 2 );
					
					$fulltext = $_POST['data'][ $key ]['val'] ;
				elseif( $val['type'] == 'map' ):
					preg_match_all( '^\((.*)\, (.*)\)^', $_POST['data'][ $key ]['min'], $match );
					if( !isset( $match[1][0] ) || !isset( $match[2][0] ) )
						continue;
					$lat['min'] = $match[1][0];
					$lon['min'] = $match[2][0];
					preg_match_all( '^\((.*)\, (.*)\)^', $_POST['data'][ $key ]['max'], $match );
					if( !isset( $match[1][0] ) || !isset( $match[2][0] ) )
						continue;
					$lat['max'] = $match[1][0];
					$lon['max'] = $match[2][0];
					sort( $lat );
					sort( $lon );
					if( !isset( $args['meta_query'] ) )
						$args['meta_query'] = array();
					
					$args['meta_query'][] = array(
							'key'		=>	$val['lon'],
							'value'		=>	$lon,
							'type' => 'NUMERIC',
							'compare'	=>	'BETWEEN'
					);	
					$args['meta_query'][] = array(
							'key'		=>	$val['lat'],
							'value'		=>	$lat,
							'type' => 'NUMERIC',
							'compare'	=>	'BETWEEN'
					);	
				endif;
			endif;				
		endforeach;
		
		if( isset( $_POST['data']['orderby'] ) ):
					preg_match_all( '^(.*)\[(.*)\|(.*)\]^', $_POST['data']['orderby']['val'], $matches );
					$data_type = $matches[1][0];
					$data_value = $matches[2][0];
					$order = $matches[3][0];
					if( in_array( $order, array('desc', 'asc' ) ) )
						$args['order'] = $order;
						
					if( $data_type == 'meta' ){
						$args['meta_key'] = $data_value;
						$args['orderby'] = 'meta_value_num';
					}
						
					if( $data_type == 'post' ){
						if( in_array( $data_value, array( 'ID', 'author', 'title', 'name', 'date', 'modified', 'parent', 'rand', 'comment_count', 'menu_order' ) ) )
						$args['orderby'] = $data_value;
					}
		endif;
		
		if( isset( $_POST['data']['page'] ) )
			$args['paged'] = (int) $_POST['data']['page']['val'];
		
		$data['result'] = array();
		$args = apply_filters( 'sf-filter-args', $args );
		$wpdb->query( 'SET OPTION SQL_BIG_SELECTS = 1' );
		$query = new WP_Query( $args );
		if( isset( $field['debug'] ) && $field['debug'] == 1 ):
			$data['args'] = $args;
			$data['query'] = $query;
		endif;
		remove_filter( 'posts_join_paged', 'sf_content_filter_join' );
		remove_filter( 'posts_where', 'sf_content_filter' );
		if( $query->have_posts() ):
			while( $query->have_posts() ): $query->the_post();
				ob_start();
				require( $template_file . '.php' );
				$template = ob_get_contents();
				ob_end_clean();
				if( $field['highlight'] == 1 && $fulltext != '' ):
					$template_single = preg_replace( '^#the_title#^', preg_replace( '^(' . preg_quote( $fulltext ) . ')^i', '<span class="sf-selected">$1</span>' ,get_the_title() ), $template );
					$template_single = preg_replace( '^#the_excerpt#^', preg_replace( '^(' . preg_quote( $fulltext ) . ')^i', '<span class="sf-selected">$1</span>' ,strip_tags( get_the_excerpt() ) ), $template_single );
					$template_single = preg_replace( '^#the_content#^', preg_replace( '^(' . preg_quote( $fulltext ) . ')^i', '<span class="sf-selected">$1</span>' ,strip_tags( get_the_content() ) ), $template_single );
				else:
					$template_single = preg_replace( '^#the_title#^', get_the_title(), $template );
					$template_single = preg_replace( '^#the_excerpt#^', get_the_excerpt(), $template_single );
					$template_single = preg_replace( '^#the_content#^', get_the_content(), $template_single );
				endif;
				$template_single = preg_replace( '^#the_author#^', get_the_author(), $template_single );
				$template_single = preg_replace( '^#the_date#^', get_the_date(), $template_single );
				$template_single = preg_replace( '^#the_permalink#^', get_permalink(), $template_single );
				$template_single = preg_replace( '^#the_id#^', get_the_ID(), $template_single );
				$template_single = preg_replace( '^#count_comments#^', wp_count_comments( get_the_ID() )->approved, $template_single );
				
				if( isset( $field['tax'] ) && is_array( $field['tax'] ) ):
				foreach( $field['tax'] as $t ):
					$terms = get_the_terms( get_the_ID(), $t );
					$termstring = '';
					if( is_array( $terms ) ):
						foreach( $terms as $term ):
							if( $termstring != '' )
								$termstring .= ', ';
							
							$termname = $term->name;
							if( $field['highlight'] == 1 ):
								foreach( $field['fields'] as $key => $val ){
									if( isset( $val['datasource'] ) ):
										preg_match_all( '^(.*)\[(.*)\]^', $val['datasource'], $match );
										$data_type = $match[1][0];
										$data_value = $match[2][0];
										if( isset( $_POST['data'][ $key ]['val'] ) && $_POST['data'][ $key ]['val'] == $term->term_id && $data_value == $t )
											$termname = '<span class="sf-selected">' . $term->name . '</span>';
									endif;
								}
							endif;
							$termstring .= $termname;
						endforeach;
					endif;
					$template_single = preg_replace( '^#tax_' . preg_quote( $t ) . '#^', $termstring, $template_single );
				endforeach;
				endif;
				
				if( isset( $field['meta'] ) && is_array( $field['meta'] ) ):
				foreach( $field['meta'] as $m ):
					$meta = get_post_meta( get_the_ID(), $m, true );
					if( is_array( $meta ) )
						continue;
						
					if( $field['highlight'] == 1):
						foreach( $field['fields'] as $key => $val ){
							if( isset( $val['datasource'] ) ):
								preg_match_all( '^(.*)\[(.*)\]^', $val['datasource'], $match );
								$data_type = $match[1][0];
								$data_value = $match[2][0];
								if( isset( $_POST['data'][ $key ]['val'] ) && $_POST['data'][ $key ]['val'] == $meta && $data_value == $m ):
									$meta = '<span class="sf-selected">' . $meta . '</span>';
								elseif( isset( $fulltext ) && !empty( $fulltext ) ):
									$meta = preg_replace( '^(' . preg_quote( $fulltext ) . ')^i', '<span class="sf-selected">$1</span>', $meta );
								endif;
							endif;
						}
					endif;
					$template_single = preg_replace( '^#meta_' . preg_quote( $m ) . '#^', $meta, $template_single );					
				endforeach;
				endif;
				$image = "";
				if( has_post_thumbnail() ):
					$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumb' );
					$image = '<img src="' . $image[0] . '" width="' . $image[1] . '" height="' . $image[2] . '" alt="' . get_the_title() . '" />';
				endif;				
				$template_single = preg_replace( '^#thumbnail#^', $image, $template_single );
				$data['result'][] = '<li>' . apply_filters( 'sf-results-single-result', $template_single ) . '</li>';
			endwhile;
		endif;
		
		if( count( $data['result'] ) == 0 ):			
			ob_start();
			require( $template_file . '-noresult.php' );
			$template_noresult = ob_get_contents();
			ob_end_clean();
			$data['result'][] = '<li class="sf-noresult">' . apply_filters( 'sf-results-noresult', $template_noresult ) . '</li>';
		endif;
		
		
		
		if( defined( 'ICL_LANGUAGE_CODE' )  ):
			global $sitepress;
			$num_of_posts = sf_count_posts( $sitepress->get_current_language(), $field['posttype'] );
		else:
			$num_of_posts = 0;
			if( is_array( $field['posttype'] ) ):
				foreach( $field['posttype'] as $posttype )
					$num_of_posts += wp_count_posts( $posttype )->publish;
			else:
					$num_of_posts += wp_count_posts( $field['posttype'] )->publish;
			endif;
		endif;
		
		if( !isset( $field['head'] ) || $field['head'] == 1 )
			$data['head'] = sprintf( __( '<span class="sf-foundcount">%d results</span> out of <span class="sf-totalcount">%d posts</span>', 'sf' ), $query->found_posts, $num_of_posts );
			
		$data['nav'] = array();
		if( $query->max_num_pages > 1 ):
			$pages_around_result = 4;
			if( !isset( $_POST['data']['page'] ) )
				$paged = 1;
			else
				$paged = (int) $_POST['data']['page']['val'];
			$i = 0;
			
			if( $paged > 1 )
				$data['nav'][]='<li><span class="sf-nav-click sf-nav-left-arrow" data-href="' . ( $paged - 1 ) . '">&laquo;</span></li>';
			while( $i < $query->max_num_pages ){
				$i++;
				if( $i == 1 || ( $i > $paged - $pages_around_result && $i < $paged + $pages_around_result ) || $i == $query->max_num_pages ){
					if( $i != $paged )
						$data['nav'][]='<li><span class="sf-nav-click" data-href="' . ( $i ) . '">' . $i . '</span></li>';
					else
						$data['nav'][]='<li><span class="sf-nav-current">' . $i . '</span></li>';
				} elseif( ( $i == $paged - $pages_around_result || $i == $paged + $pages_around_result )  ){
						$data['nav'][]='<li><span class="sf-nav-three-points">...</span></li>';
				}
			}
			if( $paged < $query->max_num_pages )
				$data['nav'][]='<li><span class="sf-nav-click sf-nav-right-arrow" data-href="' . ( $paged + 1 ) . '">&raquo;</span></li>';		
			
		endif;
		return $data;
	}
?>