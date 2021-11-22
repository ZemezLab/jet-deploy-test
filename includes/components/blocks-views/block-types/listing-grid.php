<?php
/**
 * Listing Grid View
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Blocks_Views_Type_Grid' ) ) {

	/**
	 * Define Jet_Engine_Blocks_Views_Type_Listing_Grid class
	 */
	class Jet_Engine_Blocks_Views_Type_Listing_Grid extends Jet_Engine_Blocks_Views_Type_Base {

		/**
		 * Returns block name
		 *
		 * @return string
		 */
		public function get_name() {
			return 'listing-grid';
		}

		/**
		 * Return attributes array
		 *
		 * @return array
		 */
		public function get_attributes() {
			return apply_filters( 'jet-engine/blocks-views/listing-grid/attributes', array(
				'lisitng_id' => array(
					'type' => 'string',
					'default' => '',
				),
				'columns' => array(
					'type' => 'number',
					'default' => 3,
				),
				'columns_tablet' => array(
					'type' => 'number',
					'default' => 3,
				),
				'columns_mobile' => array(
					'type' => 'number',
					'default' => 1,
				),
				'is_archive_template' => array(
					'type' => 'boolean',
					'default' => false,
				),
				'post_status' => array(
					'type'    => 'array',
					'items'   => array( 'type' => 'string' ),
					'default' => array( 'publish' ),
				),
				'use_random_posts_num' => array(
					'type' => 'boolean',
					'default' => false,
				),
				'posts_num' => array(
					'type' => 'number',
					'default' => 6,
				),
				'max_posts_num' => array(
					'type' => 'number',
					'default' => 6,
				),
				'is_masonry' => array(
					'type' => 'boolean',
					'default' => false,
				),
				'use_custom_post_types' => array(
					'type' => 'boolean',
					'default' => false,
				),
				'custom_post_types' => array(
					'type'    => 'array',
					'items'   => array( 'type' => 'string' ),
					'default' => array(),
				),
				'equal_columns_height' => array(
					'type' => 'boolean',
					'default' => false,
				),
				'custom_query' => array(
					'type' => 'boolean',
					'default' => false,
				),
				'custom_query_id' => array(
					'type' => 'string',
					'default' => '',
				),
				'not_found_message' => array(
					'type' => 'string',
					'default' => __( 'No data was found', 'jet-engine' ),
				),
				'custom_posts_query' => array(
					'type' => 'string',
					'default' => '',
				),
				'posts_query' => array(
					'type' => 'array',
					'default' => array(),
				),
				'hide_widget_if' => array(
					'type' => 'string',
					'default' => '',
				),
				'lazy_load' => array(
					'type' => 'boolean',
					'default' => false,
				),
				'lazy_load_offset' => array(
					'type' => 'number',
					'default' => 0,
				),
				'use_load_more' => array(
					'type' => 'boolean',
					'default' => false,
				),
				'load_more_type' => array(
					'type' => 'string',
					'default' => 'click',
				),
				'load_more_id' => array(
					'type' => 'string',
					'default' => '',
				),
				'carousel_enabled' => array(
					'type' => 'boolean',
					'default' => false,
				),
				'slides_to_scroll' => array(
					'type' => 'number',
					'default' => 1,
				),
				'arrows' => array(
					'type' => 'boolean',
					'default' => true,
				),
				'dots' => array(
					'type' => 'boolean',
					'default' => false,
				),
				'autoplay' => array(
					'type' => 'boolean',
					'default' => true,
				),
				'autoplay_speed' => array(
					'type' => 'number',
					'default' => 5000,
				),
				'infinite' => array(
					'type' => 'boolean',
					'default' => true,
				),
				'center_mode' => array(
					'type' => 'boolean',
					'default' => false,
				),
				'effect' => array(
					'type' => 'string',
					'default' => 'slide',
				),
				'speed' => array(
					'type' => 'number',
					'default' => 500,
				),
				'scroll_slider_enabled' => array(
					'type' => 'boolean',
					'default' => false,
				),
				'scroll_slider_on' => array(
					'type' => 'array',
					'default' => array( 'desktop', 'tablet', 'mobile' ),
				),
				'static_column_width' => array(
					'type' => 'number',
					'default' => 200,
				),
				'static_column_width_tablet' => array(
					'type' => 'number',
					'default' => 200,
				),
				'static_column_width_mobile' => array(
					'type' => 'number',
					'default' => 200,
				),
				'terms_object_ids' => array(
					'type' => 'string',
					'default' => '',
				),
				'terms_orderby' => array(
					'type' => 'string',
					'default' => 'name',
				),
				'terms_order' => array(
					'type' => 'string',
					'default' => 'DESC',
				),
				'terms_hide_empty' => array(
					'type' => 'boolean',
					'default' => true,
				),
				'terms_include' => array(
					'type' => 'string',
					'default' => '',
				),
				'terms_exclude' => array(
					'type' => 'string',
					'default' => '',
				),
				'terms_offset' => array(
					'type' => 'string',
					'default' => '0',
				),
				'terms_parent' => array(
					'type' => 'string',
					'default' => '',
				),
				'terms_child_of' => array(
					'type' => 'string',
					'default' => '',
				),
				'terms_meta_query' => array(
					'type' => 'array',
					'default' => array(),
				),
				'term_meta_query_relation' => array(
					'type' => 'string',
					'default' => 'AND',
				),
				'meta_query_relation' => array(
					'type' => 'string',
					'default' => 'AND',
				),
				'tax_query_relation' => array(
					'type' => 'string',
					'default' => 'AND',
				),
				'users_meta_query_relation' => array(
					'type' => 'string',
					'default' => 'AND',
				),
				'users_role__in' => array(
					'type' => 'array',
					'default' => array(),
				),
				'users_role__not_in' => array(
					'type' => 'array',
					'default' => array(),
				),
				'users_include' => array(
					'type' => 'string',
					'default' => '',
				),
				'users_exclude' => array(
					'type' => 'string',
					'default' => '',
				),
				'users_search_query' => array(
					'type' => 'string',
					'default' => '',
				),
				'users_search_columns' => array(
					'type' => 'array',
					'default' => array(),
				),
				'users_meta_query' => array(
					'type' => 'array',
					'default' => array(),
				),
				'_block_id' => array(
					'type'    => 'string',
					'default' => '',
				),
			) );
		}

		/**
		 * Add style block options
		 *
		 * @return void
		 */
		public function add_style_manager_options() {

			$this->controls_manager->start_section(
				'style_controls',
				array(
					'id'    => 'section_caption_style',
					'title' => esc_html__( 'Columns', 'jet-engine' ),
					'initialOpen' => true,
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'    => 'horizontal_gap',
					'label' => __( 'Horizontal Gap', 'jet-engine' ),
					'type'  => 'range',
					'units' => array(
						array(
							'value'     => 'px',
							'intervals' => array(
								'step' => 1,
								'min'  => 0,
								'max'  => 100,
							),
						),
					),
					'css_selector' => array(
						'{{WRAPPER}} .jet-listing-grid__item' => 'padding-left: calc({{VALUE}}{{UNIT}} / 2); padding-right: calc({{VALUE}}{{UNIT}} / 2);',
						'{{WRAPPER}} .jet-listing-grid__items' => 'margin-left: calc(-{{VALUE}}{{UNIT}} / 2); margin-right: calc(-{{VALUE}}{{UNIT}} / 2); width: calc(100% + {{VALUE}}{{UNIT}});',
					),
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'    => 'vertical_gap',
					'label' => __( 'Vertical Gap', 'jet-engine' ),
					'type'  => 'range',
					'units' => array(
						array(
							'value'     => 'px',
							'intervals' => array(
								'step' => 1,
								'min'  => 0,
								'max'  => 100,
							),
						),
					),
					'css_selector' => array(
						'{{WRAPPER}} .jet-listing-grid__item' => 'padding-top: calc({{VALUE}}{{UNIT}} / 2); padding-bottom: calc({{VALUE}}{{UNIT}} / 2);',
					),
				)
			);

			$this->controls_manager->end_section();

			$this->controls_manager->start_section(
				'style_controls',
				array(
					'id'    => 'section_loader_style',
					'title' => esc_html__( 'Loader', 'jet-engine' ),
					'condition' => array(
						'lazy_load' => true,
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'    => 'loader_color',
					'label' => esc_html__( 'Color', 'jet-engine' ),
					'type'  => 'color-picker',
					'css_selector' => array(
						'{{WRAPPER}} .jet-listing-grid-loading:after' => 'color: {{VALUE}};',
					),
				)
			);

			$this->controls_manager->end_section();

		}

		public function slider_before( $settings = array(), $widget = null ) {

			if ( empty( $_REQUEST['context'] ) || 'edit' !== $_REQUEST['context'] || empty( $_REQUEST['attributes'] ) ) {
				return;
			}

			if ( ! $widget->is_carousel_enabled( $settings ) ) {
				return;
			}

			if ( empty( $settings['arrows'] ) ) {
				return;
			}

			echo $widget->get_arrow_icon( 'prev', $settings, 'slick-arrow' );

		}

		public function slider_after( $settings = array(), $widget = null ) {

			if ( empty( $_REQUEST['context'] ) || 'edit' !== $_REQUEST['context'] || empty( $_REQUEST['attributes'] ) ) {
				return;
			}

			if ( ! $widget->is_carousel_enabled( $settings ) ) {
				return;
			}

			if ( ! empty( $settings['arrows'] ) ) {
				echo $widget->get_arrow_icon( 'next', $settings, 'slick-arrow' );
			}

			if ( ! empty( $settings['dots'] ) ) {

				echo '<ul class="jet-slick-dots" style="" role="tablist">';

					$number = $widget->get_posts_num( $settings );

					for ( $i = 1;  $i <= $number;  $i++ ) {
						$active_class = ( 1 === $i ) ? 'slick-active' : '';
						echo '<li class="' . $active_class . '" role="presentation"><span>' . $i . '</span></li>';
					}

				echo '</ul>';

			}

		}

		public function render_callback( $attributes = array() ) {

			$item       = $this->get_name();
			$attributes = $this->prepare_attributes( $attributes );

			if ( $this->is_edit_mode() ) {
				$attributes['lazy_load'] = false;
			}

			if ( empty( $attributes['columns_mobile'] ) ) {
				$attributes['columns_mobile'] = 1;
			}

			$render     = jet_engine()->listings->get_render_instance( $item, $attributes );
			$listing_id = $attributes['lisitng_id'];

			if ( ! $render ) {
				return __( 'Listing renderer class not found', 'jet-engine' );
			}

			ob_start();

			add_action( 'jet-engine/listing/grid-items/before', array( $this, 'slider_before' ), 10, 2 );
			add_action( 'jet-engine/listing/grid-items/after', array( $this, 'slider_after' ), 10, 2 );

			$render->render_content();

			remove_action( 'jet-engine/listing/grid-items/before', array( $this, 'slider_before' ), 10, 2 );
			remove_action( 'jet-engine/listing/grid-items/after', array( $this, 'slider_after' ), 10, 2 );

			if ( $listing_id && ! jet_engine()->blocks_views->is_blocks_listing( $listing_id )
				&& jet_engine()->has_elementor()
				&& ! wp_style_is( 'elementor-frontend', 'registered' )
			) {

				Elementor\Plugin::$instance->frontend->register_styles();
				Elementor\Plugin::$instance->frontend->enqueue_styles();
				Elementor\Plugin::$instance->frontend->print_fonts_links();

				wp_print_styles( 'elementor-frontend' );

				$css_file = Elementor\Core\Files\CSS\Post::create( $listing_id );
				$css_file->print_css();
			}

			if ( $listing_id && jet_engine()->blocks_views->is_blocks_listing( $listing_id ) ) {

				$style = get_post_meta( $listing_id, '_jet_sm_ready_style', true );

				if ( $style ) {
					printf( '<style>%s</style>', $style );
				}

			}

			$content = ob_get_clean();

			return sprintf(
				'<div class="jet-listing-grid--blocks%3$s" data-element-id="%1$s" data-listing-type="blocks">%2$s</div>',
				$attributes['_block_id'],
				$content,
				! empty( $attributes['className'] ) ? ' ' . $attributes['className'] : ''
			);
		}

	}

}
