<?php
defined('_JEXEC') or die;

$app = JFactory::getApplication();
$doc = JFactory::getDocument();

// SYSTEM VARIABLES
require_once(__DIR__.'/../../_system.vars.php');

class uploader {

	// ENVIA O ARQUIVO
	public static function uploadFile($id, $table, $files, $cfg) {
		if(empty($id) || $id == 0) return JText::_('MSG_FILEERRO');
    // database connect
    $db = JFactory::getDbo();
    // get user's data
    $user = JFactory::getUser();

    $fileMsg  = '';
    $path     = $cfg['uploadDir'];
    $maxSize  = $cfg['maxFileSize'];

    for($i = 0; $i < count($files['size']); $i++) {

      $fErro = $files["error"][$i];
      $fSize = $files["size"][$i];
      $fType = $files["type"][$i];
      $fTemp = $files["tmp_name"][$i];
      $fName = $files["name"][$i];

      if($fSize > 0 && $fErro == UPLOAD_ERR_OK) :

				$fileErr	= false;
        // verifica o tamanho do arquivo
        if ($fSize > $maxSize) :
					$mfs = (int)($cfg['maxFileSize']/1000000);
          $fileMsg .= JText::sprintf('MSG_FILESIZE', $mfs);
					$fileErr = true;
        else :
          // verifica o tipo do arquivo
					if(!in_array(strtolower($fType), array_merge($cfg['fileTypes']['image'], $cfg['fileTypes']['file']))) :
          	$fileMsg .= JText::sprintf('MSG_FILETYPE', $fName);
						$fileErr = true;
					endif;
				endif;

        if(!$fileErr) :

          // prepara o arquivo
          $fExt				= strtolower(substr($fName, strrpos($fName, '.'))); //get file extention
					$oName			= basename($fName,$fExt); // get only name, without extension
          $fileName		= $oName.'_'.$id.$i.$fExt; //new file name

          // verifica o arquivo
          $cFile = self::getFile($table, '', $id, $i, $cfg['uploadDir']);

					// cria o diretório caso não exista
					if (!is_dir($cfg['uploadDir'])) mkdir($cfg['uploadDir'], 0755, true);

          if(move_uploaded_file($fTemp, $cfg['uploadDir'].$fileName)) :

            // verifica se já existe um arquivo
            $cFile = self::getFile($table, '', $id, $i, $cfg['uploadDir']);

            // salva os dados do arquivo
      			$query  = 'INSERT INTO '. $db->quoteName($table)
      			.'('.
              $db->quoteName('id_parent') .','.
      				$db->quoteName('index') .','.
      				$db->quoteName('filename') .','.
      				$db->quoteName('originalName') .','.
      				$db->quoteName('filesize') .','.
      				$db->quoteName('mimetype') .','.
      				$db->quoteName('extension') .','.
      				$db->quoteName('created_by')
      			.') VALUES ('.
      				$id .','.
      				$i .','.
      				$db->quote($fileName) .','.
      				$db->quote($fName) .','.
      				$db->quote($fSize) .','.
      				$db->quote($fType) .','.
      				$db->quote($fExt) .','.
      				$user->id
      			.')';

      			try {

      				$db->setQuery($query);
      				$db->execute();

              // deleta o arquivo caso já exista
              if(!empty($cFile['filename']))
              self::deleteFile($cFile['filename'], $table, $path, JText::_('MSG_FILEERRODEL'));

      			} catch (RuntimeException $e) {

      				$fileMsg .= $e->getMessage();
              // remove o arquivo caso os dados não sejam salvos
              self::removeFile($cfg['uploadDir'].$fileName);

      			}

          else :
            $fileMsg .= JText::_('MSG_FILEERRO');
          endif;

        endif;

      endif; // end 'upload' file

    } // end for

    return $fileMsg;
  }

	// PEGA O NOME DO ARQUIVO
	public static function getFile($table, $file, $id, $index = 0, $path) {
    // database connect
    $db = JFactory::getDbo();

    if($file && !empty($file)) :
      $query = 'SELECT * FROM '. $db->quoteName($table) .' WHERE '. $db->quoteName('filename') .'='. $db->quote($file);
    elseif($id) :
      $query = 'SELECT * FROM '. $db->quoteName($table) .' WHERE '. $db->quoteName('index') .'='. $index.' AND '. $db->quoteName('id_parent') .'='. $id;
    endif;
    if(isset($query)) :
      $db->setQuery($query);
      $f = $db->loadAssoc();
      return (!empty($f['filename']) && file_exists($path.$f['filename'])) ? $f : $f;
    else :
      return false;
    endif;
  }

  // PEGA O NOME DOS ARQUIVOS
	public static function getFiles($table, $ids) {
		if(empty($ids) || $ids == 0) return false;
    // database connect
    $db = JFactory::getDbo();

    // FILE: GET FILENAME
    $query = 'SELECT * FROM '. $db->quoteName($table) .' WHERE '. $db->quoteName('id_parent') .' IN ('.$ids.') ORDER BY '. $db->quoteName('index');
    $db->setQuery($query);
    return $db->loadObjectList();
  }

  // VERIFICA SE EXISTE REGISTRO DO ARQUIVO
	public static function fileExist($table, $file) {
		if(empty($file)) return false;
    // database connect
    $db = JFactory::getDbo();

    $query = 'SELECT count(*) FROM '. $db->quoteName($table) .' WHERE '. $db->quoteName('filename') .'='. $db->quote($file);
    $db->setQuery($query);
    return $db->loadResult();
  }

	// DELETA OS DADOS E REMOVE ARQUIVO
	public static function deleteFile($file, $table, $path, $errMsg) {
		if(empty($file)) return $errMsg;
    // database connect
    $db = JFactory::getDbo();

    // verifica se o arquivo existe
    $exist = self::fileExist($table, $file);
    if($exist > 0) :
      // deleta os dados do arquivo
      $query = 'DELETE FROM '. $db->quoteName($table) .' WHERE '. $db->quoteName('filename') .'='. $db->quote($file);
      try {
        $db->setQuery($query);
        $db->execute();
        // remove o arquivo do diretório
        self::removeFile($path.$file);
      } catch (RuntimeException $e) {
        return $errMsg.': '.$e->getMessage();
      }
    endif;
  }

	// DELETA OS DADOS E REMOVE TODOS OS ARQUIVOS
	public static function deleteFiles($ids, $table, $path, $errMsg) {
		if(empty($ids) || $ids == 0) return $errMsg;
    // database connect
    $db = JFactory::getDbo();

    // verifica se existem arquivos
    $files = self::getFiles($table, $ids);

    if(count($files) > 0) :
      // deleta os dados do arquivo
      $query = 'DELETE FROM '. $db->quoteName($table) .' WHERE '. $db->quoteName('id_parent') .' IN ('.$ids.')';
      try {
        $db->setQuery($query);
        $db->execute();
        foreach ($files as $file) {
          // remove o arquivo do diretório
          self::removeFile($path.$file->filename);
        } // end foreach
      } catch (RuntimeException $e) {
        return $errMsg.': '.$e->getMessage();
      }
    endif;
  }

	// REMOVE ARQUIVO
	public static function removeFile($file) {
    if($file && !empty($file) && file_exists($file)) unlink($file);
  }

} // end class 'uploader'
