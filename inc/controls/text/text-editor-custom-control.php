<?php
namespace PMPro_Diagnostics\inc\controls\text;

defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return null;
}

/**
 * Class to create a custom tags control
 */
class Text_Editor_Custom_Control extends \WP_Customize_Control {

	/**
	 * Render content in customizer panel
	 */
	public function render_content() {
		?>
		<label>
			<span class="customize-text_editor customize-control-title">
				<?php echo esc_html( $this->label ); ?>
			</span>
		<?php
			$settings = array(
				'textarea_name' => $this->id,
			);
			wp_editor( $this->value(), $this->id, $settings );
		?>
		</label>
		<?php
	}
}
