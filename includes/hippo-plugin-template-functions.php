<?php

    defined( 'ABSPATH' ) or die( 'Keep Quit' );

    function hippo_theme_plugin_locate_template( $template_name, $third_party_path = FALSE ) {

        $template_name = ltrim( $template_name, '/' );
        $template_path = Hippo_Theme_Plugin()->template_override_dir();
        $default_path  = Hippo_Theme_Plugin()->template_path();

        if ( $third_party_path && is_string( $third_party_path ) ) {
            $default_path = untrailingslashit( $third_party_path );
        }

        // Look within passed path within the theme - this is priority.
        $template = locate_template(
            array(
                trailingslashit( $template_path ) . trim( $template_name ),
                'hippo-theme-plugin-template-' . trim( $template_name )
            )
        );

        // Get default template/
        if ( empty( $template ) ) {
            $template = trailingslashit( $default_path ) . trim( $template_name );
        }

        // Return what we found.
        return apply_filters( 'hippo_theme_plugin_locate_template', $template, $template_name, $template_path );
    }

    function hippo_theme_plugin_get_template( $template_name, $template_args = array(), $third_party_path = FALSE ) {

        $template_name = ltrim( $template_name, '/' );

        $located = apply_filters( 'hippo_theme_plugin_get_template', hippo_theme_plugin_locate_template( $template_name, $third_party_path ) );

        do_action( 'hippo_theme_plugin_before_get_template', $template_name, $template_args );

        extract( $template_args );

        if ( file_exists( $located ) ) {
            include $located;
        } else {
            trigger_error( sprintf( esc_html__( 'Hippo Theme Plugin try to load "%s" but template "%s" was not found.', 'hippo-theme-plugin' ), $located, $template_name ), E_USER_WARNING );
        }

        do_action( 'hippo_theme_plugin_after_get_template', $template_name, $template_args );
    }

    function hippo_theme_plugin_get_theme_file_path( $file, $third_party_path = FALSE ) {

        $file         = ltrim( $file, '/' );
        $template_dir = Hippo_Theme_Plugin()->template_override_dir();
        $default_path = Hippo_Theme_Plugin()->template_path();

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

        return apply_filters( 'hippo_theme_plugin_get_theme_file_path', $path, $file );
    }

    function hippo_theme_plugin_get_theme_file_uri( $file, $third_party_uri = FALSE ) {

        $file         = ltrim( $file, '/' );
        $template_dir = Hippo_Theme_Plugin()->template_override_dir();
        $default_uri  = Hippo_Theme_Plugin()->template_uri();

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

        return apply_filters( 'hippo_theme_plugin_get_theme_file_uri', $uri, $file );
    }
