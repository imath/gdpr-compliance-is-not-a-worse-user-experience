<?php

/**
 * @group functions
 */
class GCINAWUE_Functions extends WP_UnitTestCase {

	public function test_gcinawue_comment_reply_link() {
		$reset = $_SERVER['REQUEST_URI'];

		$p = $this->factory->post->create();
		$c = $this->factory->comment->create_and_get( array( 'comment_post_ID' => $p ) );

		$url = get_comment_link( $c );

		$_SERVER['REQUEST_URI'] = str_replace( '#' . parse_url( $url, PHP_URL_FRAGMENT ), '', $url );

		$expected = get_comment_reply_link( array( 'depth' => 1, 'max_depth' => 2 ), $c );

		$_SERVER['REQUEST_URI'] = add_query_arg( array(
			'_gcinawue_unapproved' => $c->comment_ID
		) );

		$tested = get_comment_reply_link( array( 'depth' => 1, 'max_depth' => 2 ), $c );

		$_SERVER['REQUEST_URI'] = $reset;

		$this->assertEquals( $tested, $expected );
	}
}
