<?php
/**
 * Home Template
 *
 * This template is loaded when on the home/blog page.
 *
 * @package Hybrid
 * @subpackage Template
 */

get_header(); ?>

<div class="column narrow topbar-spacer group" id="left-column">
    <?php dynamic_sidebar( 'left-wiget-container' ); ?>
</div>
<div class="column group" id="main-column">
    <?php
            $wp_query = new WP_Query();
            $wp_query->query( array( 'posts_per_page' => get_option( 'posts_per_page' ), 'paged' => $paged ) );
            $more = 0;
    ?>
    <?php if ( $wp_query->have_posts() ) : while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
      

    <div id="story-list-container" class="list-container">
        <div class="story-list inner-container topbar-spacer" id="story-items" style="width: 520px; ">
            <div class="stories" data-digg-hashstate="#filter:recent;mediaType:media;topic:all;page:1">
                <div class="story-item item-20101125185111_01742c61-5fce-492d-84bd-ce55cfd55339 no-media first-item group">
                    <div class="story-item-gutters group">
                        <div class="story-item-diggbtn">
                            <?php do_action('vote');?>
                        </div>
                        <div class="story-item-content group">
                            <div class="story-item-details">
                                <h3 class="story-item-title">
                                    <a href="http://digg.com/story/r/new_study_reveals_how_cannabis_suppresses_immune_functions" target="_blank">
                                       <?php the_title();?>
                                    </a>
                                </h3>
                                <p class="story-item-description">
                                    <a href="http://digg.com/search?q=site:physorg.com" class="story-item-source">
                                        physorg.com
                                    </a>
                                   <?php the_content( sprintf( __( 'Continue reading %1$s', 'hybrid' ), the_title( ' "', '"', false ) ) ); ?>
                              
                                </p>
                            </div>
                            <ul class="story-item-meta group">
                                <li class="story-item-submitter">
                                    via
                                    <a href="http://digg.com/leaprinces" class="hcard-trigger" data-digg-username="leaprinces">
                                        leaprinces
                                    </a>
                                </li>
                                <li class="story-item-comments">
                                    <a href="http://digg.com/news/science/new_study_reveals_how_cannabis_suppresses_immune_functions">
                                        <span class="storylist-icon comments dib"></span><span class="storylist-item-comment-count">32</span> Comments
                                    </a>
                                </li>
                                <li class="story-item-save">
                                    <a class="action-save has-tooltip" title="Save">
                                        <span class="storylist-icon save dib"></span><span class="action-label">Save</span>
                                    </a>
                                </li>
                                <li class="story-item-bury">
                                    <a class="action-bury has-tooltip">
                                        <span class="storylist-icon bury dib"></span><span class="action-label">Bury</span>
                                    </a>
                                </li>
                            </ul>
                            <div class="inline-comment-container">
                            </div>
                            <div class="story-item-activity group hidden">
                            </div>
                        </div>
                        <div class="story-item-media">
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
            <?php endwhile; ?>

            <?php hybrid_after_singular(); // After singular hook ?>

    <?php else: ?>

            <p class="no-data">
                    <?php _e( 'Apologies, but no results were found.', 'hybrid' ); ?>
            </p><!-- .no-data -->

    <?php endif; ?>


    <div id="inline-comment-container-home">
    </div>
</div>

</div>

<div class="column wide group" id="right-column">
    <?php dynamic_sidebar( 'right-wiget-container' ); ?>
</div>

<?php get_footer(); ?>