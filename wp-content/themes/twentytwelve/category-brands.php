<?php
/**
 *Template Name: tumbnail
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

get_header(); ?>

	<div id="primary" class="site-content">
		<div id="content" role="main">
                    <?php $recent = new WP_Query(array( 'category_name' => 'brands', 'posts_per_page' => '16' )); ?>
                    <?php while ( $recent->have_posts() ) : $recent->the_post(); ?>
                        <div id="left-block" style="float: left; margin: 15px;">
				    <?php echo get_the_post_thumbnail(null, 'thumbnail'); ?>
                            <center><h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4></center>
                        </div>
                    <?php endwhile; ?>
                    
                <div style="text-align:center;">
                    <?php posts_nav_link(' · ', 'previous page', 'next page'); ?>
                </div>
                    
                <?php wp_reset_postdata(); // end of the loop. ?>
                    
		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>