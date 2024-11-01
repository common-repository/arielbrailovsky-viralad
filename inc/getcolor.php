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

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Elegir Color</title>
</head>
<script language="JavaScript" type="text/javascript">

var QSelem = new Array();

/* Retrieves the ID for which the color will be inserted into. */
function recuperarColor() {
  var querystring = window.location.search.substring(1);
  var elem = querystring.split('&');
  for (var i=0; i<elem.length; i++) {
    var pos = elem[i].indexOf('=');
    if (pos > 0) {
       var key = elem[i].substring(0,pos);
       var val = elem[i].substring(pos+1);
       QSelem[key] = val;
    }
  }
  if (QSelem['pid'] == 'font_color') {
	var pcolor = window.opener.document.getElementById('abva_font_color').value;
  } else if (QSelem['pid'] == 'bg_color') {
	var pcolor = window.opener.document.getElementById('abva_bg_color').value;
  } else if (QSelem['pid'] == 'border_color') {
	var pcolor = window.opener.document.getElementById('abva_border_color').value;
  }  
  document.getElementById('enterColor').value = pcolor;
}


/* Selects and inserts the color */
function selectColor(color) {
	var font_color 		 = window.opener.document.getElementById('abva_font_color');
	var bg_color   		 = window.opener.document.getElementById('abva_bg_color');
	var border_color   	 = window.opener.document.getElementById('abva_border_color');
	var font_color_btn   = window.opener.document.getElementById('abva_font_color_btn');
	var bg_color_btn 	 = window.opener.document.getElementById('abva_bg_color_btn');
	var border_color_btn = window.opener.document.getElementById('abva_border_color_btn');
	var preview_txt      = window.opener.document.getElementById('preview_txt');
	var picked_color     = document.getElementById('enterColor');
	
	if (QSelem['pid'] == 'font_color') {
		font_color.value = picked_color.value;
		font_color_btn.style.backgroundColor = picked_color.value;
		preview_txt.style.color = picked_color.value;
	} else if (QSelem['pid'] == 'bg_color') {
		bg_color.value = picked_color.value;
		bg_color_btn.style.backgroundColor = picked_color.value;
		preview_txt.style.backgroundColor = picked_color.value;
	} else if (QSelem['pid'] == 'border_color') {
		border_color.value = picked_color.value;
		border_color_btn.style.backgroundColor = picked_color.value;
		preview_txt.style.borderBottom = '1px solid ' + picked_color.value;
	}
	window.close();
}


/* Updates the preview pane color on omuse over */
function previewColor(color) {
	document.getElementById('enterColor').value = color;
	document.getElementById('PreviewColor').style.backgroundColor = color;
}
</script>
<body bgcolor="#EEEEEE" marginwidth="0" marginheight="0" topmargin="0" leftmargin="0" onLoad="recuperarColor();">
<center>
<table border="0" cellspacing="0" cellpadding="4" width="100%">
<form onSubmit="selectColor(document.getElementById('enterColor').value);">
 <tr>
  <td valign=center><div style="background-color: #000000; padding: 1; height: 21px; width: 50px"><div id="PreviewColor" style="height: 100%; width: 100%"></div></div></td>
  <td valign=center><input type="text" size="15" id="enterColor" name="enterColor" onKeyUp="previewColor(this.value)" onBlur="selectColor(this.value);"></td>
  <td width="100%"></td>
 </tr></form>
</table>
<style>
td {width:15px; height:15px; font-size: 8px;}
</style>
<table border=0 cellspacing=1 cellpadding=0 style="cursor: hand;" >
   <?php 
	$c=0;
	$salto = 51;
	for ($rojo=0; $rojo<=255; $rojo += $salto ) {
		for ($verde=0; $verde<=255; $verde += $salto) {
			for ($azul=0; $azul<=255; $azul += $salto) {
				if($c==0) echo '<tr>';
				$tc = sprintf("%02X%02X%02X",$rojo,$verde,$azul);
				?>
				<td style="background-color:#<?php echo $tc; ?>;" onMouseOver="previewColor('#<?php echo $tc; ?>')" onClick="selectColor('#<?php echo $tc; ?>')">
				</td>
				<?php
				if($c==17) {
					echo '</tr>';
					$c=0;
				} else
					$c++;
			}
		}
	}
?>
</table>

</center>
</body>
</html>
