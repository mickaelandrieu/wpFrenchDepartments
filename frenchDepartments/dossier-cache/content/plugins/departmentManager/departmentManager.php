<?php
	
	/*
	Plugin Name: Department Manager 
	Plugin URI: http://mickael-andrieu.fr
	Description: Gestion des départements français
	Version: 1.0
	Author: Mickaël Andrieu
	Author URI: http://mickael-andrieu.fr
	License: MIT
	*/

	add_action('init','frenchDepartment_init');
	add_action('add_meta_boxes','frenchDepartment_meta_box_add');
	add_action('save_post', 'frenchDepartment_meta_box_save');
	add_action('admin_enqueue_scripts', 'add_scripts');
	add_action('manage_posts_custom_column', 'frenchDepartment_manage_posts_custom_column', 10, 2);
	add_filter('manage_edit-frenchDepartment_columns', 'frenchDepartment_manage_posts_columns');

	function frenchDepartment_init()
	{
		$labels = array(
			'name' => 'Départements français',
			'singular_name' => 'departement',
			'add_new' => 'Ajouter ou modifier un département',
			'all_items' => 'Départements',
			'edit_new_item' => 'Ajouter un nouveau département',
			'edit_item' => 'Editer un département',
			'new_item' => 'Nouveau département',
			'view_item' => 'Voir département',
			'search_items' => 'Rechercher un département',
			'not_found' => 'Non trouvé',
			'not_found_in_trash' => 'No trouvé dans la corbeille',
			'parent_item_colon' => 'departement',
			'menu_name' => 'Départements français',
		);

		register_post_type('department',array(
			'public' => true,
			'publicly_queryable' => false,
			'labels' => $labels,
			'menu_position' => null,
			'supports' => array('thumbnail','title'),
			'menu_icon'          => plugins_url() .'/departmentManager/images/departmentManager.png'

		));

		add_image_size('department-image',1000,300,true);

		function add_scripts()
		{
		    wp_enqueue_style( 'wp-color-picker' );
		    wp_enqueue_script( 'my-script-handle', plugins_url().'/departmentManager/js/script_collection.js', array( 'wp-color-picker' ), '1.0', true );
	
		}
	}

	function frenchDepartment_meta_box_add()
	{
	    add_meta_box( 'my-meta-box-id', 'Submit a department', 'frenchDepartment_meta_box_cb', 'department', 'normal', 'high' );  
	}

	function frenchDepartment_meta_box_cb($post) {

	    global $post;
	    $postView = plugins_url()."/departmentManager/partials/post-view.html.php";
		
		$values = get_post_custom( $post->ID );  
		$number = isset( $values['meta_box_number'] ) ? esc_attr( $values['meta_box_number'][0] ) : null;  
		$region = isset( $values['meta_box_region'] ) ? esc_attr( $values['meta_box_region'][0] ) : null;    
		$color = isset( $values['meta_box_color'] ) ? esc_attr( $values['meta_box_color'][0] ) : null;  
		$description = isset( $values['meta_box_description'] ) ? esc_attr( $values['meta_box_description'][0] ) : null;  
 
        wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );

        $postView = file_get_contents($postView);
		$regionSelector = displayRegionsSelector($region);

		$html = Dm_emulateTwigTemplating($postView,
										 array('%number%' => $number,
							   				   '%color%' => $color,
							   				   '%description%' => $description,
							   				   '%regionSelector%' => $regionSelector,
						           
							  				   )
                                        );

    	echo $html;
	}

	function frenchDepartment_meta_box_save($post_id)
	{ 
	    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return; 
	    if(!isset($_POST['meta_box_nonce']) || !wp_verify_nonce($_POST['meta_box_nonce'], 'my_meta_box_nonce')) return; 
	    if(!current_user_can( 'edit_post' )) return;  
	      
	     

  	    if(isset($_POST['meta_box_number']))  
    		update_post_meta( $post_id, 'meta_box_number', $_POST['meta_box_number']);  
     
	    if(isset($_POST['meta_box_region']))  
	        update_post_meta( $post_id, 'meta_box_region', esc_attr($_POST['meta_box_region']));  

	    if(isset( $_POST['meta_box_color']))  
	        update_post_meta( $post_id, 'meta_box_color', esc_attr($_POST['meta_box_color']));

	    if(isset($_POST['meta_box_description']))  
	        update_post_meta( $post_id, 'meta_box_description', esc_attr($_POST['meta_box_description']));

	} 


	function frenchDepartment_show($limit = 10)
	{
		$frontView = plugins_url()."/departmentManager/partials/front-view.html.php";
		//ajout d'une feuille de style
		wp_register_style('table-frenchDeparment', plugins_url('css/style.css', __FILE__));
        wp_enqueue_style('table-frenchDeparment');

        $departments = new WP_query("post_type=department&posts_per_page=$limit");
        $frontView = file_get_contents($frontView);
		$rows = displayDepartmentsAsRows($departments);
		$html = Dm_emulateTwigTemplating($frontView, array('%rows%' => $rows));

		echo $html;
	}

	

	function frenchDepartment_manage_posts_columns($columns) {
	    global $wp_query;
	    unset(
	            $columns['author'], $columns['tags'], $columns['comments'],$columns['date']
	    );

	    $columns = array_merge($columns, array('number' => __('Number'),
	    									   'region' => __('Region'),
	    									   'color' => __('Couleur'),
	    									   'description' => __('Description'),
	    									   'featured_image_department' => __('Photo')));
	    return $columns;
	}

	function frenchDepartment_manage_posts_custom_column($column, $post_id) {
	    
	    switch ($column) {
	        case 'title':
	            $dept_val = get_post($post_id, 'title');
	        case 'number':
	            $dept_val = get_post_meta($post_id, 'meta_box_number', true);
	            break;
            case 'region':
                $dept_val = get_post_meta($post_id, 'meta_box_region', true);
                break;
           	case 'color':
           	    $dept_val = get_post_meta($post_id, 'meta_box_color', true);
           	    break;
           	case 'description':
           	    $dept_val = get_post_meta($post_id, 'meta_box_description', true);
           	    break;
	        case 'featured_image1':
	        case 'featured_image_department':
	            if (has_post_thumbnail())
	                $dept_val = get_the_post_thumbnail($post_id,'thumbnail');
	            break;
	    }

	    echo $dept_val;
	}

	function displayRegionsSelector($regionSelected){

		$selector = "<select name='meta_box_region' id='meta_box_region'><optgroup label='Régions'>";
		$regions = array('alsace' => 'Alsace',
						 'aquitaine' => 'Aquitaine',
						 'auvergne' => 'Auvergne',
						 'basse-normandie' => 'Basse Normandie',
						 'bourgogne' => 'Bourgogne',
						 'bretagne' => 'Bretagne',
						 'centre' => 'Centre',
						 'champagne-ardenne' => 'Champagne Ardenne',
						 'corse' => 'Corse',
						 'franche-comte' => 'Franche Comté',
						 'haute-normandie' => 'Haute Normandie',
						 'ile-de-france' => 'Île de France',
						 'languedoc-roussillon' => 'Languedoc Roussillon',
						 'limousin' => 'Limousin',
						 'lorraine' => 'Lorraine',
						 'midi-pyrenees' => 'Midi Pyrénées',
						 'nord-pas-de-calais' => 'Nord-Pas-de-Calais',
						 'pays-de-la-loire' => 'Pays de la Loire',
						 'picardie' => 'Picardie',
						 'poitou-charentes' => 'Poitou-Charentes',
						 'provence-alpes-cote-azur' => 'Provence-Alpes-Côte d\'Azur',
						 'rhone-alpes' => 'Rhône-Alpes'
						);
            	foreach($regions as $key => $region):
					$selector .= "<option value='$key'". Dm_selected($regionSelected, $key) . ">$region</option>";
            	endforeach;
            	$selector .= "</optgroup></select>";
		return $selector;
	}

	function displayDepartmentsAsRow($post)
	{
		$row = "<tr>
					<td>" . get_post_meta($post->ID,'meta_box_number',true) . "</td>
					<td>" . get_post_meta($post->ID,'meta_box_region',true) . "</td>
					<td>" . get_post_meta($post->ID,'meta_box_color',true) . "</td>
					<td>" . get_the_post_thumbnail($post->ID,'thumbnail') . "</td>
					<td>" . get_post_meta($post->ID,'meta_box_description',true) . "</td>
				</tr>";
		return $row;
	}

	function displayDepartmentsAsRows($departments)
	{
		$rows = '';
		while($departments->have_posts()){
			$departments->the_post();
			global $post;
			$rows .= displayDepartmentsAsRow($post);
		}
		return $rows;
	}

	function Dm_emulateTwigTemplating(&$fullString, $params)
	{
		foreach($params as $paramKey => $param){
			$fullString = str_replace("$paramKey", $param, $fullString);
		}
		return $fullString;
	}

	function Dm_selected($label, $match){
	    if($label === $match){
		return "selected='selected'";
	    }
	    else{
		return "";
	    }
	}

	add_shortcode('departmentShow', 'frenchDepartment_show');