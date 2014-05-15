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
        $post_id = $wp_query->post->ID;
        
        $title = apply_filters( 'widget_title', $instance['title'] );
        

        echo $args['before_widget'];
        
        $options = get_option(ConnatixJSPlugin::$OPTIONS_KEY);
        
        if($options != null && isset($options->_id) && $options->_id != $post_id)
            echo "<script type='text/javascript' src='http://cdn.connatix.com/min/connatix.renderer.infeed.min.js' mode='fast' data-connatix-token='".$options->_token."'></script>";
        
        echo $args['after_widget'];
	}

	public function form( $instance ) {
        
	}

	public function update( $new_instance, $old_instance ) {
        
	}
}