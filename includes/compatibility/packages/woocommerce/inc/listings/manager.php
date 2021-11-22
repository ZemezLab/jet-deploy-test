<?php

namespace Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Listings;

use Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Package;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Manager {

	/**
	 * Class constructor
	 */
	public function __construct() {

		add_action( 'jet-engine/query-builder/init', [ $this, 'init' ] );

		add_filter(
			'jet-engine/listing/data/object-fields-groups',
			[ $this, 'add_source_fields' ]
		);

		add_filter(
			'jet-engine/listings/dynamic-link/fields',
			[ $this, 'add_link_source_fields' ]
		);

		add_filter(
			'jet-engine/listings/dynamic-image/fields',
			[ $this, 'add_image_source_fields' ],
			10, 2
		);

		add_filter(
			'jet-engine/blocks-views/editor/dynamic-image/fields',
			[ $this, 'add_blocks_editor_image_source_fields' ],
			10, 2
		);

		add_filter(
			'jet-engine/listings/dynamic-image/custom-image',
			[ $this, 'custom_image_renderer' ],
			10, 2
		);

		add_filter(
			'jet-engine/listings/dynamic-image/custom-url',
			[ $this, 'custom_image_url' ],
			10, 2
		);

		add_filter(
			'jet-engine/listings/dynamic-link/custom-url',
			[ $this, 'custom_link_url' ],
			10, 2
		);

		add_filter(
			'jet-engine/listing/custom-post-id',
			[ $this, 'set_wc_queried_product_id' ],
			10, 2
		);

		add_filter(
			'jet-engine/listings/macros/current-id',
			[ $this, 'set_wc_queried_product_id' ],
			10, 2
		);

		add_filter(
			'jet-reviews/compatibility/listing/post/current-id',
			[ $this, 'set_wc_queried_product_id' ],
			10, 2
		);

		if ( $this->is_attrs_autoregister_enabled() ) {
			add_filter(
				'jet-engine/listing/data/wc-product-query/object-fields-groups',
				[ $this, 'add_attrs_autoregister_source_fields' ]
			);
		}

		add_filter(
			'jet-engine/listings/data/prop-not-found',
			[ $this, 'get_wc_product_method_with_param' ],
			10, 3
		);

		add_filter(
			'jet-engine/listings/data/get-meta/query',
			[ $this, 'get_meta' ],
			10, 2
		);

	}

	/**
	 * Initialize additional listings files.
	 */
	public function init() {
		require_once Package::instance()->package_path( 'listings/query.php' );
		new Query();
	}

	/**
	 * Returns products attributes auto-register status for dynamic tags source.
	 *
	 * @return mixed|void
	 */
	public function is_attrs_autoregister_enabled() {
		return apply_filters( 'jet-engine/listing/data/wc-product-query/autoregister-wc-attributes', true );
	}

	/**
	 * Add source fields into the dynamic field widget
	 *
	 * @param $groups
	 *
	 * @return mixed
	 */
	public function add_source_fields( $groups ) {

		$groups[] = [
			'label'   => __( 'WooCommerce', 'jet-engine' ),
			'options' => apply_filters( 'jet-engine/listing/data/wc-product-query/object-fields-groups', [
				'get_id'                       => __( 'Product ID', 'jet-engine' ),
				'get_title'                    => __( 'Title', 'jet-engine' ),
				'get_slug'                     => __( 'Product Slug', 'jet-engine' ),
				'get_type'                     => __( 'Type', 'jet-engine' ),
				'get_status'                   => __( 'Product Status', 'jet-engine' ),
				'get_sku'                      => __( 'SKU', 'jet-engine' ),
				'get_description'              => __( 'Description', 'jet-engine' ),
				'get_short_description'        => __( 'Short Description', 'jet-engine' ),
				'get_price_html'               => __( 'Price HTML String', 'jet-engine' ),
				'get_price'                    => __( 'Plain Price', 'jet-engine' ),
				'get_regular_price'            => __( 'Plain Regular Price', 'jet-engine' ),
				'get_sale_price'               => __( 'Plain Sale Price', 'jet-engine' ),
				'get_stock_status'             => __( 'Stock Status', 'jet-engine' ),
				'get_stock_quantity'           => __( 'Stock Quantity', 'jet-engine' ),
				'wc_get_product_category_list' => __( 'Categories', 'jet-engine' ),
				'wc_get_product_tag_list'      => __( 'Tags', 'jet-engine' ),
				'get_average_rating'           => __( 'Average Rating', 'jet-engine' ),
				'get_review_count'             => __( 'Review Count', 'jet-engine' ),
				'get_total_sales'              => __( 'Total Sales', 'jet-engine' ),
				'get_date_on_sale_from'        => __( 'Date on Sale from', 'jet-engine' ),
				'get_date_on_sale_to'          => __( 'Date on Sale to', 'jet-engine' ),
				'get_height'                   => __( 'Height', 'jet-engine' ),
				'get_length'                   => __( 'Length', 'jet-engine' ),
				'get_weight'                   => __( 'Weight', 'jet-engine' ),
				'get_width'                    => __( 'Width', 'jet-engine' ),
				'get_max_purchase_quantity'    => __( 'Max Purchase Quantity', 'jet-engine' ),
				'get_tax_status'               => __( 'Tax Status', 'jet-engine' ),
			] ),
		];

		return $groups;

	}

	/**
	 * Add product attributes auto-register source fields into the dynamic field widget
	 *
	 * @param $fields
	 *
	 * @return mixed
	 */
	public function add_attrs_autoregister_source_fields( $fields ) {

		$attributes = wc_get_attribute_taxonomies();

		if ( ! empty( $attributes ) ) {
			foreach ( $attributes as $attribute ) {
				$fields[ 'wc_attr::' . $attribute->attribute_name ] = __( 'Product attr: ', 'jet-engine' ) . $attribute->attribute_label;
			}
		}

		return $fields;

	}

	/**
	 * Handle and return WC_Product class method with parameters.
	 *
	 * @param $result
	 * @param $prop
	 * @param $object
	 *
	 * @return false|mixed|string
	 */
	public function get_wc_product_method_with_param( $result, $prop, $object ) {

		if ( $object && is_callable( $prop ) && is_a( $object, 'WC_Product' ) ) {
			if ( 'wc_get_product_category_list' === $prop || 'wc_get_product_tag_list' === $prop ) {
				$result = call_user_func( $prop, $object->get_id() );
			}
		}

		if ( $this->is_attrs_autoregister_enabled() ) {
			if ( false !== strpos( $prop, 'wc_attr::' ) && is_callable( [ $object, 'get_attribute' ] ) ) {
				$result = $object->get_attribute( str_replace( 'wc_attr::', '', $prop ) );
			}
		}

		return $result;

	}

	/**
	 * Add source fields into the dynamic link widget
	 *
	 * @param $groups
	 *
	 * @return mixed
	 */
	public function add_link_source_fields( $groups ) {

		$groups[] = [
			'label'   => __( 'WooCommerce', 'jet-engine' ),
			'options' => [
				'get_permalink' => __( 'Permalink', 'jet-engine' ),
			],
		];

		return $groups;

	}

	/**
	 * Add source fields into the dynamic image widget
	 *
	 * @param $groups
	 *
	 * @return mixed
	 */
	public function add_image_source_fields( $groups, $for ) {

		if ( 'media' === $for ) {
			$groups[] = [
				'label'   => __( 'WooCommerce', 'jet-engine' ),
				'options' => [
					'get_image' => __( 'Featured Image', 'jet-engine' ),
				],
			];
		} else {
			$groups[] = [
				'label'   => __( 'WooCommerce', 'jet-engine' ),
				'options' => [
					'get_permalink' => __( 'Permalink', 'jet-engine' ),
				],
			];
		}

		return $groups;

	}

	/**
	 * Add source fields into the blocks editor dynamic image widget
	 *
	 * @param $groups
	 *
	 * @return mixed
	 */
	public function add_blocks_editor_image_source_fields( $groups, $for ) {

		if ( 'media' === $for ) {
			$groups[] = [
				'label' => __( 'WooCommerce', 'jet-engine' ),
				'values' => [
					[
						'value' => 'get_image',
						'label' => __( 'Featured Image', 'jet-engine' ),
					],
				],
			];
		} else {
			$groups[] = [
				'label' => __( 'WooCommerce', 'jet-engine' ),
				'values' => [
					[
						'value' => 'get_permalink',
						'label' => __( 'Permalink', 'jet-engine' ),
					],
				],
			];
		}

		return $groups;

	}

	/**
	 * Custom image renderer for custom content type
	 *
	 * @param       $result
	 * @param       $settings
	 *
	 * @return false|string
	 */
	public function custom_image_renderer( $result, $settings ) {

		$current_object = jet_engine()->listings->data->get_current_object();
		$size           = isset( $settings['dynamic_image_size'] ) ? $settings['dynamic_image_size'] : 'full';

		if ( ! isset( $current_object ) ) {
			return $result;
		}

		$image = isset( $settings['dynamic_image_source'] ) ? $settings['dynamic_image_source'] : '';

		if ( ! $image ) {
			return $result;
		}

		if ( is_callable( [ $current_object, $image ] ) ) {
			ob_start();
			echo call_user_func( [ $current_object, $image ], $size );
			return ob_get_clean();
		}

		return $result;

	}

	/**
	 * Returns custom URL for the dynamic image
	 *
	 * @param $result
	 * @param $settings
	 *
	 * @return false|mixed|string
	 */
	public function custom_image_url( $result, $settings ) {

		$url = $this->get_custom_link_by_setting( 'image_link_source', $settings );

		if ( ! $url ) {
			return $result;
		} else {
			return $url;
		}

	}

	/**
	 * Returns custom URL for dynamic link widget
	 *
	 * @param $result
	 * @param $settings
	 *
	 * @return false|mixed|string
	 */
	public function custom_link_url( $result, $settings ) {

		$url = $this->get_custom_link_by_setting( 'dynamic_link_source', $settings );

		if ( ! $url ) {
			return $result;
		} else {
			return $url;
		}

	}

	/**
	 * Returns custom value from dynamic prop by setting
	 *
	 * @param $setting
	 * @param $settings
	 *
	 * @return false|string|\WP_Error
	 */
	public function get_custom_link_by_setting( $setting, $settings ) {

		$current_object = jet_engine()->listings->data->get_current_object();

		if ( ! isset( $current_object ) ) {
			return false;
		}

		$link   = isset( $settings[ $setting ] ) ? $settings[ $setting ] : '';
		$result = false;

		if ( ! $link ) {
			return $result;
		}

		if ( is_callable( [ $current_object, $link ] ) ) {
			$result = call_user_func( [ $current_object, $link ] );
		}

		return $result;

	}

	/**
	 * Set correct products id after `WC_Product_Query` for post loop output.
	 *
	 * @param $id
	 * @param $object
	 *
	 * @return mixed
	 */
	public function set_wc_queried_product_id( $id, $object ) {

		if ( $object && is_a( $object, 'WC_Product' ) ) {
			$id = $object->get_id();
		}

		return $id;

	}

	/**
	 * Returns `WC_Product_Query` current meta
	 *
	 * @param $value
	 * @param $key
	 *
	 * @return mixed
	 */
	public function get_meta( $value, $key ) {

		$object = jet_engine()->listings->data->get_current_object();

		if ( $object && is_a( $object, 'WC_Product' ) ) {
			if ( jet_engine()->relations->is_relation_key( $key ) ) {
				$single = false;
			} else {
				$single = true;
			}

			return get_post_meta( $object->get_id(), $key, $single );
		} else {
			return $value;
		}

	}

}
