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

?>

<html charset="utf-8">
    <head>


<?php

   error_reporting( E_ALL ^E_NOTICE );

	$_path = "";
	$curr_path = dirname( __FILE__ );
	$abva_path = str_replace("\\", "/", strstr($curr_path, 'wp-content'));
	$count = substr_count(trim($abva_path, '/'), '/');
	if($count > 0) {
			for ($i=0; $i<=$count; $i++)
				$_path .= "../";
	}

  
   function genSecurityKey($length = 20){		
		$security = "";
		$possible = "12345678bcdefghijklmnopqrstuvwxy"; 
  
        $i = 0;     
        while ($i < $length) { 
           $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
           if (!strstr($security, $char)) { 
             $security .= $char;
             $i++;
           }
        } 
        return $security;
    }
    // generar una clave única para esta sesión
    $security = genSecurityKey();

    $ajustes = array(
        'font_family' => 'Arial', 
        'font_size'   => 11, 
        'font_color'  => '#0f0f0f',                             
        'bg_color'    => '#f0f0f0', 
        'border_color'=> '#555555', 
        'text_align'  => 'left',
        'font_italic' => 0,
        'font_bold'   => 1,
        'font_underline' => 0                             
      );
    
    $opciones   = array();   // opciones del plugin
    $mi_request = $_REQUEST;   // contiene los parametros GET/POST
    
    if (!defined("ABVAPATH")) define("ABVAPATH","..");
    if (!defined("ABVAURL"))  define("ABVAURL","../");
    
    
?>


