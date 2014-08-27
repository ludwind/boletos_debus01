<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package Catch Themes
 * @subpackage Adventurous
 * @since Adventurous 1.0
 */
?> 
			<?php 
            /** 
             * adventurous_content_sidebar_close hook
             *
             * HOOKED_FUNCTION_NAME PRIORITY
             *
             * adventurous_content_sidebar_wrapper_close 10
             */
            do_action( 'adventurous_content_sidebar_close' ); ?> 
            
		<?php
        /** 
         * adventurous_main_close hook
         *
         * HOOKED_FUNCTION_NAME PRIORITY
         *
         * adventurous_main_wrapper_close 10
         */
        do_action( 'adventurous_main_close' ); ?>             
   	</div><!-- #main-wrapper -->    
</div><!-- #page .hfeed .site -->

<!-------------------- Footer boletos ------------ -->
<div class="footerboletos"><section><ul>
	<li>
		<h1>sobre nosotros</h1><br>
		Somos una compa&ntilde;&iacute;a multinacional con sede en Estados Unidos de
		Norteam&eacute;rica y distintos pa&iacute;ses en Am&eacute;rica Latina,
		creada en el a&ntilde;o 2007 y con m&aacute;s de diez a&ntilde;os de experiencia
		en la venta de boletos de bus.<p><Br>Una compa&ntilde;&iacute;a integrada y representada
		por empresas de transporte terrestre cuyo &uacute;nico objetivo es poner a su
		disposici&oacute;n la m&aacute;s amplia gama de rutas y buses, para que desde la
		comodidad de su casa u oficina pueda reservar en l&iacute;nea en
		boletosdebus.com para cualquier destino al que desee viajar, pudiendo ser
		desde y hacia cualquier parte en Norte, Centro y pr&oacute;ximamente Sur Am&eacute;rica.
	</li>
	<li class="fporquereservar">
		<h1>&#191;porqu&eacute; reservar?</h1><br>
		<ul>
			<li><h2>La mejor reserva en l&iacute;nea de Latinoam&eacute;rica</h2><br>
			Nuestro sitio web cuenta con la mejor plataformade res&eacute;rva
			en l&iacute;nea de latinoam&eacute;rica.</li>
			<li><h2>Te llevamos a donde necesitas</h2><br>
			S&oacute;lo en boletosdebus.com podr&aacute;s encontrar los mejores destinos
			p&oacute;ara viajar en bus y reservar en l&iacute;nea</li>
			<li><h2>Atenci&oacute;n al cliente personalizada</h2><br>
			Todo nuestro personal est&aacute; capacitado para darte la mejor atenci&oacute;n
			que puedas tener, informaci&oacute;n completa y objetiva</li>
		</ul>
	</li>
	<li>
		<h1>encu&eacute;ntranos</h1><br>
		<ul>
			<li>Guatemala, Via 2 4-44 Zona 4 Ciudad Guatemala</li>
			<li>El Salvador, Juan Pablo II y 19 Ave. Norte Local 7</li>
			<li>Los &#193;ngeles 1926 W. Olympic Blvd. Los &#193;ngeles CA 90006</li>
			<li>PACOIMA. 9760 Laurerl cyn Blvd</li>
			<li>Santa Ana, Ca.305 E. 17th Street Santa Ana Ca.</li>
		</ul>
		<span>(213) 368 1172 - (503) 2222-8500 - (502) 2361-1403 </span>
		<article><img src="wp-content/themes/boletosdebus/img/home/foot-fb.png"/></article>
		<article><img src="wp-content/themes/boletosdebus/img/home/foot-g.png"/></article>
		<article><img src="wp-content/themes/boletosdebus/img/home/foot-rss.png"/></article>
		<article><img src="wp-content/themes/boletosdebus/img/home/foot-pint.png"/></article>
		<article><img src="wp-content/themes/boletosdebus/img/home/foot-in.png"/></article>
	</li>
</ul></section></div>

<div class="afterfooter-boletos">
	<section>
	<article>Desarrollado por <A href="http://shopyourwebsite.com">shopyourwebsite.com</A></article>
	<h1>boletosdebus.com</h1> Todos los derechos reservados</section>
</div>


<?php wp_footer(); ?>

</body>
</html>