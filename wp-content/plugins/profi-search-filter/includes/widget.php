<?php

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'sf_load_widget' );
function sf_load_widget() {
	register_widget( 'Searchfrom_Widget' );
}

class Searchfrom_Widget extends WP_Widget {
	function Searchfrom_Widget() {
		$widget_ops = array( 'classname' => 'searchform_widget', 'description' => __( 'Include a Search Form into your Sidebar', 'sf' ) );
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'searchform-widget' );
		$this->WP_Widget( 'searchform-widget', __( 'Profi Search Form Widget', 'sf' ), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		global $wpdb;
		extract( $args );
		if( !isset( $instance['target'] ) || empty( $instance['target'] ) || !isset( $instance['searchform'] ) || empty( $instance['searchform'] ) )
			return;
		echo $before_widget;
		echo $before_title . $instance["title"] . $after_title;
		
		if( is_array( $instance['searchform'] ) )
			$instance['searchform'] = $instance['searchform'][ICL_LANGUAGE_CODE];
			
		
		$fields = get_option( 'sf-fields' );
		foreach( $fields as $field )
			if( $field['name'] == $instance['searchform'] )
				break;
				
		if( function_exists( 'icl_object_id' ) )
			$instance['target'] = icl_object_id( $instance['target'], 'page', true );

		
		if( isset( $field['custom_css'] ) && trim( $field['custom_css'] ) != '' ):
		?><style><?php echo stripslashes( $field['custom_css'] );?></style><?php
		endif;
		?>
		
			<form method="post" data-withfilter="<?php if( isset( $instance['withfilter'] ) ): echo $instance['withfilter']; else: echo '0'; endif; ?>" data-autoload="<?php echo $instance["autoload"] ?>" action="<?php echo get_permalink( $instance['target'] ); ?>">
				<?php if( defined( 'ICL_LANGUAGE_CODE' )  ):
					global $sitepress; ?>
					<input type="hidden" name="wpml" value="<?php echo $sitepress->get_current_language(); ?>" />
				<?php endif; ?>
				<input type="hidden" name="search-id" value="<?php echo $field['name']; ?>" />
				<ul class="sf-widget">
				<?php foreach( $field['fields'] as $key => $element ):
						if( $element['type'] == 'hidden' )
							continue;
			if( isset( $element['datasource'] ) ):
				preg_match_all( '^(.*)\[(.*)\]^', $element['datasource'], $match );
				$data_type = $match[1][0];
				$data_value = $match[2][0];
			else:
				$data_type = '';
				$data_value = '';
			endif;
			
			$class_hide = "";
			$style_hide = "";
			$cond_key = "";
			$cond_value = "";
			
			if( isset( $element['cond_key'] ) ):
				$cond_key = $element['cond_key'];
				$cond_value = $element['cond_value'];
			if( ( $element['cond_key'] != -1 || !empty( $element['cond_key'] ) ) && !empty( $element['cond_value'] ) ){
				$class_hide= "-hide";
				$style_hide = 'style="display:none;"';
				
				if( isset( $_POST[ $element['cond_key'] ] ) && ( ( !is_array( $_POST[ $element['cond_key'] ] ) && $_POST[ $element['cond_key'] ] == $element['cond_value'] ) || ( is_array( $_POST[ $element['cond_key'] ] ) && in_array( $element['cond_key'], $_POST[ $element['cond_key'] ]  ) ) ) ){
					$style_hide = "";
					$class_hide= "-hide sf-widget-element";
				}
			}
			endif;
		?>

		<?php if( $element['type'] == 'btnsearch' || $element['type'] == 'btnreset' ):
		?><input type="<?php if( $element['type'] == 'btnsearch' )echo 'submit'; else echo 'reset'; ?>" class="sf-button-<?php echo $element['type']; ?>" value="<?php echo $element['fieldname']; ?>" />
		<?php
		else:
		?>
		<li data-id="<?php echo $key; ?>" <?php echo $style_hide . ' data-condkey="' . $cond_key . '" data-condval="'  . $cond_value .  '"'; ?> class="sf-widget-element<?php  echo $class_hide; ?> <?php echo $element['type']; ?>">
			<span><?php echo $element['fieldname']; ?></span>	
		<?php			
			if( $element['type'] == 'select' ):
			?>
			<select id="sf-field-<?php echo $key; ?>" name="<?php echo $key; ?>"><option></option><?php
				if( $data_type == 'tax' && $element['options'] == 'auto' ):
					$args = array(
						'orderby'       => 'name', 
						'order'         => 'ASC',
						'hide_empty'    => true
					);
					$terms = get_terms( $data_value, $args );
					if( isset( $element['hierarchical'] ) && $element['hierarchical'] == 1 ):
						$terms = order_terms_hierarchical( $terms, $element['hierarchical_symbol_to_indent'] );
					endif;
					foreach( $terms as $term ):
					?><option <?php if( isset( $_POST[ $key ] ) && $_POST[ $key ] == $term->term_id ) echo ' selected="selected" '; ?> value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option><?php
					endforeach;
				elseif( $data_type == 'meta' && $element['options'] == 'auto' ):
					$values = get_postmeta_values( $data_value );
					foreach( $values as $val ):
					?>
					<option <?php if( isset( $_POST[ $key ] ) && $_POST[ $key ] == $val->meta_value ) echo ' selected="selected" '; ?> value="<?php echo $val->meta_value; ?>"><?php echo $val->meta_value; ?></option>					
					<?php
					endforeach;
				elseif( $element['options'] == 'individual' ):
					foreach( $element['option_key'] as $option_key => $val ):
					?>
					<option <?php if( isset( $_POST[ $key ] ) && $_POST[ $key ] == $val ) echo ' selected="selected" '; ?> value="<?php echo $val; ?>"><?php echo $element['option_val'][$option_key]; ?></option>
					<?php
					endforeach;
				
				elseif( $data_type == 'others' ):
					if( $data_value == 'author' ):
						$args = array(
							'who'	=>	'authors'
						);
						$authors = apply_filters( 'sf-get-authors', get_users( $args ) );
						foreach( $authors as $author ):
						?>
						<option <?php if( isset( $_POST[ $key ] ) && $_POST[ $key ] == $author->ID ) echo ' selected="selected" '; ?> value="<?php echo $author->ID; ?>"><?php echo $author->data->display_name; ?></option>
						<?php
						endforeach;
					endif;
				endif;
				
			?></select><?php
			elseif( $element['type'] == 'map' ):
				?>
				<script id="google-script"></script>
				<script>
					window.onload = function(){
						if( typeof google == 'undefined' ){
							document.getElementById( 'google-script' ).src = "http://maps.googleapis.com/maps/api/js?key=<?php echo $element['apikey']; ?>&sensor=false&callback=sf_loadmap"
						} else {
							sf_loadmap();
						}
					};
				</script>
				<div class="sf-widget-map-wrapper" data-rect-max="<?php if( isset( $_POST[ $key . '|max' ] ) ) echo $_POST[ $key . '|max' ]; ?>" data-rect-min="<?php if( isset( $_POST[ $key . '|min' ] ) ) echo $_POST[ $key . '|min' ]; ?>" [data-index="<?php echo $key; ?>" data-lat="<?php echo $element['center_lat']; ?>" data-style="<?php echo $element['style']; ?>" data-lon="<?php echo $element['center_lon']; ?>" data-zoom="<?php echo $element['zoom']; ?>"></div>
				<?php
			elseif( $element['type'] == 'input' ):
				?>
				<input placeholder="<?php echo $element['fieldname']; ?>" id="sf-field-<?php echo $key; ?>" name="<?php echo $key; ?>" <?php if( isset( $_POST[ $key ] ) ) echo ' value="' . $_POST[ $key ] . '" '; ?> />
				<?php	
			elseif( $element['type'] == 'checkbox' ):
			?>
				<div class="sf-widget-checkbox-wrapper">
				<?php 
					if( $element['options'] == 'individual' ): ?>
						<?php foreach( $element['option_key'] as $option_key => $val ):
						?>
						<label><input type="checkbox" value="<?php echo $val; ?>" <?php if( isset( $_POST[ $key ] ) && in_array( $val, $_POST[ $key ] ) ) echo ' checked="checked" '; ?> name="<?php echo $key; ?>[]" /> <?php echo $element['option_val'][$option_key]; ?></label>
						<?php
						endforeach;
			
					elseif( $data_type == 'tax' && $element['options'] == 'auto' ):
						$args = array(
							'orderby'       => 'name', 
							'order'         => 'ASC',
							'hide_empty'    => true
						);
						$terms = get_terms( $data_value, $args );
						foreach( $terms as $term ): 
							?>
							<label><input type="checkbox" <?php if( isset( $_POST[ $key ] ) && in_array( $term->term_id, $_POST[ $key ] ) ) echo ' checked="checked" '; ?> value="<?php echo $term->term_id; ?>" name="<?php echo $key; ?>[]" /> <?php echo $term->name; ?></label>
							<?php 
						endforeach;
					elseif( $data_type == 'meta' && $element['options'] == 'auto' ):
						$values = get_postmeta_values( $data_value );
						foreach( $values as $val ):
						?>
						<label><input type="checkbox" <?php if( isset( $_POST[ $key ] ) && in_array( $val->meta_value, $_POST[ $key ] ) ) echo ' checked="checked" '; ?> value="<?php echo $val->meta_value; ?>" name="<?php echo $key; ?>[]" /> <?php echo $val->meta_value; ?></label>					
						<?php
						endforeach;
					elseif( $data_type == 'others' ):
						if( $data_value == 'author' ):
							$args = array(
								'who'	=>	'authors'
							);
							$authors = apply_filters( 'sf-get-authors', get_users( $args ) );
							foreach( $authors as $author ):
							?>
							<label><input type="checkbox" <?php if( isset( $_POST[ $key ] ) && in_array( $author->ID, $_POST[ $key ] ) ) echo ' checked="checked" '; ?> value="<?php echo $author->ID; ?>" name="<?php echo $key; ?>[]" /> <?php echo $author->data->display_name ?></label>
							<?php
							endforeach;
						endif;
					endif; ?>
				</div>
			<?php
			elseif( $element['type'] == 'fulltext' ):
			?>
				<div class="sf-widget-fulltext-wrapper">
					<input <?php if( isset( $_POST[ $key ] ) ) echo ' value="' . $_POST[ $key ] . '" '; ?>placeholder="<?php echo $element['fieldname']; ?>" name="<?php echo $key; ?>" />
				</div>
			<?php
			elseif( $element['type'] == 'orderby' ): ?>
				<select name="orderby">
				<?php foreach( $element['orderby'] as $ek => $e ): ?>
					<option <?php if( isset( $_POST['orderby'] ) && $e == $_POST[ 'orderby' ] ) echo ' selected="selected" '; ?>value="<?php echo $e; ?>"><?php echo $element['orderbylabel'][ $ek ]; ?></option>
				<?php endforeach; ?>				
				</select>
			<?php
			elseif( $element['type'] == 'radiobox' ):
			?>
				<div class="sf-widget-radiobox-wrapper">
				<?php 
					if( $element['options'] == 'individual' ): ?>
						<?php foreach( $element['option_key'] as $option_key => $val ):
						?>
						<label><input type="radio" <?php if( isset( $_POST[ $key ] ) && $val == $_POST[ $key ] ) echo ' checked="checked" '; ?> value="<?php echo $val; ?>" name="<?php echo $key; ?>" /> <?php echo $element['option_val'][$option_key]; ?></label>
						<?php
						endforeach;
			
					elseif( $data_type == 'tax' && $element['options'] == 'auto' ):
						$args = array(
							'orderby'       => 'name', 
							'order'         => 'ASC',
							'hide_empty'    => true
						);
						$terms = get_terms( $data_value, $args );
						foreach( $terms as $term ): 
							?>
							<label><input type="radio" <?php if( isset( $_POST[ $key ] ) && $term->term_id == $_POST[ $key ] ) echo ' checked="checked" '; ?> value="<?php echo $term->term_id; ?>" name="<?php echo $key; ?>" /> <?php echo $term->name; ?></label>
							<?php 
						endforeach;
					elseif( $data_type == 'meta' && $element['options'] == 'auto' ):
						$values = get_postmeta_values( $data_value );
						foreach( $values as $val ):
						?>
						<label><input type="radio" <?php if( isset( $_POST[ $key ] ) && $val->meta_value == $_POST[ $key ] ) echo ' checked="checked" '; ?> value="<?php echo $val->meta_value; ?>" name="<?php echo $key; ?>" /> <?php echo $val->meta_value; ?></label>					
						<?php
						endforeach;
						
					elseif( $data_type == 'others' ):
						if( $data_value == 'author' ):
							$args = array(
								'who'	=>	'authors'
							);
							$authors = apply_filters( 'sf-get-authors', get_users( $args ) );
							foreach( $authors as $author ):
							?>
							<label><input type="radio" <?php if( isset( $_POST[ $key ] ) && $author->ID == $_POST[ $key ] ) echo ' checked="checked" '; ?> value="<?php echo $author->ID; ?>" name="<?php echo $key; ?>" /> <?php echo $author->data->display_name ?></label>
							<?php
							endforeach;
						endif;
					endif; ?>
				</div>
			<?php
			
			elseif( $element['type'] == 'range' ):	
					$element['posmin'] = $element['start_range'];
					$element['posmax'] = $element['end_range'];
					
					if( isset( $_POST[ $key . '|min' ] ) )
						$element['posmin'] = $_POST[ $key . '|min' ];
					if( isset( $_POST[ $key . '|max' ] ) )
						$element['posmax'] = $_POST[ $key . '|max' ];
			?>
			<div class="sf-widget-range-wrapper" data-title="<?php echo esc_attr( $element['fieldname'] ); ?>" data-step="<?php if( isset( $element['step'] ) ) echo $element['step']; else echo '1'; ?>" data-source="<?php echo $key; ?>" data-start="<?php echo $element['start_range']; ?>" data-unitfront="<?php if( isset( $element['unit_front'] ) && $element['unit_front'] == 1 ) echo '1'; else echo '0'; ?>" data-end="<?php echo $element['end_range']; ?>" data-unit="<?php echo $element['unit']; ?>" data-posmin="<?php echo $element['posmin']; ?>" data-posmax="<?php echo $element['posmax']; ?>"></div>
			<?php
			endif;
		?>
		</li>
		<?php
		endif;
		endforeach; ?>
	</ul>
			</form>
		<?php
		echo $after_widget;
		wp_reset_query();
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] =  $new_instance['title'];
		$instance['searchform'] =  $new_instance['searchform'];
		$instance['target'] =  $new_instance['target'];
		$instance['autoload'] =  $new_instance['autoload'];
		$instance['withfilter'] =  $new_instance['withfilter'];
		return $instance;
	}


