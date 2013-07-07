<?php


/*
Plugin Name: Ma collection
Plugin URI: http://karimben.fr
Description: Description de mon plugin Widget qui sera affiché sur la page des plugins de mon blog WordPress
Author: Karim Ben
Version: 1.0
Author URI: http://www.mon-blog.fr/
*/


    function ma_collection()
	{
	    register_widget("ma_collection");

	}
		add_action("widgets_init", "ma_collection");    



class ma_collection extends WP_widget
{
 
    function ma_collection()
	{
	    $options = array(
                "classname" => "Collection Widget",
                "description" => "Recupere les objets de ma classe"
                );
 
	    $this->WP_widget("mon-plugin-widget", "Collection Widget", $options);

	}
	


	function widget($args, $instance)
	{


	    extract($args);
	    global $wpdb;
	    $table_prefix = $wpdb->prefix;
	 
		print_r($_POST);


	    echo $before_widget;
	  	echo '<div id="collectionteam">';
			$collections = new WP_query("post_type=collection");

			?>
				<table class="table-collectionteam">
					<?php

			while ( $collections->have_posts() ) {
				$collections->the_post();
				global $post;
			
		 
					?>
						<tr>
							<td><?php echo $post->post_title;?>
						</tr>
						<tr>
							<td><?php echo the_post_thumbnail('collection-image',array('style' => 'width:150px !important;'));?>
						</tr>
						<tr>
							<td><?php echo esc_attr(get_post_meta($post->ID,'meta_box_matricule',true));?></td>
						</tr>
						<tr>
							<td><?php echo esc_attr(get_post_meta($post->ID,'meta_box_category',true));?></td>
						</tr>
						<tr>
							<td><?php echo esc_attr(get_post_meta($post->ID,'meta_box_color',true));?></td>
						</tr>
						<tr>
							<td><?php echo esc_attr(get_post_meta($post->ID,'meta_box_description',true));?></td>
						</tr>
						<tr>
							<td><?php echo esc_attr(get_post_meta($post->ID,'meta_box_marque',true));?></td>
						</tr>
					
					<?php
				
			}
		echo '</table></div>';

	    echo $after_widget;
	 
	}

 
    function update($new_instance, $old_instance)
	{


	    $instance['title'] = esc_attr($new_instance['title']);
	    $instance['id'] = esc_attr($new_instance['id']);
	    $instance['choix'] = esc_attr($new_instance['choix']);

	    return $instance;
	} 

    function form($instance)
	{
	
	    $defaut = array(
	                "title" => 'BMX',
	                "id" => 'Sport'
	              
	                );

	    $instance = wp_parse_args($instance, $defaut);
	 
	    global $wpdb;
	    $table_prefix = $wpdb->prefix;

		$collections = new WP_query("post_type=collection");
		?>

		            <label for="collection-title">Nom</label>  
		<p>  
            <select name="collection-title" id="collection-title">  
          
		          <?php
		
			while ( $collections->have_posts() ) {
				$collections->the_post();
				global $post;

			 	$instance['title'] = $post->post_title;
			 	$instance['id'] = $post->ID;
			 	$instance['choix'];
				
				$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : null;  
				$id = isset( $instance['id'] ) ? esc_attr( $instance['id'] ) : null;  
				
			    ?>
		              <option value="<?php echo $id; ?>" <?php selected( $instance['choix'], $id ); ?> > <?php echo $title; ?></option>  
		    	<?php
			 		}
			 	?>
	 	    </select>  
        </p>
	<?php
	
		return $instance;	
	}
}


 
