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

	function test_action_ref_array() {
		add_action( 'testing-action', function( &$param1, $param2, &$param3, $param4 ) {
			$param1 = true;
			$param2 = true;
			$param3 = true;
			$param4 = true;
		}, 10, 4 );

		$arg1 = false;
		$arg2 = false;
		$arg3 = false;
		$arg4 = false;

		// Only args 1 and 3 should get changed here.
		do_action_ref_array( 'testing-action', [ &$arg1, &$arg2, &$arg3, $arg4 ] );
		$this->assertTrue( $arg1 );
		$this->assertFalse( $arg2 );
		$this->assertTrue( $arg3 );
		$this->assertFalse( $arg4 );
	}

	function test_filter_ref_array() {
		add_action( 'testing-filter', function( $param1, $param2, &$param3, $param4 ) {
			$param1 = true;
			$param2 = true;
			$param3 = true;
			$param4 = true;
			return $param1;
		}, 10, 4 );

		$arg1 = false;
		$arg2 = false;
		$arg3 = false;
		$arg4 = false;

		// Only args 1 and 3 should get changed here.
		$arg1 = apply_filters_ref_array( 'testing-filter', [ $arg1, &$arg2, &$arg3, $arg4 ] );
		$this->assertTrue( $arg1 );
		$this->assertFalse( $arg2 );
		$this->assertTrue( $arg3 );
		$this->assertFalse( $arg4 );
	}

	function test_pre_get_posts() {
		$post_id = self::factory()->post->create();

		add_action( 'pre_get_posts', function( &$query ) use ( $post_id ) {
			$query->query_vars = [
				'p' => $post_id,
			];
		} );

		$observed_qvs = false;
		add_action( 'pre_get_posts', function( &$query ) use ( &$observed_qvs ) {
			$observed_qvs = $query->query_vars;
		}, 100 );

		$this->assertSame( [ $post_id ], array_column( get_posts( 'name=qqqqqqq' ), 'ID' ) );
		$this->assertSame( [ 'p' => $post_id ], $observed_qvs );
	}

	function test_posts_request() {
		add_filter( 'posts_request', function( $request, &$query ) {
			$query->post_count = 98765;
			return $request;
		}, 10, 2 );

		$test_ran = false;
		add_filter( 'posts_pre_query', function( $posts, &$query ) use ( &$test_ran ) {
			if ( 98765 !== $query->post_count ) {
				throw new \Exception( 'Failed asserting that post count matches' );
			} else {
				$test_ran = true;
			}
			return $posts;
		}, 10, 2 );

		$test_query = new \WP_Query( [ 'post_type' => 'post' ] );

		// If we made it this far, we're good.
		$this->assertTrue( $test_ran );
	}
}
