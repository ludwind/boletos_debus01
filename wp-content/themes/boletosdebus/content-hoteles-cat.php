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


<li><a href="<?php the_permalink(); ?>">
<?php the_post_thumbnail( 'bdb-ofertasimg', array( 'class' => 'featured-img' ) ); ?>
<article><h2><?php the_title(); ?></h2>	

<?php $content = get_the_content(); $trimmed_content = wp_trim_words( $content, 15 ); ?>
<p><?php echo $trimmed_content; ?></p>
<h3>desde $<?php the_field('precio_desde'); ?> / noche</h3>	
</article>
<div class="viajarahora">Ver m&aacute;s<span>></span></div>
</a></li>

        
    
