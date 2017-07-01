// Scripts e customizações exclusivas para o Internet Explorer
// IMPORTANTE: Esse arquivo é visível por todos os browsers pois contempla outros produtos relacionados como o 'windows phone'

//JQUERY
jQuery(document).ready(function() {
	// "solução" para problema com altura default da div no IE 
	jQuery(".ie7 div:empty").css("font-size","1px");
	
});

// CORREÇÃO PARA WINDOWS PHONE 8 E INTERNET EXPLORER 10
// ambos não diferenciam largura de acordo com o dispositivo, portanto,  não se aplica corretamente as @media queries no CSS do Bootstrap. 
// Para resolver isso, você pode, opcionalmente, incluir o seguinte CSS e JavaScript para contornar este problema, até as questões da Microsoft uma correção.
if (navigator.userAgent.match(/IEMobile\/10\.0/)) {
	var msViewportStyle = document.createElement("style");
	msViewportStyle.appendChild(
		document.createTextNode(
			"@-ms-viewport{width:auto!important}"
		)
	);
	document.getElementsByTagName("head")[0].appendChild(msViewportStyle);
}