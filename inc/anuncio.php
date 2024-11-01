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


    ob_start();
    
    session_start();
    
    error_reporting(E_ALL);
    
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
    mysql_select_db($DB_NAME,$wpdb);
    
	function printErrorAndDie($msg){
		global $wpdb;
		$output = ob_get_contents();
		ob_clean();
		print json_encode(array("error"=>true,"success"=>false,"data"=>$msg,"output"=>$output));
		if ($wpdb) mysql_close($wpdb);
		die();
	}
	
	function printResultAndDie($data,$ajustes = null){
		global $wpdb;
		$output = ob_get_contents();
		ob_clean();
		print json_encode(array("error"=>false,"success"=>true,"data"=>$data,"ajustes"=>$ajustes,"output"=>$output));
		if ($wpdb) mysql_close($wpdb);
		die();		
	}
	
    // si no hay conexión a la BBDD dar error
	if (!$wpdb) printErrorAndDie("wpdb fail->".$_path.'wp-config.php');


	// comprobar que se ha llamado correctamente desde nuestra web	
	$security = (isset($_REQUEST['security'])) ? $_REQUEST['security'] : "";
    if (!preg_match("/^[1-8b-y]{20}$/",$security)) printErrorAndDie("security break");
	// a partir de aquí se supone que la llamada es "legal"
	
	$accion = (isset($_REQUEST['accion'])) ? $_REQUEST['accion'] : "";

//  GET ====  conseguir los datos del anuncio pasado como argumento
if ($_SERVER['REQUEST_METHOD']=="GET") {
	
	$id = $_GET['id'];
	if ($id) {
		   $result = @mysql_query("SELECT * FROM ".$table_name_data." WHERE `id`=".$id,$wpdb);
		   $data   = @mysql_fetch_array($result);
		   $text = $data["text"];
		   if ($text) $data["text"] = html_entity_decode($text,ENT_COMPAT,"UTF-8");
		   $result = @mysql_query("SELECT * FROM ".$table_name_options." WHERE `id_ad`=".$id,$wpdb);
		   $ajustes= array();
		   while ($fila = @mysql_fetch_assoc($result)) { 
		       $key  = $fila['param'];
			   $ajustes[$key] = $fila['value'];
		   }
		   printResultAndDie($data,$ajustes);
	}else{
		   printErrorAndDie("get: parameter fail");
    }
    die();
}

    
    // función que graba los ajustes del anuncio en la tabla options
    function setParam($id=0, $param, $value) {
		global $wpdb;
		global $data;
		global $table_name_options;
	
		if("" != $param) {
			$sql = "SELECT value FROM ".$table_name_options." WHERE param = '".$param."' AND id_ad = ".$id;
			$result = @mysql_query($sql,$wpdb); 
			if (!$result) {  
				$data['setparam'][$param]['error0'] = @mysql_error($wpdb); 
			}else {
			 if(@mysql_num_rows($result)) {
				$sql = "UPDATE ".$table_name_options." SET value = '".$value."' WHERE param = '".$param."' AND id_ad = ".$id;
				$result = @mysql_query($sql,$wpdb);
				if (!$result)  $data['setparam'][$param]['error1']=mysql_error($wpdb);
			 }else {
				$sql = "INSERT INTO ".$table_name_options." (`id_ad`, `param`, `value`) VALUES (".$id.", '".$param."', '".$value."')";
				$result = @mysql_query($sql,$wpdb); 
				if (!$result)  $data['setparam'][$param]['error2']=mysql_error($wpdb);
			 }
		  }
		}
	}
	
// función para evitar la inyección de SQL/Javascript
function limpia($str) {	        
    // elimino las etiquetas susceptibles de inyectar código SQL
    $str2 = preg_replace("#(SELECT|DELETE|UPDATE|INSERT|DROP)\s#i",'"$1 "',$str);
    // elimino las etiquetas susceptibles de inyectar código JS
    $str  = preg_replace("#(<(\/|\s*)SCRIPT.*>)#i","<pre>$1</pre>",$str2);
    return $str;
}

