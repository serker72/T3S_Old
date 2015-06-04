<?php
/**
 * The Template for displaying all single posts
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

get_header(); ?>

	<div id="primary" class="site-content">
		<div id="content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
                                <?php $cat = get_the_category(); ?>
                    
				<?php get_template_part( 'content', 'news' ); ?>

				<nav class="nav-single">
                                    <h3 class="assistive-text"><a href="<?php echo get_term_link( (int)$cat[0]->term_id, $cat[0]->taxonomy ); ?>"><?php echo $cat[0]->cat_name; ?></a></h3>
				</nav><!-- .nav-single -->

				<?php //comments_template( '', true ); ?>

			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>