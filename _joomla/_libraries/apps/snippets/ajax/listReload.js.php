<?php
// LIST RELOAD
// (Re)carrega a listagem AJAX dos dados
?>
window.<?php echo $APPTAG?>_listReload = function(reload, remove, ids, onlyChilds, relNameId, relId) {
	<?php if($cfg['itemView']) : ?>
		if(reload) location.href = '<?php echo JURI::current()?>?vID='+pReload;
	<?php else : ?>
		<?php if(!$cfg['showList']) echo 'return;' ?>
		<?php if($cfg['listFull']) : ?>
			if(reload) {
				<?php $qs = !empty($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : '' ?>
				location.href = '<?php echo JURI::current().$qs?>';
			} else if(ids.length > 0) {
				for(i = 0; i < ids.length; i++) {
					if(remove) jQuery('#<?php echo $APPTAG?>-item-'+ids[i]).remove();
				}
			}
			fReload = false;
			return;
		<?php else : ?>
			// inicia o loader
			toggleLoader();
			// relation
			<?php echo $APPTAG?>oCHL = (typeof onlyChilds !== "null" && typeof onlyChilds !== "undefined" && onlyChilds == true) ? 1 : 0;
			<?php echo $APPTAG?>rNID = (typeof relNameId !== "null" && typeof relNameId !== "undefined") ? relNameId : '';
			<?php echo $APPTAG?>rID = (typeof relId !== "null" && typeof relId !== "undefined" && relId !== 0) ? relId : 0;
			<?php if(!empty($_SESSION[$RTAG.'RelTable'])) echo $APPTAG.'_setRelation('.$APPTAG.'rID);'; ?>
			jQuery.ajax({
				url: "<?php echo $URL_APP_FILE ?>.list.ajax.php?aTag=<?php echo $APPTAG?>&rTag=<?php echo $RTAG?>&oCHL="+<?php echo $APPTAG?>oCHL+"&rNID="+<?php echo $APPTAG?>rNID+"&rID="+<?php echo $APPTAG?>rID,
				type: 'POST',
				cache: false,
				success: function(data) {
					// encerra o loader
					toggleLoader();
					// load content
					list.html(data);
				},
				error: function(xhr, status, error) {
					<?php // ERROR STATUS -> Executa quando houver um erro na requisição ajax
					require(JPATH_CORE.DS.'apps/snippets/ajax/ajaxError.js.php');
					?>
					<?php echo $APPTAG?>_formExecute(true, false, false); // encerra o loader
				},
				complete: function() {
					// Reload Javascript Base
					// como o ajax carrega 'novos elementos'
					// é necessário recarrega o DOM para atribuir o JS default à esses elementos
					setCoreDefinitions(); // core
					setCustomDefinitions(); // custom
					// TODO: Reload Modal 'Regular Labs'
					// RegularLabsModals.init();
				}
			});
		<?php endif; ?>
	<?php endif;?>
};
<?php
// init list
if($cfg['showList'] && !$cfg['listModal'] && !$cfg['listFull']) echo $APPTAG.'_listReload(false, false, 0, '.$APPTAG.'oCHL, '.$APPTAG.'rNID, '.$APPTAG.'rID);';
?>
