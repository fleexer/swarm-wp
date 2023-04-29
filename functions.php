<?php
/**
 * Theme functions and definitions.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * https://developers.elementor.com/docs/hello-elementor-theme/
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_CHILD_VERSION', '2.0.0' );

/**
 * Load child theme scripts & styles.
 *
 * @return void
 */
function hello_elementor_child_scripts_styles() {

	wp_enqueue_style(
		'hello-elementor-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		[
			'hello-elementor-theme-style',
		],
		HELLO_ELEMENTOR_CHILD_VERSION
	);

}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_scripts_styles', 20 );

function swarm_offer_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'organization' => '',
        'offering' => '',
    ), $atts, 'swarm_offer' );

    $organization_id = $atts['organization'];
    $offering_id = $atts['offering'];

    $api_url = "https://api.swarmplus.ca/organization/$organization_id/offering/$offering_id";

    $response = wp_remote_get( $api_url );

    if ( is_wp_error( $response ) ) {
        return '<p>Sorry, there was an error retrieving the offering.</p>';
    }

    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body );

    $title = $data->title;
    $description = $data->description;
    $image_url = $data->image_url;

    $output = '<div class="swarm-offer">';
    $output .= '<h2>' . esc_html( $title ) . '</h2>';
    $output .= '<p>' . esc_html( $description ) . '</p>';
    $output .= '<img src="' . esc_url( $image_url ) . '" />';
    $output .= '</div>';

    return $output;
}

add_shortcode( 'swarm_offer', 'swarm_offer_shortcode' );

