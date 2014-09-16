<?php
if( isset( $_POST['search-id' ] ) ):
	$postdata = $_POST;
	$fields = get_option( 'sf-fields' );
	foreach( $fields as $field )
		if( $field['name'] == $postdata['search-id'] )
			break;
	$_POST['data'] = $postdata;
	$data = sf_do_search();
	?>
<div class="sf-wrapper">	
	<style>
	<?php if( isset( $field['columns'] ) ): ?>
	<?php if( $field['columns'] == 2 ): ?>
	ul.sf-result > li{
		margin: 2% 0;
		margin-right: 2%;
		float: left; 
		width: 49%;
	}

	ul.sf-result > li:nth-child(2n){
		margin-right: 0;
	}

	ul.sf-result > li:nth-child(2n+1){
		clear: both;
	}
	<?php elseif( $field['columns'] == 3 ): ?>
	ul.sf-result > li{
		margin: 2% 0;
		margin-right: 2%;
		float: left; 
		width: 32%;
	}

	ul.sf-result > li:nth-child(3n){
		margin-right: 0;
	}

	
	ul.sf-result > li:nth-child(3n+1){
		clear: both;
	}
	<?php elseif( $field['columns'] == 4 ): ?>
	ul.sf-result > li{
		margin: 2% 0;
		margin-right: 2%;
		float: left; 
		width: 23.5%;
	}

	ul.sf-result > li:nth-child(4n){
		margin-right: 0;
	}

	ul.sf-result > li:nth-child(4n):after{
		clear: both;
		display: block;
		content: '';
	}
	
	ul.sf-result > li:nth-child(4n+1){
		clear: both;
	}
	<?php endif; ?>
	<?php endif; ?>
	<?php if( isset( $field['border'] ) ): ?>
	.sf-result li{
		border: 1px solid <?php echo $field['border']; ?>;
	}
	<?php endif; ?>
	<?php if( isset( $field['background'] ) ): ?>
	.sf-result li{
		background: <?php echo $field['background']; ?>;
	}
	<?php endif; ?>
	<?php if( isset( $field['highlightcolor'] ) ): ?>
	.sf-selected{
		background-color: <?php echo $field['highlightcolor']; ?>;
	}
	<?php endif; ?>
	
	ul.sf-result > li.sf-noresult{
		float: none;
		width: 100%;
		margin: 0;
	}
	
	</style>
	<script>
		var sf_columns = <?php if( isset( $field['columns'] ) ) echo $field['columns']; else echo 1;?>;
	</script>
	<div class="sf-filter">
		<?php if( defined( 'ICL_LANGUAGE_CODE' )  ):
			global $sitepress; ?>
			<input type="hidden" name="wpml" value="<?php echo $sitepress->get_current_language(); ?>" />
		<?php endif; ?>
		<?php 
		foreach( $postdata as $key => $val ):
			if( !is_array( $val ) ):?>
				<input type="hidden" name="<?php echo $key; ?>" value="<?php echo $val; ?>" /><?php 
			else: 
				foreach( $val as $v ):
					?>
					<input type="hidden" name="<?php echo $key; ?>[]" value="<?php echo $v; ?>" />
					<?php
				endforeach;			
			endif; 
		endforeach; ?>
	</div>
	<?php if( isset( $data['head'] ) ): ?>
	<div class="sf-result-head">
		<?php echo $data['head']; ?>
	</div>
	<?php apply_filters( 'sf-after-result-head', '' ); ?>
	<?php endif; ?>
	
	<ul class="sf-result">
		<?php foreach( $data['result'] as $r ) echo $r; ?>
	</ul>	
	<?php apply_filters( 'sf-after-results', '' ); ?>
	<ul class="sf-nav">
		<?php foreach( $data['nav'] as $r ) echo $r; ?>
	</ul>	
	<?php apply_filters( 'sf-after-navigation', '' ); ?>
</div>
	<script>jQuery( document ).ready( function(){ sf_adjust_elements_waitimg(); });</script>
	<?php
	
		if( isset( $data['args'] ) ):
	?><p>Debug Mode</p>
	<pre>Args:
<?php print_r( $data['args'] ); ?>
Query:
<?php print_r( $data['query'] ); ?></pre>
	<?php endif; 
endif;
?>