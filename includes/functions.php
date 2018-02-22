<?php
	
	defined( 'ABSPATH' ) or die( 'Keep Quit' );
	
	//-------------------------------------------------------------------------------
	// Available Product Attribute Types
	//-------------------------------------------------------------------------------
	
	if ( ! function_exists( 'wvs_available_attributes_types' ) ):
		function wvs_available_attributes_types() {
			$types = array();
			
			$types[ 'color' ] = array(
				'title'  => esc_html__( 'Color', 'woo-variation-swatches' ),
				'output' => 'wvs_color_variation_attribute_options'
			);
			
			$types[ 'image' ] = array(
				'title'  => esc_html__( 'Image', 'woo-variation-swatches' ),
				'output' => 'wvs_image_variation_attribute_options'
			);
			
			$types[ 'button' ] = array(
				'title'  => esc_html__( 'Button', 'woo-variation-swatches' ),
				'output' => 'wvs_button_variation_attribute_options'
			);
			
			return apply_filters( 'wvs_available_attributes_types', $types );
		}
	endif;
	
	//-------------------------------------------------------------------------------
	// Add attribute types on WooCommerce taxonomy
	//-------------------------------------------------------------------------------
	
	if ( ! function_exists( 'wvs_product_attributes_types' ) ):
		function wvs_product_attributes_types( $selector ) {
			
			foreach ( wvs_available_attributes_types() as $key => $options ) {
				$selector[ $key ] = $options[ 'title' ];
			}
			
			return $selector;
		}
	
	endif;
	
	//-------------------------------------------------------------------------------
	// Enable Ajax Variation
	//-------------------------------------------------------------------------------
	if ( ! function_exists( 'wvs_ajax_variation_threshold' ) ):
		function wvs_ajax_variation_threshold() {
			return absint( woo_variation_swatches()->get_option( 'threshold' ) );
		}
	endif;
	
	//-------------------------------------------------------------------------------
	// Add settings
	// Add Theme Support:
	// add_theme_support( 'woo-variation-swatches', array( 'tooltip' => FALSE, 'stylesheet' => FALSE, 'style'=>'rounded' ) );
	//-------------------------------------------------------------------------------
	
	if ( ! function_exists( 'wvs_settings' ) ):
		
		function wvs_settings() {
			
			do_action( 'before_wvs_settings', woo_variation_swatches() );
			
			woo_variation_swatches()->add_setting( 'general', esc_html__( 'General', 'woo-variation-swatches' ), array(
				array(
					'title'  => esc_html__( 'Display Section', 'woo-variation-swatches' ),
					'desc'   => esc_html__( 'Simply change some visual styles', 'woo-variation-swatches' ),
					'fields' => apply_filters( 'wvs_general_setting_fields', array(
						array(
							'id'      => 'tooltip',
							'type'    => 'checkbox',
							'title'   => esc_html__( 'Enable Tooltip', 'woo-variation-swatches' ),
							'desc'    => esc_html__( 'Enable / Disable plugin default tooltip on each product attribute.', 'woo-variation-swatches' ),
							'default' => TRUE
						),
						array(
							'id'      => 'stylesheet',
							'type'    => 'checkbox',
							'title'   => esc_html__( 'Enable Stylesheet', 'woo-variation-swatches' ),
							'desc'    => esc_html__( 'Enable / Disable plugin default stylesheet', 'woo-variation-swatches' ),
							'default' => TRUE
						),
						array(
							'id'      => 'threshold',
							'type'    => 'number',
							'title'   => esc_html__( 'Ajax variation threshold', 'woo-variation-swatches' ),
							'desc'    => __( 'Control the number of enable ajax variation threshold, If you set <code>1</code> all product variation will be load via ajax. Default value is <code>30</code>', 'woo-variation-swatches' ),
							'default' => 30,
							'min'     => 1,
							'max'     => 100,
						),
						array(
							'id'      => 'style',
							'type'    => 'radio',
							'title'   => esc_html__( 'Shape style', 'woo-variation-swatches' ),
							'desc'    => esc_html__( 'Attribute Shape Style', 'woo-variation-swatches' ),
							'options' => array(
								'rounded' => esc_html__( 'Rounded Shape', 'woo-variation-swatches' ),
								'squared' => esc_html__( 'Squared Shape', 'woo-variation-swatches' )
							),
							'default' => 'rounded'
						),
					) )
				)
			), apply_filters( 'wvs_general_setting_default_active', TRUE ) );
			
			do_action( 'after_wvs_settings', woo_variation_swatches() );
		}
	endif;
	
	//-------------------------------------------------------------------------------
	// Add WooCommerce taxonomy Meta
	//-------------------------------------------------------------------------------
	
	if ( ! function_exists( 'wvs_add_product_taxonomy_meta' ) ) {
		
		function wvs_add_product_taxonomy_meta() {
			$fields = array();
			
			$fields[ 'color' ] = array(
				array(
					'label' => esc_html__( 'Color', 'woo-variation-swatches' ), // <label>
					'desc'  => esc_html__( 'Choose a color', 'woo-variation-swatches' ), // description
					'id'    => 'product_attribute_color', // name of field
					'type'  => 'color'
				)
			);
			
			$fields[ 'image' ] = array(
				array(
					'label' => esc_html__( 'Image', 'woo-variation-swatches' ), // <label>
					'desc'  => esc_html__( 'Choose an Image', 'woo-variation-swatches' ), // description
					'id'    => 'product_attribute_image', // name of field
					'type'  => 'image'
				)
			);
			
			$fields         = apply_filters( 'wvs_product_taxonomy_meta_fields', $fields );
			$meta_added_for = apply_filters( 'wvs_product_taxonomy_meta_for', array( 'color', 'image' ) );
			
			if ( function_exists( 'wc_get_attribute_taxonomies' ) ):
				
				$attribute_taxonomies = wc_get_attribute_taxonomies();
				if ( $attribute_taxonomies ) :
					foreach ( $attribute_taxonomies as $tax ) :
						$product_attr      = wc_attribute_taxonomy_name( $tax->attribute_name );
						$product_attr_type = $tax->attribute_type;
						if ( in_array( $product_attr_type, $meta_added_for ) ) :
							woo_variation_swatches()->add_term_meta( $product_attr, 'product', $fields[ $product_attr_type ] );
							
							do_action( 'wvs_wc_attribute_taxonomy_meta_added', $product_attr, $product_attr_type );
						endif; //  in_array( $product_attr_type, array( 'color', 'image' ) )
					endforeach; // $attribute_taxonomies
				endif; // $attribute_taxonomies
			endif; // function_exists( 'wc_get_attribute_taxonomies' )
		}
	}
	
	//-------------------------------------------------------------------------------
	// Extra Product Option Terms
	//-------------------------------------------------------------------------------
	
	if ( ! function_exists( 'wvs_product_option_terms' ) ) :
		function wvs_product_option_terms( $tax, $i ) {
			global $thepostid;
			if ( in_array( $tax->attribute_type, array_keys( wvs_available_attributes_types() ) ) ) {
				
				$taxonomy = wc_attribute_taxonomy_name( $tax->attribute_name );
				
				$args = array(
					'orderby'    => 'name',
					'hide_empty' => 0,
				);
				?>
                <select multiple="multiple" data-placeholder="<?php esc_attr_e( 'Select terms', 'woo-variation-swatches' ); ?>" class="multiselect attribute_values wc-enhanced-select" name="attribute_values[<?php echo $i; ?>][]">
					<?php
						$all_terms = get_terms( $taxonomy, apply_filters( 'woocommerce_product_attribute_terms', $args ) );
						if ( $all_terms ) :
							foreach ( $all_terms as $term ) :
								echo '<option value="' . esc_attr( $term->term_id ) . '" ' . selected( has_term( absint( $term->term_id ), $taxonomy, $thepostid ), TRUE, FALSE ) . '>' . esc_attr( apply_filters( 'woocommerce_product_attribute_term_name', $term->name, $term ) ) . '</option>';
							endforeach;
						endif;
					?>
                </select>
                <button class="button plus select_all_attributes"><?php esc_html_e( 'Select all', 'woo-variation-swatches' ); ?></button>
                <button class="button minus select_no_attributes"><?php esc_html_e( 'Select none', 'woo-variation-swatches' ); ?></button>
                <button class="button fr plus add_new_attribute"><?php esc_html_e( 'Add new', 'woo-variation-swatches' ); ?></button>
				<?php
			}
		}
	endif;
	
	//-------------------------------------------------------------------------------
	// Get a Attribute taxonomy values
	//-------------------------------------------------------------------------------
	
	// @TODO: See wc_attribute_taxonomy_id_by_name function and wc_get_attribute
	
	if ( ! function_exists( 'wvs_get_wc_attribute_taxonomy' ) ):
		function wvs_get_wc_attribute_taxonomy( $attribute_name ) {
			
			$transient = sprintf( 'wvs_get_wc_attribute_taxonomy_%s', $attribute_name );
			
			if ( ( defined( 'WP_DEBUG' ) && WP_DEBUG ) || isset( $_GET[ 'wvs_clear_transient' ] ) ) {
				delete_transient( $transient );
			}
			
			if ( FALSE === ( $attribute_taxonomy = get_transient( $transient ) ) ) {
				global $wpdb;
				
				$attribute_name     = str_replace( 'pa_', '', wc_sanitize_taxonomy_name( $attribute_name ) );
				$attribute_taxonomy = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name='{$attribute_name}'" );
				set_transient( $transient, $attribute_taxonomy );
			}
			
			return apply_filters( 'wvs_get_wc_attribute_taxonomy', $attribute_taxonomy, $attribute_name );
		}
	endif;
	
	// Clean transient
	add_action( 'woocommerce_attribute_updated', function ( $attribute_id, $attribute, $old_attribute_name ) {
		
		$transient     = sprintf( 'wvs_get_wc_attribute_taxonomy_%s', wc_attribute_taxonomy_name( $attribute[ 'attribute_name' ] ) );
		$old_transient = sprintf( 'wvs_get_wc_attribute_taxonomy_%s', wc_attribute_taxonomy_name( $old_attribute_name ) );
		delete_transient( $transient );
		delete_transient( $old_transient );
	}, 20, 3 );
	
	// Clean transient
	add_action( 'woocommerce_attribute_deleted', function ( $attribute_id, $attribute_name, $taxonomy ) {
		$transient = sprintf( 'wvs_get_wc_attribute_taxonomy_%s', $taxonomy );
		delete_transient( $transient );
	}, 20, 3 );
	
	//-------------------------------------------------------------------------------
	// Check has attribute type like color or image etc.
	//-------------------------------------------------------------------------------
	if ( ! function_exists( 'wvs_wc_product_has_attribute_type' ) ):
		function wvs_wc_product_has_attribute_type( $type, $attribute_name ) {
			$attribute = wvs_get_wc_attribute_taxonomy( $attribute_name );
			
			return isset( $attribute->attribute_type ) && ( $attribute->attribute_type == $type );
		}
	endif;
	
	//-------------------------------------------------------------------------------
	// Color Variation Attribute Options
	//-------------------------------------------------------------------------------
	
	if ( ! function_exists( 'wvs_color_variation_attribute_options' ) ) :
		function wvs_color_variation_attribute_options( $args = array() ) {
			
			$args = wp_parse_args( $args, array(
				'options'          => FALSE,
				'attribute'        => FALSE,
				'product'          => FALSE,
				'selected'         => FALSE,
				'name'             => '',
				'id'               => '',
				'class'            => '',
				'show_option_none' => esc_html__( 'Choose an option', 'woo-variation-swatches' )
			) );
			
			$options               = $args[ 'options' ];
			$product               = $args[ 'product' ];
			$attribute             = $args[ 'attribute' ];
			$name                  = $args[ 'name' ] ? $args[ 'name' ] : wc_variation_attribute_name( $attribute );
			$id                    = $args[ 'id' ] ? $args[ 'id' ] : sanitize_title( $attribute ) . $product->get_id();
			$class                 = $args[ 'class' ];
			$show_option_none      = $args[ 'show_option_none' ] ? TRUE : FALSE;
			$show_option_none_text = $args[ 'show_option_none' ] ? $args[ 'show_option_none' ] : esc_html__( 'Choose an option', 'woocommerce' ); // We'll do our best to hide the placeholder, but we'll need to show something when resetting options.
			
			if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
				$attributes = $product->get_variation_attributes();
				$options    = $attributes[ $attribute ];
			}
			
			echo '<select id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . ' hide woo-variation-raw-select" style="display:none" name="' . esc_attr( $name ) . '" data-attribute_name="' . esc_attr( wc_variation_attribute_name( $attribute ) ) . '" data-show_option_none="' . ( $show_option_none ? 'yes' : 'no' ) . '">';
			
			if ( $args[ 'show_option_none' ] ) {
				echo '<option value="">' . esc_html( $show_option_none_text ) . '</option>';
			}
			
			if ( ! empty( $options ) ) {
				if ( $product && taxonomy_exists( $attribute ) ) {
					// Get terms if this is a taxonomy - ordered. We need the names too.
					$terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );
					
					foreach ( $terms as $term ) {
						if ( in_array( $term->slug, $options ) ) {
							echo '<option value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $args[ 'selected' ] ), $term->slug, FALSE ) . '>' . apply_filters( 'woocommerce_variation_option_name', $term->name ) . '</option>';
						}
					}
				}
			}
			
			echo '</select>';
			
			echo '<ul class="list-inline variable-items-wrapper color-variable-wrapper" data-attribute_name="' . esc_attr( wc_variation_attribute_name( $attribute ) ) . '">';
			if ( ! empty( $options ) ) {
				if ( $product && taxonomy_exists( $attribute ) ) {
					$terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );
					
					foreach ( $terms as $term ) {
						if ( in_array( $term->slug, $options ) ) {
							$get_term_meta  = sanitize_hex_color( get_term_meta( $term->term_id, 'product_attribute_color', TRUE ) );
							$selected_class = ( sanitize_title( $args[ 'selected' ] ) == $term->slug ) ? 'selected' : '';
							?>
                            <li data-wvstooltip="<?php echo esc_html( $term->name ) ?>" class="variable-item color-variable-item color-variable-item-<?php echo $term->slug ?> <?php echo $selected_class ?>" title="<?php echo esc_html( $term->name ) ?>" data-value="<?php echo esc_attr( $term->slug ) ?>"><span style="background-color:<?php echo esc_attr( $get_term_meta ) ?>;"></span></li>
							<?php
						}
					}
				}
			}
			echo '</ul>';
		}
	endif;
	
	//-------------------------------------------------------------------------------
	// Image Variation Attribute Options
	//-------------------------------------------------------------------------------
	
	if ( ! function_exists( 'wvs_image_variation_attribute_options' ) ) :
		function wvs_image_variation_attribute_options( $args = array() ) {
			
			$args = wp_parse_args( $args, array(
				'options'          => FALSE,
				'attribute'        => FALSE,
				'product'          => FALSE,
				'selected'         => FALSE,
				'name'             => '',
				'id'               => '',
				'class'            => '',
				'show_option_none' => esc_html__( 'Choose an option', 'woo-variation-swatches' )
			) );
			
			$options               = $args[ 'options' ];
			$product               = $args[ 'product' ];
			$attribute             = $args[ 'attribute' ];
			$name                  = $args[ 'name' ] ? $args[ 'name' ] : wc_variation_attribute_name( $attribute );
			$id                    = $args[ 'id' ] ? $args[ 'id' ] : sanitize_title( $attribute ) . $product->get_id();
			$class                 = $args[ 'class' ];
			$show_option_none      = $args[ 'show_option_none' ] ? TRUE : FALSE;
			$show_option_none_text = $args[ 'show_option_none' ] ? $args[ 'show_option_none' ] : esc_html__( 'Choose an option', 'woocommerce' ); // We'll do our best to hide the placeholder, but we'll need to show something when resetting options.
			
			if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
				$attributes = $product->get_variation_attributes();
				$options    = $attributes[ $attribute ];
			}
			
			echo '<select id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . ' hide woo-variation-raw-select" style="display:none" name="' . esc_attr( $name ) . '" data-attribute_name="' . esc_attr( wc_variation_attribute_name( $attribute ) ) . '" data-show_option_none="' . ( $show_option_none ? 'yes' : 'no' ) . '">';
			
			if ( $args[ 'show_option_none' ] ) {
				echo '<option value="">' . esc_html( $show_option_none_text ) . '</option>';
			}
			
			if ( ! empty( $options ) ) {
				if ( $product && taxonomy_exists( $attribute ) ) {
					// Get terms if this is a taxonomy - ordered. We need the names too.
					$terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );
					
					foreach ( $terms as $term ) {
						if ( in_array( $term->slug, $options ) ) {
							echo '<option value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $args[ 'selected' ] ), $term->slug, FALSE ) . '>' . apply_filters( 'woocommerce_variation_option_name', $term->name ) . '</option>';
						}
					}
				}
			}
			
			echo '</select>';
			
			echo '<ul class="list-inline variable-items-wrapper image-variable-wrapper" data-attribute_name="' . esc_attr( wc_variation_attribute_name( $attribute ) ) . '">';
			if ( ! empty( $options ) ) {
				if ( $product && taxonomy_exists( $attribute ) ) {
					$terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );
					
					foreach ( $terms as $term ) {
						if ( in_array( $term->slug, $options ) ) {
							$attachment_id  = absint( get_term_meta( $term->term_id, 'product_attribute_image', TRUE ) );
							$image          = wp_get_attachment_image_url( $attachment_id );
							$selected_class = ( sanitize_title( $args[ 'selected' ] ) == $term->slug ) ? 'selected' : '';
							?>
                            <li data-wvstooltip="<?php echo esc_html( $term->name ) ?>" class="variable-item image-variable-item image-variable-item-<?php echo $term->slug ?> <?php echo $selected_class ?>" title="<?php echo esc_html( $term->name ) ?>" data-value="<?php echo esc_attr( $term->slug ) ?>"><img alt="<?php echo esc_html( $term->name ) ?>" src="<?php echo esc_url( $image ) ?>"></li>
							<?php
						}
					}
				}
			}
			echo '</ul>';
		}
	endif;
	
	//-------------------------------------------------------------------------------
	// Button Variation Attribute Options
	//-------------------------------------------------------------------------------
	
	if ( ! function_exists( 'wvs_button_variation_attribute_options' ) ) :
		function wvs_button_variation_attribute_options( $args = array() ) {
			
			$args = wp_parse_args( $args, array(
				'options'          => FALSE,
				'attribute'        => FALSE,
				'product'          => FALSE,
				'selected'         => FALSE,
				'name'             => '',
				'id'               => '',
				'class'            => '',
				'show_option_none' => esc_html__( 'Choose an option', 'woo-variation-swatches' )
			) );
			
			$options               = $args[ 'options' ];
			$product               = $args[ 'product' ];
			$attribute             = $args[ 'attribute' ];
			$name                  = $args[ 'name' ] ? $args[ 'name' ] : wc_variation_attribute_name( $attribute );
			$id                    = $args[ 'id' ] ? $args[ 'id' ] : sanitize_title( $attribute ) . $product->get_id();
			$class                 = $args[ 'class' ];
			$show_option_none      = $args[ 'show_option_none' ] ? TRUE : FALSE;
			$show_option_none_text = $args[ 'show_option_none' ] ? $args[ 'show_option_none' ] : esc_html__( 'Choose an option', 'woocommerce' ); // We'll do our best to hide the placeholder, but we'll need to show something when resetting options.
			
			if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
				$attributes = $product->get_variation_attributes();
				$options    = $attributes[ $attribute ];
			}
			
			echo '<select id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . ' hide woo-variation-raw-select" style="display:none" name="' . esc_attr( $name ) . '" data-attribute_name="' . esc_attr( wc_variation_attribute_name( $attribute ) ) . '" data-show_option_none="' . ( $show_option_none ? 'yes' : 'no' ) . '">';
			
			if ( $args[ 'show_option_none' ] ) {
				echo '<option value="">' . esc_html( $show_option_none_text ) . '</option>';
			}
			
			if ( ! empty( $options ) ) {
				if ( $product && taxonomy_exists( $attribute ) ) {
					// Get terms if this is a taxonomy - ordered. We need the names too.
					$terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );
					
					foreach ( $terms as $term ) {
						if ( in_array( $term->slug, $options ) ) {
							echo '<option value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $args[ 'selected' ] ), $term->slug, FALSE ) . '>' . apply_filters( 'woocommerce_variation_option_name', $term->name ) . '</option>';
						}
					}
				}
			}
			
			echo '</select>';
			
			echo '<ul class="list-inline variable-items-wrapper button-variable-wrapper" data-attribute_name="' . esc_attr( wc_variation_attribute_name( $attribute ) ) . '">';
			if ( ! empty( $options ) ) {
				if ( $product && taxonomy_exists( $attribute ) ) {
					$terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );
					
					foreach ( $terms as $term ) {
						if ( in_array( $term->slug, $options ) ) {
							$selected_class = ( sanitize_title( $args[ 'selected' ] ) == $term->slug ) ? 'selected' : '';
							?>
                            <li data-wvstooltip="<?php echo esc_html( $term->name ) ?>" class="variable-item button-variable-item button-variable-item-<?php echo $term->slug ?> <?php echo $selected_class ?>" title="<?php echo esc_html( $term->name ) ?>" data-value="<?php echo esc_attr( $term->slug ) ?>"><span><?php echo esc_html( $term->name ) ?></span></li>
							<?php
						}
					}
				}
			}
			echo '</ul>';
		}
	endif;
	
	//-------------------------------------------------------------------------------
	// Generate Option HTML
	//-------------------------------------------------------------------------------
	
	if ( ! function_exists( 'wvs_variation_attribute_options_html' ) ):
		function wvs_variation_attribute_options_html( $html, $args ) {
			ob_start();
			
			$available_type_keys = array_keys( wvs_available_attributes_types() );
			$available_types     = wvs_available_attributes_types();
			$default             = TRUE;
			
			foreach ( $available_type_keys as $type ) {
				if ( wvs_wc_product_has_attribute_type( $type, $args[ 'attribute' ] ) ) {
					$output_callback = $available_types[ $type ][ 'output' ];
					$output_callback( array(
						                  'options'   => $args[ 'options' ],
						                  'attribute' => $args[ 'attribute' ],
						                  'product'   => $args[ 'product' ],
						                  'selected'  => $args[ 'selected' ]
					                  ) );
					
					$default = FALSE;
				}
			}
			
			if ( $default ) {
				echo $html;
			}
			
			return ob_get_clean();
		}
	endif;