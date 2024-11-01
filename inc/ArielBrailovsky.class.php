<?php

/* 
 * Plugin Name:   ArielBrailovsky-ViralAd
 * Plugin URI:    http://tools.arielbrailovsky.com/viralad
 * Description:   Linea de publicidad en lo alto de la página. 
 *                Más info en <a href="http://tools.arielbrailovsky.com/viralad">http://tools.arielbrailovsky.com/viralad/</a>
 * Author:        Ariel Brailovsky
 * Desarrollo:    Ariel Brailovsky, J.Laso, M.Santos
 * Author URI:    http://tools.arielbrailovsy.com
 * 
 */



define ('ABRKY_PREFIX_FUNC', "aBrky_");
define ('ABRKY_PREFIX_TEXT', "ABrky ");

//error_reporting (E_ALL);

class ArielBrailovskyClass {
    
    /**  variables de la clase **/
	
    var $opciones   = array();   // opciones del plugin
    var $ajustes    = array();   // ajustes del plugin
    var $mi_request = array();   // contiene los parametros GET/POST
    var	$defAjustes = array(
        'font_family' => 'Arial', 
        'font_size'   => 11, 
        'font_color'  => '#0f0f0f',                             
        'bg_color'    => '#f0f0f0', 
        'border_color'=> '#555555', 
        'text_align'  => 'left',  
        'font_italic' => 0,
        'font_bold'   => 0,
        'font_underline'=>0                           
      );
    
    var $urlSitio      = '';  // la url del sitio WP
    var $pathWpContent = '';  // el path de wp-content dentro del sistema
	var $selectedId    = 0;   // ID del link que mostraremos
    var $activado      = 0;   // Si ya hemos mostrado la barra
    var $preactivado   = 0;
    var $html_content  = '';
	
    public function getPathWpContent() { return $this->pathWpContent; }
    function getUrlSitio() { return $this->urlSitio; }
    function getOpciones() { return $this->opciones; }
    function getAjustes() { return $this->ajustes; }
    
    
	/* ===================================================
     * determina la ubicación de las rutas
	 * =================================================== */
    function ArielBrailovskyClass(){
       // se queda con la parte de la ruta que pende de wp-content/plugins
       $mi_path = preg_replace("/^.*[\\\\\/]wp-content[\\\\\/]plugins[\\\\\/]/", "", dirname(__FILE__));
       $mi_path = str_replace('\\','/',$mi_path);
       $this->pathWpContent = $mi_path;
       $mi_path = get_bloginfo('wpurl');
       $this->urlSitio = (strpos( $mi_path,'http://')) ? $mi_path : get_bloginfo('siteurl') ;
	   //Comprobamos si el plugin está habilitado
	   $this->activado = get_option('abva_activated');
    }
	
	/* ===================================================
     * Función de instalación
	 * =================================================== */
	function abva_install() {
		global $wpdb;
		
		ob_start();
		$abva_version = "1.0.8";
		$table_name_options = $wpdb->prefix . 'abva_options';
		$table_name_data    = $wpdb->prefix . 'abva_data';
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		
		if (! $st = dbDelta("DROP TABLE IF EXISTS " . $table_name_options)) add_option("abva_error1","status: ".$st);
		if (! $st = dbDelta("DROP TABLE IF EXISTS " . $table_name_data)) add_option("abva_error2","status: ".$st);

		$sql = "CREATE TABLE " . $table_name_options . " (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				id_ad mediumint(9) NOT NULL,
				param VARCHAR(55) DEFAULT '' NOT NULL,
				value VARCHAR(55) DEFAULT '' NOT NULL,
				UNIQUE KEY id (id)
				);";
		
		if (!$st = dbDelta($sql))  add_option('avba_error3',"status ".$st);
	
