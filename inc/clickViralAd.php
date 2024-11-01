<?php

/* 
 * Plugin Name:   ArielBrailovsky-ViralAd
 * Plugin URI:    http://tools.arielbrailovsky.com/viralad
 * Description:   Linea de publicidad en lo alto de la página. 
 *                Más info en <a href="http://tools.arielbrailovsky.com/viralad">http://tools.arielbrailovsky.com/viralad/</a>
 * Author:        Ariel Brailovsky
 * Desarrollo:    Ariel Brailovsky, J.Laso, M.Santos
 * Author URI:    http://tools.arielbrailovsy.com
 */

    //  este archivo redirige al link indicado por el Ads que se le pasa como
    //  parámetro en 'id'
	$ruta = "";
	$miruta = dirname( __FILE__ );
	$abva_ruta = str_replace("\\", "/", strstr($miruta, 'wp-content'));
	$c = substr_count(trim($abva_ruta, '/'), '/');

	if($c>0) {
		for($i=0; $i<=$c; $i++)
			$ruta .= "../";
	}
	include($ruta.'wp-config.php');

    $id = isset($_GET['id']) ? $_GET['id'] : 0;
    $abva_table = $table_prefix.'abva_data';

	if ($id) {
		$sql = "SELECT url FROM $abva_table WHERE id='$id'";
		$rs = mysql_query($sql);
		$redirect_url = mysql_result($rs,0,'url');

		// añadir http(s):// si no lo contiene ya la dirección del link
		if (strpos($redirect_url,'http://') === false && strpos($redirect_url,'https://') === false)
			$redirect_url = 'http://'.$redirect_url;

		// actualizar los clicks de ese Ad
		if(!is_admin() && !is_feed() && !is_user_logged_in()) {
			$sql = "UPDATE $abva_table SET total_clicks=total_clicks+1, last_clicks=last_clicks+1 WHERE id='$id'";
			mysql_query($sql);
		}
	} else {
		$redirect_url = "http://www.arielbrailovsky.com/wordpress";
	}

    header("location: $redirect_url");
    die();
?>