<script type="text/javascript">
	var cargandoViralForm = new Image();
	cargandoViralForm.src = '<?php echo ABVAURL ?>/img/loading.gif';

    <!--//
        //  validación del formulario de edición/alta de anuncios //
		function __validarUnoForm() {
			var abva_text = document.getElementById('abva_text');
			var abva_url  = document.getElementById('abva_url');
			if ( abva_text.value == '' ) {
				alert('Es necesario indicar un nombre');
				abva_text.focus();
				return false;
			}
			if ( abva_url.value == '' ) {
				alert('Es necesario indicar una URL');
				abva_url.focus();
				return false;
			}
			return true;
		}
                  
        //---------------------------------------//
		function __abvaMarcarDes(parent) {
			var now = parent.checked;
			var frm = document.abvaform2;
			var len = frm.elements.length;
			for ( i=0; i<len; i++ ) {
				if ( frm.elements[i].name=='abva[delete][]' ) {
					frm.elements[i].checked = now;
				}
			}
		}
		
		//---------------------------------------//
		
		function __abvaDeleteChecked() {
		  if (confirm("¡Atención!\n\nVoy a borrar los marcados.\n\n¿Estás seguro?")) {
			document.body.style.cursor = "wait";
			var lista = document.abva_anuncios_form;
			var re = /abva_check\[(\d+)\]/;
			for (var i=0; i<lista.elements.length; i++) {
				if (lista.elements[i].checked) {
					name = lista.elements[i].name;
					result = name.match(re);
				    __abvaBorrarAnuncio(result[1])
				}
			}
			setTimeout("recargarPagina()",500);
		  }
		}
		//---------------------------------------//
		
		function __abvaResetChecked() {
		  if (confirm("¡Atención!\n\nVoy a reiniciar los marcados.\n\n¿Estás seguro?")) {
      	    document.body.style.cursor = 'wait';
			var lista = document.abva_anuncios_form;
			var re = /abva_check\[(\d+)\]/;
			for (var i=0; i<lista.elements.length; i++) {
				if (lista.elements[i].checked) {
					name = lista.elements[i].name;
					result = name.match(re);
					//alert("name="+name+"\n\nre="+result[1]);
				    __abvaResetAnuncio(result[1])
				}
			}
			setTimeout("recargarPagina()",500);
		  }
		}		
		
		//---------------------------------------//
		
		function __anyadirNuevo(){
			__abvaReiniciarFormulario()
			// seleccionar el primer campo
			document.getElementById("abva_text").focus();
		}
		
		// cambia el estado de visualización de un div determinado
		function __abvaShowHide(Div, Img) {
			var divCtrl = document.getElementById(Div);
			var Img     = document.getElementById(Img);
			if(divCtrl.style=="" || divCtrl.style.display=="none") {
				divCtrl.style.display = "block";
				if (Img) Img.src = '<?php echo ABVAURL ?>/img/minus.gif';
			}
			else if(divCtrl.style!="" || divCtrl.style.display=="block") {
				divCtrl.style.display = "none";
				if (Img) Img.src = '<?php echo ABVAURL ?>/img/plus.gif';
			}
		}
		
		//--------------------------------------------------------------//
		function __abvaVistaPrevia(src) {
			var preview_txt  = document.getElementById('preview_txt');
			var text_style_b = document.getElementById('abva_font_bold');
			var text_style_i = document.getElementById('abva_font_italic');
			var text_style_u = document.getElementById('abva_font_underline');
			
			if(src.id=='abva_font_family')
				preview_txt.style.fontFamily = src.value;
			if(src.id=='abva_font_size') {
				var valor = src.options[src.selectedIndex].value;
				preview_txt.style.fontSize = valor+'px';
			}
			if(src.id=='abva_text_align')
				preview_txt.style.textAlign = src.value;
				
			if(text_style_b.checked==true)
				preview_txt.style.fontWeight = 'bold';
			else
				preview_txt.style.fontWeight = 'normal';
			if(text_style_i.checked==true)
				preview_txt.style.fontStyle = 'italic';
			else
				preview_txt.style.fontStyle = 'normal';
			if(text_style_u.checked==true)
				preview_txt.style.textDecoration = 'underline';
			else
				preview_txt.style.textDecoration = 'none';
		}
		
		//-- vista previa todo --//
		function __abvaPreview() {
			var preview_txt  = document.getElementById('preview_txt');
			var b = document.getElementById('abva_font_bold');
			var i = document.getElementById('abva_font_italic');
			var text_style_u = document.getElementById('abva_font_underline');
			
			//Botones de color
			var abva_font_color_btn = document.getElementById('abva_font_color_btn');
			var abva_bg_color_btn = document.getElementById('abva_bg_color_btn');
			var abva_border_color_btn = document.getElementById('abva_border_color_btn');
			
			//Campos de color
			var abva_font_color = document.getElementById('abva_font_color');
			var abva_bg_color = document.getElementById('abva_bg_color');
			var abva_border_color = document.getElementById('abva_border_color');
			
			var abva_font_size = document.getElementById('abva_font_size');
			var valor_font = abva_font_size.options[abva_font_size.selectedIndex].value;
			
			preview_txt.style.fontFamily = document.getElementById('abva_font_family').value;
			preview_txt.style.fontSize = valor_font+'px';
            preview_txt.style.color = abva_font_color.value;	
            preview_txt.style.borderColor = abva_border_color.value;
            preview_txt.style.backgroundColor = abva_bg_color.value;
			abva_font_color_btn.style.backgroundColor = abva_font_color.value;
			abva_bg_color_btn.style.backgroundColor = abva_bg_color.value;
			abva_border_color_btn.style.backgroundColor = abva_border_color.value;
			preview_txt.style.textAlign = document.getElementById('abva_text_align').value;
			if (document.getElementById('abva_font_italic').checked)			   
			   preview_txt.style.fontStyle = 'italic';
 		    else
			   preview_txt.style.fontStyle = 'normal';
			if (document.getElementById('abva_font_underline').checked)
			   preview_txt.style.textDecoration = 'underline';
			else
			   preview_txt.style.textDecoration = 'none';
			if (document.getElementById('abva_font_bold').checked)
			   preview_txt.style.fontWeight = 'bold';
			else
			   preview_txt.style.fontWeight = 'normal';
		}
		//-------------------------------------------------------------//
		
		function __abvaCargarAnuncio(id){
			
			   function recogeJSON(){
	             if(mi_xhr.readyState == 4){
					if (mi_xhr.status==200){			           
			           var json=eval("("+mi_xhr.responseText+")");
			           if (json.success) {
						   document.getElementById("abva_id").value=json.data.id;						   
						   document.getElementById("abva_id2").value=json.data.id;
						   document.getElementById("abva_text").value=json.data.text;
						   document.getElementById("abva_url").value=json.data.url;
						   document.getElementById("abva_active").checked = (json.data.active=="1")?true:false;
						   document.getElementById("abva_priority").value=json.data.priority;
						   // poner los ajustes de la fuente, etc...
						   document.getElementById("abva_font_family").value = json.ajustes.font_family;
						   document.getElementById("abva_font_size").value = json.ajustes.font_size;
						   document.getElementById("abva_font_color").value = json.ajustes.font_color;
						   document.getElementById("abva_bg_color").value = json.ajustes.bg_color;
						   document.getElementById("abva_border_color").value = json.ajustes.border_color;
						   document.getElementById("abva_text_align").value = json.ajustes.text_align;
						   
					       document.getElementById("abva_font_italic").checked = (json.ajustes.font_italic)?true:false
					       document.getElementById("abva_font_underline").checked = (json.ajustes.font_underline)?true:false
					       document.getElementById("abva_font_bold").checked = (json.ajustes.font_bold)?true:false

						   // cuando se carguen los tipos actualizar la vista previa
						   document.getElementById("preview_txt").innerHTML=json.data.text;
						   // actualizar la previsualización
						   __abvaPreview();
						   // se posiciona en el primer campo del formulario para editar
						   document.getElementById("abva_text").focus();
						   document.body.style.cursor = 'default';
					   }else if(json.error) {
						   alert("Error\n\n"+json.data);
					   }
		            }
				 	document.getElementById('cargandoViralForm').innerHTML='';
	             } else {
				 	document.getElementById('cargandoViralForm').innerHTML='<img src="<?php echo ABVAURL ?>/img/loading.gif"/>';
				 }
               }
            document.body.style.cursor = 'wait';
			// traer los datos por AJAX
			peticion("GET",
			         "<?php echo ABVAURL ?>/inc/anuncio.php",
			         "id="+encodeURIComponent(id)+
			         "&nocache="+encodeURIComponent(Math.random())+
			         "&security=<?php echo $security ?>",
			         recogeJSON);
		}
		
		//----------------------------------------------------------------------//
		
		function __pagina(start){
			   function recogeJSON(){
	             if(mi_xhr.readyState == 4){
					if (mi_xhr.status==200){			           
			           var json=eval("("+mi_xhr.responseText+")");
			           if (json.success) {
							html = '<table class="wp-list-table widefat fixed tags">';
							html += '<thead>';
							html += '<tr>';
							html += '<th class="manage-column check-column"><input type="checkbox" name="checkall" onClick="__abvaMarcarDes(this)"/></th>';
							html += '<th class="manage-column column-name sortable desc" style="width:40%;"><strong>Texto/URL</strong></th>';
							html += '<th class="manage-column" style="width:12%;"><strong>Activo</strong></th>';
							html += '<th class="rightjus manage-column" style="width:18%;"><strong>Prioridad</strong></th>';
							html += '<th nowrap class="rightjus manage-column" style="width:20%;"><strong>Impresiones</strong></th>';
							html += '<th class="rightjus manage-column" style="width:10%;"><strong>Clicks</strong></th>';
							html += '</tr>';
							html += '</thead>';
							html += '<tbody class="list:tag">';
						   //alert(""+json.data.length+"\n"+json.data[0].text);
						   for (i=0; i<json.data.length; i++){
							   html += "<tr valign='top' class='manage-column'>";
                               html += "<th class='check-column'><input type='checkbox' name='abva_check["+json.data[i].id+"]'/></th>";
                               html += "<td><a href='#' onClick='__abvaCargarAnuncio("+json.data[i].id+"); return false;'>"+json.data[i].text+"</a><br/>";
                               html += "<a href='"+json.data[i].url+"' target='_blank'>"+json.data[i].url+"</a></td>";
                               html += "<td>"+json.data[i].active+"</td>";
                               html += "<td class='rightjus'>"+json.data[i].priority+"</td>";
                               html += "<td class='rightjus'>"+json.data[i].impressions+"</td>";
                               html += "<td class='rightjus'>Total: "+json.data[i].total_clicks+"<br/>Ultim: "+json.data[i].last_clicks+"</td></tr>";
							   //html += "<tr><td>"+json.data[i].id+"</td></tr>";
						   };
							html += '</tbody>';
							html += '<tfoot>';
							html += '<tr>';
							html += '<th class="manage-column" colspan="6">';
							html += '<input type="button" value="Borrar los marcados" onClick="__abvaDeleteChecked();" />&nbsp;&nbsp;';
							html += '<input type="button" value="Reiniciar contadores de los marcados" onClick="__abvaResetChecked();" />';    
							html += '<span id="abva_pg"><a href="#" onClick="__pagina(0);">*</a></span>';
							html += '</td></tr></tfoot></table>';
						   	document.getElementById("lista").innerHTML = html;
							document.getElementById("abva_pg").innerHTML = json.navi;
						 }else if(json.error) {
						   alert("Error\n\n"+json.data);
					   }
		            }
				 	document.getElementById('cargandoViralForm').innerHTML='';
	             } else {
				 	document.getElementById('cargandoViralForm').innerHTML='<img src="<?php echo ABVAURL ?>/img/loading.gif"/>';
				 }
               }

			// traer los datos por AJAX
			peticion("GET",
			         "<?php echo ABVAURL ?>/inc/anuncios.php",
			         "start="+encodeURIComponent(start)+
			         "&nocache="+encodeURIComponent(Math.random())+
			         "&security=<?php echo $security ?>",
			         recogeJSON);
		}
		
		//----------------------------------------------------------------------//
				
		function __abvaReiniciarFormulario(){
						   document.getElementById("abva_id").value='';						   
						   document.getElementById("abva_id2").value='';
						   document.getElementById("abva_text").value='Nuevo anuncio';
						   document.getElementById("abva_url").value='http://';
						   document.getElementById("abva_active").checked = false;
						   document.getElementById("abva_priority").value='5';	
						   document.getElementById("abva_font_family").value = 'Arial';
						   document.getElementById("abva_font_size").value = '11';
						   document.getElementById("abva_font_color").value = '#0f0f0f';
						   document.getElementById("abva_bg_color").value = '#f0f0f0';
						   document.getElementById("abva_border_color").value = '#555555';
						   document.getElementById("abva_text_align").value = 'left';
						   document.getElementById("abva_font_italic").checked = false;						   
						   document.getElementById("abva_font_underline").checked = false;
						   document.getElementById("abva_font_bold").checked = false;	
						   document.getElementById("preview_txt").innerHTML='Nuevo anuncio';
						   __abvaPreview();
						   document.getElementById("abva_text").focus();			
		}

        // 
        var newDiv = document.createElement('span');   
        newDiv.id = 'ajaxActivity';  
        var oImg=document.createElement("img");
        oImg.setAttribute('src', '<?php echo ABVAURL ?>/img/loader.gif');
        //oImg.setAttribute('alt', 'na');
        //oImg.setAttribute('height', '1px');
        //oImg.setAttribute('width', '1px');
        newDiv.appendChild(oImg);


