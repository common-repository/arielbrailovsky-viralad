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


	session_start();
	
	$name = $_POST['nombre'];
	$mail = $_POST['email'];

?>
<html>
<body onLoad="document.miform.submit();">
<!-- AWeber Web Form Generator 3.0 -->
<form name="miform" method="post" action="http://www.aweber.com/scripts/addlead.pl">
<div style="display: none;">
<input type="hidden" name="meta_web_form_id" value="179796742" />
<input type="hidden" name="meta_split_id" value="" />
<input type="hidden" name="listname" value="ab-afiliados" />
<input type="hidden" name="redirect" value="http://www.aweber.com/thankyou.htm?m=default" id="redirect_71316fe04c818d0bc7049af968bbf760" />
<input type="hidden" name="meta_adtracking" value="My_Web_Form" />
<input type="hidden" name="meta_message" value="1" />
<input type="hidden" name="meta_required" value="name,email" />
<input type="hidden" name="meta_tooltip" value="" />
<input id="awf_field-22198619" type="hidden" name="name" value="<?php echo $name;?>" />
<input class="text" id="awf_field-22198620" type="hidden" name="email" value="<?php echo $mail;?>"  />
</div>
<img src="http://forms.aweber.com/form/displays.htm?id=jOyc7Jxs7CxM" alt="" />
</form>
 
<!-- /AWeber Web Form Generator 3.0 -->
</body>
</html>
