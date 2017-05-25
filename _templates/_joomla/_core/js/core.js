// JOOMLA
jQuery(function() {

  // VERTICAL MENU
  // abre o(s) nível(is) do item ativo marcando como ativo também o(s) item(ns) pai
  if(elementExist(jQuery('.sm-menu'))) jQuery('.sm-menu dt.active').addClass('current').parents('dd').show().prev('dt').addClass('active opened');

  // NAVBAR -> menu de administração
  // corrige o problema da expansão do link quando o dropdown é acionado
  if(elementExist(jQuery('.dropdown-menu'))) jQuery('.dropdown-menu').removeClass('jmoddiv');

  // MODALS -> Colorbox
  // esconde o header do modal quando vier vazio
  jQuery(document).bind('cbox_complete', function(){
    if(jQuery('#colorbox').find('#cboxTitle').is(':empty')) jQuery('#colorbox').addClass('no-header');
  });
  jQuery(document).bind('cbox_closed', function(){
    jQuery('#colorbox').removeClass('no-header');
  });

  // LOGIN
  // Atribui o foco no onload
  if(elementExist(jQuery('#login'))) {
    jQuery('#login').on('shown.bs.modal', function () {
      jQuery(this).find('#mod_login_username').focus();
    });
  }

  // FITVIDS
  // chamada do plugin 'fitvids' para deixar os vídeos fluidos...
  // http://fitvidsjs.com/
  jQuery("#wrapper").fitVids();

  // FONTSIZER -> Redimensionamento da Fonte no conteúdo
  if(elementExist(jQuery('#fontsize'))) {
    jQuery('#fontsize').fontSize({alvo:'#content', setCookie:false, opResetar:false});
  }

  // ZOOM NA IMAGEM
  if(elementExist(jQuery('.scroll-image-zoom'))){
    var obj = '.scroll-image-zoom';
    jQuery.getScript(URLBase+'/templates/base/libs/content/wheelzoom.js', function(){
      jQuery(obj).each(function() {
        var imgZoom = jQuery(this);
        imgZoom.css('cursor','ne-resize');
        wheelzoom(imgZoom);
        imgZoom.bind('touchstart mousedown', function() { imgZoom.css('cursor','all-scroll'); });
        imgZoom.on('touchend mouseup', function() { imgZoom.css('cursor','ne-resize'); });
        imgZoom.on('dblclick', function() {
          document.querySelector(obj).dispatchEvent(new CustomEvent('wheelzoom.reset'));
        });
      });
    });
  }

});
