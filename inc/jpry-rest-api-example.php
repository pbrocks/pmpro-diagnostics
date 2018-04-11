<?php

namespace FFTC\Api;

add_action( 'fftc_setup', __NAMESPACE__ . '\\setup' );
/**
 * Setup hooks.
 *
 * @author Jeremy Pry
 */
function setup() {
	add_action( 'rest_api_init', __NAMESPACE__ . '\\register_routes' );
}

/**
 * Register our rest routes.
 *
 * @author Jeremy Pry
 */
function register_routes() {
	$namespace = 'fftc/v1';

	// Register the routes
	register_rest_route( $namespace, '/submissions/(?P<id>[\d]+)', array(
		'args' => array(
			'id' => array(
				'description' => __( 'Unique identifier for the event.', 'fftc-bat-raffle' ),
				'type'        => 'integer',
				'required'    => true,
			),
		),
		array(
			'methods'  => \WP_REST_Server::READABLE,
			'callback' => __NAMESPACE__ . '\\get_form_status',
		),
		array(
			'methods'             => \WP_REST_Server::EDITABLE,
			'callback'            => __NAMESPACE__ . '\\update_form_status',
			'permission_callback' => __NAMESPACE__ . '\\check_update_permissions',
			'args'                => array(
				'status' => array(
					'description' => __( 'The submission status.', 'fftc-bat-raffle' ),
					'type'        => 'string',
					'enum'        => array( 'open', 'close' ),
					'required'    => true,
				),
			),
		),
	) );

	register_rest_route( $namespace, '/raffle/(?P<id>[\d]+)', array(
		'args' => array(
			'id' => array(
				'description' => __( 'Unique identifier for the event.', 'fftc-bat-raffle' ),
				'type'        => 'integer',
				'required'    => true,
			),
		),
		array(
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => __NAMESPACE__ . '\\get_raffle_result',
			'permission_callback' => __NAMESPACE__ . '\\check_raffle_permissions',
		),
	) );
}

/**
 * Get the form ID from the given request.
 *
 * @author Jeremy Pry
 *
 * @param \WP_REST_Request $request
 *
 * @return int|\WP_Error
 */
function get_form_id_from_request( $request ) {
	$form_id = \FFTC\Event\get_form_id_from_event( $request['id'] );
	if ( ! \GFAPI::form_id_exists( $form_id ) ) {
		return new \WP_Error(
			'rest_form_not_found',
			__( 'TThe form id could not be found for that event.', 'fftc-bat-raffle' ),
			array( 'status' => 404 )
		);
	}

	return $form_id;
}

/**
 * Get the form submission status.
 *
 * @author Jeremy Pry
 *
 * @param \WP_REST_Request $request
 *
 * @return \WP_REST_Response|\WP_Error
 */
function get_form_status( $request ) {
	$form_id = get_form_id_from_request( $request );
	if ( is_wp_error( $form_id ) ) {
		return $form_id;
	}

	// Determine whether the form is open.
	require_once( \GFCommon::get_base_path() . '/form_display.php' );
	$form   = \GFAPI::get_form( $form_id );
	$status = null === \GFFormDisplay::validate_form_schedule( $form ) ? 'open' : 'closed';

	return rest_ensure_response( array( 'status' => $status ) );
}

/**
 * Update the submission status for the form.
 *
 * @author Jeremy Pry
 *
 * @param \WP_REST_Request $request
 *
 * @return \WP_REST_Response|\WP_Error
 */
function update_form_status( $request ) {
	$form_id = get_form_id_from_request( $request );
	if ( is_wp_error( $form_id ) ) {
		return $form_id;
	}

	$status   = $request['status'];
	$event_id = $request['id'];
	switch ( $status ) {
		case 'open':
			// Verify that a winner has not yet been picked.
			$winner_id = \FFTC\Event\get_winner_from_event( $event_id );
			if ( $winner_id ) {
				return new \WP_Error(
					'winner_already_picked',
					__( 'The form cannot be reopened, because a winner has already been selected.' ),
					array( 'status' => 400 )
				);
			}

			\FFTC\Form\remove_end( $form_id );
			$status = 'open';
			break;

		case 'close':
			$timezone = tribe_get_event_meta( $event_id, '_EventTimezone' );
			\FFTC\Form\update_end( $form_id, new \DateTime( 'now', new \DateTimeZone( $timezone ) ) );
			$status = 'closed';
			break;
	}

	// Make sure to purge the caches.
	maybe_purge_cache();

	return rest_ensure_response( array( 'status' => $status ) );
}

/**
 * Check that someone doing an update has the correct permissions.
 *
 * @author Jeremy Pry
 *
 * @param \WP_REST_Request $request
 *
 * @return bool|\WP_Error
 */
function check_update_permissions( $request ) {
	if ( ! current_user_can( 'volunteer' ) ) {
		return new \WP_Error(
			'rest_forbidden_context',
			__( 'Sorry, you are not allowed to modify the status of this form.', 'fftc-bat-raffle' ),
			array( 'status' => rest_authorization_required_code() )
		);
	}

	return true;
}

/**
 * Check that someone triggering the raffle has the correct permissions.
 *
 * @author Jeremy Pry
 *
 * @param \WP_REST_Request $request
 *
 * @return bool|\WP_Error
 */
function check_raffle_permissions( $request ) {
	if ( ! current_user_can( 'volunteer' ) ) {
		return new \WP_Error(
			'rest_forbidden_context',
			__( 'Sorry, you are not allowed to get the raffle result for this form.', 'fftc-bat-raffle' ),
			array( 'status' => rest_authorization_required_code() )
		);
	}

	return true;
}

/**
 * Get the results of the raffle.
 *
 * @author Jeremy Pry
 *
 * @param \WP_REST_Request $request
 *
 * @return \WP_REST_Response|\WP_Error
 */
function get_raffle_result( $request ) {
	$event_id = $request['id'];
	$winner   = \FFTC\Raffle\handle_raffle( $event_id );
	if ( is_wp_error( $winner ) ) {
		return $winner;
	}

	$data = array(
		'first_name' => $winner['1.3'],
		'last_name'  => $winner['1.6'],
		'phone'      => $winner['3'],
		'email'      => $winner['2'],
	);

	return rest_ensure_response( array( 'winner' => $data ) );
}

/**
 * Maybe purge caches when running on WP Engine.
 *
 * @author Jeremy Pry
 */
function maybe_purge_cache() {
	if ( ! class_exists( 'WpeCommon' ) ) {
		return;
	}

	$page_ids = \FFTC\Main\get_form_pages();
	foreach ( $page_ids as $page_id ) {
		\WpeCommon::purge_varnish_cache( $page_id, true );
	}
}
