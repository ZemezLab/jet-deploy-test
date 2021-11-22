<?php
/**
 * Elementor views manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Blocks_Views_Editor' ) ) {

	/**
	 * Define Jet_Engine_Blocks_Views_Editor class
	 */
	class Jet_Engine_Blocks_Views_Editor {

		public function __construct() {

			add_action( 'enqueue_block_editor_assets', array( $this, 'blocks_assets' ), -1 );

			add_action( 'add_meta_boxes', array( $this, 'add_css_meta_box' ) );
			add_action( 'save_post', array( $this, 'save_meta' ) );

		}

		public function save_meta( $post_id ) {

			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			if ( isset( $_POST['_jet_engine_listing_css'] ) ) {
				$css = esc_attr( $_POST['_jet_engine_listing_css'] );
				update_post_meta( $post_id, '_jet_engine_listing_css', $css );
			}

			$settings_keys = array(
				'jet_engine_listing_post_type',
				'jet_engine_listing_tax',
			);

			$settings_to_store    = array();
			$el_settings_to_store = array();

			foreach ( $settings_keys as $key ) {
				if ( isset( $_POST[ $key ] ) ) {
					$store_key = str_ireplace( 'jet_engine_listing_', '', $key );
					$settings_to_store[ $store_key ] = esc_attr( $_POST[ $key ] );
					$el_settings_to_store[ 'listing_' . $store_key ] = esc_attr( $_POST[ $key ] );
				}
			}

			if ( ! empty( $settings_to_store ) ) {

				$listing_settings = get_post_meta( $post_id, '_listing_data', true );
				$elementor_page_settings = get_post_meta( $post_id, '_elementor_page_settings', true );

				if ( empty( $listing_settings ) ) {
					$listing_settings = array();
				}

				if ( empty( $elementor_page_settings ) ) {
					$elementor_page_settings = array();
				}

				$listing_settings        = array_merge( $listing_settings, $settings_to_store );
				$elementor_page_settings = array_merge( $elementor_page_settings, $el_settings_to_store );

				update_post_meta( $post_id, '_listing_data', $listing_settings );
				update_post_meta( $post_id, '_elementor_page_settings', $elementor_page_settings );

			}

			do_action( 'jet-engine/blocks/editor/save-settings', $post_id );

		}

		/**
		 * Add listing item CSS metabox
		 */
		public function add_css_meta_box() {

			add_meta_box(
				'jet_engine_lisitng_settings',
				__( 'Listing Item Settings', 'jet-engine' ),
				array( $this, 'render_settings_box' ),
				jet_engine()->listings->post_type->slug(),
				'side'
			);

			add_meta_box(
				'jet_engine_lisitng_css',
				__( 'Listing Items CSS', 'jet-engine' ),
				array( $this, 'render_css_box' ),
				jet_engine()->listings->post_type->slug(),
				'side'
			);

		}

		/**
		 * Render box settings HTML
		 *
		 * @return [type] [description]
		 */
		public function render_settings_box( $post ) {

			$settings = get_post_meta( $post->ID, '_listing_data', true );

			if ( empty( $settings ) ) {
				$settings = array();
			}

			$source   = ! empty( $settings['source'] ) ? $settings['source'] : 'posts';
			$choices  = array();
			$selected = false;
			$label    = null;
			$name     = null;

			switch ( $source ) {
				case 'posts':
				case 'repeater':

					$selected = ! empty( $settings['post_type'] ) ? $settings['post_type'] : 'post';
					$choices  = jet_engine()->listings->get_post_types_for_options();
					$label    = __( 'Listing Post Type', 'jet-engine' );
					$name     = 'jet_engine_listing_post_type';
					break;

				case 'terms':
					$selected = ! empty( $settings['tax'] ) ? $settings['tax'] : 'category';
					$choices  = jet_engine()->listings->get_taxonomies_for_options();
					$label    = __( 'Listing Taxonomy', 'jet-engine' );
					$name     = 'jet_engine_listing_tax';
					break;

			}

			echo '<style>
				.jet-engine-base-control select {
					box-sizing: border-box;
					margin: 0 0 5px;
				}
				.jet-engine-base-control .components-base-control__label {
					display: block;
					font-weight: bold;
					padding: 0 0 5px;
				}
			</style>';
			echo '<div class="components-base-control jet-engine-base-control">';

				if ( ! empty( $choices ) ) {
					echo '<div class="components-base-control__field">';
						echo '<label class="components-base-control__label" for="' . $name . '">';
							echo $label;
						echo '</label>';
						echo '<select id="' . $name . '" name="' . $name . '" class="components-select-control__input">';
						foreach ( $choices as $key => $value ) {
							printf( '<option value="%1$s"%3$s>%2$s</option>',
								$key,
								$value,
								selected( $selected, $key, false )
							);
						}
						echo '</select>';
					echo '</div>';
				}

				do_action( 'jet-engine/blocks/editor/settings-meta-box', $post );

				echo '<p>';
					_e( 'You need to reload page after saving to apply new settings', 'jet-engine' );
				echo '</p>';
			echo '</div>';

		}

		/**
		 * Render CSS metabox
		 *
		 * @return [type] [description]
		 */
		public function render_css_box( $post ) {

			$css = get_post_meta( $post->ID, '_jet_engine_listing_css', true );

			if ( ! $css ) {
				$css = '';
			}

			?>
			<div class="jet-eingine-listing-css">
				<p><?php
					_e( 'When targeting your specific element, add <code>selector</code> before the tags and classes you want to exclusively target, i.e: <code>selector a { color: red;}</code>', 'jet-engine' );
				?></p>
				<textarea class="components-textarea-control__input jet_engine_listing_css" name="_jet_engine_listing_css" rows="16" style="width:100%"><?php
					echo $css;
				?></textarea>
			</div>
			<?php

		}

		/**
		 * Get meta fields for post type
		 *
		 * @return array
		 */
		public function get_meta_fields() {

			if ( jet_engine()->meta_boxes ) {
				return jet_engine()->meta_boxes->get_fields_for_select( 'plain', 'blocks' );
			} else {
				return array();
			}

		}

		/**
		 * Get meta fields for post type
		 *
		 * @return array
		 */
		public function get_repeater_fields() {

			if ( jet_engine()->meta_boxes ) {
				$groups = jet_engine()->meta_boxes->get_fields_for_select( 'repeater', 'blocks' );
			} else {
				$groups = array();
			}

			if ( jet_engine()->options_pages ) {
				$groups[] = array(
					'label'  => __( 'Other', 'jet-engine' ),
					'values' => array(
						array(
							'value' => 'options_page',
							'label' => __( 'Options' ),
						),
					),
				);
			}

			return $groups;

		}

		/**
		 * Get meta fields for post type
		 *
		 * @return array
		 */
		public function get_dynamic_sources( $for = 'media' ) {

			if ( 'media' === $for ) {

				$default = array(
					'label'  => __( 'General', 'jet-engine' ),
					'values' => array(
						array(
							'value' => 'post_thumbnail',
							'label' => __( 'Post thumbnail', 'jet-engine' ),
						),
						array(
							'value' => 'user_avatar',
							'label' => __( 'User avatar (works only for user listing and pages)', 'jet-engine' ),
						),
					),
				);

			} else {

				$default = array(
					'label'  => __( 'General', 'jet-engine' ),
					'values' => array(
						array(
							'value' => '_permalink',
							'label' => __( 'Permalink', 'jet-engine' ),
						),
						array(
							'value' => 'delete_post_link',
							'label' => __( 'Delete current post link', 'jet-engine' ),
						),
					),
				);

				if ( jet_engine()->modules->is_module_active( 'profile-builder' ) ) {
					$default['values'][] = array(
						'value' => 'profile_page',
						'label' => __( 'Profile Page', 'jet-engine' ),
					);
				}

			}

			$result      = array();
			$meta_fields = array();

			if ( jet_engine()->meta_boxes ) {
				$meta_fields = jet_engine()->meta_boxes->get_fields_for_select( $for, 'blocks' );
			}

			if ( jet_engine()->options_pages ) {
				$default['values'][] = array(
					'value' => 'options_page',
					'label' => __( 'Options', 'jet-engine' ),
				);
			}

			$result = apply_filters(
				'jet-engine/blocks-views/editor/dynamic-image/fields',
				array_merge( array( $default ), $meta_fields ),
				$for
			);

			$extra_fields = apply_filters( 'jet-engine/listings/dynamic-image/fields', array(), $for );

			if ( ! empty( $extra_fields ) ) {
				foreach ( $extra_fields as $data ) {

					if ( ! is_array( $data ) ) {
						continue;
					}

					$values = array();

					if ( ! empty( $data['options'] ) ) {
						foreach ( $data['options'] as $val => $label ) {
							$values[] = array(
								'value' => $val,
								'label' => $label,
							);
						}
					}

					$result[] = array(
						'label'  => $data['label'],
						'values' => $values,
					);
				}
			}

			return $result;

		}

		/**
		 * Get registered options fields
		 *
		 * @return array
		 */
		public function get_options_fields( $type = 'plain' ) {
			if ( jet_engine()->options_pages ) {
				return jet_engine()->options_pages->get_options_for_select( $type, 'blocks' );
			} else {
				return array();
			}
		}

		/**
		 * Returns filter callbacks list
		 *
		 * @return [type] [description]
		 */
		public function get_filter_callbacks() {

			$callbacks = jet_engine()->listings->get_allowed_callbacks();
			$result    = array( array(
				'value' => '',
				'label' => '--',
			) );

			foreach ( $callbacks as $function => $label ) {
				$result[] = array(
					'value' => $function,
					'label' => $label,
				);
			}

			return $result;

		}

		public function get_filter_callbacks_args() {

			$result     = array();
			$disallowed = array( 'checklist_divider_color' );

			foreach ( jet_engine()->listings->get_callbacks_args() as $key => $args ) {

				if ( in_array( $key, $disallowed ) ) {
					continue;
				}

				$args['prop'] = $key;

				if ( ! empty( $args['description'] ) ) {
					$args['description'] = wp_strip_all_tags( $args['description'] );
				}

				if ( 'select' === $args['type'] ) {

					$options = $args['options'];
					$args['options'] = array();

					foreach ( $options as $value => $label ) {
						$args['options'][] = array(
							'value' => $value,
							'label' => $label,
						);
					}
				}

				$args['condition'] = $args['condition']['filter_callback'];

				$result[] = $args;
			}

			return $result;
		}

		/**
		 * Returns all taxonomies list for options
		 *
		 * @return [type] [description]
		 */
		public function get_taxonomies_for_options() {

			$result     = array();
			$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );

			foreach ( $taxonomies as $taxonomy ) {

				if ( empty( $taxonomy->object_type ) || ! is_array( $taxonomy->object_type ) ) {
					continue;
				}

				foreach ( $taxonomy->object_type as $object ) {
					if ( empty( $result[ $object ] ) ) {
						$post_type = get_post_type_object( $object );

						if ( ! $post_type ) {
							continue;
						}

						$result[ $object ] = array(
							'label'  => $post_type->labels->name,
							'values' => array(),
						);
					}

					$result[ $object ]['values'][] = array(
						'value' => $taxonomy->name,
						'label' => $taxonomy->labels->name,
					);

				};
			}

			return array_values( $result );

		}

		/**
		 * Register plugin sidebar
		 *
		 * @return [type] [description]
		 */
		public function blocks_assets() {

			//if ( 'jet-engine' !== get_post_type() ) {
			//	return;
			//}

			wp_enqueue_script(
				'jet-engine-blocks-views',
				jet_engine()->plugin_url( 'assets/js/admin/blocks-views/blocks.js' ),
				array( 'wp-plugins', 'wp-element', 'lodash' ),
				jet_engine()->get_version(),
				true
			);

			do_action( 'jet-engine/blocks-views/editor-script/after' );

			global $post;

			$settings = array();
			$post_id  = false;

			if ( $post ) {
				$settings = get_post_meta( $post->ID, '_elementor_page_settings', true );
				$post_id  = $post->ID;
			}

			if ( empty( $settings ) ) {
				$settings = array();
			}

			$source     = ! empty( $settings['listing_source'] ) ? $settings['listing_source'] : 'posts';
			$post_type  = ! empty( $settings['listing_post_type'] ) ? $settings['listing_post_type'] : 'post';
			$tax        = ! empty( $settings['listing_tax'] ) ? $settings['listing_tax'] : 'category';
			$rep_source = ! empty( $settings['repeater_source'] ) ? esc_attr( $settings['repeater_source'] ) : '';
			$rep_field  = ! empty( $settings['repeater_field'] ) ? esc_attr( $settings['repeater_field'] ) : '';
			$rep_option = ! empty( $settings['repeater_option'] ) ? esc_attr( $settings['repeater_option'] ) : '';

			jet_engine()->listings->data->set_listing( jet_engine()->listings->get_new_doc( array(
				'listing_source'    => $source,
				'listing_post_type' => $post_type,
				'listing_tax'       => $tax,
				'repeater_source'   => $rep_source,
				'repeater_field'    => $rep_field,
				'repeater_option'   => $rep_option,
				'is_main'           => true,
			), $post_id ) );

			$current_object_id = $this->get_current_object();
			$field_sources     = jet_engine()->listings->data->get_field_sources();
			$sources           = array();

			foreach ( $field_sources as $value => $label ) {
				$sources[] = array(
					'value' => $value,
					'label' => $label,
				);
			}

			$link_sources = $this->get_dynamic_sources( 'plain' );
			$link_sources = apply_filters( 'jet-engine/blocks-views/dynamic-link-sources', $link_sources );

			$media_sources = $this->get_dynamic_sources( 'media' );
			$media_sources = apply_filters( 'jet-engine/blocks-views/dynamic-media-sources', $media_sources );

			/**
			 * Format:
			 * array(
			 *  	'block-type-name' => array(
			 *  		array(
			 * 				'prop' => 'prop-name-to-set',
			 * 				'label' => 'control-label',
			 * 				'condition' => array(
			 * 					'prop' => array( 'value' ),
			 * 				)
			 * 			)
			 *  	)
			 *  )
			 */
			$custom_controls = apply_filters( 'jet-engine/blocks-views/custom-blocks-controls', array() );
			$custom_panles   = array();

			$config = apply_filters( 'jet-engine/blocks-views/editor-data', array(
				'isJetEnginePostType'   => 'jet-engine' === get_post_type(),
				'settings'              => $settings,
				'object_id'             => $current_object_id,
				'fieldSources'          => $sources,
				'imageSizes'            => jet_engine()->listings->get_image_sizes( 'blocks' ),
				'metaFields'            => $this->get_meta_fields(),
				'repeaterFields'        => $this->get_repeater_fields(),
				'mediaFields'           => $media_sources,
				'linkFields'            => $link_sources,
				'optionsFields'         => $this->get_options_fields( 'plain' ),
				'mediaOptionsFields'    => $this->get_options_fields( 'media' ),
				'userRoles'             => Jet_Engine_Tools::get_user_roles_for_js(),
				'repeaterOptionsFields' => $this->get_options_fields( 'repeater' ),
				'filterCallbacks'       => $this->get_filter_callbacks(),
				'filterCallbacksArgs'   => $this->get_filter_callbacks_args(),
				'taxonomies'            => $this->get_taxonomies_for_options(),
				'queriesList'           => \Jet_Engine\Query_Builder\Manager::instance()->get_queries_for_options( true ),
				'objectFields'          => jet_engine()->listings->data->get_object_fields( 'blocks' ),
				'postTypes'             => Jet_Engine_Tools::get_post_types_for_js(),
				'glossariesList'        => jet_engine()->glossaries->get_glossaries_for_js(),
				'atts'                  => array(
					'dynamicField' => jet_engine()->blocks_views->block_types->get_block_atts( 'dynamic-field' ),
					'dynamicLink'  => jet_engine()->blocks_views->block_types->get_block_atts( 'dynamic-link' ),
					'listingGrid'  => jet_engine()->blocks_views->block_types->get_block_atts( 'listing-grid' ),
				),
				'customPanles'          => $custom_panles,
				'customControls'        => $custom_controls,
				'injections'            => apply_filters( 'jet-engine/blocks-views/listing-injections-config', array(
					'enabled' => false,
				) ),
				'relationsTypes'        => array(
					array(
						'value' => 'grandparents',
						'label' => __( 'Grandparent Posts', 'jet-engine' ),
					),
					array(
						'value' => 'grandchildren',
						'label' => __( 'Grandchildren Posts', 'jet-engine' ),
					),
				),
				'listingOptions' => jet_engine()->listings->get_listings_for_options( 'blocks' ),
				'hideOptions'    => jet_engine()->listings->get_widget_hide_options( 'blocks' ),
				'activeModules'  => jet_engine()->modules->get_active_modules(),
			) );

			wp_localize_script(
				'jet-engine-blocks-views',
				'JetEngineListingData',
				apply_filters( 'jet-engine/blocks-views/editor/config', $config )
			);

			wp_enqueue_style(
				'jet-engine-blocks-views',
				jet_engine()->plugin_url( 'assets/css/admin/blocks-views.css' ),
				array(),
				jet_engine()->get_version()
			);

		}

		/**
		 * Returns information about current object
		 *
		 * @param  [type] $source [description]
		 * @return [type]         [description]
		 */
		public function get_current_object() {

			if ( 'jet-engine' !== get_post_type() ) {
				return get_the_ID();
			}

			$source    = jet_engine()->listings->data->get_listing_source();
			$object_id = null;

			switch ( $source ) {

				case 'posts':
				case 'repeater':

					$post_type = jet_engine()->listings->data->get_listing_post_type();

					$posts = get_posts( array(
						'post_type'        => $post_type,
						'numberposts'      => 1,
						'orderby'          => 'date',
						'order'            => 'DESC',
						'suppress_filters' => false,
					) );

					if ( ! empty( $posts ) ) {
						$post = $posts[0];
						jet_engine()->listings->data->set_current_object( $post );
						$object_id = $post->ID;
					}

					break;

				case 'terms':

					$tax   = jet_engine()->listings->data->get_listing_tax();
					$terms = get_terms( array(
						'taxonomy'   => $tax,
						'hide_empty' => false,
					) );

					if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
						$term = $terms[0];
						jet_engine()->listings->data->set_current_object( $term );
						$object_id = $term->term_id;
					}

					break;

				case 'users':

					$object_id = get_current_user_id();
					jet_engine()->listings->data->set_current_object( wp_get_current_user() );

					break;

				default:

					$object_id = apply_filters(
						'jet-engine/blocks-views/editor/config/object/' . $source,
						false,
						$this
					);

					break;

			}

			return $object_id;

		}

	}

}