function removeDiv(aDiv)
{
  var elSel = document.getElementById(aDiv);
  var i;
  for (i = elSel.length - 1; i>=0; i--) {
      elSel.remove(i);
  }
}

        function actividadAjax(on,divDest){
           var scr = document.getElementById(divDest);
           if (on==1) scr.parentNode.insertBefore(newDiv, scr)
                else  removeDiv("ajaxActivity");
		}
		//----------------------------------------------------------------------//
		function __abvaGrabarAnuncio(){
			
			function recogeJSON(){
				if(mi_xhr.readyState == 4){
					if (mi_xhr.status==200){
						actividadAjax(0,"barragrabar");
						var json=eval("("+mi_xhr.responseText+")");
						if (json.success) {
							// borrar todos los datos del formulario
							__abvaReiniciarFormulario();
							setTimeout("recargarPagina()",200);
						}else if(json.error) {
							alert("Error\n\n"+json.data);
						}
					}
				 	document.getElementById('cargandoViralForm').innerHTML='';
				} else {
				 	document.getElementById('cargandoViralForm').innerHTML='<img src="<?php echo ABVAURL ?>/img/loading.gif"/>';
				}
			}
			// validar el formulario antes
			if (__validarUnoForm()){
			document.body.style.cursor = 'wait';
			//selects
			var obj_prio = document.getElementById("abva_priority");
			var obj_font = document.getElementById("abva_font_family");
			var obj_fsiz = document.getElementById("abva_font_size");
			var obj_alig = document.getElementById("abva_text_align");
			//checkboxes
			var obj_activ = (document.getElementById("abva_active").checked)?1:0;
			var obj_fital = (document.getElementById("abva_font_italic").checked)?1:0;
			var obj_fbold = (document.getElementById("abva_font_bold").checked)?1:0;
			var obj_funde = (document.getElementById("abva_font_underline").checked)?1:0;
			// grabar los datos por AJAX
			actividadAjax(1,"barragrabar");
			var param = "id=" + encodeURIComponent(document.getElementById("abva_id").value)+
			            "&text="+encodeURIComponent(document.getElementById("abva_text").value)+
			            "&url="+encodeURIComponent(document.getElementById("abva_url").value)+
			            "&active="+encodeURIComponent(obj_activ)+
			            "&priority="+encodeURIComponent(obj_prio.options[obj_prio.selectedIndex].value)+
			            "&ffamily="+encodeURIComponent(obj_font.options[obj_font.selectedIndex].value)+
			            "&fsize="+encodeURIComponent(obj_fsiz.options[obj_fsiz.selectedIndex].value)+
			            "&fcolor="+encodeURIComponent(document.getElementById("abva_font_color").value)+
			            "&bgcolor="+encodeURIComponent(document.getElementById("abva_bg_color").value)+
			            "&bdcolor="+encodeURIComponent(document.getElementById("abva_border_color").value)+
			            "&talign="+encodeURIComponent(obj_alig.options[obj_alig.selectedIndex].value)+
			            "&fitalic="+encodeURIComponent(obj_fital)+
			            "&funderline="+encodeURIComponent(obj_funde)+
			            "&fbold="+encodeURIComponent(obj_fbold)+
			            "&nocache="+encodeURIComponent(Math.random())+
			            "&security="+encodeURIComponent("<?php echo $security ?>");
			peticion("POST","<?php echo ABVAURL ?>/inc/anuncio.php",param,recogeJSON);
		    }
		}
		//--------------------------------------------------------------------//
		function __abvaResetAnuncio(id){
			
			   function recogeJSON(){
	             if(mi_xhr.readyState == 4){
		            if (mi_xhr.status==200){
			           var json=eval("("+mi_xhr.responseText+")");
			           if (json.success) {
                           
					   }else if(json.error) {
						   alert("Error:\n\n"+json.data);
					   }
		            }
				 	document.getElementById('cargandoViralForm').innerHTML='';
	             } else {
				 	document.getElementById('cargandoViralForm').innerHTML='<img src="<?php echo ABVAURL ?>/img/loading.gif"/>';
				 }
               }
			// pedir la orden por AJAX
			var param = "id=" + encodeURIComponent(id)+
			            "&accion=reset"+
			            "&nocache="+encodeURIComponent(Math.random())+
			            "&security="+encodeURIComponent("<?php echo $security ?>");
			peticion("POST","<?php echo ABVAURL ?>/inc/anuncio.php",param,recogeJSON);
		    
		}
		//--------------------------------------------------------------------//
		function recargarPagina(){
			//document.body.style.cursor = 'default';
			location.reload();
		}
		
		var cuantosllevo = 0;
		function __abvaBorrarAnuncio(id){
			
			   function recogeJSON(){
	             if(mi_xhr.readyState == 4){
		            if (mi_xhr.status==200){
			           var json = eval("("+mi_xhr.responseText+")");
			           cuantosllevo--; 
			           if (cuantosllevo == 0) setTimeout("recargarPagina()",1000);
			           if (json.success) {
                           //if (json.output) alert(json.output);
					   }else if(json.error) {
						   alert("Error:\n\n"+json.data);
					   }
		            }
				 	document.getElementById('cargandoViralForm').innerHTML='';
	             } else {
				 	document.getElementById('cargandoViralForm').innerHTML='<img src="<?php echo ABVAURL ?>/img/loading.gif"/>';
				 }
               }
			// pedir la orden por AJAX
			var param = "id=" + encodeURIComponent(id)+
			            "&accion=delete"+
			            "&nocache="+encodeURIComponent(Math.random())+
			            "&security="+encodeURIComponent("<?php echo $security ?>");
			cuantosllevo++;
			peticion("POST","<?php echo ABVAURL ?>/inc/anuncio.php",param,recogeJSON);
		    
		}
		//--------- ajax ------------//

   function ini_xhr(){
	   if (window.XMLHttpRequest){
		   return new XMLHttpRequest();
	   }else if(window.ActiveXObject) {
		   return new ActiveXObject("Microsoft.XMLHTPP");
	   }
   }
   
   function peticion(metodo,url,param,callback){
	   mi_xhr = ini_xhr();
	   if (mi_xhr){ 			
		   mi_xhr.onreadystatechange = callback;
		   if (metodo=="GET"){
			   url = url+"?"+param;
		   }
		   mi_xhr.open(metodo,url,true);
		   mi_xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		   if (metodo=="POST") {
		      mi_xhr.send(param);
	       }else{
			  mi_xhr.send(null);
		   }
	   }
   }
   
		//-->
