<?php
	$post_tmp = $_POST;
	$_POST['data'] = $_SESSION['sf'];
	$postdata = $_SESSION['sf'];
	$fields = get_option( 'sf-fields' );
	foreach( $fields as $field )
		if( $field['name'] == $_POST['data']['search-id'] )
			break;
			
?>
<!-- Search Filter: <?php echo $attr['id']; ?>-->
<?php echo $content; ?>
	<script>
		var sf_columns = <?php if( isset( $field['columns'] ) ) echo $field['columns']; else echo 1;?>;
	</script>
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
		<?php
				$results = sf_do_search( array( get_the_ID() ) );
		
		?>
	
	<div class="sf-filter">
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
	<ul class="sf-result">
		<?php
			if( isset( $results ) ):				
				foreach( $results['result'] as $r )
					echo $r;
			endif;
		?>
	</ul>	
	<?php apply_filters( 'sf-after-results', '' ); ?>
	<ul class="sf-nav">
		<?php
			if( isset( $results ) ):				
				foreach( $results['nav'] as $r )
					echo $r;
			endif;
		?>
	
	</ul>	
	<?php apply_filters( 'sf-after-navigation', '' ); ?>
</div>

		<?php
			if( isset( $results ) ):				
				?>
				<script>sf_adjust_elements_waitimg();</script>
				<?php
			endif;
			
		$_POST = $post_tmp;
		?>