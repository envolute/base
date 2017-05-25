<?php
/* SISTEMA PARA CADASTRO DE TELEFONES
 * AUTOR: IVO JUNIOR
 * EM: 18/02/2016
*/
defined('_JEXEC') or die;
$ajaxRequest = false;
require('config.php');
// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_BASE.'/templates/base/source/helpers/base.php');

$app = JFactory::getApplication('site');

// load Scripts
$doc = JFactory::getDocument();
$doc->addScript(JURI::base().'templates/base/js/forms.js');
$doc->addScript(JURI::base().'templates/base/js/validate.js');

// get current user's data
$user = JFactory::getUser();
$groups = $user->groups;

// verifica o acesso
if($user->guest) :
  $app->redirect(JURI::root(true).'/login?return='.urlencode(base64_encode(JURI::current())));
  exit();
endif;

unset($_SESSION[$APPTAG.'Group']);
if($groups[6]) $_SESSION[$APPTAG.'Group'] = 6;
elseif($groups[7]) $_SESSION[$APPTAG.'Group'] = 7;
elseif($groups[8]) $_SESSION[$APPTAG.'Group'] = 8;
elseif($groups[10]) $_SESSION[$APPTAG.'Group'] = 10;
elseif($groups[11]) $_SESSION[$APPTAG.'Group'] = 11;
elseif($groups[12]) $_SESSION[$APPTAG.'Group'] = 12;
elseif($groups[13]) $_SESSION[$APPTAG.'Group'] = 13;
elseif($groups[14]) $_SESSION[$APPTAG.'Group'] = 14;

?>

<script>
jQuery(function() {

  var windowWidth = jQuery(window).width();
  var windowHeight = jQuery(window).height();
  var accessTime = <?php echo $_SESSION[$APPTAG.'accessTime']?> * 60;
  var periodTime = <?php echo $_SESSION[$APPTAG.'periodTime']?> * 60;
  var warning = jQuery('#modal-<?php echo $APPTAG?>-alert');

  window.testPopup = function() {
    windowTest = window.open('http://www.envolute.net/public/testPopup.html', 'wTest', 'status=1,toolbar=0,location=0,menubar=0,resizable=0,width=400,height=100');
  }

  window.startCountdown = function(time, timeLimit, accessLimit){
    var maxTime = (typeof timeLimit !== "null" && typeof timeLimit !== "undefined" && timeLimit == true) ? true : false;
    var maxAccess = (typeof accessLimit !== "null" && typeof accessLimit !== "undefined" && accessLimit == true) ? true : false;
    // Se o tempo não for zerado
    if(time >= 0){
    	// Pega a parte inteira dos minutos
    	var min = parseInt(time/60);
    	// Calcula os segundos restantes
    	var seg = time % 60;
    	// Formata o número menor que dez, ex: 08, 07, ...
    	if(min < 10){
    		min = "0"+min;
    		min = min.substr(0, 2);
    	}
    	if(seg <=9) seg = "0"+seg;
    	// Cria a variável para formatar no estilo hora/cronômetro
    	counter = '00:' + min + ':' + seg;
    	//JQuery pra setar o valor
    	jQuery("#countDown").html(counter);
    	// Define que a função será executada novamente em 1000ms = 1 segundo
    	setTimeout(function() {
        startCountdown(time, maxTime, maxAccess);
      },1000);
    	// diminui o tempo
    	time--;
    // Quando o contador chegar a zero faz esta ação
    } else {
      if(maxTime) {
        enableAccess("Liberado!\nVocê já pode iniciar um novo acesso.\n\nObs: A página será atualizada!");
      } else if(maxAccess) {
        enableAccess("Acesso Disponível!\nVocê já pode acessar as câmeras.\n\nObs: A página será atualizada!");
      }
    }
  }

  window.setAlert = function(msg) {
    warning.find('.modal-body').html(msg);
    warning.modal({backdrop: 'static', keyboard: false});
  }

  window.enableAccess = function(msg) {
      window.focus();
      alert(msg);
      location.href = '<?php echo JURI::root(true)?>/cameras';
  }

  var startCounter = false;

  // Visualiza câmeras
  window.viewCams = function() {
    jQuery.ajax({
      url: "<?php echo JURI::root(true)?>/templates/base/source/cameras/cameras.model.php?task=view",
      dataType: 'json',
      cache: false,
      success: function(data){
        jQuery.map( data, function( res ) {
          if(res.status == 1) {
            // abre a janela de acesso às câmeras
            windowCams = window.open('http://planetkids.no-ip.org', 'wCams', 'status=1,toolbar=0,location=0,menubar=0,resizable=0,width='+windowWidth+',height='+windowHeight);
            windowCams.focus();
            if(!startCounter) {
              startCounter = true;
              // inicia o contador de acesso
              var t = (res.remaining == accessTime) ? accessTime : accessTime + parseInt(res.remaining); // res.timeStart é negativo
              startCountdown(t);
              setTimeout(function() {
                windowCams.opener.focus();
                windowCams.close();
                jQuery('#btn-access-cam').remove();
                jQuery('#alert-time-limit').removeClass('hide');
                startCountdown(periodTime, true);
                startCounter = false;
                setAlert('Você atingiu o tempo máximo de acesso de <em class="text-danger strong"><?php echo $_SESSION[$APPTAG.'accessTime']?></em> minutos.<br />Você poderá acessar novamente após o período de espera de <em class="text-danger strong"><?php echo $_SESSION[$APPTAG.'accessTime']?></em> minutos.');
              },(t * 1000));
            }
          } else {
            alert("Ocorreu um erro durante o acesso. Por favor, tente novamente!");
          }
        });
      },
      error: function(xhr, status, error) {
        console.log(xhr);
        console.log(status);
        console.log(error);
      }
    });
    return false;
  };

  // verifica o acesso
  window.getAccess = function() {
    jQuery.ajax({
      url: "<?php echo JURI::root(true)?>/templates/base/source/cameras/cameras.model.php?task=access",
      dataType: 'json',
      cache: false,
      success: function(data){
        jQuery.map( data, function( res ) {
          if(res.status == 2 || res.status == 3) {
            if(!startCounter) {
              startCounter = true;
              jQuery('#btn-access-cam').remove();
              if(res.status == 2) { // limite de acessos
                jQuery('#alert-access-limit').removeClass('hide');
                // inicia o contador para tentativa
                startCountdown(periodTime, false, true);
              } else { // tempo máximo de acesso
                jQuery('#alert-time-limit').removeClass('hide');
                // inicia o contador para o tempo de acesso
                var t = (res.remaining == periodTime) ? periodTime : periodTime + (parseInt(res.remaining) + accessTime); // res.timeStart é negativo e maior que o tempo máximo de acesso
                startCountdown(t, true, false);
              }
            }
          } else if(res.status == 1) {
            jQuery('#btn-access-cam').removeClass('hide');
          } else {
            console.log(res.msg);
            alert("Ocorreu um erro durante o acesso. Por favor, tente novamente!");
          }
        });
      },
      error: function(xhr, status, error) {
        console.log(xhr);
        console.log(status);
        console.log(error);
      }
    });
    return false;
  };

  // dados de acesso
  window.accessCams = function() {
    jQuery.ajax({
      url: "<?php echo JURI::root(true)?>/templates/base/source/cameras/cameras.model.php?task=group",
      dataType: 'json',
      cache: false,
      success: function(data){
        jQuery.map( data, function( res ) {
          if(res.status == 1) {
            jQuery('#groupInfo').html('<strong>Usu&aacute;rio:</strong> <span class="font-featured text-danger left-expand-xs">'+res.login+'</span> <small class="text-live pull-right">(userName)</small><br /><strong>Senha:</strong> <span class="font-featured text-danger left-expand">'+res.key+'</span> <small class="text-live pull-right">(password)</small>');
            // verifica o acesso
            getAccess();
          } else {
            jQuery('#groupInfo').addClass('alert alert-warning alert-icon').html('Você não tem acesso às câmeras!');
          }
        });
      },
      error: function(xhr, status, error) {
        console.log(xhr);
        console.log(status);
        console.log(error);
      }
    });
    return false;
  };
  // inicializa
  <?php if(!$isAdmin) echo 'accessCams();'?>


});
</script>

