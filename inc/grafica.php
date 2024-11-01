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

if (!function_exists('json_encode')){
  function json_encode($a=false)  {
    if (is_null($a)) return 'null';
    if ($a === false) return 'false';
    if ($a === true) return 'true';
    if (is_scalar($a))    {
      if (is_float($a))      {
        // Always use "." for floats.
        return floatval(str_replace(",", ".", strval($a)));
      }

      if (is_string($a))      {
        static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
        return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
      } else   return $a;
    }
    $isList = true;
    for ($i = 0, reset($a); $i < count($a); $i++, next($a))   {
      if (key($a) !== $i)      {
        $isList = false;
        break;
      }
    }
    $result = array();
    if ($isList)    {
      foreach ($a as $v) $result[] = json_encode($v);
      return '[' . join(',', $result) . ']';
    }    else    {
      foreach ($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
      return '{' . join(',', $result) . '}';
    }
  }
}


	$_path = "";
	$curr_path = dirname( __FILE__ );
	$abva_path = str_replace("\\", "/", strstr($curr_path, 'wp-content'));
	$count = substr_count(trim($abva_path, '/'), '/');
	if($count > 0) {
			for ($i=0; $i<=$count; $i++)
				$_path .= "../";
	}


    // leer el archivo wp-config.php para tener los datos de acceso a la BBDD
    $wpconfig = file_get_contents($_path.'wp-config.php');
     
    preg_match_all(
       "#define\s*\(\s*(\'|\")(DB_NAME|DB_USER|DB_HOST|DB_PASSWORD)(\'|\")\s*,\s*(\'|\")(.*)(\'|\")\s*\)\s*;#im", 
       $wpconfig, 
       $matches, 
       PREG_SET_ORDER
    );
    
    foreach ($matches as $match) {
		${$match[2]} = $match[5];
	}

    //print ("db_name= $DB_NAME \ndb_user= $DB_USER\ndb_password= $DB_PASSWORD\ndb_host= $DB_HOST\n");
    unset($match);
    
    if (preg_match("/(^|\s*);?\\\$table_prefix\s*=\s*(\'|\")(.*)(\'|\")\s*;/i", $wpconfig, $match ))
       $wp_table_prefix = $match[3];
    else
       $wp_table_prefix = "";
               	                  	    
    // nombre de las tablas del plugin   
    $table_name_data    = $wp_table_prefix.'abva_data';
    
    //print ("table_name_data= $table_name_data; table_name_options= $table_name_options;\n");
        
    $wpdb = mysql_connect($DB_HOST, $DB_USER, $DB_PASSWORD);
    mysql_select_db($DB_NAME,$wpdb);

	function printErrorAndDie($msg){
		global $wpdb;
		print json_encode(array("error"=>true,"success"=>false,"data"=>$msg));
		if ($wpdb) mysql_close($wpdb);
		die();
	}
	
    // si no hay conexión a la BBDD dar error
	if (!$wpdb) printErrorAndDie("wpdb fail->".$_path.'wp-config.php');

	// Standard inclusions   
	require_once("./pChart/pChart/pData.class");
	require_once("./pChart/pChart/pChart.class");
	
	$views = array();
	$clicks = array();
	$rotulo = array();
	$i=0;
	$sql = "SELECT text, impressions, total_clicks FROM `{$table_name_data}` WHERE active = 1";

	$result = mysql_query($sql,$wpdb);
	if(mysql_num_rows($result)) {
		while($rs = mysql_fetch_array($result)) {		    
			$texto = preg_replace('/<br\s*\/?\s*>.*$/i','',html_entity_decode($rs['text']));  // esto deja el texto en una sola linea
			$rotulo[$i] = preg_replace('/\<\/?\w*\/?\>/i','',$texto); 
			$views[$i]  = $rs['impressions'];
			$clicks[$i] = $rs['total_clicks'];
			$i++;
		}
	}
	
	mysql_close($wpdb);
	
	

if( !function_exists('imagettftext') ) {

	function imagettftext($im, $size, $angle, $x, $y, $col, $font_file, $text) {
		$y -= 10;
		for ($i=0; $i<strlen($text); $i++) {					
			imagechar( $im, 4, $x, $y, $text[$i], 1 );
			$x += 7;
		}

    }
}

if(!function_exists('imageftbbox')) {
	function imageftbbox($size,$angle, $font_file,$text,$extrainfo=array()) {
		return array(10,10);
	}
}	
	
	

	// Dataset definition 
	$DataSet = new pData;
	$DataSet->AddPoint($views,"Impresiones");
	$DataSet->AddPoint($clicks,"Clicks");
	$DataSet->AddPoint($rotulo,"Rotulos");
	$DataSet->AddSerie("Impresiones");
	$DataSet->AddSerie("Clicks");
	$DataSet->SetAbsciseLabelSerie("Rotulos");
	
	// Initialise the graph
	$Test = new pChart(620,230);
	$Test->setFontProperties("pChart/Fonts/GeosansLight.ttf",8);
	$Test->setGraphArea(50,30,600,200);
	
	$Test->drawFilledRoundedRectangle(7,7,616,223,5,240,240,240);
	$Test->drawRoundedRectangle(5,5,618,225,5,230,230,230);

	$Test->drawGraphArea(255,255,255,TRUE);
	$Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,50,50,50,TRUE,0,2,TRUE);
	$Test->drawGrid(4,TRUE,230,230,230,50);

	//Colores
	$Test->setColorPalette(0,255,153,51);   //FF9933
	$Test->setColorPalette(1,0,204,0);      //00cc00
	$Test->setColorPalette(2,51,204,204);   //33cccc

	// Draw the line graph
	$Test->drawBarGraph($DataSet->GetData(),$DataSet->GetDataDescription());
	//$Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);
	
	// Finish the graph
	$Test->setFontProperties("pChart/Fonts/GeosansLight.ttf",8);
	$Test->drawLegend(515,45,$DataSet->GetDataDescription(),230,230,230);
	$Test->setFontProperties("pChart/Fonts/GeosansLight.ttf",14);
	$Test->drawTitle(200,22,'Anuncios en ArielBrailovsky-ViralAd',50,50,50,400);
	$Test->Stroke();



?>
