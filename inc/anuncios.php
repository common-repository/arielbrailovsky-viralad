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


    error_reporting( E_ALL & E_WARNING & E_NOTICE);
    
    session_start();
    

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
    $table_name_options = $wp_table_prefix.'abva_options'; 
    
    //print ("table_name_data= $table_name_data; table_name_options= $table_name_options;\n");
        
    $wpdb = mysql_connect($DB_HOST, $DB_USER, $DB_PASSWORD);
    mysql_select_db($DB_NAME, $wpdb);
    
	function printErrorAndDie($msg){
		global $wpdb;
		print json_encode(array("error"=>true,"success"=>false,"data"=>$msg,"sec"=>$_SESSION['abva_sec'],"session"=>$_SESSION));
		if ($wpdb) mysql_close($wpdb);
		die();
	}
	
    // si no hay conexión a la BBDD dar error
	if (!$wpdb) printErrorAndDie("wpdb fail->".$_path.'wp-config.php');

	// comprobar que se ha llamado correctamente desde nuestra web	
	$security = (isset($_REQUEST['security'])) ? $_REQUEST['security'] : "";
    if (!preg_match("/^[1-8b-y]{20}$/",$security)) printErrorAndDie("security break");
	// a partir de aquí se supone que la llamada es "legal"
	

define ("REC_PER_PAGE", 5);

function navigator($start,$total){
	//$total=100;
	$html = "";
	$actu = floor($start/REC_PER_PAGE);
	$pags = ceil($total/REC_PER_PAGE);
	$onkl = "__pagina(%d)";
	if ($start>=REC_PER_PAGE) 
		$html .= "<a href='#' onclick='".sprintf($onkl,($start-REC_PER_PAGE))."'>&lt;</a>&nbsp;";
    else
	    $html .= "&lt;&nbsp;";
	for ($p=0; $p<$pags; $p++) 
		if ($p!=$actu)
		   $html .= "<a href='#' onclick='".sprintf($onkl,$p*REC_PER_PAGE)."'>".($p+1)."</a>&nbsp;";
		else
		   $html .= "".($p+1)."&nbsp";
	if ($start<($total-REC_PER_PAGE)) 
		$html .= "<a href='#' onclick='".sprintf($onkl,$start+REC_PER_PAGE)."'>&gt;</a>";
    else
	    $html .= "&gt;";
	
	//print "start=$start;total=$total;<pre>".$html."</pre>";
	return $html;
}

//  GET ====  conseguir los anuncios
if ("GET" == $_SERVER['REQUEST_METHOD']) {
	
	$nRecords    = 0;
	$start       = (isset($_GET['start']))? $_GET['start'] : 0;
	$limit       = (isset($_GET['limit']))? $_GET['limit'] : REC_PER_PAGE;
	$limitClause = " LIMIT ".$start.",".$limit;
	
	$sql1 = "SELECT count(*) as total FROM ".$table_name_data;
	$sql2 = "SELECT * FROM ".$table_name_data.$limitClause;
	// primero averiguo cuantos registros tiene la tabla
	if ( -1 != $result = mysql_query($sql1,$wpdb)){
		   $data    = mysql_fetch_array($result);
		   $nRecords= $data[0];
		   if (-1 != $result= mysql_query($sql2,$wpdb)){
		     $lista = array();
		     $idx = 0;
		     while ($fila = mysql_fetch_assoc($result)) {		   
				 $text = $fila['text'];
				 if ($text) $fila["text"] = html_entity_decode($text,ENT_COMPAT,"UTF-8");
				 $id      = $fila['id'];
				 $lista[$idx] = $fila; 
		         $sql     = "SELECT * FROM ".$table_name_options." WHERE `id_ad`=".$id;
		         $result2 = mysql_query($sql,$wpdb);
		         $ajustes = array();
		         while($fila2 = mysql_fetch_assoc($result2)){
			         $clave = $fila2['param'];
			         $ajustes[$clave] = $fila2['value'];
		         }
		         $lista[$idx]['ajustes'] = $ajustes;
		         unset($ajustes);
		         $idx++;
		   }
		   mysql_close($wpdb);
		   $navi = navigator($start,$nRecords);
		   print json_encode(array("success"=>true,"error"=>false,"data"=>$lista,"total"=>$nRecords,"navi"=>$navi));
	   }
    }
    die();
}

    
