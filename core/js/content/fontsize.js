/*
	FontSize for jQuery (version 1.1)
	Copyright (c) 2009 Ramon Victor
	http://www.ramonvictor.com/plugin-font-size-jquery
	
	Licensed under the MIT license:
		http://www.opensource.org/licenses/mit-license.php

	Any and all use of this script must be accompanied by this copyright/license notice in its present form.
*/

(function($){
  $.fn.fontSize = function(options) {
	  var defaults = {
			alvo: 'body',
			setCookie: false,
			variacoes: 7,
			opResetar: false
		};
	var d = $.extend(defaults, options);
	
  	return this.each(function() {
		
		//Acrescentando os links para aumentar e diminuir tamanho da fonte	
		reset = d.opResetar;
		if (reset) {
			$(this).html('<a href="javascript:;" class="a-min btn btn-xs  btn-default">A</a> <a href="javascript:;" class="a-reset btn btn-xs btn-default">A</a> <a href="javascript:;" class="a-max btn btn-xs  btn-default">A</a>');
		} else {
			$(this).html('<a href="javascript:;" class="a-min btn btn-xs  btn-default">A</a> <a href="javascript:;" class="a-max btn btn-xs btn-default">A</a>');
		}
		
		alvo = d.alvo;
		cook = d.setCookie;
		nvariacoes = d.variacoes;
		
		//Verificando número de variações
		if(nvariacoes % 2 == 0){
			padrao = (nvariacoes/2) + 1;
		} else {
			padrao = parseInt((nvariacoes/2) + 1);
		}
		
		//Verificando se há cookie
		if($.cookie("fontSize") != null){
			$(alvo).addClass($.cookie("fontSize"));
		} else {
		   $(alvo).addClass("tam"+padrao);				
		}
     	
		// Recuperando o número da classe atual
		$.natual = function() {
			atual = $(alvo).attr("class");		
			t = atual.indexOf("tam");
			num = atual.substring((t+3),(t+5));
			return parseInt(num);
		}
		
		//Gravando valor da classe no cookie
		$.verifyCookie = function(nclass) {
			if(cook) {
				$.cookie('fontSize', nclass.toString());
			}
		}

			
		//Diminuindo número da classe até chegar a "1"
		$('.a-min').click(function () {
		    n = $.natual();												
			if(n>1){						
	    	    nAtual = "tam" + n;
		    	n -= 1;			
				nc = "tam" + n;
    			$(alvo).removeAttr("class");
				$(alvo).addClass(atual.replace(nAtual, nc));
				$(this).parent().parent().find('a').removeClass('disabled');		
				return $.verifyCookie(nc);				
			} else {
				$(this).addClass('disabled');
			}
		});
		
		//Aumentando o número da classe até chegar ao número total de variações
		$('.a-max').click(function () { 
   		    n = $.natual();
			if(n < nvariacoes){						
	    	    nAtual = "tam" + n;
		    	n = n + 1;	
				nc = "tam" + n;
    			$(alvo).removeAttr("class");
				$(alvo).addClass(atual.replace(nAtual, nc));
				$(this).parent().parent().find('a').removeClass('disabled');
				return $.verifyCookie(nc);	
			} else {
				$(this).addClass('disabled');
			}
		});	
		
		//função de reset
		$(".a-reset").click(function(){
			nAtual = "tam" + $.natual();
			$(alvo).removeAttr("class");
			nc = "tam"+padrao;
			$(alvo).addClass(atual.replace(nAtual, nc));	
			$(this).parent().parent().find('a').removeClass('disabled');
			return $.verifyCookie(nc);
						
		});
		
		
    });
  };
})(jQuery);

jQuery.cookie = function(name, value, options) {
	 if (typeof value != 'undefined') { // name and value given, set cookie
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        // CAUTION: Needed to parenthesize options.path and options.domain
        // in the following expressions, otherwise they evaluate to undefined
        // in the packed version for some reason...
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};