</script>


<style type="text/css">
.preview_class {
    padding: 4px 0; 
    background: <?php echo $ajustes['bg_color']?>; 
    border-bottom: 1px solid <?php echo $ajustes['border_color']?>; 
    text-align: <?php echo $ajustes['text_align']?>;
    font-family: <?php echo $ajustes['font_family']?>;
    font-size: <?php echo $ajustes['font_size']?>px;
    font-weight: <?php echo $ajustes['font_bold']?'bold':'normal'?>;
    font-style: <?php echo $ajustes['font_italic']?'italic':'normal'?>;
    text-decoration: <?php echo $ajustes['font_underline']?'underline':'none'?>; 
    color: <?php echo $ajustes['font_color']?>;
}
table, tbody, tfoot, thead, tr, th, td {
    padding: 3px;
}


#abva_main_tbl {
    width:100%;
    text-align: left;
}

.abva_int_tbl {
   width:100%;
   border:1px solid #dddddd; 
   background-color:#f1f1f1; 
   padding:0;
}

#abva_uno_tbl {
    width:100%; 
    border:1px solid #dddddd; 
    background-color:#f1f1f1; 
    padding:0;
}

.fila_impar {
    background-color: #f1eef0;
}

.fila_par {
    background-color: white;
}

.fila_cab {
    background-color: black;
    color: white;
    height: 50px;
    font-size: 24px;
}

