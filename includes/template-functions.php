<?php

    defined( 'ABSPATH' ) or die( 'Keep Quit' );

    function fvs_locate_template( $template_name, $third_party_path = FALSE ) {

        $template_name = ltrim( $template_name, '/' );
        $template_path = flatsome_variation_swatches()->template_override_dir();
        $default_path  = flatsome_variation_swatches()->template_path();

        if ( $third_party_path && is_string( $third_party_path ) ) {
            $default_path = untrailingslashit( $third_party_path );
        }

        // Look within passed path within the theme - this is priority.
        $template = locate_template(
            array(
                trailingslashit( $template_path ) . trim( $template_name ),
                'fvs-template-' . trim( $template_name )
            )
        );

        // Get default template/
        if ( empty( $template ) ) {
            $template = trailingslashit( $default_path ) . trim( $template_name );
        }

        // Return what we found.
        return apply_filters( 'fvs_locate_template', $template, $template_name, $template_path );
    }

    function fvs_get_template( $template_name, $template_args = array(), $third_party_path = FALSE ) {

        $template_name = ltrim( $template_name, '/' );

        $located = apply_filters( 'fvs_get_template', fvs_locate_template( $template_name, $third_party_path ) );

        do_action( 'fvs_before_get_template', $template_name, $template_args );

        extract( $template_args );

        if ( file_exists( $located ) ) {
            include $located;
        } else {
            trigger_error( sprintf( esc_html__( 'Flatsome Variation Swatches Plugin try to load "%s" but template "%s" was not found.', 'flatsome-variation-swatches' ), $located, $template_name ), E_USER_WARNING );
        }

        do_action( 'fvs_after_get_template', $template_name, $template_args );
    }

    function fvs_get_theme_file_path( $file, $third_party_path = FALSE ) {

        $file         = ltrim( $file, '/' );
        $template_dir = flatsome_variation_swatches()->template_override_dir();
        $default_path = flatsome_variation_swatches()->template_path();

        if ( $third_party_path && is_string( $third_party_path ) ) {
            $default_path = untrailingslashit( $third_party_path );
        }

        if ( file_exists( get_stylesheet_directory() . '/' . $template_dir . '/' . $file ) ) {
            $path = get_stylesheet_directory() . '/' . $template_dir . '/' . $file;
        } elseif ( file_exists( get_template_directory() . '/' . $template_dir . '/' . $file ) ) {
            $path = get_template_directory() . '/' . $template_dir . '/' . $file;
        } else {
            $path = trailingslashit( $default_path ) . $file;
        }

        return apply_filters( 'fvs_get_theme_file_path', $path, $file );
    }

    function fvs_get_theme_file_uri( $file, $third_party_uri = FALSE ) {

        $file         = ltrim( $file, '/' );
        $template_dir = flatsome_variation_swatches()->template_override_dir();
        $default_uri  = flatsome_variation_swatches()->template_uri();

        if ( $third_party_uri && is_string( $third_party_uri ) ) {
            $default_uri = untrailingslashit( $third_party_uri );
        }

        if ( file_exists( get_stylesheet_directory() . '/' . $template_dir . '/' . $file ) ) {
            $uri = get_stylesheet_directory_uri() . '/' . $template_dir . '/' . $file;
        } elseif ( file_exists( get_template_directory() . '/' . $template_dir . '/' . $file ) ) {
            $uri = get_template_directory_uri() . '/' . $template_dir . '/' . $file;
        } else {
            $uri = trailingslashit( $default_uri ) . $file;
        }

        return apply_filters( 'fvs_get_theme_file_uri', $uri, $file );
    }