<?php
//New results loop
$acps_results = new WP_Query( $this->acps_args );
$template = get_option('template');

//Standard loop
if( $acps_results->have_posts() ): ?>

<header class="page-header">
    <h1 class="page-title"><?php _e('Search Results','acps');?></h1>
</header>
    
<?php while( $acps_results->have_posts() ) : $acps_results->the_post(); ?>
    
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	
	<?php if( has_post_thumbnail() ): ?>
        <?php if( $template != 'twentyfourteen' ): ?>
        <div class="entry-header">
        <?php endif; ?>
       		<div class="entry-thumbnail">
                <a class="post-thumbnail" href="<?php the_permalink(); ?>">
                    <?php the_post_thumbnail(); ?>
                </a>
           </div>
        <?php if( $template != 'twentyfourteen' ): ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>

	<?php if( $template == 'twentyfourteen' ): ?>
		<header class="entry-header">
    <?php endif; ?>
    
    <div class="entry-meta">
		<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		
        if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) :
		?>
		
        	<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'acps' ), __( '1 Comment', 'twentyfourteen' ), __( '% Comments', 'twentyfourteen' ) ); ?></span>
		<?php
		endif;
			
		edit_post_link( __( 'Edit', 'acps' ), '<span class="edit-link">', '</span>' ); ?>
    </div>
        
	<?php if( $template == 'twentyfourteen' ): ?>
		</header>
    <?php endif; ?>

	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div>
    
	<?php the_tags( '<footer class="entry-meta"><span class="tag-links">', '', '</span></footer>' ); ?>
    
	</article>

	<?php
	endwhile;
	else:
	?>
	
    <header class="page-header">
        <h1 class="page-title"><?php _e( 'Nothing Found', 'acps' ); ?></h1>
    </header>
    
    <div class="page-content">
        <p><?php _e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'acps' ); ?></p>
        <?php get_search_form(); ?>
    </div>

<?php
endif;
?>