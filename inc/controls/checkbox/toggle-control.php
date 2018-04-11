<?php

namespace PMPro_Diagnostics\inc\controls\checkbox;

defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return null;
}

class Customizer_Toggle_Control extends \WP_Customize_Control {
	public $type = 'ios';

	public function info() {
		return __CLASS__;
	}

	/**
	 * Enqueue scripts/styles.
	 *
	 * @since 3.4.0
	 */
	// public function init() {
	// add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
	// }
	public function enqueue() {
		wp_enqueue_script( 'customizer-toggle-control', plugins_url( '/js/customizer-toggle-control.js', __FILE__ ), array( 'jquery' ), rand(), true );
		wp_enqueue_style( 'pure-css-toggle-buttons', plugins_url( '/pure-css-togle-buttons.css', __FILE__ ), array(), rand() );

		$css = '
			.disabled-control-title {
				color: #a0a5aa;
			}
			input[type=checkbox].tgl-light:checked + .tgl-btn {
				background: #0085ba;
			}
			input[type=checkbox].tgl-light + .tgl-btn {
			  background: #a0a5aa;
			}
			input[type=checkbox].tgl-light + .tgl-btn:after {
			  background: #f7f7f7;
			}

			input[type=checkbox].tgl-ios:checked + .tgl-btn {
			  background: #0085ba;
			}

			input[type=checkbox].tgl-flat:checked + .tgl-btn {
			  border: 4px solid #0085ba;
			}
			input[type=checkbox].tgl-flat:checked + .tgl-btn:after {
			  background: #0085ba;
			}

		';
		wp_add_inline_style( 'pure-css-toggle-buttons' , $css );
	}

	/**
	 * Render the control's content.
	 *
	 * @author soderlind
	 * @version 1.2.0
	 */
	public function render_content() {
		?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<div style="display:flex;flex-direction: row;justify-content: flex-start;">
			<?php if ( ! empty( $this->description ) ) : ?>
				<span class="customize-control-description" style="flex: 2 0 0; vertical-align: middle;"><?php echo esc_html( $this->description ); ?></span>
			<?php endif; ?>
				<input id="cb<?php echo $this->instance_number ?>" type="checkbox" class="tgl tgl-<?php echo $this->type?>" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link();
				checked( $this->value() ); ?> />
				<label for="cb<?php echo $this->instance_number ?>" class="tgl-btn"></label>
			</div>
			<?php if ( empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>
		</label>
		<?php
	}
}