// POST===grabar o actualizar los datos del anuncio pasado como argumento
if (("POST" === $_SERVER['REQUEST_METHOD']) && (!$accion)){
	global $wpdb;
	
	$data = array();
	// datos del anuncio
	$id       = limpia($_POST['id']);
	$text     = htmlentities(limpia(utf8_decode($_POST['text'])));
	//$text = $_POST['text'];
	$url      = limpia($_POST['url']);
	$active   = ("0" == $_POST['active']) ? FALSE : TRUE;
	$priority = limpia($_POST['priority']);
	// datos de los ajustes html
	$ffamily  = limpia($_POST['ffamily']);
	$fsize    = limpia($_POST['fsize']);
	$fcolor   = limpia($_POST['fcolor']);
	$bgcolor  = limpia($_POST['bgcolor']);
	$bdcolor  = limpia($_POST['bdcolor']);
	$talign   = limpia($_POST['talign']);
	$fitalic  = ("0" == $_POST['fitalic']) ? FALSE : TRUE;
	$funderline=("0" == $_POST['funderline']) ? FALSE : TRUE;
	$fbold    = ("0" == $_POST['fbold']) ? FALSE : TRUE;
	// a trabajar
	if (!$text || !$url || !isset($active) || !$priority) 
	    printErrorAndDie("put: parameter fail ($text) ($url) ($active) ($priority)");
	else{
	    if ($id) {
		   $result = mysql_query("SELECT * FROM ".$table_name_data." WHERE `id`=".$id,$wpdb);
		   if (!$result)    $data['error'][]=mysql_error($wpdb); 
	    }
	    if ($id && mysql_num_rows($result)) {
			    // existe, hay que actualizar
			    $sql = "UPDATE ".$table_name_data." SET `text`='{$text}' ,`url`='{$url}' , `active`='{$active}' , `priority`= '{$priority}' WHERE `id`={$id}";
		        $result = @mysql_query($sql,$wpdb);
		        if (!$result)    $data['error'][] = @mysql_error($wpdb);
	    }else{
			    // no existe, insertar
			    $sql = "INSERT INTO ".$table_name_data." (`text`,`url`,`active`,`priority`,`total_clicks`,`last_clicks`,`impressions`) VALUES ('{$text}' ,'{$url}' , '{$active}' , '{$priority}', 0, 0, 0)";
		        $result = @mysql_query($sql, $wpdb );
		        if (!$result)    $data['error'][] = @mysql_error($wpdb); 
				$data['id'] = $id = @mysql_insert_id($wpdb);
		}
		setParam( $id, "font_family",   $ffamily   );
		setParam( $id, "font_size",     $fsize     );
		setParam( $id, "font_color",    $fcolor    );
		setParam( $id, "bg_color",      $bgcolor   );
		setParam( $id, "border_color",  $bdcolor   );
		setParam( $id, "text_align",    $talign    );
		setParam( $id, "font_italic",   $fitalic   );
		setParam( $id, "font_underline",$funderline );
		setParam( $id, "font_bold",     $fbold     );
		//--//
	    printResultAndDie($data);
   }	
   die();	
}


// POST && accion=delete   ===  borrar los datos del anuncio pasado como argumento
if (("POST" === $_SERVER['REQUEST_METHOD']) && ( "delete" === $accion )){
	$id = limpia($_POST['id']);
	if ($id) {
		// ahora a borrar el anuncio solicitado
		$result  = @mysql_query("DELETE FROM ".$table_name_data." WHERE `id`={$id}",$wpdb);
		// ahora a borrar las opciones del anuncio
		$result2 = @mysql_query("DELETE FROM ".$table_name_options." WHERE `id_ad`={$id}",$wpdb);
		// ahora indicar al llamante que todo ha salido bien
		printResultAndDie($result,$result2);
	}else{		
		printErrorAndDie("del: parameter fail");
	}
	die();
}	

// POST && accion=reset   ===  borrar el contador parcial de clicks del anuncio pasado como argumento
if (("POST" === $_SERVER['REQUEST_METHOD']) && ("reset" === $accion)){
	$id = limpia($_POST['id']);
	if ($id) {
		// poner a cero last_clicks del anuncio
		$sql = "UPDATE `".$table_name_data."` SET `last_clicks` = 0 WHERE `id`={$id}";
		$result = @mysql_query($sql,$wpdb );
		if (@mysql_errno($wpdb))
		    printErrorAndDie( "reset: ".@mysql_error($wpdb));
		else
   		    printResultAndDie($id);
	}else{		
		printErrorAndDie("res: parameter fail");
	}
	die();
}	


// si ha llegado hasta aquí es que ha podido haber algún error
printErrorAndDie("error desconocido");

?>
