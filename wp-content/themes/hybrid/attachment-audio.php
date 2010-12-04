<?php
/**
 * Audio Attachment Template
 *
 * This audio attachment template is used when a reader is viewing a single audio attachment. 
 * Audio attachments are uploads that have a mime type of 'audio'.
 * @link http://themehybrid.com/themes/hybrid/attachments/audio
 *
 * @package Hybrid
 * @subpackage Template
 */

get_header(); ?>

	<div id="content" class="hfeed content">

		<?php hybrid_before_content(); // Before content hook ?>

		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

			<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class( 'haudio' ); ?>">

				<?php hybrid_before_entry(); // Before entry hook ?>

				<div class="entry-content">

					<?php hybrid_attachment(); ?>

					<div class="description">
						<?php the_content( sprintf( __( 'Continue reading %1$s', 'hybrid' ), the_title( ' "', '"', false ) ) ); ?>
					</div><!-- .description -->

					<p class="download">
						<a href="<?php echo wp_get_attachment_url(); ?>" title="<?php the_title_attribute(); ?>" rel="enclosure" type="<?php echo get_post_mime_type(); ?>"><?php printf( __( 'Download &quot;%1$s&quot;', 'hybrid' ), the_title( '<span class="fn">', '</span>', false) ); ?></a>
					</p><!-- .download -->

					<?php wp_link_pages( array( 'before' => '<p class="page-links pages">' . __( 'Pages:', 'hybrid' ), 'after' => '</p>' ) ); ?>

				</div><!-- .entry-content -->

				<?php hybrid_after_entry(); // After entry hook ?>

			</div><!-- .haudio .post -->

			<?php hybrid_after_singular(); // After singular hook ?>

			<?php comments_template( '/comments.php', true ); ?>

			<?php endwhile; ?>

		<?php else: ?>

			<p class="no-data">
				<?php _e( 'Apologies, but no results were found.', 'hybrid' ); ?>
			</p><!-- .no-data -->

		<?php endif; ?>

		<?php hybrid_after_content(); // After content hook ?>

	</div><!-- .content .hfeed -->

<?php get_footer(); ?>