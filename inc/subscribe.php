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

<script type="text/javascript"><!--
		function abva_subscribers_check() {
			var name = document.getElementById('nombre');
			var mail = document.getElementById('email');
			var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
			var mensaje = '';
			if(name.value == '') mensaje = '- Nombre requerido\n';
			if(reg.test(mail.value) == false) mensaje += '- Email válido requerido';
			if(mensaje == '') return true;
			else alert(mensaje);
			if(name.value=='') name.focus();
			else mail.focus();
			return false;
		}//-->
</script>
<?php
$url = plugins_url().'/arielbrailovsky-viralad';
?>
<div style="background-color:#FFFFFF; width:95%; padding:3px; border:1px solid #559955;">
	<div style="background-color:#CCCCFF; width:96%; padding:5px;">
	<h3 style="text-align:center">Suscr&iacute;bete a Ariel Brailovsky Newsletter</h3>
	<p style="text-align:center; font-size:11px">Obtendr&aacute;s valiosos consejos sobre c&oacute;mo ganar dinero rentabilizando tu blog, as&iacute; como conseguir miles de visitas gratis.</p>
	
	<table align="center" border="0" cellspacing="5">
		<form action="<?php echo $url;?>/inc/subscribing.php" method="post" onsubmit="return abva_subscribers_check();">
		<tr>
			<td>
			  <label>Nombre:</label>
			  <input type="text" name="nombre" size="30" style="border:none;" />
			</td>
		</tr>
		<tr>
			<td>
				<label>Email:</label>
			    <input type="text" name="email" size="30" style="border:none;" />
			</td>
		</tr>
		<tr>
			<td><center><input type="submit" value="Suscribirme" style="border: inset 1px #808080; background-color:#AAAAAA;" /></center></td>
		</tr>
		</form>
	</table>
	
	<p style="text-align:center; font-size:10px;">Tu informaci&oacute;n de contacto ser&aacute; tratada con la m&aacute;xima confidencialidad y jam&aacute;s ser&aacute; vendida ni cedida a terceros.</p>
	</div>
</div>

