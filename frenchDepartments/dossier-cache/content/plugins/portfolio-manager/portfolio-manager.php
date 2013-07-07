<?php

	/*
	Plugin Name: Portfolio Manager
	Plugin URI: http://benjamin-devaublanc.com
	Description: Plugin permettant la gestion d'un portfolio
	Version: 1.0
	Author: Benjamin de Vaublanc
	Author URI: http://benjamin-devaublanc.com
	License: YODACODE2
	*/


	/**
	* Execution des fonctions sur les différents hooks
	**/
	add_action('init','portfolio_init');
	add_action('add_meta_boxes','portfolio_meta_box_add');
	add_action('save_post', 'portfolio_meta_box_save');
	add_action('manage_posts_custom_column','portfolio_manage_posts_custom_column', 10, 2);
	add_filter('manage_edit-work_columns','portfolio_manage_posts_columns');



	/**
	* Permet d'initialiser les fonctionnalités liés au carrousel
	**/
	function portfolio_init (){
		$labels = array(
			'name' => 'Site',
			'singular_name' => 'Site',
			'add_new' => 'Ajouter un nouveau site',
			'all_items' => 'Tous les sites',
			'edit_new_item' => 'Ajouter un nouveau site',
			'edit_item' => 'Editer un site',
			'new_item' => 'Nouveau travail',
			'view_item' => 'Voir le site',
			'search_items' => 'Rechercher un site',
			'not_found' => 'Aucun site',
			'not_found_in_trash' => 'Aucun site dans la corbeille',
			'parent_item_colon' => 'Site',
			'menu_name' => 'Portfolio',
		);

		register_post_type('work',array(

			'public' => true,
			'publicly_queryable' => false,
			'labels' => $labels,
			'menu_position' => null,
			'supports' => array('thumbnail'),
                        'menu_icon' => plugins_url() .'/portfolio-manager/images/portfolio.png',

		));

		add_image_size('work',1000,300,true);

		//wp_enqueue_script('script',plugins_url().'/portfolio-manager/js/script.js',array('jquery'),'1.1.1',true);


	}


	/**
	* Permet dajouter ue meta_box
	**/
	function portfolio_meta_box_add()
	{
	    add_meta_box( 'my-meta-box-id', 'Remplir la fiche du work', 'portfolio_meta_box_cb', 'work', 'normal', 'high' );
	}

	/**
	* Permet de mettre en place le rendu de la meta_box
	**/
	function portfolio_meta_box_cb($post)
	{

		wp_enqueue_script('uploader',plugins_url().'/portfolio-manager/js/uploader.js');
		// $post is already set, and contains an object: the WordPress post
	     global $post;

		$values = get_post_custom( $post->ID );
		$name = isset( $values['meta_box_title'] ) ? esc_attr( $values['meta_box_title'][0] ) : null;  
		$firstname = isset( $values['meta_box_client'] ) ? esc_attr( $values['meta_box_client'][0] ) : null;  
		$url = isset( $values['meta_box_url'] ) ? esc_attr( $values['meta_box_url'][0] ) : null;  
		$type = isset( $values['meta_box_type'] ) ? esc_attr( $values['meta_box_type'][0] ) : null;  
		$technic = isset( $values['meta_box_technic'] ) ? esc_attr( $values['meta_box_technic'][0] ) : null;  
		$time = isset( $values['meta_box_time'] ) ? esc_attr( $values['meta_box_time'][0] ) : null;  
		$images = isset( $values['meta_box_images'] ) ? esc_attr( $values['meta_box_images'][0] ) : null;  
		$descr = isset( $values['meta_box_descr'] ) ? esc_attr( $values['meta_box_descr'][0] ) : null;  
		

	    //Permet de créer un champ génerer pour prévenir des attaques  
        wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );   

    	?>  
	    <p>  
    		<label for="meta_box_title">Nom du projet</label>  
    		<input type="text" name="meta_box_title" id="meta_box_title" value="<?php echo $name; ?>" />  
        </p>
        <p>  
    		<label for="meta_box_client">Nom du client</label>  
    		<input type="text" name="meta_box_client" id="meta_box_title" value="<?php echo $firstname; ?>" />  
        </p>
        <p>  
    		<label for="meta_box_url">Nom de l'URL</label>  
    		<input type="text" name="meta_box_url" id="meta_box_url" value="<?php echo $url; ?>" />  
        </p>
        <p>  
    		<label for="meta_box_type">Type de préstation</label>  
    		<input type="text" name="meta_box_type" id="meta_box_type" value="<?php echo $type; ?>" />  
        </p>
        <p>  
    		<label for="meta_box_technic">Environnemnt technique</label>  
    		<input type="text" name="meta_box_technic" id="meta_box_technic" value="<?php echo $technic; ?>" />  
        </p>
        <p>  
    		<label for="meta_box_time">Durée du projet (en jours)</label>  
    		<input type="text" name="meta_box_time" id="meta_box_time" value="<?php echo $time; ?>" />  
        </p>
         <p class="uploader-container">  
    		<label for="meta_box_images">Images</label> 
    		<input type="hidden" name="meta_box_images" id="meta_box_images" value="<?php echo $images; ?>" />  
    		<a href="#" class="button add-image-portbolio">Uploader des images</a>
        </p>
    		<label for="meta_box_descr">Description</label>  
        <p>
    		<textarea name="meta_box_descr" cols="90" rows="10" id="meta_box_descr"><?php echo $descr; ?></textarea>
        </p>

	    <?php
	}


	/**
	* Permet de sauvegarder la meta_box
	**/
	function portfolio_meta_box_save( $post_id )
	{
	    // Permet de gérer l'autosave
	    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

	    //Si la donnée est posté est que le champ géneré convient
	    if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'my_meta_box_nonce' ) ) return; 
	     
	    // Si le l'utilisateur courant a les droits d'edition
	    if( !current_user_can( 'edit_post' ) ) return;  
	      
	    //print_r($_POST); die();
	    
	    if( isset( $_POST['meta_box_title'] ) )  
	    	update_post_meta( $post_id, 'meta_box_title', $_POST['meta_box_title']);  

	    if( isset( $_POST['meta_box_client'] ) )  
	        update_post_meta( $post_id, 'meta_box_client', $_POST['meta_box_client']);  

	    if( isset( $_POST['meta_box_url'] ) )  
	        update_post_meta( $post_id, 'meta_box_url', $_POST['meta_box_url']);

	    if( isset( $_POST['meta_box_type'] ) )  
	        update_post_meta( $post_id, 'meta_box_type', $_POST['meta_box_type']);

	    if( isset( $_POST['meta_box_technic'] ) )  
	        update_post_meta( $post_id, 'meta_box_technic', $_POST['meta_box_technic']);

	    if( isset( $_POST['meta_box_time'] ) )  
	        update_post_meta( $post_id, 'meta_box_time', $_POST['meta_box_time']);

	    if( isset( $_POST['meta_box_images'] ) )  
	        update_post_meta( $post_id, 'meta_box_images', $_POST['meta_box_images']);

	    if( isset( $_POST['meta_box_descr'] ) )  
	        update_post_meta( $post_id, 'meta_box_descr', $_POST['meta_box_descr']);

	} 


	function portfolio_manage_posts_columns($columns) {
	    global $wp_query;
	    unset(
	            $columns['author'], $columns['tags'], $columns['comments']
	    );

	    $columns = array_merge(
	    	$columns, 
	    	array(
	    		'name' => __('Nom'),
		   		'client' => __('Client'),
		    	'url' => __('URL'),
		    	'type' => __('Type'),
		    	'technic' => __('Technique'),
		    	'time' => __('Durée'),
		    	'featured_image_portfolio' => __('Photo')
	    	)
	    );
	    return $columns;
	}

	function portfolio_manage_posts_custom_column($column, $post_id) {
	    switch ($column) {
	        case 'name':
	            $site_val = get_post_meta($post_id, 'meta_box_title', true);
	            break;
	        case 'client':
	            $site_val = get_post_meta($post_id, 'meta_box_client', true);
	            break;
            case 'url':
                $site_val = get_post_meta($post_id, 'meta_box_url', true);
                break;
           	case 'type':
           	    $site_val = get_post_meta($post_id, 'meta_box_type', true);
           	    break;
	        case 'technic':
	            $site_val = get_post_meta($post_id, 'meta_box_technic', true);
           	    break;
           	case 'time':
	            $site_val = get_post_meta($post_id, 'meta_box_time', true);
           	    break;
           	case 'featured_image_portfolio':
	            if (has_post_thumbnail())
	                $site_val = get_the_post_thumbnail($post_id,'thumbnail');
	            break;
	    }

	    if(isset($site_val) && !empty($site_val))echo $site_val;
	}

	function portfolio_show(){

		//ajout d'une feuille de style
		wp_register_style( 'render-portfolio', plugins_url('css/style.css', __FILE__) );
        wp_enqueue_style( 'render-portfolio' );

        //ajout du script pour la gallery du portfolio
        wp_enqueue_script('gallery',plugins_url().'/portfolio-manager/js/gallery.js');

		$works = new WP_query("post_type=work");

		?>
		<div id="render-portfolio">
			<div class="portfolio-container">	
		<?php

		while ( $works->have_posts() ) {
			$works->the_post();
			global $post;
		?>
			<div class="thumb-work">
				<h3><?php echo get_post_meta($post->ID,'meta_box_title',true);?></h3>
				<div class="main-picture"><?php echo the_post_thumbnail('work');?></div>
				<div class="gallery">
					<?php
						$images = explode(",",get_post_meta($post->ID,'meta_box_images',true));
						foreach ($images as $v) : 
					?>					
					<img src="<?php echo $v;?>"/>							
					<?php endforeach; ?>
				</div>
				<ul>
					<?php echo get_post_meta($post->ID,'meta_box_url',true) != '' ? '<li>URL : <a href="'.get_post_meta($post->ID,'meta_box_url',true).'" target="_blank">'.get_post_meta($post->ID,'meta_box_url',true).'</a></li>' : '';?>
					<?php echo get_post_meta($post->ID,'meta_box_client',true) != '' ? '<li>Client : '.get_post_meta($post->ID,'meta_box_client',true).'</li>' : '';?>
					<?php echo get_post_meta($post->ID,'meta_box_type',true) != '' ? '<li>Type de préstation : '.get_post_meta($post->ID,'meta_box_type',true).'</li>' : '';?>
					<?php echo get_post_meta($post->ID,'meta_box_technic',true) != '' ? '<li>Environnement technique : '.get_post_meta($post->ID,'meta_box_technic',true).'</li>' : '';?>
					<?php echo get_post_meta($post->ID,'meta_box_time',true) != '' ? '<li>Durée : '.get_post_meta($post->ID,'meta_box_time',true).' jours</li>' : '';?>
					
				</ul>
				
				<p><?php echo get_post_meta($post->ID,'meta_box_descr',true);?></p>
			</div>
			
		<?php
		}
		?>
			</div><!--end portfolio-container-->
		</div><!--end render-porfolio-->
		<?php

	}
	add_shortcode('portfolio','portfolio_show');





	






