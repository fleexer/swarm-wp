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

//Add shortcode
function swarm_offer_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'organization' => '',
        'offering' => '',
        'language' => 'fr',
        'cache_time' => 3600, // cache for 1 hour (in seconds)
        'tags' => 'yes',
        'keywords' => 'yes',
        'url' => 'yes',
    ), $atts, 'swarm_offer' );

    $organization_id = $atts['organization'];
    $offering_id = $atts['offering'];
    $language = $atts['language'];
    $cache_time = $atts['cache_time'];
    $tags = $atts['tags'];
    $keywords = $atts['keywords'];
    $url = $atts['url'];

    // Generate a unique cache key for this shortcode based on the attributes
    $cache_key = 'swarm_offer_' . md5( $organization_id . '_' . $offering_id . '_' . $language );

    // Attempt to retrieve data from the cache
    $cached_data = get_transient( $cache_key );
    if ( $cached_data ) {
        $data = json_decode( $cached_data );
    } else {
        // Make an API request to get the data
        $api_url = "https://api.swarmplus.ca/organization/$organization_id/offering/$offering_id?language=$language";

        $response = wp_remote_get( $api_url );

        if ( is_wp_error( $response ) ) {
            return '<p>Sorry, there was an error retrieving the offering.</p>';
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body );

        // Cache the API response data for the specified time period
        set_transient( $cache_key, $body, $cache_time );
    }

    $offering_type = $data->offeringType;
    $title = $data->title->$language;
    $output = '<div class="swarm-offer">';
    $output .= '<h2>' . esc_html( $offering_type ) . ' - ' . esc_html( $title ) . '</h2>';

    if ( $tags == 'yes' ) {
        $tags = implode( ', ', $data->tags->$language );
        $output .= '<p><strong>Tags:</strong> ' . esc_html( $tags ) . '</p>';
    }

    if ( $keywords == 'yes' ) {
        $keywords = implode( ', ', $data->keywords->$language );
        $output .= '<p><strong>Keywords:</strong> ' . esc_html( $keywords ) . '</p>';
    }

    if ( $url == 'yes' ) {
        $offer_url = "https://symbiotiq.swarm.plus/organization/$organization_id/$offering_id";
        $output .= '<p><strong>Offer URL:</strong> <a href="' . esc_url( $offer_url ) . '">' . esc_html( $offer_url ) . '</a></p>';
    }

    $output .= '</div>';

    return $output;
}

add_shortcode( 'swarm_offer', 'swarm_offer_shortcode' );



