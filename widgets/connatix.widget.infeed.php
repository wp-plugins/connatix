<?php
class Connatix_Widget_Infeed extends WP_Widget {

	public function __construct() {
        parent::__construct(
			'connatix_infeed_widget', // Base ID
			'Connatix InFeed Widget', // Name
			array( 'description' => 'Infeed Connatix Integration')
		);
	}

	public function widget( $args, $instance ) {
        global $wp_query; 
        
        echo $args['before_widget'];

        $token = "";
        
        if(is_array($instance) && isset($instance["token"]) && strlen($instance["token"]) > 0) 
            $token = $instance["token"];
        
        if($token != "")
            echo "<script type='text/javascript' src='http://cdn.connatix.com/min/connatix.renderer.infeed.min.js' mode='fast' data-connatix-token='".$token."'></script>";

        echo $args['after_widget'];
	}

	public function form( $instance ) {
        if ( isset( $instance[ 'token' ] ) ) {
			$token = $instance[ 'token' ];
		}
		else {
			$token = "";
		}
		?>
		<p>
		<label for=""><?php echo "Token: " ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'token' ); ?>" name="<?php echo $this->get_field_name( 'token' ); ?>" type="text" value="<?php echo esc_attr( $token ); ?>">
		</p>
		<?php 
	}

	public function update( $new_instance, $old_instance ) {
        $instance = array();
		$instance['token'] = ( ! empty( $new_instance['token'] ) ) ? strip_tags( $new_instance['token'] ) : '';

		return $instance;
	}
}