<?php
// BLOCK DIRECT ACCESS
if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) AND strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest") :

header( 'Cache-Control: no-cache' );
header( 'content-type: application/json; charset=utf-8' );

  function is_valid_callback($subject) {
      $identifier_syntax = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*+$/u';

      $reserved_words = array('break', 'do', 'instanceof', 'typeof', 'case',
        'else', 'new', 'var', 'catch', 'finally', 'return', 'void', 'continue',
        'for', 'switch', 'while', 'debugger', 'function', 'this', 'with',
        'default', 'if', 'throw', 'delete', 'in', 'try', 'class', 'enum',
        'extends', 'super', 'const', 'export', 'import', 'implements', 'let',
        'private', 'public', 'yield', 'interface', 'package', 'protected',
        'static', 'null', 'true', 'false');

      return preg_match($identifier_syntax, $subject)
          && ! in_array(mb_strtolower($subject, 'UTF-8'), $reserved_words);
  }

  // load Joomla's framework
  require_once('../load.joomla.php');
	$app = JFactory::getApplication('site');

  defined('_JEXEC') or die;
  $ajaxRequest = true;
  require('config.php');
  // IMPORTANTE: Carrega o arquivo 'helper' do template
  JLoader::register('baseHelper', JPATH_BASE.'/templates/base/source/helpers/base.php');

  // get current user's data
  $user = JFactory::getUser();
  $groups = $user->groups;

  //joomla get request data
  $input      = $app->input;

  // Default Params
  $APPTAG			= $input->get('aTag', $APPTAG, 'str');
  $RTAG				= $input->get('rTag', $APPTAG, 'str');
  $task       = $input->get('task', null, 'str');
  $data       = array();

  if($task != null) :

  	// database connect
  	$db = JFactory::getDbo();

    // Carrega o arquivo de tradução
    // OBS: para arquivos externos com o carregamento do framework 'load.joomla.php' (geralmente em 'ajax')
    // a language 'default' não é reconhecida. Sendo assim, carrega apenas 'en-GB'
    // Para possibilitar o carregamento da language 'default' de forma dinâmica,
    // é necessário passar na sessão ($_SESSION[$APPTAG.'langDef'])
    if(isset($_SESSION[$APPTAG.'langDef']))
    $lang->load('base_'.$APPNAME, JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);

    // params requests
    $id         = $input->get('id', 0, 'int');

  	// fields 'List' requests
    $listIds    = $input->get($APPTAG.'_ids', array(), 'array');
    $ids        = (count($listIds) > 0) ? implode($listIds, ',') : $id;
    $state      = $input->get('st', 2, 'int');
    $seq        = $input->get('sq', 0, 'int');
    $client     = $input->get('cID', 0, 'int');

    // upload actions
    $fileMsg  = '';
    if($cfg['hasUpload']) :
      $fname    = $input->get('fname', '', 'string');
      $fileId   = $input->get('fileId', 0, 'int');
      // load 'uploader' class
      JLoader::register('uploader', JPATH_BASE.'/templates/base/source/helpers/upload.php');
    endif;

  	// fields 'Form' requests
    $request                 = array();
    // default
    $request['relationId']   = $input->get('relationId', 0, 'int');
  	$request['state']        = $input->get('state', 1, 'int');
    // app
    $request['parent_id']     = $input->get('parent_id', 0, 'int');
  	$request['provider_id']  = $input->get('provider_id', 0, 'int');
    $request['client_id']    = $input->get('client_id', 0, 'int');
    $request['dependent_id'] = $input->get('dependent_id', 0, 'int');
    $request['invoice_id']   = $input->get('invoice_id', 0, 'int');
    $request['description']  = $input->get('description', '', 'string');
    $request['fixed']        = $input->get('fixed', 0, 'int');
    $request['isCard']       = $input->get('isCard', 0, 'int');
  	$request['date']         = $input->get('date', '', 'string');
      // data da parcela
      $date_installment      = $db->quote($request['date']);
  	$request['price']        = $input->get('price', 0.00, 'float');
    $request['installment']  = $input->get('installment', 1, 'int');
    $request['total']        = $input->get('total', 1, 'int');
  	$request['doc_number']   = $input->get('doc_number', '', 'string');
  	$request['note']         = $input->get('note', '', 'string');

    // price
    $price = $request['total'] != 0 ? ($request['price'] / $request['total']) : 0;

    // CUSTOM -> get user's card limit
    function getCardLimit($itemID, $cfg) {
      // database connect
    	$db = JFactory::getDbo();
      $query = 'SELECT '. $db->quoteName('card_limit') .' FROM '. $db->quoteName('#__apcefpb_clients') .' WHERE id = '.$itemID;
      $db->setQuery($query);
      $maxLimit = $db->loadResult();
      // somatório do valor das movimentações não faturadas do cliente
      $query = 'SELECT SUM(price) FROM '. $db->quoteName($cfg['mainTable']) .' WHERE client_id = '.$itemID.' AND isCard = 1 AND invoice_id = 0';
      $db->setQuery($query);
      $limit =  $db->loadResult();
      $limit = (float)$maxLimit - (float)$limit;
      return  str_replace('.', ',', $limit);
    }

    // CUSTOM -> Gera arquivo de débito
    // se informar apenas o nome da tabela, fará uma consulta em todas as colunas;
    function setFile($strQuery, $colNames = NULL, $delimitador = NULL, $enclosed = NULL, $breakEndLine = true) {


      $db = JFactory::getDBO();
      $query = $db->getQuery(true);
      $app =& JFactory::getApplication();

      $query = $db->setQuery($strQuery);
      $db->query();
      $rows = $db->getNumRows();
      if($rows > 0) :

        // criar a variável $csv
        $csv = '';
        // o numero de campos que resultou a consulta, agora servirá para montar um array com os nomes dos campos
        $names = explode(',', $colNames);
        // criamos um array associativo
        for($y = 0; $y < count($names); $y++) {

          // colocamos os nomes das colunas em letras maiúsculas
          if(is_null($delimitador) || $delimitador == '\t' || $delimitador == 'tab' || $delimitador == 'TAB') :
            $csv .= $enclosed.strtoupper($names[$y]).$enclosed."\t";
          elseif(empty($delimitador)) :
            $csv .= $enclosed.strtoupper($names[$y]).$enclosed;
          else :
            $csv .= $enclosed.strtoupper($names[$y]).$enclosed.$delimitador;
          endif;

        } // end for

        // tiramos o delimitador da última posição
        $csv = substr($csv,0,-1);
        // inserimos uma quebra de linha
        $csv .= "\r\n";
        $row = $db->loadRowList();

        // essa é a parte mais importante, onde vamos utilizar o nosso array com os nomes dos campos para poder concatenar os resultados com os nomes das colunas corretamente
        for($k = 0; $k < $rows; $k++) {

          // para cada loop a string $csv irá concatenar um novo registro
          for($i = 0; $i < count($row[$k]); $i++) {

            // a variável $csv irá receber o valor da variável $resultado na posição do nome da coluna.
            if(is_null($delimitador) || $delimitador == '\t' || $delimitador == 'tab' || $delimitador == 'TAB') :
              $csv .= $enclosed.$row[$k][$i].$enclosed."\t";
            elseif(empty($delimitador)) :
              $csv .= $enclosed.$row[$k][$i].$enclosed;
            else :
              $csv .= $enclosed.$row[$k][$i].$enclosed.$delimitador;
            endif;

          }

          // tiramos delimitador da última posição caso ele seja informado
          if($delimitador != '') :
            $csv = substr($csv,0,-1);
          endif;

          // inserimos uma quebra de linha
          if($k < ($rows - 1)) :
            $csv .= "\r\n";
          elseif($breakEndLine) :
            $csv .= "\r\n";
          endif;

        } // end for

        // e por último a função retorna a string;
        return $csv;

      else :
        return false;
      endif;


    }

  	if($id || (!empty($ids) && $ids != 0)) :  //UPDATE OR DELETE

      $num_rows = 0;
      if($id) :
        // GET FORM DATA
    		$query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('id') .' = '. $id;
    		$db->setQuery($query);
    		$db->execute();
    		$num_rows = $db->getNumRows();
    		$list = $db->loadObjectList();
        // get previous ID
        $query = 'SELECT MAX(id) FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('id') .' < '. $id;
    		$db->setQuery($query);
    		$prev = $db->loadResult();
        $prev = !empty($prev) ? $prev : 0;
        // get next ID
        $query = 'SELECT MIN(id) FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('id') .' > '. $id;
    		$db->setQuery($query);
    		$next = $db->loadResult();
        $next = !empty($next) ? $next : 0;
        if($cfg['hasUpload']) :
          // get files
          $query = 'SELECT *, TO_BASE64('. $db->quoteName('filename') .') fn, TO_BASE64('. $db->quoteName('mimetype') .') mt FROM '. $db->quoteName($cfg['fileTable']) .' WHERE '. $db->quoteName('id_parent') .' = '. $id . ' ORDER BY '. $db->quoteName('index');
      		$db->setQuery($query);
      		$listFiles = $db->loadAssocList();
        endif;
      else :
        // COUNT LIST IDS
    		$query = 'SELECT COUNT(*) FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('id') .' IN ('.$ids.')';
    		$db->setQuery($query);
    		$exist = $db->loadResult();
      endif;

  		if($num_rows || $exist) : // verifica se existe

  			// GET DATA
  			if($task == 'get' && $list) :

          foreach($list as $item) {

            $cardLimit = getCardLimit($item->client_id, $cfg);

      			$data[] = array(
              // Default Fields
      				'id'		      => $item->id,
      				'state'       => $item->state,
        			'prev'	      => $prev,
          		'next'	      => $next,
              // App Fields
              'parent_id'	  => $item->parent_id,
              'provider_id'	=> $item->provider_id,
              'client_id' 	=> $item->client_id,
              'dependent_id'=> $item->dependent_id,
              'invoice_id'	=> $item->invoice_id,
              'description'	=> $item->description,
              'fixed'       => $item->fixed,
              'isCard'	    => $item->isCard,
      				'cardLimit'   => $cardLimit,
              'date'	      => $item->date_installment,
      				'price'	      => $item->price,
      				'price_total'	=> $item->price_total,
      				'installment'	=> $item->installment,
      				'total'	      => $item->total,
      				'doc_number'	=> $item->doc_number,
      				'note'	      => $item->note
      			);
      		}

  			// UPDATE
  			elseif($task == 'save' && $id) :

  				$query  = 'UPDATE '.$db->quoteName($cfg['mainTable']).' SET ';
  				$query .=
            $db->quoteName('provider_id') .'='. $request['provider_id'] .','.
            $db->quoteName('client_id') .'='. $request['client_id'] .','.
            $db->quoteName('dependent_id') .'='. $request['dependent_id'] .','.
            $db->quoteName('invoice_id') .'='. $request['invoice_id'] .','.
            $db->quoteName('description') .'='. $db->quote($request['description']) .','.
            $db->quoteName('fixed') .'='. $request['fixed'] .','.
            $db->quoteName('isCard') .'='. $request['isCard'] .','.
            $db->quoteName('date_installment') .'='. $db->quote($request['date']) .','.
            $db->quoteName('price') .'='. $db->quote($request['price']) .','.
            $db->quoteName('doc_number') .'='. $db->quote($request['doc_number']) .','.
            $db->quoteName('note') .'='. $db->quote($request['note']) .','.
  					$db->quoteName('state') .'='. $request['state']
  				;
  				$query .= ' WHERE '. $db->quoteName('id') .'='. $id;

  				try {

  					$db->setQuery($query);
  					$db->execute();
            // Upload
            if($cfg['hasUpload'])
            $fileMsg = uploader::uploadFile($id, $cfg['fileTable'], $_FILES[$cfg['fileField']], $cfg);

            // UPDATE FIELD
            $element = $elemVal = $elemLabel = '';
            if(!empty($_SESSION[$RTAG.'FieldUpdated']) && !empty($_SESSION[$RTAG.'TableField'])) :
              $element = $_SESSION[$RTAG.'FieldUpdated'];
              $elemVal = $id;
              $query = '
              SELECT '.
                $db->quoteName($_SESSION[$RTAG.'TableField']) .', '.
                $db->quoteName('code').'
              FROM '. $db->quoteName($cfg['mainTable']).'
              WHERE id='.$id.' AND state = 1';
              $db->setQuery($query);
              $elems = $db->loadObject();
              $elemLabel = $elems->name;
            endif;

  					$data[] = array(
  						'status' => 2,
  						'msg'	=> JText::_('MSG_SAVED'),
              'uploadError' => $fileMsg,
    					'parentField'	=> $element,
    					'parentFieldVal'	=> $elemVal,
    					'parentFieldLabel'	=> baseHelper::nameFormat($elemLabel),
    					'parentFieldUrl'	=> $elemUrl
  					);

  				} catch (RuntimeException $e) {

            // Error treatment
            switch($e->getCode()) {
              case '1062':
                $sqlErr = JText::_('MSG_SQL_DUPLICATE_KEY');
                break;
              default:
                $sqlErr = 'Erro: '.$e->getCode().'. '.$e->getMessage();
            }

            $data[] = array(
    					'status'=> 0,
    					'msg'	=> $sqlErr,
              'uploadError' => $fileMsg
    				);

  				}

  			// DELETE
  			elseif($task == 'del') :

  				$query = 'DELETE FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('id') .' IN ('.$ids.')';

  				try {

  					$db->setQuery($query);
  					$db->execute();

            // FILE: remove o(s) arquivo(s)
            if($cfg['hasUpload'] && !empty($ids) && $ids != 0)
            $fileMsg = uploader::deleteFiles($ids, $cfg['fileTable'], $cfg['uploadDir'], JText::_('MSG_FILEERRODEL'));

            // DELETE RELATIONSHIP
            if(!empty($_SESSION[$RTAG.'RelTable']) && !empty($_SESSION[$RTAG.'AppNameId'])) :
              $query = 'DELETE FROM '. $db->quoteName($_SESSION[$RTAG.'RelTable']) .' WHERE '. $db->quoteName($_SESSION[$RTAG.'AppNameId']) .' IN ('.$ids.')';
              $db->setQuery($query);
      				$db->execute();
            else :
              // FORCE DELETE RELATIONSHIPS
              // força a exclusão do(s) relacionamento(s) caso os parâmetros não sejam setados
              // isso é RECOMENDÁVEL sempre que houver um ou mais relacionamentos
                // CLIENTS PLANS -> remove os registros relacionados aos clientes
                // $query = 'DELETE FROM '. $db->quoteName('#__apcefpb_clients_hosts') .' WHERE '. $db->quoteName('plan_id') .' IN ('.$ids.')';
                // $db->setQuery($query);
      					// $db->execute();
            endif;

            // UPDATE FIELD
            // executa apenas com valores individuais
            $element = $elemVal = $elemLabel = '';
            if(!empty($_SESSION[$RTAG.'FieldUpdated']) && !empty($_SESSION[$RTAG.'TableField'])) :
              $element = $_SESSION[$RTAG.'FieldUpdated'];
              $elemVal = $ids;
            endif;

  					$data[] = array(
  						'status'=> 3,
              'ids'	=> explode(',', $ids),
  						'msg'	=> JText::_('MSG_DELETED'),
              'uploadError' => $fileMsg,
    					'parentField'	=> $element,
    					'parentFieldVal'	=> $elemVal
  					);

  				} catch (RuntimeException $e) {

  					$data[] = array(
  						'status'=> 0,
  						'msg'	=> $e->getMessage(),
              'uploadError' => $fileMsg
  					);

  				}

        // STATE
        elseif($task == 'state') :

          $stateVal = ($state == 2 ? 'IF(state = 1, 0, 1)' : $state);
          $query = 'UPDATE '. $db->quoteName($cfg['mainTable']) .' SET '. $db->quoteName('state') .' = '.$stateVal.' WHERE '. $db->quoteName('id') .' IN ('.$ids.')';

          try {
            $db->setQuery($query);
            $db->execute();

            // UPDATE FIELD
            // executa apenas com valores individuais
            $element = $elemVal = $elemLabel = '';
            if(!empty($_SESSION[$RTAG.'FieldUpdated']) && !empty($_SESSION[$RTAG.'TableField'])) :
              $element = $_SESSION[$RTAG.'FieldUpdated'];
              $elemVal = $ids;
              $query = '
              SELECT '.
                $db->quoteName($_SESSION[$RTAG.'TableField']) .'
              FROM '. $db->quoteName($cfg['mainTable']).'
              WHERE '. $db->quoteName('id') .' = '.$ids;
              $db->setQuery($query);
              $elems = $db->loadObject();
              $elemLabel = $elems->name;
            endif;

            $data[] = array(
              'status' => 4,
              'state' => $state,
              'ids'	=> explode(',', $ids),
              'msg'	=> '',
    					'parentField'	=> $element,
    					'parentFieldVal'	=> $elemVal,
    					'parentFieldLabel'	=> baseHelper::nameFormat($elemLabel),
    					'parentFieldUrl'	=> $elemUrl
            );

          } catch (RuntimeException $e) {

            $data[] = array(
              'status'=> 0,
              'msg'	=> $e->getMessage()
            );

          }

  			// DELETE FILE
  			elseif($cfg['hasUpload'] && $task == 'delFile' && $fname) :

            // FILE: remove o arquivo
            $fileMsg = uploader::deleteFile($fname, $cfg['fileTable'], $cfg['uploadDir'], JText::_('MSG_FILEERRODEL'));

  					$data[] = array(
  						'status'=> 5,
  						'msg'	=> JText::_('MSG_FILE_DELETED'),
              'uploadError' => $fileMsg
  					);

  			// DELETE FILES
  			elseif($cfg['hasUpload'] && $task == 'delFiles' && $fileId) :

            // FILE: remove o arquivo
            $fileMsg = uploader::deleteFiles($fileId, $cfg['fileTable'], $cfg['uploadDir'], JText::_('MSG_FILEERRODEL'));

  					$data[] = array(
  						'status'=> 6,
  						'msg'	=> JText::_('MSG_FILE_DELETED'),
              'uploadError' => $fileMsg
  					);

        // INVOICE
        elseif($task == 'invoice') :

          $query = 'UPDATE '. $db->quoteName($cfg['mainTable']) .' SET '. $db->quoteName('invoice_id') .' = '.$state.' WHERE '. $db->quoteName('id') .' IN ('.$ids.')';

          try {
            $db->setQuery($query);
            $db->execute();

            $data[] = array(
              'status' => 1,
              'msg'	=> ''
            );

          } catch (RuntimeException $e) {

            // Error treatment
            switch($e->getCode()) {
              case '1062':
                $sqlErr = JText::_('MSG_SQL_DUPLICATE_FIXED');
                break;
              default:
                $sqlErr = 'Erro: '.$e->getCode().'. '.$e->getMessage();
            }

            $data[] = array(
              'status'=> 0,
              'msg'	=> $sqlErr
            );

          }

        // REMOVE INVOICE
        elseif($task == 'removeInvoice') :

          $query = 'UPDATE '. $db->quoteName($cfg['mainTable']) .' SET '. $db->quoteName('invoice_id') .' = "" WHERE '. $db->quoteName('id') .' IN ('.$ids.')';

          try {
            $db->setQuery($query);
            $db->execute();

            $data[] = array(
              'status' => 1,
              'msg'	=> ''
            );

          } catch (RuntimeException $e) {

            $data[] = array(
              'status'=> 0,
              'msg'	=> $e->getMessage()
            );

          }

        endif; // end task

  		endif; // num rows

  	else :

  		// INSERT
  		if($task == 'save') :

        // validation
        if($request['provider_id'] != 0 && $request['client_id'] != 0 && !empty($request['price']) && !empty($request['date'])) :

          // USERGROUP
          // 11 -> 'efetivo' -> 0 (sócio efetivo)
          // 12 -> 'aposentado' -> 0 (sócio efetivo)
          // 13 -> 'contribuinte' -> 1 (contribuinte)
          $query = 'SELECT '. $db->quoteName('usergroup') .' FROM '. $db->quoteName('#__apcefpb_clients').' WHERE '. $db->quoteName('id').' = '.$request['client_id'];
          $db->setQuery($query);
          $grp = $db->loadResult();

          $invGroup = ($grp == 13) ? 1 : 0;

          for($i = 1; $i <= $request['total']; $i++) {

            // Prepare the insert query
      			$query  = '
            INSERT INTO '. $db->quoteName($cfg['mainTable']) .'('.
              $db->quoteName('transaction_id') .','.
              $db->quoteName('parent_id') .','.
              $db->quoteName('provider_id') .','.
              $db->quoteName('client_id') .','.
              $db->quoteName('dependent_id') .','.
              $db->quoteName('invoice_id') .','.
              $db->quoteName('invoice_group') .','.
              $db->quoteName('description') .','.
              $db->quoteName('fixed') .','.
              $db->quoteName('isCard') .','.
              $db->quoteName('date') .','.
              $db->quoteName('date_installment') .','.
              $db->quoteName('price') .','.
              $db->quoteName('price_total') .','.
              $db->quoteName('installment') .','.
              $db->quoteName('total') .','.
              $db->quoteName('doc_number') .','.
              $db->quoteName('note') .','.
      				$db->quoteName('state') .','.
      				$db->quoteName('created_by')
      			.') VALUES ('.
              (isset($transactionID) ? $transactionID : 0) .','.
              (isset($id) ? $id : 0) .','.
              $request['provider_id'] .','.
              $request['client_id'] .','.
              $request['dependent_id'] .','.
      				$request['invoice_id'] .','.
      				$invGroup .','.
      				$db->quote($request['description']) .','.
      				$request['fixed'] .','.
      				$request['isCard'] .','.
      				$db->quote($request['date']) .','.
      				$date_installment .','.
      				$db->quote($price) .','.
      				$db->quote($request['price']) .','.
      				$i .','.
      				$request['total'] .','.
      				$db->quote($request['doc_number']) .','.
              $db->quote($request['note']) .','.
      				$request['state'] .','.
      				$user->id
      			.')';

      			try {

      				$db->setQuery($query);
      				$db->execute();
              $id = $db->insertid();
              if($i == 1) {
                $transactionID = $id;
                $request['invoice_id'] = 0;
                // atribui o 'transaction_id' ao item principal para a ordenação na listagem
                $query = 'UPDATE '. $db->quoteName($cfg['mainTable']) .' SET '.$db->quoteName('transaction_id').' = '.$id.' WHERE '.$db->quoteName('id').' = '.$id;
                $db->setQuery($query);
                $db->execute();
              }
              $date_installment = 'DATE_ADD('. $db->quote($request['date']) .', INTERVAL '.($i).' MONTH)';
              // Upload
              if($cfg['hasUpload'] && $id)
              $fileMsg = uploader::uploadFile($id, $cfg['fileTable'], $_FILES[$cfg['fileField']], $cfg);

              // CREATE RELATIONSHIP
              if(!empty($_SESSION[$RTAG.'RelTable']) && !empty($_SESSION[$RTAG.'RelNameId']) && !empty($_SESSION[$RTAG.'AppNameId']) && !empty($request['relationId'])) :
                $query  = '
                INSERT INTO '. $db->quoteName($_SESSION[$RTAG.'RelTable']) .'('.
                  $db->quoteName($_SESSION[$RTAG.'AppNameId']) .','.
                  $db->quoteName($_SESSION[$RTAG.'RelNameId'])
          			.') VALUES ('.
                  $id .','.
                  $request['relationId']
          			.')';
                $db->setQuery($query);
        				$db->execute();
              endif;

              // UPDATE FIELD
              $element = $elemVal = $elemLabel = '';
              if(!empty($_SESSION[$RTAG.'FieldUpdated']) && !empty($_SESSION[$RTAG.'TableField'])) :
                $element = $_SESSION[$RTAG.'FieldUpdated'];
                $elemVal = $id;
                $query = 'SELECT '. $db->quoteName($_SESSION[$RTAG.'TableField']) .', '.$db->quoteName('code').' FROM '. $db->quoteName($cfg['mainTable']).' WHERE id='.$id.' AND state = 1';
                $db->setQuery($query);
                $elems = $db->loadObject();
                $elemLabel = $elems->name;
              endif;

      				$data[] = array(
      					'status'=> 1,
      					'msg'	=> JText::_('MSG_SAVED'),
                'regID'	=> $transactionID,
                'uploadError' => $fileMsg,
      					'parentField'	=> $element,
      					'parentFieldVal'	=> $elemVal,
      					'parentFieldLabel'	=> baseHelper::nameFormat($elemLabel),
      					'parentFieldUrl'	=> $elemUrl
      				);

      			} catch (RuntimeException $e) {

              // Error treatment
              switch($e->getCode()) {
                case '1062':
                  $sqlErr = JText::_('MSG_SQL_DUPLICATE_KEY');
                  break;
                default:
                  $sqlErr = 'Erro: '.$e->getCode().'. '.$e->getMessage();
              }

              $data[] = array(
      					'status'=> 0,
      					'msg'	=> $sqlErr,
                'uploadError' => $fileMsg
      				);

      			}

          } // END FOR

        else :

          $data[] = array(
            'status'=> 0,
            'msg'	=> JText::_('MSG_ERROR'),
            'uploadError' => $fileMsg
          );

        endif; // end validation

      // CUSTOM: get dependents list of client
      elseif($task == 'cList' && $client != 0) :

        // get client_id of project
        // get contacts list of project's client
        $query = '
        SELECT id, name FROM
          '. $db->quoteName('#__apcefpb_dependents') .'
        WHERE '. $db->quoteName('client_id') .' = '.$client;

        $cardLimit = getCardLimit($client, $cfg);

        try {
          $db->setQuery($query);
      		$db->execute();
      		$num_itens = $db->getNumRows();
      		$list = $db->loadObjectList();

          if($num_itens) :
            foreach($list as $item) {
        			$data[] = array(
                // Default Fields
        				'status'  => 1,
                'client'  => $client,
                // App Fields
        				'id'	    => $item->id,
        				'name'    => baseHelper::nameFormat($item->name),
                'cardLimit' => $cardLimit
        			);
        		}
          else :
            $data[] = array(
              'status'  => 2,
              'cardLimit' => $cardLimit
            );
          endif;

        } catch (RuntimeException $e) {

          $data[] = array(
            'status'=> 0,
            'msg'	=> $e->getMessage()
          );

        }

      // CUSTOM -> ADD FIXED
      elseif($task == 'addFixed') :

        $query = '
        INSERT INTO '. $db->quoteName($cfg['mainTable']) .' ('.
          $db->quoteName('transaction_id') .','.
          $db->quoteName('parent_id') .','.
          $db->quoteName('provider_id') .','.
          $db->quoteName('client_id') .','.
          $db->quoteName('dependent_id') .','.
          $db->quoteName('invoice_group') .','.
          $db->quoteName('description') .','.
          $db->quoteName('fixed') .','.
          $db->quoteName('date') .','.
          $db->quoteName('date_installment') .','.
          $db->quoteName('price') .','.
          $db->quoteName('price_total') .','.
          $db->quoteName('installment') .','.
          $db->quoteName('total') .','.
          $db->quoteName('doc_number') .','.
          $db->quoteName('note') .','.
          $db->quoteName('state') .','.
          $db->quoteName('created_by')
        .')
        SELECT '.
          $db->quoteName('T1.transaction_id') .','.
          $db->quoteName('T1.parent_id') .','.
          $db->quoteName('T1.provider_id') .','.
          $db->quoteName('T1.client_id') .','.
          $db->quoteName('T1.dependent_id') .','.
          $db->quoteName('T1.invoice_group') .','.
          $db->quoteName('T1.description') .', 2,'.
          $db->quoteName('T1.date') .', CURDATE(),'.
          $db->quoteName('T1.price') .','.
          $db->quoteName('T1.price_total') .', 1, 1,'.
          $db->quoteName('T1.doc_number') .','.
          $db->quoteName('T1.note') .', 1,'.
          $user->id
        .' FROM '. $db->quoteName($cfg['mainTable']) .' T1
    			JOIN '. $db->quoteName('#__apcefpb_providers') .' T2
    			ON T2.id = T1.provider_id
    			JOIN '. $db->quoteName('#__apcefpb_clients') .' T3
    			ON T3.id = T1.client_id
        WHERE T1.fixed = 1 AND T1.state = 1 AND T3.state = 1 AND T1.invoice_group = '.$state.'
        ORDER BY T1.id
        ';

        try {
          $db->setQuery($query);
          $db->execute();

          $data[] = array(
            'status' => 1,
            'msg'	=> ''
          );

        } catch (RuntimeException $e) {

          // Error treatment
          switch($e->getCode()) {
            case '1062':
              $sqlErr = JText::_('MSG_SQL_DUPLICATE_FIXED');
              break;
            default:
              $sqlErr = 'Erro: '.$e->getCode().'. '.$e->getMessage();
          }

          $data[] = array(
            'status'=> 0,
            'msg'	=> $sqlErr
          );

        }

      // CUSTOM -> GENERATE CSV FILE FOR DEBITS
      elseif($task == 'invoiceFile') :

        $inv = $state;

        if($inv != 0 && $seq != 0) :

          // SELECT PARA GERAR O CÓDIGO DE REGISTRO DO TIPO 'E' -> débito automático
          $moeda		= '03'; // código da moeda -> R$ REAL
          $cod_movimento	= '0'; // débito normal
          // os campos 'tipo_1cnpj_2cpf', 'cnpj_cpf', 'reservado_futuro' são opcionais. Sendo assim, ficam em branco mesmo...
          $query_tipoE = "
            SELECT
              'E' AS cod_registro,
              REPLACE(SUBSTRING(CONCAT(username, '#########################'), 1, 25), '#', ' ') AS cod_associado,
              SUBSTRING(CONCAT('0000',agencia),-4) AS agencia_associado,
              REPLACE(SUBSTRING(CONCAT(conta, '##############'), 1, 14), '#', ' ') AS conta_associado,
              DATE_FORMAT(date, '%Y%m%d') AS data_vencimento,
              SUBSTRING(CONCAT('000000000000000', SUM(val)), -15) AS valor_debitado,
              '".$moeda."' AS moeda,
              REPLACE(SUBSTRING(CONCAT(UPPER(nome), '############################################################'), 1, 60), '#', ' ') AS uso_opcional_empresa,
              REPLACE('#', '#', ' ') AS tipo_1cnpj_2cpf,
              REPLACE('###############', '#', ' ') AS cnpj_cpf,
              REPLACE('####', '#', ' ') AS reservado_futuro,
              '".$cod_movimento."' AS cod_movimento
            FROM
              (
                SELECT
                  T4.username, T2.due_date as date, SUBSTRING_INDEX((REPLACE(REPLACE(T1.price,',',''),'.','')),'.',1) val, T6.agency agencia, CONCAT(SUBSTRING(CONCAT('000',T6.operation),-3), SUBSTRING(CONCAT('000000000',T6.account),-9)) conta, T4.name nome
                FROM
                  #__apcefpb_transactions T1
                  JOIN #__apcefpb_invoices AS T2
                  ON T2.id = T1.invoice_id
                  JOIN #__apcefpb_clients T3
                  ON T3.id = T1.client_id
                  JOIN #__users T4
                  ON T4.id = T3.user_id
                  JOIN #__apcefpb_rel_clients_banksAccounts T5
                  ON T5.client_id = T1.client_id
                  JOIN #__apcefpb_banks_accounts T6
                  ON T6.id = T5.bankAccount_id AND T6.bank_id = 1
                WHERE
                  T1.invoice_id = ".$inv."
              ) TB
            GROUP BY nome
          ";
          // SELECT PARA GERAR O CÓDIGO DE REGISTRO DO TIPO 'Z' -> Trailler (ultima linha do arquivo. Com os total de linhas e valor debitado)
          // o campo 'reservado_futuro' fica em branco mesmo...
          $query_tipoZ = "
            SELECT
              'Z' AS cod_registro,
              SUBSTRING(CONCAT('000000',(COUNT(*)+2)),-6) AS total_linhas_registros,
              SUBSTRING(CONCAT('00000000000000000',SUM(val)),-17) AS valor_debitado_total,
              REPLACE('##############################################################################################################################','#',' ') AS reservado_futuro
            FROM
              (
                SELECT
                  SUBSTRING_INDEX(SUM(REPLACE(REPLACE(T1.price,',','') ,'.','')),'.',1) val, T4.name
                FROM
                  #__apcefpb_transactions T1
                  JOIN #__apcefpb_invoices AS T2
                  ON T2.id = T1.invoice_id
                  JOIN #__apcefpb_clients T3
                  ON T3.id = T1.client_id
                  JOIN #__users T4
                  ON T4.id = T3.user_id
                  JOIN #__apcefpb_rel_clients_banksAccounts T5
                  ON T5.client_id = T1.client_id
                  JOIN #__apcefpb_banks_accounts T6
                  ON T6.id = T5.bankAccount_id AND T6.bank_id = 1
                WHERE
                  T1.invoice_id = ".$inv."
                GROUP BY T4.name
              ) TB
          ";
          // salva o sequencial utilizado
          $qSeq = 'INSERT INTO '. $db->quoteName('#__apcefpb_invoices_debits') .' ('.$db->quoteName('invoice_id').', '.$db->quoteName('sequencial').', '.$db->quoteName('created_by').') VALUES ('.$inv.', '.$seq.', '.$user->id.')';
          $db->setQuery($qSeq);
          $db->execute();

          // gera o resultado para o CSV
          $result = setFile($query_tipoE,'A15003                ,APCEF PB            ,104CAIXA ECONOM FEDERAL'.date("Ymd").substr('000000'.$seq, -6).'05DEBITO AUTOMATICO                                                     ','','',false);
          $result .= setFile($query_tipoZ,NULL,'','');
          if($result == true) :

            $query = "SELECT CONCAT('".time()."_".$inv."_',DATE_FORMAT(due_date,'%d%m%y'),'_',IF(group_id = 0, 'associado', 'contribuinte'),'.txt') filename FROM #__apcefpb_invoices WHERE id = ".$inv;
            $db->setQuery($query);
            $file = $db->loadResult();

            $path = JPATH_SITE.'images/debitos/';
            $filePath = $path.$file;

            // abrimos um arquivo somente para escrita
            $fp = fopen($filePath,"w");
            // escrevemos o conteúdo da variável
            $fwrite = fwrite($fp,$result);
            // fechamos o arquivo
            fclose($fp);

            $data[] = array(
              'status' => 1,
              'file' => $file,
              'msg'	=> ''
            );

          else :

            $data[] = array(
              'status'=> 0,
              'msg'	=> 'Consulta sem resultados'
            );

          endif;

        else :

          $data[] = array(
            'status'=> 0,
            'msg'	=> 'A fatura não existe ou o sequencial não foi informado!'
          );

        endif; // if 'inv' ou 'seq'

      endif; // end 'task'

  	endif; // end 'id'

  	$json = json_encode($data);

  	# JSON if no callback
  	if(!isset($_GET['callback'])) exit($json);

  	# JSONP if valid callback
  	if(is_valid_callback($_GET['callback'])) exit("{$_GET['callback']}($json)");

  	# Otherwise, bad request
  	header('status: 400 Bad Request', true, 400);

  endif;

else :

  # Otherwise, bad request
  header('status: 400 Bad Request', true, 400);

endif;

?>
