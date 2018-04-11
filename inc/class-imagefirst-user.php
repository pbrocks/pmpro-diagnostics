<?php

namespace ImageFirst_Customizer\inc;


defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );



class ImageFirst_User {
	public $username;

	public $name;
	public $email;
	public $database_number;

	public $customer_number;
	public $customer_database_name;

	/**
	 * @var Goodwill_Customer_Contact
	 */
	public $customerContact;

	public $notes;

	public $viewed;

	protected $database_name;

	/**
	 * A list of all the databases the user can access - maybe one
	 *
	 * @var array
	 */
	protected $database_names;

	/**
	 * An associative array of the database names with their descriptions
	 *
	 * @var array
	 */
	protected $database_descriptions;

	public static function get_instance() {
		// if ( ! array_key_exists( 'goodwill-user', $_SESSION ) ) {
		// $_SESSION['goodwill-user'] = new ImageFirst_User();
		// }
		// return $_SESSION['goodwill-user'];
		$current_user = get_current_user();
		return $current_user;
	}

	public function clear_customer() {
		$this->customerContact = null;
		$this->notes = null;
		$this->viewed = null;
		$this->customer_number = null;
		$this->customer_database_name = null;

		if ( ! headers_sent() ) {
			unset( $_COOKIE['viewed'] );
			setcookie( 'viewed', '', null, '/' );

			unset( $_COOKIE['uploaded-photos'] );
			setcookie( 'uploaded-photos', '', null, '/' );
		}
	}

	public function get_database_names() {
		if ( ! is_array( $this->database_names ) ) {
			$db = new wpdb(
				CCS_DB_USER,
				CCS_DB_PASSWORD,
				'tracs_common', // Database name
				CCS_DB_HOST
			);

			// BINARY is important because DISTINCT is case insensitive and thinks
			// databases like tracs_dataA and tracs_dataa are duplicates
			$database_names = $db->get_col(
				$db->prepare(
					'SELECT DISTINCT BINARY d.database AS "database" FROM (
					SELECT substr(program, 11) AS "database"
					FROM programs
					WHERE uname = %s
					AND program LIKE %s
					UNION ALL
					SELECT dbname as "database"
					FROM tracsdatabases
					WHERE dbnumber = %d
				) d',
					$this->username,
					'directopt_%',
					$this->database_number
				)
			);

			// Ignore specified databases (like test data)
			$database_names = array_diff(
				$database_names,
				unserialize( GOODWILL_IGNORE_DATABASES )
			);

			// Convert the existing databases into an array of strings
			$existing = goodwill_existing_tracs_databases();
			array_walk(
				$existing, function ( &$database ) {
					$database = $database->schema_name;
				}
			);

			// Only use existing databases
			$database_names = array_intersect(
				$existing,
				$database_names
			);

			$this->database_names = $database_names;
		}// End if().

		return $this->database_names;
	}

	public function get_db_name() {

		if ( $this->database_name === null ) {
			$db = new wpdb(
				CCS_DB_USER,
				CCS_DB_PASSWORD,
				'tracs_common', // Database name
				CCS_DB_HOST
			);

			$this->database_name = $db->get_var(
				$db->prepare(
					'SELECT dbname FROM tracsdatabases
					WHERE dbnumber = %d',
					$this->database_number
				)
			);
		}

		return $this->database_name;
	}

	public function get_db_description( $db_name ) {
		if ( $this->database_descriptions === null ) {
			$db = new wpdb(
				CCS_DB_USER,
				CCS_DB_PASSWORD,
				'tracs_common', // Database name
				CCS_DB_HOST
			);

			$descs = $db->get_results(
				'SELECT dbname, dbdesc
				FROM tracsdatabases'
			);

			if ( $descs ) {
				$this->database_descriptions = array();
				foreach ( $descs as $row ) {
					$this->database_descriptions[ $row->dbname ] = $row->dbdesc;
				}
			} else {
				error_log( $db->last_error );
			}
		}

		if ( is_array( $this->database_descriptions )
			&& isset( $this->database_descriptions[ $db_name ] )
		) {
			return $this->database_descriptions[ $db_name ];
		}

		return $db_name;
	}
}
