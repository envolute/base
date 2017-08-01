<?php
defined('_JEXEC') or die;

$app = JFactory::getApplication();
$doc = JFactory::getDocument();

// SYSTEM VARIABLES
require_once(__DIR__.'/../../_system.vars.php');

class uploader {

	// ENVIA O ARQUIVO
	public static function uploadFile($id, $table, $files, $fileGrp = '', $fileGtp = '', $fileCls = '', $fileLbl = '', $cfg) {

		if(empty($id) || $id == 0) return JText::_('MSG_FILEERRO');
		// database connect
		$db = JFactory::getDbo();
		// get user's data
		$user = JFactory::getUser();

		$fileMsg  = '';
		$path     = $cfg['uploadDir'];
		$maxSize  = $cfg['maxFileSize'];

		for($i = 0; $i < count($files['size']); $i++) {

			$fErro	= $files["error"][$i];
			$fSize	= $files["size"][$i];
			$fType	= $files["type"][$i];
			$fTemp	= $files["tmp_name"][$i];
			$fName	= $files["name"][$i];
			// campos integrados
			$fGroup	= $fileGrp[$i];
			$fGtype	= $fileGtp[$i];
			$fClass	= $fileCls[$i];
			$fLabel	= $fileLbl[$i];
			// start index counter
			$sIndex	= (!empty($cfg['indexFileInit']) ? $cfg['indexFileInit'] : 0) - 1;

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
					$fExt = strtolower(substr($fName, strrpos($fName, '.'))); //get file extention
					$oName = basename($fName,$fExt); // get only name, without extension
					$fileName = $oName.'_'.$id.$i.$fExt; //new file name

					// cria o diretório caso não exista
					if (!is_dir($cfg['uploadDir'])) mkdir($cfg['uploadDir'], 0755, true);

					if(move_uploaded_file($fTemp, $cfg['uploadDir'].$fileName)) :

						// verifica se já existe um arquivo
						$cFile = self::getFile($table, '', $id, $i, $cfg['uploadDir']);

						// salva os dados do arquivo
						$query  = '
							INSERT INTO '. $db->quoteName($table)
							.'('.
								$db->quoteName('id_parent') .','.
								$db->quoteName('index') .','.
								$db->quoteName('filename') .','.
								$db->quoteName('originalName') .','.
								$db->quoteName('filesize') .','.
								$db->quoteName('mimetype') .','.
								$db->quoteName('extension') .','.
								$db->quoteName('group') .','.
								$db->quoteName('groupType') .','.
								$db->quoteName('class') .','.
								$db->quoteName('label') .','.
								$db->quoteName('created_by')
							.') VALUES ('.
								$id .','.
								$i .','.
								$db->quote($fileName) .','.
								$db->quote($fName) .','.
								$db->quote($fSize) .','.
								$db->quote($fType) .','.
								$db->quote($fExt) .','.
								$db->quote($fGroup) .','.
								$db->quote($fGtype) .','.
								$db->quote($fClass) .','.
								$db->quote($fLabel) .','.
								$user->id
							.')
						';

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

		// IMPORTANTE:
		// Reorganiza os valores do campo 'index'
		self::rebuildIndexFiles($table, $id, $sIndex);

		return $fileMsg;
 	}

	// REORGANIZA OS VALORES DO CAMPO 'INDEX'
	// Remove os saltos entre os "index", pois não deve haver!!
	// Senão fica inviável a visualização correta por tipo de grupo
	// Ex: Index: 0, 2, 4 => 0, 1, 2
	public static function rebuildIndexFiles($table, $id, $index = 0) {
		// database connect
		$db = JFactory::getDbo();
		// Obs: Não tenho a menor ideia de como funciona essa query. Apenas deu certo!
		$query = '
			UPDATE '. $db->quoteName($table).' T1
			INNER JOIN
			(
				SELECT
				*,
				(@i := (@i + 1)) AS counter
				FROM '. $db->quoteName($table).' a
				CROSS JOIN (SELECT @i := '.$index.') b
				WHERE
					'. $db->quoteName('a.id_parent') .' = '.$id.' AND
					'. $db->quoteName('a.group') .'<> ""
				ORDER BY '. $db->quoteName('a.index') .'
			) AS T2
			ON '. $db->quoteName('T1.id') .' = '. $db->quoteName('T2.id') .'
			SET '. $db->quoteName('T1.index') .' = T2.counter
		';
		$db->setQuery($query);
		$db->execute();
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
			return (!empty($f['filename']) && file_exists($path.$f['filename'])) ? $f : '';
		else :
			return false;
		endif;
	}

	// PEGA O NOME DOS ARQUIVOS
	public static function getFiles($table, $ids, $group = '') {
		if(empty($ids) || $ids == 0) return false;
		// database connect
		$db = JFactory::getDbo();

		$grp = !empty($group) ? ' AND '.$db->quoteName('group').' = '. $db->quote($group) : '';
		// FILE: GET FILENAME
		$query = 'SELECT * FROM '. $db->quoteName($table) .' WHERE '. $db->quoteName('id_parent') .' IN ('.$ids.')'.$grp.' ORDER BY '. $db->quoteName('index');
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
