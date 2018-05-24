<?php
/**
 * GDPR is not a worse user experience.
 *
 * @package GDPRIsNotAWorseUserExperience\inc
 *
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Add a query arg to the redirect URL when a comment is posted by
 * a user who did not consent to cookies.
 *
 * @since  1.0.0
 *
 * @param  string     $location The redirect URL.
 * @param  WP_Comment $comment  The comment just posted.
 * @return string               The redirect URL.
 */
function gcinawue_comment_post_redirect( $location = '', $comment = null ) {
	$cookies_consent = ( isset( $_POST['wp-comment-cookies-consent'] ) );

	if ( ! $cookies_consent && 'unapproved' === wp_get_comment_status( $comment ) ) {
		$location = add_query_arg( '_gcinawue_unapproved', $comment->comment_ID, $location );
	}

	return $location;
}
add_filter( 'comment_post_redirect', 'gcinawue_comment_post_redirect', 10, 2 );

/**
 * Adds a JavaScript trick to remove the additional query arg.
 *
 * NB: WordPress use this trick to remove the `wp_removable_query_args()`
 * from the URLs inside the Administration screens.
 *
 * @since  1.0.0
 */
function gcinawue_head() {
	if ( ! is_singular() || ! isset( $_GET['_gcinawue_unapproved'] ) ) {
		return;
	}

	$id = get_queried_object_id();

	if ( 0 === $id ) {
		return;
	}

	$url = wp_get_canonical_url( $id );

	printf( '
<script>
	if ( window.history.replaceState ) {
		var canonical = \'%1$s\';
		window.history.replaceState( null, null, canonical.split(\'#\')[0]  + window.location.hash );
	}
</script>
	', esc_url_raw( $url ) );
}
add_action( 'wp_head', 'gcinawue_head' );

/**
 * Also set the current commenter when s·he did not consent to cookies.
 *
 * This way the user will keep the same level of feedback than before. Cookies are not stored
 * as requested, but there's no valuable reason for this user to have a worse experience with
 * WordPress than any other user. It is important s·he can see that their comment was saved and
 * is awaiting moderation. Without this feedback, s·he could believe their comment was not saved
 * and could submit it again & again & again!
 *
 * @param array $comment_author_data {
 *     An array of current commenter variables.
 *
 *     @type string $comment_author       The name of the author of the comment. Default empty.
 *     @type string $comment_author_email The email address of the `$comment_author`. Default empty.
 *     @type string $comment_author_url   The URL address of the `$comment_author`. Default empty.
 * }
 * @return array An array of current commenter variables.
 */
function gcinawue_get_current_commenter( $comment_author_data = array() ) {
	if ( ! isset( $_GET['_gcinawue_unapproved'] ) || array_filter( $comment_author_data ) ) {
		return $comment_author_data;
	}

	$comment_id = (int) $_GET['_gcinawue_unapproved'];

	if ( ! $comment_id ) {
		return $comment_author_data;
	}

	$comment = get_comment( $comment_id, ARRAY_A );

	if ( isset( $comment['comment_author_email'] ) ) {
		$comment_author_data = array_intersect_key( $comment, $comment_author_data );
	}

	return $comment_author_data;
}
add_filter( 'wp_get_current_commenter', 'gcinawue_get_current_commenter', 10, 1 );

/**
 * Remove the filter on `wp_get_current_commenter` asap so that
 * the comment form inputs are emptied.
 *
 * @since  1.0.0
 */
function gcinawue_get_current_commenter_remove_filter() {
	remove_filter( 'wp_get_current_commenter', 'gcinawue_get_current_commenter', 10, 1 );
}
add_action( 'pre_get_comments', 'gcinawue_get_current_commenter_remove_filter' );

/**
 * Make sure the '_gcinawue_unapproved' query var is removed from comment reply link.
 *
 * @since  1.0.0
 *
 * @param  string     $link    The comment reply link.
 * @param  array      $args    Arguments for this link.
 * @param  WP_Comment $comment The comment object.
 * @return string              The comment reply link.
 */
function gcinawue_comment_reply_link( $link = '', $args = array(), $comment = null ) {
	if ( ! isset( $comment->comment_ID ) || ! isset( $args['respond_id'] ) ) {
		return $link;
	}

	preg_match( '/href=[\'|\"](.*?)[\"|\']\s/', $link, $matches );
	$replace = trim( end( $matches ), '\' ' );

	if ( $replace && false !== strrpos( $replace, '_gcinawue_unapproved' ) ) {
		$url = esc_url( add_query_arg( array(
			'replytocom' => $comment->comment_ID,
			'_gcinawue_unapproved' => false,
		) ) ) . "#" . $args['respond_id'];

		$link = str_replace( $replace, $url, $link );
	}

	return $link;
}
add_filter( 'comment_reply_link', 'gcinawue_comment_reply_link', 0, 3 );
