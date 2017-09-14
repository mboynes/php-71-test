<?php
/**
 * Class PassByRefTest
 *
 * @package Php_71_Test
 */

/**
 * Sample test case.
 */
class PassByRefTest extends WP_UnitTestCase {
	function test_basic() {
		$arg = 'original';
		$args = [ &$arg ];
		call_user_func_array( function( &$arg ) {
			$arg = 'changed';
		}, $args );
		$this->assertSame( 'changed', $arg );
	}

	function test_pre_get_posts() {
		$post_id = self::factory()->post->create();
		add_action( 'pre_get_posts', function( &$query ) use ( $post_id ) {
			$query->query_vars = [
				'p' => $post_id,
			];
		} );

		$this->assertSame( [ $post_id ], array_column( get_posts( 'name=qqqqqqq' ), 'ID' ) );
	}
}
