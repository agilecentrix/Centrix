<?php
/**
 * Template Name: Log In
 *
 * Allow users to log in from any page on your site.
 *
 * @package Hybrid
 * @subpackage Template
 */

if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'log-in' ) {

	global $error;
	$login = wp_login( esc_attr( $_POST['user-name'] ), esc_attr( $_POST['password'] ) );
	$login = wp_signon( array( 'user_login' => esc_attr( $_POST['user-name'] ), 'user_password' => esc_attr( $_POST['password'] ), 'remember' => esc_attr( $_POST['remember-me'] ) ), false );
}

get_header(); ?>
	<div id="login">
		<div class="hfeed content">

				<div class="<?php hybrid_entry_class(); ?>">

					<div class="entry-content">

						<?php if ( is_user_logged_in() ) : // Already logged in ?>

							<?php global $user_ID; $login = get_userdata( $user_ID ); ?>

							<p class="alert">
								<?php printf( __('You are currently logged in as <a href="%1$s" title="%2$s">%2$s</a>.', 'hybrid'), get_author_posts_url( $login->ID ), $login->display_name ); ?> <a href="<?php echo wp_logout_url( get_permalink() ); ?>" title="<?php _e('Log out of this account', 'hybrid'); ?>"><?php _e('Log out &raquo;', 'hybrid'); ?></a>
							</p><!-- .alert -->

						<?php elseif ( $login->ID ) : // Successful login ?>

							<?php $login = get_userdata( $login->ID ); ?>

							<p class="alert">
								<?php printf( __('You have successfully logged in as <a href="%1$s" title="%2$s">%2$s</a>.', 'hybrid'), get_author_posts_url( $login->ID ), $login->display_name ); ?>
							</p><!-- .alert -->

						<?php else : // Not logged in ?>

							<?php if ( $error ) : ?>
								<p class="error">
									<?php echo $error; ?>
								</p><!-- .error -->
							<?php endif; ?>

							<form action="<?php the_permalink(); ?>" method="post" class="sign-in">
								<p class="form-username">
									<label for="user-name"><?php _e('Username', 'hybrid'); ?></label>
									<input type="text" name="user-name" id="user-name" class="text-input" value="<?php echo esc_attr( $_POST['user-name'] ); ?>" />
								</p><!-- .form-username -->

								<p class="form-password">
									<label for="password"><?php _e('Password', 'hybrid'); ?></label>
									<input type="password" name="password" id="password" class="text-input" />
								</p><!-- .form-password -->

								<p class="form-submit">
									<input type="submit" name="submit" class="submit button" value="<?php _e('Log in', 'hybrid'); ?>" />
									<input class="remember-me checkbox" name="remember-me" id="remember-me" type="checkbox" checked="checked" value="forever" />
									<label for="remember-me"><?php _e('Remember me', 'hybrid'); ?></label>
									<input type="hidden" name="action" value="log-in" />
								</p><!-- .form-submit -->
							</form><!-- .sign-in -->

						<?php endif; ?>

						<?php wp_link_pages( array( 'before' => '<p class="pages">' . __('Pages:', 'hybrid'), 'after' => '</p>' ) ); ?>

					</div><!-- .entry-content -->

				</div><!-- .hentry -->


		</div><!-- .content .hfeed -->
	</div>
<?php //get_footer(); ?>