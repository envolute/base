<?php
// Load Files
// Carrega os campos 'file' gerados dinâmicamente
?>
window.<?php echo $APPTAG?>_loadFiles = function(files) {
  var obj;
  var html = path = '';
  var root = '<?php echo JURI::root(true)?>';
  var len = (files.length > 0) ? parseInt(files[(files.length - 1)]['index']) : 0; // ultimo 'index'
  var f = Array();
  for(a = 0; (a < (len + 1) && files.length > 0); a++) { // len + 1, pois conta com o zero!
    <?php
    // load dinamic files
    if($cfg['dinamicFiles']) echo 'if(a >= ('.$APPTAG.'IndexFileInit - 1) && a < len) '.$APPTAG.'_setNewFile();';
    ?>
    obj = jQuery('input:file[name="file['+a+']"]');

    // Define a sequencia dos itens
    for(i = 0; i < files.length; i++) {
      if(files[i]['index'] == a) {

        desc = files[i]['filename']+'<br />'+(parseFloat(Math.round(files[i]['filesize'] / 1024)).toFixed(2))+'kb';

        // Gera os links
        if(files[i]['mimetype'].indexOf('image') == -1) {
          path = root + '/get-file?fn='+files[i]['fn']+'&mt='+files[i]['mt']+'&tag=<?php echo base64_encode($APPTAG)?>';
          html += '	<a href="'+path+'" class="base-icon-attach btn btn-default hasTooltip" data-animation="false" title="<?php echo JText::_('TEXT_DOWNLOAD'); ?><br />'+desc+'"></a>';
        } else {
          path = root + '/images/apps/<?php echo $APPNAME?>/'+files[i]['filename'];
          html += '	<a href="#" class="base-icon-eye btn btn-default hasTooltip" data-animation="false" title="<img src=\''+path+'\' style=\'width:100px;max-height:100px\' /><br />'+desc+'"></a>';
        }

        // Se for um campo obrigatório não permite a exclusão
        if(!obj.hasClass('input-required')) {
          html += '	<a href="#" class="base-icon-cancel btn btn-danger hasTooltip" data-animation="false" title="<?php echo JText::_('TEXT_DELETE').' '.JText::_('TEXT_FILE'); ?>" onclick="<?php echo $APPTAG?>_delFile(this, \''+files[i]['filename']+'\')"></a>';
        }

        // Remove o estado 'ativo' do botão de upload
        btnFileDefault(obj);
        
        // Atribui os 'botões' ao elemento
        obj.closest('.btn-file').find('.btn-group').append(html);
      }
    }

    html = path = '';
  }

  setCoreDefinitions(); // core
};