		$sql = "CREATE TABLE " . $table_name_data . " (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				`text` VARCHAR(255) DEFAULT '' NOT NULL,
				url VARCHAR(255) DEFAULT '' NOT NULL,
				active TINYINT(1) DEFAULT 0,
				priority MEDIUMINT(3) DEFAULT 0,
				total_clicks INT(9) DEFAULT 0,
				last_clicks INT(9) DEFAULT 0,
				impressions INT(9) DEFAULT 0,
				UNIQUE KEY id (id)
				);";
		
		if (!$st = dbDelta($sql))  add_option('avba_error4',"status ".$st);
						
	    // ahora se inserta un anuncio de ejemplo
		if (!$st =
		      $wpdb->query("INSERT INTO ".$table_name_data." (id, text, url, active, priority) VALUES
			            (1, '<strong>Consigue Gratis Tu Propio ViralAd</strong><br/>Click Aqu&iacute', 'http://tools.arielbrailovsky.com/viralad', 1, 50)")
	    )  add_option('avba_error5',"Excepción capturada: ".$st);
        
        				
		if (!$st =
			$wpdb->query("INSERT INTO ".$table_name_options." (id_ad, param, value) VALUES
			(1, 'font_family', 'Arial'), 
			(1, 'font_size', 16), 
			(1, 'font_color', '#0f0f0f'),                             
			(1, 'bg_color', '#ffffff'), 
			(1, 'border_color', '#ffffff'), 
			(1, 'text_align', 'center'),
			(1, 'font_italic', 0),
			(1, 'font_underline', 0),
			(1, 'font_bold', 0)")
	    )  add_option('avba_error6',"Excepción capturada: ".$st);
        			
		add_option("abva_version", $abva_version);
		add_option("abva_verified", 0);
		add_option("abva_idcb", "");
		update_option("abva_chart","SI");   // utilizar las gráficas de google en lugar de las propias
		ob_clean();
	}

	


	/* ===================================================
     * Función de desinstalación
	 * =================================================== */
	function abva_uninstall() {
		global $wpdb;
		$table_name_options = $wpdb->prefix . 'abva_options';
		$table_name_data = $wpdb->prefix . 'abva_data';
	
		$wpdb->query("DROP TABLE IF EXISTS " . $table_name_options);
		$wpdb->query("DROP TABLE IF EXISTS " . $table_name_data);
	
		delete_option("abva_version");
		delete_option("abva_verified");
		delete_option("abva_idcb");
		delete_option("abva_error1");
		delete_option("abva_error2");
		delete_option("abva_error3");
		delete_option("abva_error4");
		delete_option("abva_error5");
		delete_option("abva_error6");
	}
	
	public function getParam($id=0, $param='font_family') {
		global $wpdb;
		if(strlen($param)) {
			$table_name_data = $wpdb->prefix . 'abva_options';
			$sql = "SELECT value FROM ".$table_name_data." WHERE param = '".$param."' AND id_ad = '".$id."'";
			//echo $sql;
			$data = $wpdb->get_var($wpdb->prepare($sql));
			return $data;
		} else
			return '';
	}
	
	
	/* ===================================================
	 * Determina un elemento a mostrar
	 * =================================================== */
	private function selectLink() {
		global $wpdb;
		$table_name_data = $wpdb->prefix . 'abva_data';
		$id = 0;
		//Calculamos el total de impresiones
		$sql = "SELECT SUM(impressions) total FROM ".$table_name_data." WHERE active = 1";
		$total = $wpdb->get_var($wpdb->prepare($sql));
		//Calculamos la prioridad total
		$sql = "SELECT SUM(priority) total FROM ".$table_name_data." WHERE active = 1";
		$totalPriority = $wpdb->get_var($wpdb->prepare($sql));
		//Consultamos cuáles no han cubierto aún su cupo para determinar uno al azar
		$sql = "SELECT id, priority, impressions FROM ".$table_name_data." WHERE active = 1 ";
		if($total>0)
			$sql .= "AND priority > (impressions/".$total.")*".$totalPriority;
		$sql .=" ORDER BY priority DESC";
		$data = $wpdb->get_results($sql);
		$i = array_rand($data);
		$row = $data[$i];
		$id = $row->id;
		//echo $id;
		//Si todos cubrieron el cupo, determinamos uno al azar
		if(!$id) {
			$sql = "SELECT id FROM ".$table_name_data." WHERE active = 1";
			$data = $wpdb->get_results($sql);
			$i = array_rand($data);
			$row = $data[$i];
			$id = $row->id;
		}
		
		$this->selectedId = $id;
	}
    
	/* ===================================================
	 * Preconstruye un link dado e incrementa una impresión
	 * =================================================== */
	private function prepareLink() {
		global $wpdb;
		$table_name_data = $wpdb->prefix . 'abva_data';
		$sql = "SELECT `text` FROM ".$table_name_data." WHERE id = '".$this->selectedId."'";
		$row = $wpdb->get_row($sql);
		$texto = html_entity_decode($row->text,ENT_COMPAT,"UTF-8");
		$url   = plugins_url();
		
		$show = "<a href='".$url."/arielbrailovsky-viralad/inc/clickViralAd.php?id=".$this->selectedId."' target='_blank'>".$texto."</a>";
		if(!is_admin()) {
			$wpdb->query("UPDATE ".$table_name_data." SET impressions = impressions + 1 
					WHERE id = ".$this->selectedId);
		}
		
		return $show;
	}
	
	/* ===================================================
	 * Construcción del div
	 * =================================================== */
	function displayDiv() {
		//if(!preg_match("/.*\/wp-admin\/.*/",$_SERVER['PHP_SELF'])) {
			$url = plugins_url();
			$tdeco = ($this->getParam($this->selectedId,'font_underline'))?'underline':'none';
			$tcursiva = ($this->getParam($this->selectedId,'font_italic'))?'italic':'normal';
			$tnegrita = ($this->getParam($this->selectedId,'font_bold'))?'bold':'normal';
			//$align = ($this->getParam($this->selectedId,'text_align')=='right')?'left':'right';
			$idcb = get_option('abva_idcb');
			$img = file_get_contents('http://tools.arielbrailovsky.com/viralad/getimage.php');
			$html = "
					<script type=\"text/javascript\">
						if(document.getElementById('wpadminbar'))
							document.getElementById('wpadminbar').style.display='none';
					</script>
					<script src=\"".$url."/arielbrailovsky-viralad/js/overlib.js\" type=\"text/javascript\"></script>

					<style type=\"text/css\">
					#wpadminbar {
						display:none;
					}
					#abva_main {
						z-index:500;
						position:relative;
						width:100%;
						padding-top:4px;
						padding-top:4px;
						padding-left:0px;
						padding-right:0px;
						top:0px;
						left:0px;
						text-align:center;
						background-color:".$this->getParam($this->selectedId,'bg_color').";
						border-bottom: ".$this->getParam($this->selectedId,'border_color')." solid 1px;
						border-top: ".$this->getParam($this->selectedId,'border_color')." solid 1px;
						height: 40px;
					}
					#abva_center {
						width:600px;
						font-style:".$tcursiva.";
						font-weight:".$tnegrita.";
						text-align:".$this->getParam($this->selectedId,'text_align').";
						font-family:".$this->getParam($this->selectedId,'font_family')."; 
						font-size:".$this->getParam($this->selectedId,'font_size')."px;
					}
					*,html,body { margin-top: 0px !important; }
					#abva_main a {
						color:".$this->getParam($this->selectedId,'font_color').";
						text-decoration:".$tdeco.";
					}
					.imgabva {border:solid 1px #000000;}
					</style>
					<div id=\"abva_main\">
					<span style='position:absolute; left:2px; font-size: 8px; line-height: 10px; bottom: 3px; text-align:left;'>
					   <a target='_blank' href='http://tools.arielbrailovsky.com/viralad/hop.php?idcb=".$idcb."' onmouseover=\"return overlib('<img src=\\\'".$img."\\\' width=250 height=250 class=imgabva />');\" onmouseout=\"return nd();\">
					   <img src=\"".$url."/arielbrailovsky-viralad/img/logo_ab.png\" border=\"0\" alt=\"Powered By ArielBrailovsky.com\" title=\"Powered By ArielBrailovsky.com\"/>
					   <!--Powered&nbsp;By<br/>ArielBrailovsky.com--></a>
					</span>
					<span id=\"abva_center\">".$this->prepareLink()."</span>
					<span style='position:absolute; right:2px; color:black;font-family:Verdana; font-size: 8px; line-height: 10px; bottom: 6px; text-align:right;'>
					<!-- AddThis Button BEGIN -->
						<div class=\"addthis_toolbox addthis_default_style addthis_32x32_style \">
						<a class=\"addthis_button_preferred_1\"></a>
						<a class=\"addthis_button_preferred_3\"></a>
						<a class=\"addthis_button_compact\"></a>
						</div>
						<script type=\"text/javascript\">var addthis_config = {\"data_track_clickback\":true};</script>
						<script type=\"text/javascript\" src=\"http://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4e157ddc4febd74b\"></script>
						<!-- AddThis Button END -->
					   </span></div>";
		//}
		return $html;
	}
	
	/* ===================================================
	 * Muestra el resultado del plugin
	 * =================================================== */
	function printViralAd() {
		if (!is_admin()) {
		  if(0 === $this->activado) {
			$this->activado = 1;
			$this->selectLink();
			$miDiv =  '$1'.$this->displayDiv();
			if ( 1 == $this->preactivado) {
				$html = ob_get_contents();
				ob_end_clean();				
				echo preg_replace( '/(\<body\s+.*\>)/i', $miDiv, $html );
				$this->preactivado = 0;
			}
		  }
	    }
	}
	
	
	function prePrintViralAd() {
		if (!is_admin() && !$this->preactivado && !$this->activado) {
			$this->preactivado = 1;
			ob_start();
		}
	}

}
?>
