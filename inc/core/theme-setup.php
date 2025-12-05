<?php
/**
 * Theme setup - register menus and add theme supports
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ross_theme_setup() {
	// Register navigation locations
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'ross-theme' ),
		'footer'  => __( 'Footer Menu', 'ross-theme' ),
	) );

	// Basic theme supports
	add_theme_support( 'menus' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array(
		'height'      => 100,
		'width'       => 400,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );
	add_theme_support( 'customize-selective-refresh-widgets' );
}
add_action( 'after_setup_theme', 'ross_theme_setup' );

?>
