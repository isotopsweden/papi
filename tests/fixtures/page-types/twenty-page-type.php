<?php

class Twenty_Page_Type extends Papi_Page_Type {

	/**
	 * Define our Page Type meta data.
	 *
	 * @return array
	 */

	public function page_type() {
		return array(
			'name'        => 'Twenty page',
			'description' => 'This is a twenty page',
			'template'    => 'functions.php'
		);
	}

	/**
	 * Define our properties.
	 */

	public function register() {
	}
}