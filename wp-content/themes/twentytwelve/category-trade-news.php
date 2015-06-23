<?php
/**
 * The template for displaying Category pages
 *
 * Used to display archive-type pages for posts in a category.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

get_header(); ?>

<section id="primary" class="site-content">
    <div id="content" role="main">

    <?php if ( have_posts() ) : ?>
            <header class="archive-header">
                <div>
                    <h1 class="archive-title"><!--span><img src="/wp-content/uploads/2015/05/news_1_r.jpg" alt="News" width="150px"></span-->
                        <?php printf( __( '%s', 'twentytwelve' ), '<span>' . single_cat_title( '', false ) . '</span>' ); ?></h1>
                </div>

            <?php if ( category_description() ) : // Show an optional category description ?>
                    <div class="archive-meta"><?php echo category_description(); ?></div>
            <?php endif; ?>

            </header><!-- .archive-header -->
            
        <div class="container-fluid" style="margin: 0px; padding: 0px;">    
            <div class="row-fluid">
                <div class="span12">
                    <div class="news-wrapper">
                        <ul class="news-list">
                            
                        <?php
                        /* Start the Loop */
                        while ( have_posts() ) : the_post(); ?>
                            <li>
                                <div class="news-header">
                                    <span class="pull-left">
                                        <time datetime="<?php the_time('Y-m-d') ?>"><?php the_time('d.m.Y') ?></time>
                                    </span>
                                    <div class="clearfix"></div>
                                </div><!-- .news-header -->
                                <h4>
                                    <a href="<?php the_permalink() ?>" rel="bookmark" title="Постоянная ссылка <?php the_title(); ?>"><?php the_title(); ?></a>
                                </h4>
                                <div class="teaser-text">
                                    <span class="pull-left">
                                        <?php if ( has_post_thumbnail()) { 
                                            the_post_thumbnail('thumbnail');
                                        } ?>
                                    </span>
                                    <p>
                                        <?php the_excerpt(); //the_content('Читать дальше  »'); ?>
                                    </p>
                                    <div class="clearfix"></div>
                                </div><!-- .teaser-text -->
                                <div class="news-more">
                                    <span class="pull-left">
                                        <i><a href="<?php the_permalink(); ?>">Смотреть полностью......</a></i>
                                    </span>
                                    <span class="news-list-author pull-right">
                                        <!--Опубликовал <?php //the_author() ?>-->
                                    </span>
                                    <div class="clearfix"></div>
                                </div><!-- .news-more -->
                            </li>
                                    
                        <?php endwhile; ?>
                        </ul>
                    </div>
                </div><!-- .span9 -->
            </div><!-- .row-fluid -->
        </div><!-- .container-fluid -->
            <?php
            twentytwelve_content_nav( 'nav-below' );
            ?>

    <?php else : ?>
            <?php get_template_part( 'content', 'none' ); ?>
    <?php endif; ?>

    </div><!-- #content -->
</section><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>