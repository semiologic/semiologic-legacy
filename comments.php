<?php
#
# DO NOT EDIT THIS FILE
# ---------------------
# The Semiologic theme features a custom.php feature. This allows to hook into
# the template without editing its php files. That way, you won't need to worry
# about losing your changes when you upgrade your site.
#
# You'll find detailed sample files in the custom-samples folder
#

global $comments_captions;
global $sem_options;


if ( $post->post_password !== ''
	&& $_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password
	)
{
	echo '<p>'
		. __('Password Protected', 'sem-theme')
		. '</p>';

	return;
}

#
# Displaytrackback uri
#

if ( $sem_options['show_trackback_uri'] )
{
	display_trackback_uri();
}

#
# Display comments
#

if ( $comments )
{
	echo '<div id="comments" class="comments">' . "\n";

	$title = the_title('', '', false);

	$caption = $comments_captions['comments_on'];
	$caption = sprintf($caption, $title);

	if ( comments_open() )
	{

		$comment_form_link = ' <span class="comment_entry">'
			. '<a href="#postcomment" title="' . esc_attr($comments_captions['leave_comment']) . '">'
			. '&raquo;'
			. '</a>'
			. '</span>';
	}
	else
	{
		$comment_form_link = false;
	}

	echo '<h2>' . $caption . $comment_form_link . '</h2>' . "\n";

	foreach ( (array) $comments as $comment )
	{
		echo '<div id="comment-' . get_comment_ID() . '" class="comment">' . "\n";


		do_action('display_comment');


		echo '<div class="comment_header">' . "\n";

		$cur_date = get_comment_date();

		if ( !isset($prev_date) || $cur_date != $prev_date )
		{
			$prev_date = $cur_date;
			echo '<div class="comment_date">' . $cur_date . '</div>' . "\n";
		}

		echo '<h3>'
			. '<span class="comment_author">'
				. '<span class="avatar">'
					. get_avatar( $comment, 32 )
					. '</span>'
				. ' '
				. get_comment_author_link()
				. '</span>'
			. ' @ '
			. '<span class="comment_time">'
			. get_comment_date(__('g:i a', 'sem-theme'))
			. '</span>'
			. comment_type('', ' (' . __('Trackback', 'sem-theme') . ')', ' (' . __('Pingback', 'sem-theme') . ')')
			. '</h3>' . "\n";

		echo '</div>' . "\n";


		echo '<div class="comment_body">' . "\n"
			. apply_filters('comment_text', get_comment_text())
			. '</div>' . "\n";


		echo '<div class="spacer"></div>';


		echo '<div class="comment_actions">' . "\n";

		if ( $sem_options['show_comment_permalink'] )
		{
			echo '<span class="comment_action link_comment">'
				. '<a href="#comment-'. get_comment_ID() . '">'
				. $comments_captions['comment_permalink']
				. '</a>'
				. '</span>' . "\n";
		}

		if ( comments_open() )
		{
			echo '<span class="comment_action reply_comment">'
			. '<a href="#postcomment">'
			. $comments_captions['reply_link']
			. '</a>'
			. '</span>' . "\n";
		}

		edit_comment_link(__('Edit', 'sem-theme'), '<span class="comment_action admin_link edit_comment">', '</span>' . "\n");

		echo '</div>' . "\n";


		echo '</div> <!-- #comment -->' . "\n";
	} # foreach $comments as $comment

	echo '</div><!-- #comments -->' . "\n";
} # if $comments


#
# Display comment form
#

if ( comments_open() && !( isset($_GET['action']) && $_GET['action'] == 'print' ) )
{
	echo '<div id="comment_form" class="comment_form">' . "\n"
		. '<h2 id="postcomment">'
		. $comments_captions['leave_comment']
		. '</h2>' . "\n";


	if ( get_option('comment_registration') && !$user_ID )
	{
		$login_url = '<span class="logout">'
			. apply_filters('loginout',
				'<a href="' . esc_url(wp_login_url(apply_filters('the_permalink', get_permalink()))) . '">' . __('Login', 'sem-theme') . '</a>'
				)
			. '</span>';

		echo '<p>'
			. sprintf($comments_captions['login_required'], $login_url)
			. '</p>' . "\n";
	}
	else
	{
		echo '<form method="post" id="commentform"'
			. ' action="' . trailingslashit(site_url()) . 'wp-comments-post.php"'
			. '>' . "\n";

		if ( $user_ID )
		{
			$logout_url = '<span class="logout">'
				. apply_filters('loginout',
					'<a href="' . esc_url(wp_logout_url(apply_filters('the_permalink', get_permalink()))) . '">' . __('Logout', 'sem-theme') . '</a>'
					)
				. '</span>';
			
			$identity = '<span class="comment_author">'
				. '<a href="' . trailingslashit(site_url()) . 'wp-admin/profile.php">'
				. $user_identity
				. '</a>'
				. '</span>';

			echo '<p>'
				. sprintf($comments_captions['logged_in_as'], $identity, $logout_url)
				. '</p>' . "\n";
		}
		else
		{
			echo '<p>'
				. '<label for="author">'
				. $comments_captions['name_field']
				. ( $req
					? ( ' (*)' )
					: ''
					)
				. '<br />'
				. '<input type="text" name="author" id="author"'
					. ' value="' . esc_attr($comment_author) . '" />'
				. '</label>'
				. '</p>' . "\n";

			echo '<p>'
				. '<label for="email">'
				. $comments_captions['email_field']
				. ( $req
					? ( ' (*)' )
					: ''
					)
				. '<br />'
				. '<input type="text" name="email" id="email"'
					. ' value="' . esc_attr($comment_author_email) . '" />'
				. '</label>'
				. '</p>' . "\n";


			echo '<p>'
				. '<label for="url">'
				. $comments_captions['url_field']
				. '<br />'
				. '<input type="text" name="url" id="url"'
					. ' value="' . esc_attr($comment_author_url) . '" />'
				. '</label>'
				. '</p>' . "\n";
		} # if ( $user_ID )
 		
		# WP Review Site support
		
		if ( function_exists('ratings_input_table') && has_filter('comment_form', 'ratings_input_table') ) {
			remove_action('comment_form', 'ratings_input_table');
			ratings_input_table();
		}	
		
		echo '<textarea name="comment" id="comment" cols="48" rows="10"></textarea>' . "\n";

		if ( !$user_ID && $req )
			echo '<p>'
				.  $comments_captions['required_fields']
				. '</p>' . "\n";
		
		echo '<p>'
			. '<input name="submit" type="submit" id="submit"'
				. ' value="' . esc_attr($comments_captions['submit_field']) . '"'
				. ' />'
			. '</p>' . "\n";

		do_action('comment_form', $post->ID);

		echo '<input type="hidden" name="comment_post_ID" value="' . $post->ID . '" />' . "\n"
			. '</form>' . "\n";

		if ( function_exists('show_manual_subscription_form') )
		{
			show_manual_subscription_form();
		}
	} # get_option('comment_registration') && !$user_ID

	echo '</div><!-- #commentform -->' . "\n";
} # comments_open()
?>