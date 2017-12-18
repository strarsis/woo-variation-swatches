<?php
	
	defined( 'ABSPATH' ) or die( 'Keep Quit' );
	
	//-------------------------------------------------------------------------------
	// Add attribute types on WooCommerce taxonomy
	//-------------------------------------------------------------------------------
	
	if ( ! function_exists( 'wvs_product_attributes_types' ) ):
		function wvs_product_attributes_types( $selector ) {
			$selector[ 'color' ]  = esc_html__( 'Color', 'woo-variation-swatches' );
			$selector[ 'image' ]  = esc_html__( 'Image', 'woo-variation-swatches' );
			$selector[ 'button' ] = esc_html__( 'Button', 'woo-variation-swatches' );
			
			return $selector;
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
			
			if ( function_exists( 'wc_get_attribute_taxonomies' ) ):
				
				$attribute_taxonomies = wc_get_attribute_taxonomies();
				if ( $attribute_taxonomies ) :
					foreach ( $attribute_taxonomies as $tax ) :
						$product_attr      = wc_attribute_taxonomy_name( $tax->attribute_name );
						$product_attr_type = $tax->attribute_type;
						if ( in_array( $product_attr_type, array( 'color', 'image' ) ) ) :
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
			if ( in_array( $tax->attribute_type, array( 'color', 'image', 'button' ) ) ) {
				
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
			
			echo '<select id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . ' hide" style="display:none" name="' . esc_attr( $name ) . '" data-attribute_name="' . esc_attr( wc_variation_attribute_name( $attribute ) ) . '" data-show_option_none="' . ( $show_option_none ? 'yes' : 'no' ) . '">';
			
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
			
			echo '<ul class="list-inline variable-items-wrapper color-variable-wrapper">';
			if ( ! empty( $options ) ) {
				if ( $product && taxonomy_exists( $attribute ) ) {
					$terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );
					
					foreach ( $terms as $term ) {
						if ( in_array( $term->slug, $options ) ) {
							$get_term_meta  = sanitize_hex_color( get_term_meta( $term->term_id, 'product_attribute_color', TRUE ) );
							$selected_class = ( sanitize_title( $args[ 'selected' ] ) == $term->slug ) ? 'selected' : '';
							?>
                            <li data-toggle="tooltip" data-placement="top" class="variable-item color-variable-item color-variable-item-<?php echo $term->slug ?> <?php echo $selected_class ?>" title="<?php echo esc_html( $term->name ) ?>" data-value="<?php echo esc_attr( $term->slug ) ?>"><span style="background-color:<?php echo esc_attr( $get_term_meta ) ?>;"></span></li>
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
			
			echo '<select id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . ' hide" style="display:none" name="' . esc_attr( $name ) . '" data-attribute_name="' . esc_attr( wc_variation_attribute_name( $attribute ) ) . '" data-show_option_none="' . ( $show_option_none ? 'yes' : 'no' ) . '">';
			
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
			
			echo '<ul class="list-inline variable-items-wrapper image-variable-wrapper">';
			if ( ! empty( $options ) ) {
				if ( $product && taxonomy_exists( $attribute ) ) {
					$terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );
					
					foreach ( $terms as $term ) {
						if ( in_array( $term->slug, $options ) ) {
							$attachment_id  = absint( get_term_meta( $term->term_id, 'product_attribute_image', TRUE ) );
							$image          = wp_get_attachment_image_url( $attachment_id );
							$selected_class = ( sanitize_title( $args[ 'selected' ] ) == $term->slug ) ? 'selected' : '';
							?>
                            <li data-toggle="tooltip" data-placement="top" class="variable-item image-variable-item image-variable-item-<?php echo $term->slug ?> <?php echo $selected_class ?>" title="<?php echo esc_html( $term->name ) ?>" data-value="<?php echo esc_attr( $term->slug ) ?>"><img alt="<?php echo esc_html( $term->name ) ?>" src="<?php echo esc_url( $image ) ?>"></li>
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
			
			echo '<select id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . ' hide" style="display:none" name="' . esc_attr( $name ) . '" data-attribute_name="' . esc_attr( wc_variation_attribute_name( $attribute ) ) . '" data-show_option_none="' . ( $show_option_none ? 'yes' : 'no' ) . '">';
			
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
			
			echo '<ul class="list-inline variable-items-wrapper button-variable-wrapper">';
			if ( ! empty( $options ) ) {
				if ( $product && taxonomy_exists( $attribute ) ) {
					$terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );
					
					foreach ( $terms as $term ) {
						if ( in_array( $term->slug, $options ) ) {
							$selected_class = ( sanitize_title( $args[ 'selected' ] ) == $term->slug ) ? 'selected' : '';
							?>
                            <li data-toggle="tooltip" data-placement="top" class="variable-item button-variable-item image-variable-item-<?php echo $term->slug ?> <?php echo $selected_class ?>" title="<?php echo esc_html( $term->name ) ?>" data-value="<?php echo esc_attr( $term->slug ) ?>"><span><?php echo esc_html( $term->name ) ?></span></li>
							<?php
						}
					}
				}
			}
			echo '</ul>';
		}
	endif;
 
	//-------------------------------------------------------------------------------
	// Get a Attribute taxonomy values
	//-------------------------------------------------------------------------------
 
	if ( ! function_exists( 'wvs_get_wc_attribute_taxonomy' ) ):
		function wvs_get_wc_attribute_taxonomy( $attribute_name ) {
			
			$transient = sprintf( 'wvs_get_wc_attribute_taxonomy_%s', $attribute_name );
			
			if ( ( defined( 'WP_DEBUG' ) && WP_DEBUG ) || isset( $_GET[ 'wvs_clear' ] ) ) {
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
	// Generate Option HTML
	//-------------------------------------------------------------------------------
 
	if ( ! function_exists( 'wvs_variation_attribute_options_html' ) ):
		function wvs_variation_attribute_options_html( $html, $args ) {
			ob_start();
			if ( wvs_wc_product_has_attribute_type( 'color', $args[ 'attribute' ] ) ):
				
				wvs_color_variation_attribute_options( array(
					                                       'options'   => $args[ 'options' ],
					                                       'attribute' => $args[ 'attribute' ],
					                                       'product'   => $args[ 'product' ],
					                                       'selected'  => $args[ 'selected' ]
				                                       ) );

            elseif ( wvs_wc_product_has_attribute_type( 'image', $args[ 'attribute' ] ) ):
				
				wvs_image_variation_attribute_options( array(
					                                       'options'   => $args[ 'options' ],
					                                       'attribute' => $args[ 'attribute' ],
					                                       'product'   => $args[ 'product' ],
					                                       'selected'  => $args[ 'selected' ]
				                                       ) );

            elseif ( wvs_wc_product_has_attribute_type( 'button', $args[ 'attribute' ] ) ):
				
				wvs_button_variation_attribute_options( array(
					                                        'options'   => $args[ 'options' ],
					                                        'attribute' => $args[ 'attribute' ],
					                                        'product'   => $args[ 'product' ],
					                                        'selected'  => $args[ 'selected' ]
				                                        ) );
			endif;
			
			return ob_get_clean();
		}
	endif;