	function form( $instance ) {

		$defaults = array( 'button' => __( 'Search', 'sf' ), 'title' => __( 'Search', 'sf' ) );
		$instance = wp_parse_args( (array) $instance, $defaults ); 
		$searchforms = get_option( 'sf-fields' );
		$args = array(
				'sort_order' => 'ASC',
				'sort_column' => 'post_title',
				'post_type' => 'page',
				'post_status' => 'publish'
		); 
		$pages = get_pages($args); 
		
		if( !isset( $pages ) || !is_array( $pages ) ):
			?><p><strong><?php printf( __( 'Before you are able to create a Searchform Widget, <a href="%s">you have to create a page, where the results can be shown</a>', 'sf' ), 'post-new.php?post_type=page' ); ?></strong></p><?php
		elseif( !isset( $searchforms ) || !is_array( $searchforms ) ): 
			?><p><strong><?php printf( __( 'Before you are able to create a Searchform Widget, <a href="%s">you have to create a Searchform here</a>', 'sf' ), 'admin.php?page=search-filter-new' ); ?></strong></p><?php			
		else:
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'sf' ); ?>:</label>
		</p>
		<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		
		<?php
			if( !function_exists( 'icl_get_languages' ) ):
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'searchform' ); ?>"><?php _e( 'Select Searchform', 'sf' ); ?>:</label>
		</p>
		<select id="<?php echo $this->get_field_id( 'searchform' ); ?>" name="<?php echo $this->get_field_name( 'searchform' ); ?>">
				<option></option>
				<?php foreach( $searchforms as $sf ): ?>
				<option <?php if( isset( $instance['searchform'] ) && $instance['searchform'] == $sf['name'] ) echo 'selected="selected"'; ?> value="<?php echo $sf['name']; ?>"><?php echo $sf['name']; ?></option>
				<?php endforeach; ?>
			</select>
		<p>
		<?php
			else:
			$languages = icl_get_languages('skip_missing=0&orderby=name&order=asc');
			foreach( $languages as $l ):
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'searchform' ); ?>-<?php echo $l['language_code']; ?>"><?php printf( __( 'Select Searchform for %s', 'sf' ), $l['native_name'] ); ?>:</label>
		</p>
		<select id="<?php echo $this->get_field_id( 'searchform' ); ?>-<?php echo $l['language_code']; ?>" name="<?php echo $this->get_field_name( 'searchform' ); ?>[<?php echo $l['language_code']; ?>]">
				<option></option>
				<?php foreach( $searchforms as $sf ): ?>
				<option <?php if( isset( $instance['searchform'] ) && $instance['searchform'][ $l['language_code'] ] == $sf['name'] ) echo 'selected="selected"'; ?> value="<?php echo $sf['name']; ?>"><?php echo $sf['name']; ?></option>
				<?php endforeach; ?>
			</select>
		<p>
		<?php
			endforeach;
			endif; ?>
			<label for="<?php echo $this->get_field_id( 'target' ); ?>"><?php _e( 'Search Result Page', 'sf' ); ?>:</label>
		</p>
		<select id="<?php echo $this->get_field_id( 'target' ); ?>" name="<?php echo $this->get_field_name( 'target' ); ?>">
				<option></option>
				<?php foreach( $pages as $page ): ?>
				<option <?php if( isset( $instance['target'] ) && $instance['target'] == $page->ID ) echo 'selected="selected"'; ?> value="<?php echo $page->ID; ?>"><?php echo $page->post_title; ?></option>
				<?php endforeach; ?>
			</select>
		<p>
			<label for="<?php echo $this->get_field_id( 'autoload' ); ?>"><?php _e( 'Ajax Autoload', 'sf' ); ?>:</label>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'autoload' ); ?>" <?php if( $instance['autoload'] == 1 ) echo 'checked="checked" '; ?> name="<?php echo $this->get_field_name( 'autoload' ); ?>" value="1">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'withfilter' ); ?>"><?php _e( 'Show filter on top of the search results', 'sf' ); ?>:</label>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'withfilter' ); ?>" <?php if( isset( $instance['withfilter'] ) && $instance['withfilter'] == 1 ) echo 'checked="checked" '; ?> name="<?php echo $this->get_field_name( 'withfilter' ); ?>" value="1">
		</p>
		<?php endif; ?>
		

	<?php
	}
}

?>