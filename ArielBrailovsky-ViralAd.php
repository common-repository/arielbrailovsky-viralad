<?php
/* 
 * Plugin Name:   ArielBrailovsky-ViralAd
 * Plugin URI:    http://tools.arielbrailovsky.com/viralad
 * Description:   Linea de publicidad en lo alto de la página. 
 *                Más info en <a href="http://tools.arielbrailovsky.com/viralad">http://tools.arielbrailovsky.com/viralad/</a>
 * Author:        Ariel Brailovsky
 * Author URI:    http://tools.arielbrailovsy.com
 * Desarrollo:    Ariel Brailovsky, J.Laso, M.Santos
 * Version:       1.0.8
 *
 * 
 */


$mi_url = preg_replace("/^.*[\\\\\/]wp-content[\\\\\/]plugins[\\\\\/]/", '', dirname(__FILE__));
$mi_url = str_replace('\\','/',$mi_url);
define("ABVAURL", '../wp-content/plugins/'.$mi_url);

$mi_path = preg_match("/^.*[\\\\\/]wp-content[\\\\\/]plugins[\\\\\/]/", dirname(__FILE__), $matches);
if($mi_path) 
	$mi_path = $matches[0];
else
	$mi_path = "";
define("ABVAPATH", $mi_path.$mi_url);

require_once (ABVAPATH.'/inc/ArielBrailovsky.class.php');

class aBrkyViralAd extends ArielBrailovskyClass {
    /* ===================================================
     * Inicialización
     * =================================================== */
	function aBrkyViralAd() {
		if(get_option('abva_version')) {
			add_action('wp_head',   array(&$this, 'prePrintViralAd'));
			add_action('wp_footer', array(&$this, 'printViralAd'));
			add_filter('get_header',   array(&$this, 'prePrintViralAd'));
			add_filter('get_footer', array(&$this, 'printViralAd'));
		}
		add_action('admin_menu', array(&$this, '_AddMenu'));
		add_action('activate_arielbrailovsky-viralad/ArielBrailovsky-ViralAd.php', array(&$this, 'abva_install'));
		add_action('deactivate_arielbrailovsky-viralad/ArielBrailovsky-ViralAd.php', array(&$this, 'abva_uninstall'));
	}
	
    /* ===================================================
     * Muestra las opciones de configuración del plugin
     * =================================================== */
    function _PagOpciones(){
		 global $wpdb;
         include($this->getPathWpContent().'inc/ajustes.php');
    }
    
    /* ===================================================
     * función que añade el menú de ajustes al correspondiente de WP
     * =================================================== */
    function _AddMenu() {
        add_options_page(
			'ArielBrailovsky-ViralAd',        // título de la página
			'ArielBrailovsky-ViralAd',      // título del menu
			'manage_options',                   // rol para manejar
			$this->getPathWpContent(),                           // menú slug
			array(&$this, '_PagOpciones')   // callback
		);
    }
}  // fin de la clase ViralAd

$ViralAdPlugin = new aBrkyViralAd();

?>
