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
    $project    = $input->get('pID', 0, 'int');

    // upload actions
    $fileMsg  = '';
    if($cfg['hasUpload']) :
      $fname    = $input->get('fname', '', 'string');
      $fileId   = $input->get('fileId', 0, 'int');
      // load 'uploader' class
      JLoader::register('uploader', JPATH_BASE.'/templates/base/source/helpers/upload.php');
    endif;

  	// fields 'Form' requests
    $request                    = array();
    // default
    $request['relationId']      = $input->get('relationId', 0, 'int');
  	$request['state']           = $input->get('state', 1, 'int');
    // app
    $request['user_id']         = $input->get('user_id', 0, 'int');
    $request['project_id']      = $input->get('project_id', 0, 'int');
    $request['projectType_id']  = $input->get('projectType_id', 0, 'int');
    $request['price']           = $input->get('price', 0.00, 'float');
    $request['discount']        = $input->get('discount', 0.00, 'float');
    $request['status']          = $input->get('status', 0, 'int');

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
        $csv = $headRow;
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
      			$data[] = array(
              // Default Fields
      				'id'		      => $item->id,
      				'state'       => $item->state,
        			'prev'	      => $prev,
          		'next'	      => $next,
              // App Fields
              'user_id'	        => $item->user_id,
              'project_id'	    => $item->project_id,
              'projectType_id'  => $item->projectType_id,
              'price'           => $item->price,
              'discount'        => $item->discount,
              'status'          => $item->status
      			);
      		}

  			// UPDATE
  			elseif($task == 'save' && $id) :

  				$query  = 'UPDATE '.$db->quoteName($cfg['mainTable']).' SET ';
  				$query .=
            $db->quoteName('projectType_id') 	.'='. $request['projectType_id'] .','.
            $db->quoteName('price') 	.'='. $db->quote($request['price']) .','.
            $db->quoteName('discount') 	.'='. $db->quote($request['discount']) .','.
            $db->quoteName('status') 	.'='. $request['status'] .','.
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
              $query = 'SELECT '. $db->quoteName($_SESSION[$RTAG.'TableField']) .' FROM '. $db->quoteName($cfg['mainTable']).' WHERE id='.$id.' AND state = 1';
              $db->setQuery($query);
              $elemLabel = $db->loadResult();
            endif;

  					$data[] = array(
  						'status' => 2,
  						'msg'	=> JText::_('MSG_SAVED'),
              'uploadError' => $fileMsg,
    					'parentField'	=> $element,
    					'parentFieldVal'	=> $elemVal,
    					'parentFieldLabel'	=> baseHelper::nameFormat($elemLabel)
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
              // RESULTADOS -> remove os registros relacionados aos resultados do evento
              // $query = 'DELETE FROM '. $db->quoteName('#__zenite_results') .' WHERE '. $db->quoteName('projectResult_id') .' IN ('.$ids.')';
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
              $query = 'SELECT '. $db->quoteName($_SESSION[$RTAG.'TableField']) .' FROM '. $db->quoteName($cfg['mainTable']).' WHERE '. $db->quoteName('id') .' = '.$ids;
              $db->setQuery($query);
              $elemLabel = $db->loadResult();
            endif;

            $data[] = array(
              'status' => 4,
              'state' => $state,
              'ids'	=> explode(',', $ids),
              'msg'	=> '',
    					'parentField'	=> $element,
    					'parentFieldVal'	=> $elemVal,
    					'parentFieldLabel'	=> baseHelper::nameFormat($elemLabel)
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

  			endif; // end task

  		endif; // num rows

  	else :

  		// INSERT
  		if($task == 'save') :

        // validation
        if($request['project_id'] != 0 && $request['projectType_id'] != 0) :

          // Prepare the insert query
    			$query  = '
          INSERT INTO '. $db->quoteName($cfg['mainTable']) .'('.
            $db->quoteName('user_id') .','.
            $db->quoteName('project_id') .','.
            $db->quoteName('projectType_id') .','.
            $db->quoteName('price') .','.
            $db->quoteName('discount') .','.
            $db->quoteName('status') .','.
    				$db->quoteName('state') .','.
    				$db->quoteName('created_by')
    			.') VALUES ('.
            $request['user_id'] .','.
            $request['project_id'] .','.
            $request['projectType_id'] .','.
            $db->quote($request['price']) .','.
            $db->quote($request['discount']) .','.
            $request['status'] .','.
    				$request['state'] .','.
    				$user->id
    			.')';

    			try {

    				$db->setQuery($query);
    				$db->execute();
            $id = $db->insertid();
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
              $query = 'SELECT '. $db->quoteName($_SESSION[$RTAG.'TableField']) .' FROM '. $db->quoteName($cfg['mainTable']).' WHERE id='.$id.' AND state = 1';
              $db->setQuery($query);
              $elemLabel = $db->loadResult();
            endif;

    				$data[] = array(
    					'status'=> 1,
    					'msg'	=> JText::_('MSG_SAVED'),
              'regID'	=> $id,
              'uploadError' => $fileMsg,
    					'parentField'	=> $element,
    					'parentFieldVal'	=> $elemVal,
    					'parentFieldLabel'	=> baseHelper::nameFormat($elemLabel)
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

        else :

          $data[] = array(
            'status'=> 0,
            'msg'	=> JText::_('MSG_ERROR'),
            'uploadError' => $fileMsg
          );

        endif; // end validation

      // CUSTOM: get contacts list of client
      elseif($task == 'cList' && !empty($project) && $project != 0) :

        // get types list
        $query = '
        SELECT
          '. $db->quoteName('T1.id') .',
          '. $db->quoteName('T2.name') .' category,
          '. $db->quoteName('T3.name') .' disability,
          '. $db->quoteName('T1.distance') .',
          '. $db->quoteName('T1.distance_unit') .'
        FROM
          '. $db->quoteName('#__zenite_projects_types') .' T1
          JOIN '. $db->quoteName('#__zenite_projects_categories') .' T2
          ON T2.id = T1.category_id
          LEFT JOIN '. $db->quoteName('#__zenite_disabilities') .' T3
          ON T3.id = T1.disability_id
        WHERE
          T1.project_id = '.$project.' AND T1.state = 1
        ORDER BY '. $db->quoteName('T2.name') .' ASC, '. $db->quoteName('T1.distance') .' ASC,'. $db->quoteName('T1.id') .' ASC';

        try {
          $db->setQuery($query);
      		$db->execute();
      		$num_itens = $db->getNumRows();
      		$list = $db->loadObjectList();

          if($num_itens) :
            foreach($list as $item) {
              $d = !empty($item->disability) ? ' ('.baseHelper::nameFormat($item->disability).')' : '';
        			$data[] = array(
                // Default Fields
        				'status'  => 1,
                // App Fields
        				'id'	    => $item->id,
        				'name'    => baseHelper::nameFormat($item->category).' '.$item->distance.($item->distance_unit == 0 ? ' m' : ' Km').$d
                // 'opt' => $list
        			);
        		}
          else :
            $data[] = array(
              'status'  => 2
            );
          endif;

        } catch (RuntimeException $e) {

          $data[] = array(
            'status'=> 0,
            'msg'	=> $e->getMessage()
          );

        }

      // USERS SYNCRONIZE
      elseif($task == 'userSync') :

        // seleciona os contatos com relação
        $query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable']).' WHERE status = 0 AND state = 1';
        $db->setQuery($query);
        $bills = $db->loadObjectList();

        // // verifica através de webservice
        // require_once("WSBillingStatus.php");
        // foreach ($bills as $item) {
        //   // Inicia a classe WSBillingStatus
        //   $WSBillingStatus = new WSBillingStatus();
        //   // Cria o cabe�alho SOAP
        //   $xmlObj = $WSBillingStatus->add_node("","soap-env:Envelope");
        //   $WSBillingStatus->add_attributes($xmlObj, array("xmlns:soap-env" => "http://schemas.xmlsoap.org/soap/envelope/") );
        //   $xmlObj = $WSBillingStatus->add_node($xmlObj,"soap-env:Body");
        //   // Cria  o elemento m:F2bCobranca
        //   $xmlObjF2bCobranca = $WSBillingStatus->add_node($xmlObj,"m:F2bSituacaoCobranca");
        //   $WSBillingStatus->add_attributes($xmlObjF2bCobranca, array("xmlns:m" => "http://www.f2b.com.br/soap/wsbillingstatus.xsd") );
        //   // Cria o elemento mensagem
        //   $xmlObj = $WSBillingStatus->add_node($xmlObjF2bCobranca,"mensagem");
        //   $WSBillingStatus->add_attributes($xmlObj, array("data" => date("Y-m-d"), "numero" => $item->id));
        //   // Cria o elemento cliente
        //   $xmlObj = $WSBillingStatus->add_node($xmlObjF2bCobranca,"cliente");
        //   $WSBillingStatus->add_attributes($xmlObj, array("conta" => "9023010621330163", "senha" => "06113001"));
        //   // Cria o elemento cobranca
        //   $xmlObjCobranca = $WSBillingStatus->add_node($xmlObjF2bCobranca,"cobranca");
        //   // ********************** situa��o das cobran�as ************************************
        //   $WSBillingStatus->add_attributes($xmlObjCobranca, array("numero_documento" => $item->id));
        //   // envia dados
        //   $WSBillingStatus->send($WSBillingStatus->getXML());
        //   $resposta = $WSBillingStatus->resposta;
        //   if(strlen($resposta) > 0){
        //   	// Reinicia a classe WSBillingStatus, agora com uma string XML
        //   	$WSBillingStatus = new WSBillingStatus($resposta);
        //
        //   	// LOG
        //   	$log = $WSBillingStatus->pegaLog();
        //   	if($log["texto"] == "OK"){
        //   		// COBRANCAS
        //   		$cobranca = $WSBillingStatus->pegaCobranca();
        //   		$status = $cobranca[0]["situacao"];
        //
        //       // reseta o user_id
        //       $query = 'UPDATE '. $db->quoteName($cfg['mainTable']) .' SET user_id = 0 WHERE id = '.$usr->id;
        //       $db->setQuery($query);
        //       $db->execute();
        //
        //   } else {
        //   	echo '<font color="red">Sem resposta</font>';
        //   }
        //
        //
        //   // se o nome do usuário vier vazio significa que o usuário não existe
        //   if(empty($usr->uName)) :
        //
        //   // se um dos dados for diferente entre os sistema, atualiza...
        //   elseif($usr->cName != $usr->uName || $usr->cEmail != $usr->uEmail) :
        //     $query = 'UPDATE '. $db->quoteName($cfg['mainTable']) .' SET name = UPPER('. $db->quote($usr->uName) .'), email = '. $db->quote($usr->uEmail) .' WHERE id = '.$usr->id;
        //     $db->setQuery($query);
        //     $db->execute();
        //   endif;
        // }
        //
        // $data[] = array(
        //   'status' => $query
        // );

      // CUSTOM -> GENERATE CSV FILE FOR DEBITS
      elseif($task == 'subsFile') :

        $project = $state;

        if($project != 0) :

          $query = '
        		SELECT
              '. $db->quoteName('T6.name') .' AS nome,
              '. $db->quoteName('T7.cpf') .' AS cpf,
              '. $db->quoteName('T7.gender') .' AS sexo,
              DATE_FORMAT('. $db->quoteName('T7.birthday') .', "%d/%m/%Y") AS aniversario,
              '. $db->quoteName('T7.phone_number') .' AS telefone,
              '. $db->quoteName('T6.email') .' AS email,
              '. $db->quoteName('T1.team') .' AS equipe,
              '. $db->quoteName('T4.name') .' AS modalidade,
              CONCAT('. $db->quoteName('T3.distance') .', " ", IF('. $db->quoteName('T3.distance_unit') .' = 1, "Km", "m")) AS distancia,
              '. $db->quoteName('T5.name') .' AS deficiencia,
              '. $db->quoteName('T1.sizeShirt') .' AS camisa,
              ('. $db->quoteName('T1.price') .' - '. $db->quoteName('T1.discount') .') AS valor,
              DATE_FORMAT('. $db->quoteName('T1.created_date') .', "%d/%m/%Y") AS date
            FROM
              '. $db->quoteName($cfg['mainTable']) .' T1
              JOIN '. $db->quoteName('#__zenite_projects') .' T2
              ON T2.id = T1.project_id
              JOIN '. $db->quoteName('#__zenite_projects_types') .' T3
              ON T3.id = T1.projectType_id
              JOIN '. $db->quoteName('#__zenite_projects_categories') .' T4
              ON T4.id = T3.category_id
              LEFT JOIN '. $db->quoteName('#__zenite_disabilities') .' T5
              ON T5.id = T3.disability_id
              JOIN '. $db->quoteName('#__users') .' T6
              ON T6.id = T1.user_id
              JOIN '. $db->quoteName('#__zenite_user_info') .' T7
              ON T7.user_id = T6.id
            WHERE T2.id = '.$project.' AND T1.status = 2
          ';
          // gera o resultado para o CSV
          $result = setFile($query,'NOME,CPF,SEXO,DATA DE NASCIMENTO,TELEFONE,EMAIL,EQUIPE,MODALIDADE,DISTÂNCIA,DEFICIÊNCIA,TAM. DA CAMISA,VALOR,DATA DA INSCRIÇÃO',NULL,'',false);
          if($result == true) :

            $file = 'inscricoes-evento-'.$project.'.csv';
            $path = JPATH_SITE.'images/registrations/';
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
            'msg'	=> 'O evento não existe!'
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