.colum_titu {
    width:65px;
}


#abva_ajustes_vistaprevia,
#contenedor {
    overflow: hidden;
    float: none;
    position: relative;
}

#abva_preview{
	float: right;
	width: 39%;
}
#derecha {
    float:right;
    width: 32%;
}
#abva_ajustes_div {
	float:left;
	width: 59%;
}
#izquierda {
    float: left;
    width: 67%;

}

#advertising {
	border: 1px solid #DFDFDF;
	padding: 5px;
	color: gray;
	font-size: 11px;
	font-weigth: bold;
	width: 95%;
	background: url("<?php echo ABVAURL ?>/img/gray-grad.png") repeat-x scroll left top #DFDFDF;

}


#publicidad {
	border: 1px solid #DFDFDF;
	color: white;
	padding: 5px;
	font-size: 12px;
	font-weigth: bold;
	width: 95%;
	background: url("<?php echo ABVAURL ?>/img/gray-grad.png") repeat-x scroll left top #DFDFDF;

}

.rightjus {!important text-align: right;}

</style>

</head>

<?php  

	$_path = "";
	$curr_path = dirname( __FILE__ );
	$abva_path = str_replace("\\", "/", strstr($curr_path, 'wp-content'));
	$count = substr_count(trim($abva_path, '/'), '/');
	if($count > 0) {
			for ($i=0; $i<=$count; $i++)
				$_path .= "../";
	}
		
		$_path = __DIR__."/".$_path;
		
	//echo $_path.'wp-config.php';
	//ob_start();
    //include($_path.'wp-config.php');
    //ob_end_clean();

    // nombre de mis tablas    
    $table_name_data    = $wpdb->prefix.'abva_data';
    $table_name_options = $wpdb->prefix.'abva_options'; 
    
    //echo $table_name_data;
   
	/* ===================================================
	 * Obtiene el array de datos
	 * =================================================== */
	 /*
	 function listAds($order='') {
		global $wpdb;
		global $table_name_data;
		global $table_name_options;
   
		//
		if($order=='')		$ord = '';
			else			$ord = ' ORDER BY '.$order;
		$datos = $wpdb->get_results("SELECT * FROM ".$table_name_data.$ord);
		//print_r($datos);
		$result = array();
		$idx = 0;
		foreach($datos as $dato) { 
		   //print_r ($dato);
		   $sql = "SELECT * FROM ".$table_name_options." WHERE `id_ad`=".$dato->id;
		   //print "<br/>KEY=<strong>$key</strong><br/>SQL=<strong>$sql</strong><br/>";
		   $filas= $wpdb->get_results($sql);
		   $ajustes = array();
		   foreach($filas as $fila){
			   $clave = $fila->param;
			   $ajustes[$clave] = $fila->value;
		   }
		   foreach ($dato as $k=>$e) $result[$idx][$k]=$e;
		   $result[$idx]['ajustes'] = $ajustes;
		   unset($ajustes);
		   $idx++;
		}
		//print_r($result);
		return $result;
	}
*/
	   
   if ('POST' == $_SERVER['REQUEST_METHOD']) {
		if (('Guardar' == $_POST['abva_accion']) && ($abva_idcb = $_POST['abva_idcb']))
			update_option('abva_idcb',$abva_idcb);
			if ( "SI" == $_REQUEST['abva_chart']) update_option('abva_chart', 'SI');
			                                 else update_option('abva_chart', 'NO');

   }else{	   	
       $abva_idcb = get_option('abva_idcb');
       if (!$abva_idcb) update_option('abva_idcb',"?");
   }

