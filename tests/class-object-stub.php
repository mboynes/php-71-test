<?php

class Object_Stub {
	public $request, $test_prop;

	public function pass_object() {
		$this->request = apply_filters_ref_array( 'passes_object', array( $this->request, &$this ) );
		return $this->test_prop;
	}

	public function pass_prop() {
		$this->request = apply_filters_ref_array( 'passes_prop', array( $this->request, &$this->test_prop ) );
		return $this->test_prop;
	}

	public function pass_var() {
		$test_var = null;
		$this->request = apply_filters_ref_array( 'passes_var', array( $this->request, &$test_var ) );
		return $test_var;
	}
}
