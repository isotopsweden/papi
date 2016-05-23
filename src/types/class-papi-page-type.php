<?php

/**
 * Papi type that handle all psot types except attachment,
 * option data and rendering. All page types should extend
 * this class.
 */
class Papi_Page_Type extends Papi_Page_Type_Meta {

	/**
	 * Remove meta boxes.
	 *
	 * @var array
	 */
	private $remove_meta_boxes = [];

	/**
	 * Should the Page Type be displayed in WordPress admin or not?
	 *
	 * @param  string $post_type
	 *
	 * @return bool
	 */
	public function display( $post_type ) {
		return true;
	}

	/**
	 * Get post type.
	 *
	 * @return string
	 */
	public function get_post_type() {
		return papi_get_post_type();
	}

	/**
	 * Get post type supports that will be removed.
	 *
	 * @return array
	 */
	private function get_post_type_supports() {
		$supports = ['custom-fields'];

		if ( method_exists( $this, 'remove' ) ) {
			$output   = $this->remove();
			$output   = is_string( $output ) ? [$output] : $output;
			$output   = is_array( $output ) ? $output : [];
			$output   = array_filter( $output, 'is_string' );
			$supports = array_merge( $supports, $output );
		}

		$parent_class  = get_parent_class( $this );
		$parent_remove = method_exists( $parent_class, 'remove' );

		while ( $parent_remove ) {
			$parent        = new $parent_class();
			$output        = $parent->remove();
			$output        = is_string( $output ) ? [$output] : $output;
			$output        = is_array( $output ) ? $output : [];
			$output        = array_filter( $output, 'is_string' );
			$supports      = array_merge( $supports, $output );
			$parent_class  = get_parent_class( $parent_class );
			$parent_remove = method_exists( $parent_class, 'remove' );
		}

		return $supports;
	}

	/**
	 * Setup page type.
	 */
	public function setup() {
		parent::setup();

		// Remove post type support and meta boxes.
		$this->remove_post_type_support();

		// Add support for displaying information in publish box from a page type.
		if ( method_exists( $this, 'publish_box' ) ) {
			add_action( 'post_submitbox_misc_actions', [$this, 'publish_box'] );
		}

		// Hide page template dropdown if it shouldn't be showed.
		if ( ! $this->show_page_template ) {
			add_filter( 'theme_page_templates', '__return_empty_array' );
		}
	}

	/**
	 * Remove post type support action.
	 */
	public function remove_post_type_support() {
		global $_wp_post_type_features;

		$post_type = $this->get_post_type();

		if ( empty( $post_type ) ) {
			return;
		}

		$post_type_supports = $this->get_post_type_supports();

		foreach ( $post_type_supports as $key => $value ) {
			if ( is_numeric( $key ) ) {
				$key = $value;
				$value = '';
			}

			if ( isset( $_wp_post_type_features[$post_type], $_wp_post_type_features[$post_type][$key] ) ) {
				unset( $_wp_post_type_features[$post_type][$key] );
				continue;
			}

			// Add non post type support to remove meta boxes array.
			if ( empty( $value ) ) {
				$value = 'normal';
			}

			$this->remove_meta_boxes[] = [$key, $value];
		}

		add_action( 'add_meta_boxes', [$this, 'remove_meta_boxes'], 999 );
	}

	/**
	 * Remove meta boxes.
	 */
	public function remove_meta_boxes() {
		$post_type = $this->get_post_type();

		foreach ( $this->remove_meta_boxes as $item ) {
			remove_meta_box( $item[0], $post_type, $item[1] );
		}
	}
}
