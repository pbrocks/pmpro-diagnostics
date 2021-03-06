<?php
namespace PMPro_Diagnostics\inc\controls\select;

defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return null;
}

class Post_Dropdown_Custom_Control extends \WP_Customize_Control {

	private $posts = false;

	public function __construct( $manager, $id, $args = array(), $options = array() ) {
		$postargs    = wp_parse_args(
			$options, array(
				'numberposts' => '-1',
			)
		);
		$this->posts = get_posts( $postargs );

		parent::__construct( $manager, $id, $args );
	}

	/**
	 * Render content in customizer panel
	 */
	public function render_content() {
		if ( ! empty( $this->posts ) ) {
			?>
	   <label>
		<span class="customize-post-dropdown customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<select name="<?php echo esc_html( $this->id ); ?>" id="<?php echo esc_html( $this->id ); ?>">
		<?php
		foreach ( $this->posts as $post ) {
			printf( '<option value="%s" %s>%s</option>', $post->ID, selected( $this->value(), $post->ID, false ), $post->post_title );
		}
			?>
			  </select>
			 </label>
			<?php
		}
	}
}