function FontSelector ($def='',$name='abva[font_family]',$id='abva_font_family'){                    
    $fuentes = array (
       'Arial',
       'Comic Sans MS',
       'Courier New',
       'Georgia',
       'Impact',
       'Sans Serif',
       'Tahoma',
       'Times New Roman',
       'Verdana' 
       );

  $html = '<select name="'.$name.'" id="'.$id.'" style="width:105px;" onchange="__abvaVistaPrevia(this)">';
    
  foreach ($fuentes as $fuente){ 
     $html .= "<option value='".$fuente."' "
        .(($def == $fuente)? "selected" : "")." style='font-family:".$fuente.";'>{$fuente}</option>";
  }
  $html .= "</select>";
  return $html;
}



function estadistica(){
	global $wpdb;
	$table_name_data    = $wpdb->prefix.'abva_data';
	
	$views = array();
	$clicks = array();
	$rotulo = array();
	$i=0;
	$sql = "
	  SELECT `text`, `impressions`, `total_clicks` 
	  FROM `".$table_name_data."` 
	  WHERE active = 1"
	;

	$result = $wpdb->get_results($sql);
	$max    = 0;
	foreach($result as $rs) {
		    $texto = preg_replace('/<br\s*\/?\s*>.*$/i','',html_entity_decode($rs->text));  // esto deja el texto en una sola linea
			$rotulo[$i] = preg_replace('/\<\/?\w*\/?\>/i','',$texto); //html_entity_decode($rs->text));
			$views [$i] = $rs->impressions;
			$clicks[$i] = $rs->total_clicks;
			if ($rs->impressions > $max) $max = $rs->impressions;
			if ($rs->total_clicks > $max) $max = $rs->total_clicks;
			$i++;
	}

    $i--;
    
    $chd1 = "";
    $chd2 = "";
    $chx1 = "";
    for ($j=0; $j<=$i; $j++){		
		$chd1 .= $views[$j].(($j<$i)? ",":"");
		$chd2 .= $clicks[$j].(($j<$i)? ",":"");
		$chx1 .= "|".$rotulo[$j];
	}
	
	$i++;
	$max = floor($max * 1.15);
	
	$ancho = 700;
	$sep   = 3;
	$hueco = floor ( ($ancho-40)  / $i ) ;
	$barra = floor(($hueco - $sep) / 2);
	if ($barra > 100) { 
		$barra = 100; 
		$sep = floor (($ancho - (2*$i*$barra)) / $i);
	}
	
	$m1 = floor ($max / 4);

    $img =  "http://chart.apis.google.com/chart".
            "?cht=bvg".  					// gráfica vertical de barras
            "&chco=FF9933,00CC00,33CCCC". 	// colores de las barras
            "&chbh=".$barra.",0,".$sep.    	// ancho y separación de las barras
            "&chxt=x,y".         			// tipo de ejes
            "&chds=50,".$max.		  		// escala de datos
            "&chxl=0:".$chx1.       		// rótulo del eje X
            "|1:|0|".$m1."|".(2*$m1)."|".(3*$m1)."|".$max.                  // etiquetas de las derecha
            "&chd=t:".$chd1."|".$chd2.   	// datos
            "&chf=c,lg,90,c0c0c0,0.5,ffffff,0".
            "&chs=".$ancho."x180";				// tamaño del gráfico
    return //$table_name_data.$sql.print_r($result,true).
    $img;
}
	


  $orden = "";  // orden de los anuncios
?>


<body>

    <div id="contenedor">
        <div id="izquierda" style="overflow:hidden;">
            
    <!-- div principal que muestra los anuncios, ajustes y vista previa  -->
    <div id="abva_main_tbl" style="float:left;">
     
        <div id="abva_anuncios_div">
            <form name="abva_anuncios_form" method="post">

				<span></span><strong>Anuncios actuales</strong><span style="float:right;">&nbsp;&nbsp;<input type="button" value="A&ntilde;adir nuevo" onClick="__anyadirNuevo();" /></span></span>
				<div id="lista"></div>
            </form>
    </div>

<br/>

