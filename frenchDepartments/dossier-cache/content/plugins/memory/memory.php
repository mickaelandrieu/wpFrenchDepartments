<?php

	/*
	Plugin Name: Memory
	Plugin URI: http://benjamin-devaublanc.com
	Description: Plugin permettant de jouer au memory
	Version: 1.1
	Author: Benjamin de Vaublanc, Mickaël Andrieu
	Author URI: http://benjamin-devaublanc.com
	License: CCBySA
	*/

add_action('admin_menu','memory_menu');

function memory_menu() {
	add_menu_page(
		'Memory',
		'Memory',
		'activate_plugins',
		'memorypanel',
		'render_panel',
		plugins_url().'/memory/images/memory.png',
		3
	);

}

function render_panel() {
	add_option('option_memory_type');
	$panelView = plugins_url()."/memory/partials/panel-view.html.php";

	$currentMemoryType = get_option('option_memory_type');

	if( isset($_POST['memory_update']) && ($_POST['option_memory'] != $currentMemoryType)  ){
		update_option('option_memory_type', $_POST['option_memory']);
	}

	switch($currentMemoryType){
		case 1: $config_option = 'Memory activé avec les images de la gallerie'; break;
		case 2: $config_option = 'Memory activé avec les chiffres'; break;
		default: $config_option = 'Memory non activé, veuillez sélectionner une option';
	}
  	$panelView = file_get_contents($panelView);
  	$html = emulateTwigTemplating($panelView, array('%config_option%' => $config_option));
	echo $html;
}

function memory_show() {
	$currentMemoryType = get_option('option_memory_type');
	$gameView = plugins_url()."/memory/partials/game-view.html.php";

	wp_register_style('render-memory', plugins_url('css/style.css', __FILE__));
    wp_enqueue_style('render-memory');
	wp_enqueue_script('memory',plugins_url().'/memory/js/memory.js');

	switch($currentMemoryType){
		case 1:
			$list = createPictureList();
			break;

		case 2:
			$list = createNumberList();
			break;
		default:
			$list = "";
	}
	$gameView = file_get_contents($gameView);
	$html = emulateTwigTemplating($gameView, array('%list%' => $list));
	echo $html;
}

function createPictureList()
{
	$query_args = array(
						'post_type' => 'attachment',
						'post_mime_type' =>'image',
						'post_status' => 'inherit',
						'posts_per_page' => 5
					);

	$query_pictures = new WP_Query($query_args);

	$picturesList = "<ul class='datas hidden pictures'>";
		foreach($query_pictures->posts as $image){
		    $picturesList .= "<li>" . wp_get_attachment_url($image->ID) ."</li>";
		}
	$picturesList .= "</ul>";

	return $picturesList;
}

function createNumberList()
{
		return "<ul class='datas hidden'>
					<li>1</li>
				<li>2</li>
				<li>3</li>
				<li>4</li>
				<li>5</li>
				<li>6</li>
			</ul>";
}

function emulateTwigTemplating(&$fullString, $params)
{
	foreach($params as $paramKey => $param){
		$fullString = str_replace("$paramKey", $param, $fullString);
	}
	return $fullString;
}

add_shortcode('memory','memory_show');
