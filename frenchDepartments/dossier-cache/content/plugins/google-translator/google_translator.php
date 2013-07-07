<?php
/*
Plugin Name:  Google Translator
Plugin URI: http://www.vjcatkick.com/?page_id=6575
Description: Translate your blog from sidebar instantly
Version: 0.0.4
Author: V.J.Catkick
Author URI: http://www.vjcatkick.com/
*/

/*
License: GPL
Compatibility: WordPress 2.6 with Widget-plugin.

Installation:
Place the widget_single_photo folder in your /wp-content/plugins/ directory
and activate through the administration panel, and then go to the widget panel and
drag it to where you would like to have it!
*/

/*  Copyright V.J.Catkick - http://www.vjcatkick.com/

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


/* Changelog
* Jan 24 2009 - v0.0.1
- Initial release
* Jan 27 2009 - v0.0.2
- bug fix
* Aug 26 2010 - v0.0.3
- added sponcer link
* June 28 2011 - v0.0.4
- removed sponcer link, due to google's api change
*/


function widget_google_translate_init() {
	if ( !function_exists('wp_register_sidebar_widget') )
		return;

	function widget_google_translate( $args ) {
		extract($args);

		$options = get_option('widget_google_translate');
		$title = $options['widget_google_translate_title'];
		$widget_google_translate_sl = $options['widget_google_translate_sl'];
		$widget_google_translate_append_sl = (boolean)$options['widget_google_translate_append_sl'];

		$output = '<div id="widget_google_translate"><ul>';

		// section main logic from here 

// code here
		$sel_options = '<option value="ar">Arabic</option><option value="bg">Bulgarian</option><option value="ca">Catalan</option><option value="zh-CN">Chinese (Simplified)</option><option value="zh-TW">Chinese (Traditional)</option><option value="hr">Croatian</option><option value="cs">Czech</option><option value="da">Danish</option><option value="nl">Dutch</option><option value="en" >English</option><option value="tl">Filipino</option><option value="fi">Finnish</option><option value="fr">French</option><option value="de">German</option><option value="el">Greek</option><option value="iw">Hebrew</option><option value="hi">Hindi</option><option value="id">Indonesian</option><option value="it">Italian</option><option value="ja">Japanese</option><option value="ko">Korean</option><option value="lv">Latvian</option><option value="lt">Lithuanian</option><option value="no">Norwegian</option><option value="pl">Polish</option><option value="pt">Portuguese</option><option value="ro">Romanian</option><option value="ru">Russian</option><option value="sr">Serbian</option><option value="sk">Slovak</option><option value="sl">Slovenian</option><option value="es">Spanish</option><option value="sv">Swedish</option><option value="uk">Ukrainian</option><option value="vi">Vietnamese</option>';

		$sel_options_tl = str_replace( '"en"', '"en" selected ', $sel_options );
		$c_pointer = '"' . $widget_google_translate_sl . '"';
		$sel_options_sl = str_replace( $c_pointer, $c_pointer . 'selected ', $sel_options );

		$output_form = '<form action="http://translate.google.com/translate">';
		$output_form .= '<input name="u" id="url" value="' . 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . '" type="hidden" />';
		if( $widget_google_translate_append_sl ) {
			$output_form .= 'Translate from:<br />';
			$output_form .= '<select name="sl" style="width:auto">' . $sel_options_sl . '</select><br />';
		}else {
			$output_form .= '<input name="sl" value="' . $widget_google_translate_sl . '" type="hidden" />';
		} /* if else */
		$output_form .= 'Translate to:<br />';
		$output_form .= '<select name="tl" style="width:auto">' . $sel_options_tl . '</select><br />';
		$output_form .= '<input name="hl" value="en" type="hidden" />';
		$output_form .= '<input name="ie" value="UTF-8" type="hidden" />';

		$output_form .= '<script type="text/javascript" >
		var thestr = window.location.href;
		var mystrlen = ' . strlen( "http://" . $_SERVER['SERVER_NAME'] ) . ';
		var sresult = thestr.indexOf( "' . $_SERVER['SERVER_NAME'] . '" );
		if( sresult == -1 || sresult >= mystrlen) {
			document.write( \'<input value="' . __( 'Translate' ) . '" type="submit" disabled /> \' );
			document.write( \'<a href="" target="_top" >' . __( 'return to original' ) . '</a>\' );
		}else{
			document.write( \'<input value="' . __( 'Translate' ) . '" type="submit" />\' );
		}
		</script>';
		$output_form .= '</form>';

		$output .= $output_form;
		$output .= '<div class="google_translate_footer" style="text-align:right; font-size:9px;color: #888;">';
		$output .= 'Powered by <a href="http://translate.google.com/" target="_blank" >Google Translate</a>.';
		$output .= '</div>';

		// 0.0.3 added
//		$output .= '<div class="google_translate_footer" style="text-align:right; font-size:9px;color: #888;">';
//		$output .= '<a href="http://www.asiatranslate.net/translation-services.html" target="_blank" >translation services</a>';
//		$output .= '</div>';


		// These lines generate the output
		$output .= '</ul></div>';

		echo $before_widget . $before_title . $title . $after_title;
		echo $output;
		echo $after_widget;
	} /* widget_google_translate() */

	function widget_google_translate_control() {
		$options = $newoptions = get_option('widget_google_translate');
		if ( $_POST["widget_google_translate_submit"] ) {
			$newoptions['widget_google_translate_title'] = strip_tags(stripslashes($_POST["widget_google_translate_title"]));
			$newoptions['widget_google_translate_sl'] = $_POST["widget_google_translate_sl"];
			$newoptions['widget_google_translate_append_sl'] = (boolean)$_POST["widget_google_translate_append_sl"];
		}
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('widget_google_translate', $options);
		}

		// those are default value
		if ( !$options['widget_google_translate_sl'] ) $options['widget_google_translate_sl'] = "en";

		// data for pref panel
		$widget_google_translate_sl = $options['widget_google_translate_sl'];
		$widget_google_translate_append_sl = (boolean)$options['widget_google_translate_append_sl'];

		$title = htmlspecialchars($options['widget_google_translate_title'], ENT_QUOTES);
?>

		<?php _e('Title:'); ?> <input style="width: 170px;" id="widget_google_translate_title" name="widget_google_translate_title" type="text" value="<?php echo $title; ?>" /><br />


		<?php _e('Base language:'); ?> 
		<?php $base_selector = '<select id="widget_google_translate_sl" name="widget_google_translate_sl" style="width:auto"><option value="ar">Arabic</option><option value="bg">Bulgarian</option><option value="ca">Catalan</option><option value="zh-CN">Chinese</option><option value="hr">Croatian</option><option value="cs">Czech</option><option value="da">Danish</option><option value="nl">Dutch</option><option value="en">English</option><option value="tl">Filipino</option><option value="fi">Finnish</option><option value="fr">French</option><option value="de">German</option><option value="el">Greek</option><option value="iw">Hebrew</option><option value="hi">Hindi</option><option value="id">Indonesian</option><option value="it">Italian</option><option value="ja">Japanese</option><option value="ko">Korean</option><option value="lv">Latvian</option><option value="lt">Lithuanian</option><option value="no">Norwegian</option><option value="pl">Polish</option><option value="pt">Portuguese</option><option value="ro">Romanian</option><option value="ru">Russian</option><option value="sr">Serbian</option><option value="sk">Slovak</option><option value="sl">Slovenian</option><option value="es">Spanish</option><option value="sv">Swedish</option><option value="uk">Ukrainian</option><option value="vi">Vietnamese</option></select> <br />';
			$c_pointer = '"' . $widget_google_translate_sl . '"';
			echo str_replace( $c_pointer, $c_pointer . 'selected ', $base_selector );
		?>

		<input id="widget_google_translate_append_sl" name="widget_google_translate_append_sl" type="checkbox" <?php if( $widget_google_translate_append_sl ) { echo "checked";}  ?>/> Add source language selector<br /><br />
		*Remember, this is 'machine' translation.


  	    <input type="hidden" id="widget_google_translate_submit" name="widget_google_translate_submit" value="1" />

<?php
	} /* widget_google_translate_control() */

	wp_register_sidebar_widget('Gtranslator','Google Translator', 'widget_google_translate');
	wp_register_widget_control('Gtranslator','Google Translator', 'widget_google_translate_control' );
} /* widget_google_translate_init() */

add_action('plugins_loaded', 'widget_google_translate_init');

?>