<div id="div_ajustes_vistaprevia">
	<!-- este div presenta los ajustes del plugin -->
    <div id="abva_ajustes_div">
        <form method="post" name="abva_ajustes_form">
        <table class="wp-list-table widefat fixed tags">
			<thead>
			  <tr>
                <th class="manage-column" width=30%><strong>Anuncio</strong>
					<span id="cargandoViralForm"></span>
				</th>
                <th class="manage-column" id="abva_id2"></th>
              </tr>
            </thead>
			<script>__pagina(0);</script>
            <tbody>
   		     <tr>
				<td colspan="2">            
                  <label for="text-label">Texto</label>
			      <input type="hidden" name="abva[id]" id="abva_id" value="" />
                  <input type="text" name="abva[text]" id="abva_text" value="" size="34" maxlength="255" /> &nbsp;&nbsp;
                </td>
             </tr>
             <tr>
				<td colspan="2">                           
                  <label for="url-label">URL:</label>
                  <input type="text" name="abva[url]" id="abva_url" value="http://" size="34" maxlength="255" /> &nbsp;&nbsp;
				</td>                  
             </tr>
             <tr>
				<td>Prioridad:</td>
				<td>
                  <select name="abva[priority]" id="abva_priority">
                            <?php for ($i = 0; $i <= 100; $i +=5) {
                                if ($i) { ?>
                                    <option value="<?php echo $i; ?>" <?php if ($i == $abva['priority'])
                                echo "CHECKED" ?>><?php echo $i; ?></option>
                                    <?php
                                }
                            }
                            ?>
                  </select>
                </td>
             </tr>
             <tr>
  				<td>Activo:</td>                  
                <td>
                  <input type="checkbox" id="abva_active" name="abva[active]"/>
				</td>
			</tr>
            <tr>			
              <td>Fuente:</td>
              <td><?php echo FontSelector($ajustes['font-familiy']) ?></td>
            </tr>
            <tr>
                <td>Tama&ntilde;o:</td>
                <td>
                  <select name="abva[font_size]" id="abva_font_size" style="width:105px;" onChange="__abvaVistaPrevia(this)">
                    <?php for($size=10;$size<17;$size++): ?>
                      <option value="<?php echo $size ?>" <?php if ($ajustes['font_size'] == $size) print 'selected'; ?> style="font-size:<?php echo $size ?>px"><?php echo $size; ?>px</option>
                    <?php endfor ?>
                  </select>
                </td>
            </tr>
            <tr>
                <td>Color<br/>texto:</td>
                <td>
                    <input type="text" name="abva[font_color]" id="abva_font_color" value="<?php echo $ajustes['font_color']; ?>" style="width:70px;" readonly />
                    <input type="button" name="abva_font_color_btn" id="abva_font_color_btn" title="Select Font Color" style="line-height:8px;width:20px;cursor:pointer;cursor:hand;background-color:<?php echo $ajustes['font_color']; ?>" onclick='window.open("<?php echo ABVAURL; ?>/inc/getcolor.php?pid=font_color","colorpicker","left=300,top=200,width=240,height=220,resizable=0");' />
                </td>
            </tr>
            <tr>
                <td>Color<br/>fondo:</td>
                <td>
                    <input type="text" name="abva[bg_color]" id="abva_bg_color" value="<?php echo $ajustes['bg_color']; ?>" style="width:70px;" readonly />
                    <input type="button" name="abva_bg_color_btn" id="abva_bg_color_btn" title="Select Background Color" style="line-height:8px;width:20px;cursor:pointer;cursor:hand;background-color:<?php echo $ajustes['bg_color']; ?>" onclick='window.open("<?php echo ABVAURL; ?>/inc/getcolor.php?pid=bg_color","colorpicker","left=300,top=200,width=240,height=220,resizable=0");' />
                </td>
            </tr>
            <tr>
                <td>Color<br/>borde:</td>
                <td>
                    <input type="text" name="abva[border_color]" id="abva_border_color" value="<?php echo $ajustes['border_color']; ?>" style="width:70px;" readonly />
                    <input type="button" name="abva_border_color_btn" id="abva_border_color_btn" title="Select Border Color" style="line-height:8px;width:20px;cursor:pointer;cursor:hand;background-color:<?php echo $ajustes['border_color']; ?>" onclick='window.open("<?php echo ABVAURL; ?>/inc/getcolor.php?pid=border_color","colorpicker","left=300,top=200,width=240,height=220,resizable=0");' />
                </td>
            </tr>
            <tr>
                <td>Alineaci&oacute;n:</td>
                <td>
                    <select name="abva[text_align]" id="abva_text_align" style="width:105px;" onChange="__abvaVistaPrevia(this)">
                        <option value="center" <?php if ($ajustes['text_align'] == 'center') print'selected'; ?>>Center</option>
                        <option value="left" <?php if ($ajustes['text_align'] == 'left') print'selected'; ?>>Left</option>
                        <option value="right" <?php if ($ajustes['text_align'] == 'right') print'selected'; ?>>Right</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Efecto:</td>
                <td>
                    <input type="checkbox" name="abva[font_bold]"      id="abva_font_bold"      <?php echo $ajustes['font_bold']; ?>      onclick="__abvaVistaPrevia(this)">Bold &nbsp; 
                    <input type="checkbox" name="abva[font_italic]"    id="abva_font_italic"    <?php echo $ajustes['font_italic']; ?>    onclick="__abvaVistaPrevia(this)">Italic &nbsp; 
                    <input type="checkbox" name="abva[font_underline]" id="abva_font_underline" <?php echo $ajustes['font_underline']; ?> onClick="__abvaVistaPrevia(this)">Underline &nbsp; 
                </td>
            </tr>
            </tbody>
            <tfoot>
              <tr>
                <th class="manage-column" colspan="2">
                    <input type="button" name="abva[guardar]" value="Guardar" onClick="__abvaGrabarAnuncio();" />&nbsp;<span id="barragrabar"></span>&nbsp;
                    <input type="button" name="abva[cancelar]" value="Cancelar" onClick="__abvaReiniciarFormulario();" />                    
                </td>
              </tr>
            </tfoot>
        </table>
    </form>
    </div>
    
    
    <!-- vista previa del anuncio genérico -->    
    <div id="abva_preview">
        <table class="wp-list-table widefat fixed tags">
            <thead>
				<tr>
                  <th class="manage-column"><strong>Vista previa</strong></hd>
                </tr>
            </thead>
            <tbody>
              <tr>
                <td class="preview_class" id="preview_txt">Ariel Brailovsky expertos en WordPress</td>
              </tr>
            </tbody>
        </table>
 
   
    <br/>
    
      <form method="post" url="<?php echo $_SERVER['PHP_SELF'] ?>" name="abva_ajustes_grales">
        <table class="wp-list-table widefat fixed tags">
			<thead>
              <tr>
                <th class="manage-column"><strong>Ajustes generales</strong></th>
              </tr>
            </thead>
            <tbody>
            <tr>
               <td><strong>id Clickbank:</strong>
                <input type="text" name="abva_idcb" id="abva_idcb" value="<?php echo $abva_idcb; ?>" size="15" /><br/>
                ¿ No est&aacute;s registrado en Clickbank ? 
                <a href="http://www.arielbrailovsky.com/go/ClickBank" target="_blank">Registrate aqu&iacute;</a>
               </td>
            </tr>
            <tr>
				<td><strong>&iquest;Gr&aacute;ficas google?</strong>
				<input type="checkbox" name="abva_chart" value="SI" id="abva_chart" <?php echo ("SI"==get_option('abva_chart'))? " checked='checked' ":"" ; ?> />
				</td>
            </tr>
            <tfoot>
              <tr>
                <th class="manage-column">
                    <input type="submit" name="abva_accion" value="Guardar" />
                </td>
              </tr>
            </tfoot>
        </table>
        </form>
    
    </div>
    
    </div> <!-- div de ajustes y vista previa -->

    </div> <!-- fin div id=abva_main_tbl -->
    

       </div> <!-- div izquierda -->
                
        <!-- contenedor de la derecha, con informacion adicional dde nosostros -->
        <div id="derecha">

            <div id="advertising" class="widefat">
                    Permite que otras personas se beneficien de este plugin, al mismo tiempo
                    podr&aacute;s obtener beneficios para t&iacute; 
                    <a href="#" onClick="__abvaShowHide('leermas_advertising','img_leermas_advertising');">
						<img src="<?php echo ABVAURL ?>/img/plus.gif" id="img_leermas_advertising" alt="[..]"/></a>
                    <div id="leermas_advertising" style="display:none;">
						Cuando tengas tu propia red de anuncios, el beneficio, 
						gota a gota repercutir&aacute; en tu bolsillo, en un par
						de clicks puedes crearte una cuenta en <a href="http://www.arielbrailovsky.com/go/ClickBank">ClickBank</a> y empezar
						a ganar dinero.<br/>
						<?php for ($i=1;$i<7;$i++) { echo "Error".$i."=".get_option("abva_error".$i)."<br/>"; } ?>
                    </div>
            </div>
            <br/>
            <div id="publicidad" class="widefat">
				<strong>Ariel Brailovsky expertos en WordPress</strong>
				<ul style="list-style:none;">
					<a href="http://www.arielbrailovsky.com/support/"><li>Soporte</li></a>
					<a href="http://tutoriales.arielbrailovsky.com/"><li>Tutoriales</li></a>
				</ul>
				<hr />
				<strong>Otros recursos sobre WordPress</strong>
				<hr />
				<strong>Social Media</strong>
				<ul style="list-style:none;">
				    <li><a href="http://www.facebook.com/ariel.brailovsky" style="color:#FFFFFF; text-decoration:none;" target="_blank"><img src="<?php echo ABVAURL ?>/img/facebook.jpg" width="20" border="0"/>&nbsp;Facebook</a></li>
				    <li><a href="http://twitter.com/arielbrailovsky" style="color:#FFFFFF; text-decoration:none;" target="_blank"><img src="<?php echo ABVAURL ?>/img/twitter.png" width="20" border="0"/>&nbsp;Twitter</a></li>
  			    </ul>
                    <!--ABVAPATH = <strong> <?php //echo ABVAPATH ?> </strong>-->
            </div>
            <br/>
            <div id="suscribir">
				<?php include_once(ABVAPATH."/inc/subscribe.php"); ?>
            </div>
        </div> <!-- div derecha -->
        <?php if( ("SI" == get_option('abva_chart')) || !function_exists('imagettftext') ): ?>
           <br/>
           <div style="padding-top: 20px; clear: left;">
			   <h2>Estad&iacute;sticas de los anuncios</h2>
	          <img src="<?php echo estadistica(); ?>" />
	       </div>
	    <?php else: ?>
		   <img src="<?php echo plugins_url()?>/arielbrailovsky-viralad/inc/grafica.php?nocache=<?php echo rand(); ?>" width="100%" />
		<?php endif; ?>
    </div>   <!-- contenedor -->  
    
</body>
</html>
