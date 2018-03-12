<?php
// APP JS DEFAULT VARS
// Variáveis default da app

// MENSAGEM INICIAL
// Mostra a mensagem inicial da página previamente armazenada em uma variável de sessão...
$alertCtx = 'success';
if(isset($_SESSION[$APPTAG.'baseAlert']) && !empty($_SESSION[$APPTAG.'baseAlert']['message'])) :
	// define o contexto "success, warning, danger..."
	if(isset($_SESSION[$APPTAG.'baseAlert']['context']) && !empty($_SESSION[$APPTAG.'baseAlert']['context'])) $alertCtx = $_SESSION[$APPTAG.'baseAlert']['context'];
	// mostra a notificação...
	echo '$.baseNotify({ msg: "'.$_SESSION[$APPTAG.'baseAlert']['message'].'", type: "'.$alertCtx.'" });';
	// limpa a mensagem
	unset($_SESSION[$APPTAG.'baseAlert']);
endif;
?>

var mainForm		= jQuery('#form-<?php echo $APPTAG?>');
window.mainForm_<?php echo $APPTAG?> = mainForm;
	// form paginator
	var fPager		= jQuery('#<?php echo $APPTAG?>-formPaginator');
	var fPrev		= jQuery('#<?php echo $APPTAG?>-prev');
	var fNext		= jQuery('#<?php echo $APPTAG?>-next');
	var fRest		= jQuery('#<?php echo $APPTAG?>-restart');
	var btnPrev		= jQuery('#btn-<?php echo $APPTAG?>-prev');
	var btnNext		= jQuery('#btn-<?php echo $APPTAG?>-next');
	var btnRest		= jQuery('#btn-<?php echo $APPTAG?>-restart');
	var btnReset	= jQuery('#btn-<?php echo $APPTAG?>-reset');
var popup			= jQuery('#modal-<?php echo $APPTAG?>');
var fReload			= false;
var pReload			= 0; // valor do parametro no regarregamento para a view
var list			= jQuery('#list-<?php echo $APPTAG?>');
var listPopup		= jQuery('#modal-list-<?php echo $APPTAG?>');
	// lista completa
	var formFilter	= jQuery('#filter-<?php echo $APPTAG?>');
	var formList	= jQuery('#form-list-<?php echo $APPTAG?>');
		// Qtd. máxima de inputs enviados através do ajax para o php...
		window.maxInputVars = 1000; // valor padrão do php
	var formLimit	= jQuery('#form-limit-<?php echo $APPTAG?>');
	var formOrder	= jQuery('#form-order-<?php echo $APPTAG?>');
	// relacionamento
	window.<?php echo $APPTAG?>oCHL	= <?php echo isset(${$APPTAG.'OnlyChildList'}) && !empty(${$APPTAG.'OnlyChildList'}) ? ${$APPTAG.'OnlyChildList'} : 0?>;
	window.<?php echo $APPTAG?>rNID	= '<?php echo isset(${$APPTAG.'RelListNameId'}) ? ${$APPTAG.'RelListNameId'} : ''?>';
	window.<?php echo $APPTAG?>rID	= <?php echo isset(${$APPTAG.'RelListId'}) && !empty(${$APPTAG.'RelListId'}) ? ${$APPTAG.'RelListId'} : 0?>;

// DEFAULT FIELDS
var formId	 		= jQuery('#<?php echo $APPTAG?>-id');
var displayId	 	= jQuery('#<?php echo $APPTAG?>-displayId');
window.formId_<?php echo $APPTAG?> = formId;
var relationId		= jQuery('#<?php echo $APPTAG?>-relationId');
// state is default
var state			= mainForm.find('input[name=state]:radio');
	var active		= mainForm.find('#<?php echo $APPTAG?>-state-1');
	var inactive	= mainForm.find('#<?php echo $APPTAG?>-state-0');
// se houver upload
<?php if($cfg['hasUpload']) :?>
	var files 			= mainForm.find("input:file");
	<?php if($cfg['dinamicFiles']) :?>
	// valor inicial do index do arquivo... considerando '0' o campo estático
	window.<?php echo $APPTAG?>IndexFile = window.<?php echo $APPTAG?>IndexFileInit = <?php echo $cfg['indexFileInit']?>;
	<?php endif;?>
<?php endif;?>
