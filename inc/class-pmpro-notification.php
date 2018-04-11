<?php
namespace PMPro_Diagnostics\inc;

defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

/**
 * This class is used to display Admin notifications.
 *
 * Example usage -
 *   Info notification:
 *      new PMPro_Notification( 'Testing Info Notification' ); // OR
 *      new PMPro_Notification( 'Testing Info Notification', 'success' );
 *   Warning notification:
 *      new PMPro_Notification( 'Testing Warning Notification', 'warning' );
 *   Error notification:
 *      new PMPro_Notification( 'Testing Error Notification', 'error' );
 *   Info notification with stack trace:
 *      new PMPro_Notification( 'Testing Info Notification', 'success', true );
 **/
class PMPro_Notification {

	protected $title           = 'PMPro Notification';
	protected $dashicons_class = 'dashicons-welcome-view-site';

	public function __construct( $message, $type = 'success', $include_backtracke = false ) {
		$this->build( $message, 'notice-' . $type, $include_backtracke );
	}

	private function build( $message, $type, $include_backtrace ) {
		$backtrace_object = $include_backtrace ? debug_backtrace() : false;
		$notification     = new PMPro_Notification_Builder(
			$message,
			$type,
			$backtrace_object,
			$this->title,
			$this->dashicons_class
		);
		add_action( 'admin_notices', array( $notification, 'get_notification_html' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_style' ) );
	}

	public function enqueue_style() {
		if ( ! wp_style_is( 'admin-notification', 'enqueued' ) ) {
			wp_enqueue_style(
				'admin-notification',
				plugins_url( 'css/admin-notification.css', dirname( __FILE__ ) )
			);
		}
	}
}

class PMPro_Notification_Builder {
	private $message;
	private $notification_type;
	private $debug;
	private $title;
	private $dashicons_class;

	public function __construct( $message, $notification_type, $debug_backtrace_object, $title, $dashicons_class ) {
		$this->message           = $message;
		$this->notification_type = $notification_type;
		$this->debug             = $debug_backtrace_object;
		$this->title             = $title;
		$this->dashicons_class   = $dashicons_class;
	}

	public function get_notification_html() {
		?>
		<div id='message' class='notice <?php esc_html_e( $this->notification_type ); ?>'>
			<p>
				<span class='dashicons <?php esc_html_e( $this->dashicons_class ); ?> new-relic-notification-icon'></span>
				<strong><?php esc_html_e( $this->title ); ?>:</strong>

				<?php
					echo wp_kses(
						$this->message, array(
							'a'      => array(
								'href'   => array(),
								'target' => array(),
							),
							'strong' => array(),
							'b'      => array(),
							'i'      => array(),
						)
					);
				?>

				<?php if ( ! empty( $this->debug ) ) : ?>

					<?php $this->render_debug_stack(); ?>

				<?php endif; ?>

			</p>
		</div>
		<?php
	}

	private function render_debug_stack() {
		$stack = $this->debug;

		?>
		<div class='stack-trace-container'>
			<div class='stack-trace-title'>Stack trace:</div>
			<?php
			$stack_length = count( $stack );
			for ( $i = 0; $i < $stack_length; $i++ ) {
				$entry = $stack[ $i ];

				$class = '';
				$type  = '';
				if ( isset( $entry['class'] ) && isset( $entry['type'] ) ) {
					$class = $entry['class'];
					$type  = $entry['type'];
				}

				$function = $entry['function'];

				$args = $entry['args'];

				$file = 'NO_FILE';
				if ( isset( $entry['file'] ) ) {
					$file = $entry['file'];
				}

				$line = 'NO_LINE';
				if ( isset( $entry['line'] ) ) {
					$line = $entry['line'];
				}

				?>
				<div class='stack-trace'>
					&#8618; <span class='code'><span class='code-highlight'><?php echo esc_html( $class ); ?></span><?php echo esc_html( $type ); ?><span class='code-highlight'><?php echo esc_html( $function ); ?></span>(
					<?php

					$args_length = count( $args );
					for ( $j = 0; $j < $args_length; $j ++ ) {

						?>
						<span class='code-highlight'><?php echo esc_html( $args[ $j ] ); ?></span>
						<?php
						if ( $j !== $args_length - 1 ) :
							?>
							,
	<?php
						// Output a comma after each non-last argument
						endif;
					}

					?>
					)</span> in <?php echo esc_html( $file ); ?>:<?php echo esc_html( $line ); ?>
				</div>
				<?php
			}// End for().
			?>
		</div>
		<?php
	}
}
