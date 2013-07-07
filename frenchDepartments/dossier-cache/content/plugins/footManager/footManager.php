<?php
	/*
	Plugin Name: Foot Manager
	Plugin URI: http://benjamin-devaublanc.com
	Description: Plugin permettant de gérer des équipe de football
	Version: 1.1
	Author: Benjamin de Vaublanc, Mickaël Andrieu
	Author URI: http://benjamin-devaublanc.com
	License: CCBySA
	*/


	/**
	* Execution des fonctions sur les différents hooks
	**/
	add_action('init','footManager_init');
	add_action('add_meta_boxes','footManager_meta_box_add');
	add_action('save_post', 'footManager_meta_box_save');
	add_action('manage_posts_custom_column', 'footManager_manage_posts_custom_column', 10, 2);
	add_filter('manage_edit-footballer_columns', 'footManager_manage_posts_columns');




	/**
	* Permet d'initialiser les fonctionnalités liés au carrousel
	**/
	function footManager_init (){

		$labels = array(
			'name' => 'Foot Manager',
			'singular_name' => 'Footballer',
			'add_new' => 'Ajouter un nouveau joueur',
			'all_items' => 'L\'équipe',
			'edit_new_item' => 'Ajouter un joueur',
			'edit_item' => 'Editer un joueur',
			'new_item' => 'Nouveau joueur',
			'view_item' => 'Voir le joueur',
			'search_items' => 'Rechercher un joueur',
			'not_found' => 'Aucun joueur trouvé',
			'not_found_in_trash' => 'Aucun joueur trouvé dans la corbeille',
			'parent_item_colon' => 'Footballer',
			'menu_name' => 'Foot Manager',
		);

		register_post_type('footballer',array(
			'public'             => true,
			'publicly_queryable' => false,
			'labels'             => $labels,
			'menu_position'      => null,
			'supports'           => array('thumbnail'),
            'menu_icon'          => plugins_url() .'/footManager/images/footManager.png'

		));

		add_image_size('footballer',1000,300,true);
	}


	/**
	* Permet dajouter ue meta_box
	**/
	function footManager_meta_box_add()
	{
	    add_meta_box( 'my-meta-box-id',
	    			  'Remplir la fiche du footballer',
	    			  'footManager_meta_box_cb',
	    			  'footballer',
	    			  'normal',
	    			  'high'
	    			);
	}

	/**
	* Permet de mettre en place le rendu de la meta_box
	**/
	function footManager_meta_box_cb($post)
	{
		// $post is already set, and contains an object: the WordPress post  
	    global $post;
        $panelView = plugins_url()."/footManager/partials/panel-view.html.php";

		$values = get_post_custom( $post->ID );
		$name = isset($values['meta_box_name']) ? $values['meta_box_name'][0] : null;
		$firstname = isset($values['meta_box_firstname']) ? $values['meta_box_firstname'][0] : null;  
		$role = isset($values['meta_box_role']) ? $values['meta_box_role'][0] : null;  
		$number = isset($values['meta_box_number']) ? $values['meta_box_number'][0] : null;  
		$check = isset($values['my_meta_box_check']) ? $values['my_meta_box_check'][0]  : null;

	    //Permet de créer un champ génerer pour prévenir des attaques
        wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
		$panelView = file_get_contents($panelView);
		$roleSelection = displayRolesSelector($role);
        $numberSelection = displayNumberSelector((int)$number);
		$html = Fm_emulateTwigTemplating($panelView, array('%name%' => $name,
							   '%firstname%' => $firstname,
							   '%roleSelection%' => $roleSelection,
						           '%numberSelection%' => $numberSelection
							  )
                                        );
	echo $html;
	}


	/**
	* Permet de sauvegarder la meta_box
	**/
	function footManager_meta_box_save($post_id)
	{
	    // Permet de gérer l'autosave
	    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

	    //Si la donnée est posté est que le champ géneré convient
	    if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'my_meta_box_nonce') ) return;

	    // Si le l'utilisateur courant a les droits d'edition
	    if( !current_user_can( 'edit_post' ) ) return;

	    if( isset( $_POST['meta_box_name'] ) )
	    	update_post_meta( $post_id, 'meta_box_name', wp_kses( $_POST['meta_box_name']) );

	    if( isset( $_POST['meta_box_firstname'] ) )
	        update_post_meta( $post_id, 'meta_box_firstname', wp_kses( $_POST['meta_box_firstname']) );

	    if( isset( $_POST['meta_box_role'] ) )
	        update_post_meta( $post_id, 'meta_box_role', esc_attr( $_POST['meta_box_role'] ) );  

	    if( isset( $_POST['meta_box_number'] ) )
	        update_post_meta( $post_id, 'meta_box_number', esc_attr( $_POST['meta_box_number'] ) );

	}

	function footManager_show($limit = 10){

		$frontView = plugins_url()."/footManager/partials/front-view.html.php";
		$frontView = file_get_contents($frontView);

		wp_register_style('table-footballManager', plugins_url('css/style.css', __FILE__));
       	wp_enqueue_style('table-footballManager');

       	$footballers = new WP_query("post_type=footballer&posts_per_page=$limit");
       	$rows = displayFootballersAsRows($footballers);

       	$html = Fm_emulateTwigTemplating($frontView, array('%rows%' => $rows));
		echo $html;
	}

	function footManager_manage_posts_columns($columns) {
	    global $wp_query;
	    unset(
	            $columns['author'], $columns['tags'], $columns['comments'],$columns['date']
	    );

	    $columns = array_merge($columns, array('name' => __('Nom'), 'firstname' => __('Prénom'),  'role' => __('Poste'),'title' => 'Footballer', 'number' => __('Numéro'), 'featured_image' => __('Photo')));
	    return $columns;
	}

	function footManager_manage_posts_custom_column($column, $post_id) {
	    switch ($column) {
	        case 'name':
	            $footballer_val = get_post_meta($post_id, 'meta_box_name', true);
	            break;
	        case 'firstname':
	            $footballer_val = get_post_meta($post_id, 'meta_box_firstname', true);
	            break;
            case 'role':
                $footballer_val = get_post_meta($post_id, 'meta_box_role', true);
                break;
           	case 'number':
           	    $footballer_val = get_post_meta($post_id, 'meta_box_number', true);
           	    break;
	        case 'featured_image':
	            if (has_post_thumbnail())
	                $footballer_val = get_the_post_thumbnail($post_id,'thumbnail');
	            break;
	    }
            //update_post_meta($post_id, 'meta_box_title', "footballer");

	   if(isset($footballer_val) && !empty($footballer_val))
	       echo $footballer_val;
	}

	function displayRolesSelector($effectiveRole){
		$roles = array('gardien','defenseur','milieu','attaquant');
		$selector = "<select name='meta_box_role' id='meta_box_role'>";

		foreach($roles as $role){
			$selector .= "<option value='$role'". Fm_selected($effectiveRole, $role) . ">". ucfirst($role) ."</option>";
		}
		$selector .= "</select>";

		return $selector;
	}
	
	function displayNumberSelector($number){
		$selector = "<select name='meta_box_number' id='meta_box_number'><option></option>";
            	for($i = 1; $i <= 14; $i++):
			$selector .= "<option value='$i'". Fm_selected($number, $i) . ">$i</option>";
            	endfor;
            	$selector .= "</select>";
		return $selector;
	}

	function displayFootballersAsRow($post)
	{
		$row = "<tr>
					<td>" . get_post_meta($post->ID,'meta_box_name',true) . "</td>
					<td>" . get_post_meta($post->ID,'meta_box_firstname',true) . "</td>
					<td>" . get_post_meta($post->ID,'meta_box_role',true) . "</td>
					<td>" . get_post_meta($post->ID,'meta_box_number',true) . "</td>
					<td>" . get_the_post_thumbnail($post->ID, 'thumbnail') . "</td>
				</tr>";
		return $row;
	}

	function displayFootballersAsRows($footballers)
	{
		$rows = '';
		while($footballers->have_posts()){
			$footballers->the_post();
			global $post;
			$rows .= displayFootballersAsRow($post);
		}
		return $rows;
	}

	function Fm_emulateTwigTemplating(&$fullString, $params)
	{
		foreach($params as $paramKey => $param){
			$fullString = str_replace("$paramKey", $param, $fullString);
		}
		return $fullString;
	}

	function Fm_selected($label, $match){
	    if($label === $match){
		return "selected='selected'";
	    }
	    else{
		return "";
	    }
	}
	
	add_shortcode('footballManager','footManager_show');