<div class="well obj-to-right" style="width: 300px; max-width: 100%;">
  <h4 class="no-margin-top">
    Dados de Acesso
    <div class="text-sm text-live font-condensed">Utilize os dados abaixo para acessar as câmeras</div>
  </h4>
  <p id="groupInfo"></p>
  <hr />
  <span id="btn-access-cam" class="<?php if(!$isAdmin) echo 'hide'?>">
    <div class="alert alert-warning text-center">
      <h4><span class="text-live"><span class="base-icon-attention"></span> Importante</span></h4>
      Desative o bloqueador de popup do seu navegador para este site!<br />
      <a href="#" onclick="testPopup()" class="text-sm text-live font-primary strong"><u>Clique Aqui</u> para testar popups desse site</a>
    </div>
    <a href="#" class="btn btn-lg btn-success btn-block" onclick="viewCams()"> <span class="base-icon-videocam btn-icon"></span> Acessar C&acirc;meras </a>
  </span>
  <div id="alert-time-limit" class="text-center hide">
    <h4 class="text-live no-margin">Per&iacute;odo de Espera</h4>
    <p>Voc&ecirc; atingiu o tempo m&aacute;ximo de acesso. Um novo acesso poder&aacute; ser realizado em:</p>
  </div>
  <div id="alert-access-limit" class="text-center hide">
    <h4 class="text-live no-margin">Limite de Acessos</h4>
    <p>As c&acirc;meras est&atilde;o operando no limite m&aacute;ximo de acessos simult&acirc;neos. Um novo acesso poder&aacute; ser realizado em:</p>
  </div>
  <h1 id="countDown" class="text-center bottom-space-sm">00:00:00</h1>
  <div class="modal fade" id="modal-<?php echo $APPTAG?>-alert" tabindex="-1" role="dialog" aria-labelledby="modal-<?php echo $APPTAG?>Label">
		<div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Tempo Máximo de Acesso</h4>
        </div>
  			<div class="modal-body"></div>
        <div class="modal-footer">
          <button name="btn-'.$APPTAG.'-cancel" id="btn-'.$APPTAG.'-cancel" class="btn btn-success btn-sm" data-dismiss="modal">OK</button>
        </div>
      </div>
		</div>
	</div>
</div>
