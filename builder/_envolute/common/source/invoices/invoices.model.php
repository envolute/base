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
    $param      = $input->get('dt', '', 'string');

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
  	$request['project_id']   = $input->get('project_id', 0, 'int');
    $request['type']         = $input->get('type', 0, 'int');
  	$request['due_date']     = $input->get('due_date', '', 'string');
  	$request['month']        = $input->get('month', 0, 'int');
  	$request['year']         = $input->get('year', date('Y'), 'string');
  	$request['price']        = $input->get('price', 0.00, 'float');
    $request['create_boleto']= $input->get('create_boleto', 1, 'int');
  	$request['description']  = $input->get('description', '', 'string');
  	$request['description1'] = $input->get('description1', '', 'string');
  	$request['description2'] = $input->get('description2', '', 'string');
  	$request['description3'] = $input->get('description3', '', 'string');
  	$request['description4'] = $input->get('description4', '', 'string');
  	$request['hosting']      = $input->get('hosting', 0, 'int');
  	$request['discount']     = $input->get('discount', 0.00, 'float');
  	$request['discount_note']= $input->get('discount_note', '', 'string');
  	$request['tax']          = $input->get('tax', 0.00, 'float');
  	$request['tax_note']     = $input->get('tax_note', '', 'string');
  	$request['assessment']           = $input->get('assessment', 0.00, 'float');
  	$request['assessment_note']      = $input->get('assessment_note', '', 'string');
  	$request['email_subject']        = $input->get('email_subject', '', 'string');
  	$request['email_content']        = $input->get('email_content', '', 'raw');
  	$request['email_subject_resend'] = $input->get('email_subject_resend', '', 'string');
  	$request['email_content_resend'] = $input->get('email_content_resend', '', 'raw');
  	$request['url_boleto']           = $input->get('url_boleto', '', 'string');
  	$request['bankAccount_info']     = $input->get('bankAccount_info', '', 'raw');
  	$request['paid_date']            = $input->get('paid_date', '', 'string');
    $paid = (!empty($request['paid_date']) && $request['paid_date'] != '0000-00-00') ? 1 : 0;
  	$request['note']         = $input->get('note', '', 'string');

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
      				'project_id'	=> $item->project_id,
      				'type'	      => $item->type,
      				'due_date'	  => $item->due_date,
      				'month'	      => $item->month,
      				'year'	      => $item->year,
      				'price'	      => $item->price,
      				'create_boleto'=> $item->create_boleto,
      				'description'	=> $item->description,
      				'description1'=> $item->description1,
      				'description2'=> $item->description2,
      				'description3'=> $item->description3,
      				'description4'=> $item->description4,
      				'hosting'	    => $item->hosting,
      				'discount'	  => $item->discount,
      				'discount_note'=> $item->discount_note,
      				'tax'	        => $item->tax,
      				'tax_note'    => $item->tax_note,
      				'assessment'	=> $item->assessment,
      				'assessment_note'        => $item->assessment_note,
      				'sent'                   => $item->sent,
      				'email_subject'	         => $item->email_subject,
      				'email_content'	         => $item->email_content,
      				'email_subject_resend'	 => $item->email_subject_resend,
      				'email_content_resend'	 => $item->email_content_resend,
      				'url_boleto'	           => $item->url_boleto,
      				'bankAccount_info'	     => $item->bankAccount_info,
      				'paid'	      => $item->paid,
      				'paid_date'   => $item->paid_date,
      				'note'	      => $item->note,
              'files'       => $listFiles
      			);
      		}

  			// UPDATE
  			elseif($task == 'save' && $id) :

  				$query  = 'UPDATE '.$db->quoteName($cfg['mainTable']).' SET ';
  				$query .=
            $db->quoteName('type') .'='. $request['type'] .','.
            $db->quoteName('due_date') .'='. $db->quote($request['due_date']) .','.
            $db->quoteName('month') .'='. $request['month'] .','.
            $db->quoteName('year') .'='. $request['year'] .','.
            $db->quoteName('price') .'='. $db->quote($request['price']) .','.
            $db->quoteName('create_boleto') .'='. $request['create_boleto'] .','.
            $db->quoteName('description') .'='. $db->quote($request['description']) .','.
            $db->quoteName('description1') .'='. $db->quote($request['description1']) .','.
            $db->quoteName('description2') .'='. $db->quote($request['description2']) .','.
            $db->quoteName('description3') .'='. $db->quote($request['description3']) .','.
            $db->quoteName('description4') .'='. $db->quote($request['description4']) .','.
  					$db->quoteName('hosting') .'='. $request['hosting'] .','.
            $db->quoteName('discount') .'='. $db->quote($request['discount']) .','.
            $db->quoteName('discount_note') .'='. $db->quote($request['discount_note']) .','.
            $db->quoteName('tax') .'='. $db->quote($request['tax']) .','.
            $db->quoteName('tax_note') .'='. $db->quote($request['tax_note']) .','.
            $db->quoteName('assessment') .'='. $db->quote($request['assessment']) .','.
            $db->quoteName('assessment_note') .'='. $db->quote($request['assessment_note']) .','.
            $db->quoteName('email_subject') .'='. $db->quote($request['email_subject']) .','.
            $db->quoteName('email_content') .'='. $db->quote($request['email_content']) .','.
            $db->quoteName('email_subject_resend') .'='. $db->quote($request['email_subject_resend']) .','.
            $db->quoteName('email_content_resend') .'='. $db->quote($request['email_content_resend']) .','.
            $db->quoteName('url_boleto') .'='. $db->quote($request['url_boleto']) .','.
            $db->quoteName('bankAccount_info') .'='. $db->quote($request['bankAccount_info']) .','.
            $db->quoteName('paid') .'='. $paid .','.
            $db->quoteName('paid_date') .'='. $db->quote($request['paid_date']) .','.
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
              SELECT
                '. $db->quoteName('T2.name') .' project,
                '. $db->quoteName('T3.name') .' client,
                '. $db->quoteName('T1.due_date') .',
                '. $db->quoteName('T1.month') .',
                '. $db->quoteName('T1.year') .'
              FROM
                '. $db->quoteName($cfg['mainTable']).' T1
            		JOIN '. $db->quoteName('#__envolute_projects').' T2
            		ON T2.id = T1.project_id
            		JOIN '. $db->quoteName('#__envolute_clients').' T3
            		ON T3.id = T2.client_id
              WHERE T1.id='.$id;
              $db->setQuery($query);
              $elems = $db->loadObject();
              $elemLabel = baseHelper::dateFormat($elems->due_date, 'd.m').' - '.baseHelper::nameFormat($elems->project).' ['.baseHelper::nameFormat($elems->client).']';
            endif;

  					$data[] = array(
  						'status' => 2,
  						'msg'	=> JText::_('MSG_SAVED'),
              'uploadError' => $fileMsg,
    					'parentField'	=> $element,
    					'parentFieldVal'	=> $elemVal,
    					'parentFieldLabel'	=> $elemLabel
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
                // TASKS -> reseta os registros relacionados aos registros de atividades
                $query = 'UPDATE '. $db->quoteName('#__envolute_tasks_timer') .' SET '. $db->quoteName('invoice_id') .' = 0 WHERE '. $db->quoteName('invoice_id') .' IN ('.$ids.')';
                $db->setQuery($query);
      					$db->execute();
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
              $elemVal = $id;
              $query = '
              SELECT
                '. $db->quoteName('T2.name') .' client,
                '. $db->quoteName('T1.due_date') .',
                '. $db->quoteName('T1.month') .',
                '. $db->quoteName('T1.year') .'
              FROM
                '. $db->quoteName($cfg['mainTable']).' T1
                JOIN '. $db->quoteName('#__envolute_clients').' T2
                ON T2.id = T1.client_id
              WHERE T1.id='.$id;
              $db->setQuery($query);
              $elems = $db->loadObject();
              $elemLabel = baseHelper::getMonthName($elems->month).' / '.$elems->year.' ('.baseHelper::dateFormat($elems->due_date, 'd.m').') - '.baseHelper::nameFormat($elems->client);
            endif;

            $data[] = array(
              'status' => 4,
              'state' => $state,
              'ids'	=> explode(',', $ids),
              'msg'	=> '',
    					'parentField'	=> $element,
    					'parentFieldVal'	=> $elemVal,
    					'parentFieldLabel'	=> $elemLabel
            );

          } catch (RuntimeException $e) {

            $data[] = array(
              'status'=> 0,
              'msg'	=> $e->getMessage()
            );

          }

        // STATE
        elseif($task == 'sendInvoice') :

          $query = '
            SELECT
          		'. $db->quoteName('T1.id') .',
          		'. $db->quoteName('T1.project_id') .',
              '. $db->quoteName('T1.type') .' invoiceType,
          		'. $db->quoteName('T2.name') .' project,
          		'. $db->quoteName('T2.client_id') .',
          		'. $db->quoteName('T3.name') .' client,
          		'. $db->quoteName('T3.email') .',
          		'. $db->quoteName('T3.email_payment') .',
          		'. $db->quoteName('T3.type') .' clientType,
          		'. $db->quoteName('T3.doc_number') .',
          		'. $db->quoteName('T1.due_date') .',
              '. $db->quoteName('T1.price') .',
              '. $db->quoteName('T1.create_boleto') .',
          		'. $db->quoteName('T1.description') .',
          		'. $db->quoteName('T1.description1') .',
          		'. $db->quoteName('T1.description2') .',
          		'. $db->quoteName('T1.description3') .',
          		'. $db->quoteName('T1.description4') .',
          		'. $db->quoteName('T1.hosting') .',
          		'. $db->quoteName('T1.discount') .',
          		'. $db->quoteName('T1.discount_note') .',
          		'. $db->quoteName('T1.tax') .',
          		'. $db->quoteName('T1.tax_note') .',
          		'. $db->quoteName('T1.assessment') .',
          		'. $db->quoteName('T1.assessment_note') .',
          		'. $db->quoteName('T1.sent') .',
          		'. $db->quoteName('T1.resend_date') .',
          		'. $db->quoteName('T1.email_subject') .',
          		'. $db->quoteName('T1.email_content') .',
          		'. $db->quoteName('T1.email_subject_resend') .',
          		'. $db->quoteName('T1.email_content_resend') .',
          		'. $db->quoteName('T1.url_boleto') .',
          		'. $db->quoteName('T1.bankAccount_info') .',
          		'. $db->quoteName('T1.paid') .'
          	FROM
          		'. $db->quoteName($cfg['mainTable']) .' T1
          		JOIN '. $db->quoteName('#__envolute_projects').' T2
          		ON T2.id = T1.project_id
          		JOIN '. $db->quoteName('#__envolute_clients').' T3
          		ON T3.id = T2.client_id
          	WHERE
          		T1.id = '.$id;
          ;
        	$db->setQuery($query);
        	$inv = $db->loadObject();

          if($inv->id != null && $inv->paid == 0) : // verifica se existe

            // EMAIL SENDER CONFIG
            $config = JFactory::getConfig();
            $domain = baseHelper::getDomain();
            $senderName = $config->get('fromname');
            $senderMail = 'adm@envolute.com';
            $clientEmail[] = !empty($inv->email_payment) ? $inv->email_payment : $inv->email;
            $demonstrativo = '';
            if(!empty($inv->description)) :
              $demonstrativo .= $inv->description.'<br />';
              if(!empty($inv->description1)) $demonstrativo .= $inv->description1.'<br />';
              if(!empty($inv->description2)) $demonstrativo .= $inv->description2.'<br />';
              if(!empty($inv->description3)) $demonstrativo .= $inv->description3.'<br />';
              if(!empty($inv->description4)) $demonstrativo .= $inv->description4.'<br />';
              $demonstrativo .= '<br />';
            endif;

            // cobrança de hospedagem
            $hostPrice = (float)0.00;
            if($inv->hosting == 1) :
              $query = '
                SELECT
                  SUM(IF('.$db->quoteName('T1.price').' != '.$db->quote('0.00').', '.$db->quoteName('T1.price').', '.$db->quoteName('T2.price').')) priceHost
                FROM
                  '. $db->quoteName('#__envolute_hosts') .' T1
                  JOIN '. $db->quoteName('#__envolute_hosts_plans') .' T2
                  ON T2.id = T1.plan_id
                WHERE
                  T1.project_id = '.$inv->project_id.' AND T1.state = 1';
              ;
              $db->setQuery($query);
              $hostPrice = $db->loadResult();
            endif;

            if($state == 1) :

              $totalResend = $inv->price + (float)($inv->tax + $inv->assessment);
              // hospedagem
              $totalResend += $hostPrice;
              // desconto
              $totalResend -= $inv->discount;
              // total geral
              $totalResend = strval(number_format($totalResend, 2));

              // envia e-mail para lembrete de cobrança
              $mailSubject = $inv->email_subject_resend;
              $msgBoleto = !empty($inv->url_boleto) ? JText::sprintf('MSG_EMAIL_CONTENT_BOLETO', $inv->url_boleto) : '';
              $msgBankAccount = !empty($inv->bankAccount_info) ? JText::sprintf('MSG_EMAIL_CONTENT_BANK_INFO', $inv->bankAccount_info) : '';
              $msgIntro = JText::sprintf('MSG_EMAIL_RESEND_INTRO', $inv->id);
              $msgContent = JText::sprintf('MSG_EMAIL_CONTENT', baseHelper::nameFormat($inv->client), $msgIntro, $inv->description, $inv->email_content_resend, baseHelper::dateFormat($inv->due_date), baseHelper::priceFormat($totalResend), htmlentities(urlencode(base64_encode($inv->id))), $msgBoleto, $msgBankAccount);
              $msgFooter = JText::sprintf('MSG_EMAIL_FOOTER', $domain);
              $mailBody = baseHelper::mailTemplateDefault($msgContent, 'Fat. ID: #'.$id, $msgFooter, 'logo-news-team.png');
              $mailResend = JFactory::getMailer();
              $mailResend->sendMail($senderMail, $senderName, $clientEmail, $mailSubject, $mailBody, true);

            else :

              $total = 0.00;
              if($inv->invoiceType == 1) :

                $total = $inv->price;

              else :

                // registros de atividades dessa fatura
              	$query = '
              		SELECT
              			'. $db->quoteName('T1.id') .',
              			SUM('. $db->quoteName('T1.price') .') price,
              			'. $db->quoteName('T2.price') .' priceFixed,
              			'. $db->quoteName('T1.billable') .',
              			'. $db->quoteName('T1.state') .'
              		FROM
              			'. $db->quoteName('#__envolute_tasks_timer') .' T1
              			JOIN '. $db->quoteName('#__envolute_tasks') .' T2
              			ON T2.id = T1.task_id
              			LEFT JOIN '. $db->quoteName($cfg['mainTable']) .' T3
              			ON T3.id = T1.invoice_id
              		WHERE
              				T1.invoice_id = '.$id.'
                  GROUP BY T2.title, T1.billable';
              	;
              	$db->setQuery($query);
              	$res = $db->loadObjectList();

              	foreach($res as $item) {
              		// PRICE
              		$total += ($item->billable == 0) ? $item->priceFixed : $item->price;
              	}

              endif;

              // acrescenta taxa e multa
              $totalPay = $total + (float)($inv->tax + $inv->assessment);
              // hospedagem
              $totalPay += $hostPrice;
              // desconto
              $totalPay -= ($inv->discount);
              // total geral
              $totalPay = strval(number_format($totalPay, 2));

              if($totalPay != '0.00') :

                $sendMail = 1;
                $urlBoleto = $inv->url_boleto;
                if($inv->create_boleto == 1) :

                  $query = '
                    SELECT
                      '. $db->quoteName('T1.id') .',
                      '. $db->quoteName('T1.zip_code') .' cep,
                      '. $db->quoteName('T1.address') .' logradouro,
                      '. $db->quoteName('T1.address_number') .' numero,
                      '. $db->quoteName('T1.address_info') .' complemento,
                      '. $db->quoteName('T1.address_district') .' bairro,
                      '. $db->quoteName('T1.address_city') .' cidade,
                      '. $db->quoteName('T1.address_state') .' uf
                  	FROM
                  		'. $db->quoteName('#__envolute_addresses') .' T1
                  		JOIN '. $db->quoteName('#__envolute_rel_clients_addresses').' T2
                  		ON T2.address_id = T1.id
                  	WHERE
                  		T1.main = 1 AND T2.client_id = '.$inv->client_id.'
                    LIMIT 1'
                  ;
                	$db->setQuery($query);
                	$loc = $db->loadObject();

                  $cep = !empty($loc->cep) ? $loc->cep : '58000000';
                  $logradouro = !empty($loc->logradouro) ? $loc->logradouro : 'Envolute Avenue';
                  $numero = !empty($loc->numero) ? $loc->numero : '100';
                  $bairro = !empty($loc->bairro) ? $loc->bairro : 'Praia';
                  $cidade = !empty($loc->cidade) ? $loc->cidade : 'João Pessoa';
                  $uf = !empty($loc->uf) ? $loc->uf : 'PB';

                  // CRIA NOVA COBRANÇA F2B -------------------------------------------------
                  require("../_f2b/WSBilling.php");
                  // Inicia a classe WSBilling
                  $WSBilling = new WSBilling();

                  // Cria o cabeçalho SOAP
                  $xmlObj = $WSBilling->add_node("","soap-env:Envelope");
                  $WSBilling->add_attributes($xmlObj, array("xmlns:soap-env" => "http://schemas.xmlsoap.org/soap/envelope/") );
                  $xmlObj = $WSBilling->add_node($xmlObj,"soap-env:Body");
                  // Cria  o elemento m:F2bCobranca
                  $xmlObjF2bCobranca = $WSBilling->add_node($xmlObj,"m:F2bCobranca");
                  $WSBilling->add_attributes($xmlObjF2bCobranca, array("xmlns:m" => "http://www.f2b.com.br/soap/wsbilling.xsd") );
                  // Cria o elemento mensagem
                  $xmlObj = $WSBilling->add_node($xmlObjF2bCobranca,"mensagem");
                  $WSBilling->add_attributes($xmlObj, array("data" => date("Y-m-d"),
                                                            "numero" => $id,
                                                            "tipo_ws" => "WebService"));
                  // Cria o elemento sacador
                  $xmlObj = $WSBilling->add_node($xmlObjF2bCobranca,"sacador");
                  $WSBilling->add_attributes($xmlObj, array("conta" => "9023010621330163"));
                  $WSBilling->add_content($xmlObj,"Ivo Monteiro Bezerra Junior");
                  // Cria o elemento cobranca
                  $xmlObjCobranca = $WSBilling->add_node($xmlObjF2bCobranca,"cobranca");
                  $WSBilling->add_attributes($xmlObjCobranca,
                    array(
                      "valor" => $totalPay,
                      "tipo_cobranca" => "B",
                      "num_document" => 'ENV-'.$id
                    )
                  );
                  // Cria os elementos demonstrativos (Até 10 linhas com 80 caracteres cada)
                  $desc = !empty($inv->description) ? $inv->description : JText::_('TEXT_DESCRIPTION');
                  $xmlObj = $WSBilling->add_node($xmlObjCobranca,"demonstrativo");
                  $WSBilling->add_content($xmlObj, utf8_decode($desc));
                  if(!empty($inv->description1)) :
                    $xmlObj = $WSBilling->add_node($xmlObjCobranca,"demonstrativo");
                    $WSBilling->add_content($xmlObj,utf8_decode($inv->description1));
                  endif;
                  if(!empty($inv->description2)) :
                    $xmlObj = $WSBilling->add_node($xmlObjCobranca,"demonstrativo");
                    $WSBilling->add_content($xmlObj,utf8_decode($inv->description2));
                  endif;
                  if(!empty($inv->description3)) :
                    $xmlObj = $WSBilling->add_node($xmlObjCobranca,"demonstrativo");
                    $WSBilling->add_content($xmlObj,utf8_decode($inv->description3));
                  endif;
                  if(!empty($inv->description4)) :
                    $xmlObj = $WSBilling->add_node($xmlObjCobranca,"demonstrativo");
                    $WSBilling->add_content($xmlObj,utf8_decode($inv->description4));
                  endif;
                  //Cria o elemento agendamento
                  $xmlObj = $WSBilling->add_node($xmlObjF2bCobranca,"agendamento");
                  $WSBilling->add_attributes($xmlObj,
                    array(
                      "vencimento" => $inv->due_date,
                      "sem_vencimento" => "s"
                    )
                  );
                  $WSBilling->add_content($xmlObj,utf8_decode(JText::_('TEXT_CASH_PAYMENT')));
                  // Cria o elemento sacado
                  $xmlObjSacado = $WSBilling->add_node($xmlObjF2bCobranca,"sacado");
                  $WSBilling->add_attributes($xmlObjSacado,
                    array(
                      "grupo" => "Clientes",
                      "codigo" => "ENV-".$inv->client_id,
                      "envio" => "n"
                    )
                  );
                  // Cria o elemento nome
                  $xmlObj = $WSBilling->add_node($xmlObjSacado,"nome");
                  $WSBilling->add_content($xmlObj,baseHelper::removeAcentos($inv->client));
                  // Cria o elemento email
                  $xmlObj = $WSBilling->add_node($xmlObjSacado,"email");
                  $WSBilling->add_content($xmlObj,$inv->email);
                  if(!empty($inv->email_payment)) :
                    // Cria o elemento email
                    $xmlObj = $WSBilling->add_node($xmlObjSacado,"email");
                    $WSBilling->add_content($xmlObj,$inv->email_payment);
                  endif;
                  // Cria o elemento documento (cpf/cnpj)
                  if(!empty($inv->doc_number)) :
                    $doc = ($inv->clientType == 1) ? "cnpj" : "cpf";
                    $xmlObj = $WSBilling->add_node($xmlObjSacado, $doc);
                    $WSBilling->add_content($xmlObj, $inv->doc_number);
                  endif;
                  // Cria o elemento endereco
                  if(!empty($logradouro) && !empty($numero) && !empty($cidade) && !empty($uf) && !empty($cep)) :
                    $xmlObj = $WSBilling->add_node($xmlObjSacado,"endereco");
                    $WSBilling->add_attributes($xmlObj,
                      array(
                        "logradouro" => utf8_decode($logradouro),
                        "numero" => $numero,
                        "complemento" => utf8_decode($complemento),
                        "bairro" => utf8_decode($bairro),
                        "cidade" => utf8_decode($cidade),
                        "estado" => $uf,
                        "cep" => str_replace('-', '', $cep)
                      )
                    );
                  endif;
                  // envia dados
                  $WSBilling->send($WSBilling->getXML());
                  // retorno
                  $resposta = $WSBilling->resposta;
                  $retornoF2B = '';
                  if(strlen($resposta) > 0) {
                    // Reinicia a classe WSBlling, agora com uma string XML
                    $WSBilling = new WSBilling($resposta);
                    // LOG
                    $log = $WSBilling->pegaLog();
                    if($log["texto"] == "OK") {
                      $cobranca = $WSBilling->pegaCobranca();
                      $urlBoleto = $cobranca[0]["url"];
                      $query = 'UPDATE '. $db->quoteName($cfg['mainTable']) .' SET '. $db->quoteName('price') .' = '.$total.', '. $db->quoteName('sent') .' = 1, '. $db->quoteName('sent_date') .' = NOW(), '. $db->quoteName('url_boleto') .' = '.$db->quote($urlBoleto).' WHERE '. $db->quoteName('id') .' = '.$id;

                      try {

                        $db->setQuery($query);
                        $db->execute();

                        $data[] = array(
                          'status' => 1,
                          'id'	=> $id
                        );

                      } catch (RuntimeException $e) {

                        $sendMail = 0;
                        $data[] = array(
                          'status'=> 0,
                          'msg'	=> $e->getMessage()
                        );

                      }

                    } else {
                      $msg = '';
                      foreach($log as $key => $value){
                  			$erro .= $value."\n";
                  		}
                      $sendMail = 0;
                      $data[] = array(
                        'status' => 0,
                        'id'	=> $id,
                        'msg' => $erro
                      );
                    }
                  } else {
                    $sendMail = 0;
                    $data[] = array(
                      'status' => 0,
                      'id'	=> $id,
                      'msg' => '[F2B] - '.JText::_('TEXT_NO_RESPONSE')
                    );
                  }
                  // CRIA NOVA COBRANÇA F2B -------------------------------------------------

                else :

                  $query = 'UPDATE '. $db->quoteName($cfg['mainTable']) .' SET '. $db->quoteName('price') .' = '.$db->quote($total).', '. $db->quoteName('sent') .' = 1, '. $db->quoteName('sent_date') .' = NOW() WHERE '. $db->quoteName('id') .' = '.$id;

                  try {

                    $db->setQuery($query);
                    $db->execute();

                    $data[] = array(
                      'status' => 1,
                      'id'	=> $id
                    );

                  } catch (RuntimeException $e) {

                    $sendMail = 0;
                    $data[] = array(
                      'status'=> 0,
                      'msg'	=> $e->getMessage()
                    );

                  }

                endif;

                if($sendMail == 1) :
                  // Envia e-mail de cobrança
                  $mailSubject = $inv->email_subject;
                  $msgBoleto = !empty($urlBoleto) ? JText::sprintf('MSG_EMAIL_CONTENT_BOLETO', $urlBoleto) : '';
                  $msgBankAccount = !empty($inv->bankAccount_info) ? JText::sprintf('MSG_EMAIL_CONTENT_BANK_INFO', $inv->bankAccount_info) : '';
                  $msgIntro = JText::sprintf('MSG_EMAIL_SEND_INTRO', $inv->id, baseHelper::dateFormat(date('Y-m-d')));
                  $msgContent = JText::sprintf('MSG_EMAIL_CONTENT', baseHelper::nameFormat($inv->client), $msgIntro, $inv->description, $inv->email_content, baseHelper::dateFormat($inv->due_date), baseHelper::priceFormat($totalPay), htmlentities(urlencode(base64_encode($inv->id))), $msgBoleto, $msgBankAccount);
                  $msgFooter = JText::sprintf('MSG_EMAIL_FOOTER', $domain);
                  $mailBody = baseHelper::mailTemplateDefault($msgContent, 'Fat. ID: #'.$id, $msgFooter, 'logo-news-team.png');
                  $mailSend = JFactory::getMailer();
                  // Anexos -> Nota fiscal e/ou boleto
                  if($cfg['hasUpload']) :
              			JLoader::register('uploader', JPATH_BASE.'/templates/base/source/helpers/upload.php');
              			$files[$inv->id] = uploader::getFiles($cfg['fileTable'], $inv->id);
                    for($i = 0; $i < count($files[$inv->id]); $i++) {
              				if(!empty($files[$inv->id][$i]->filename) && $i < 2) :
              					$filePath = JPATH_BASE.DS.'images/uploads/'.$APPNAME.DS.$files[$inv->id][$i]->filename;
                        $mailSend->addAttachment($filePath);
              				endif;
              			}
              		endif;
                  $mailSend->sendMail($senderMail, $senderName, $clientEmail, $mailSubject, $mailBody, true);
                endif;

              else :

                $data[] = array(
                  'status' => 0,
                  'id'	=> $id,
                  'msg' => JText::_('MSG_ZERO_TOTAL')
                );

              endif;

            endif;

          else :

            $data[] = array(
              'status' => 0,
              'id'	=> $id,
              'msg' => JText::_('MSG_INVOICE_NO_EXIST')
            );

          endif;

        // PAYMENT
        elseif($task == 'pay' && !empty($param) && $param != '0000-00-00') :

          $query = 'UPDATE '. $db->quoteName($cfg['mainTable']) .' SET '. $db->quoteName('sent') .' = 1, '. $db->quoteName('sent_date') .' = NOW(), '. $db->quoteName('paid') .' = 1, '. $db->quoteName('paid_date') .' = '.$db->quote($param).' WHERE '. $db->quoteName('id') .' = '.$id;

          try {
            $db->setQuery($query);
            $db->execute();

            $query = '
              SELECT
            		'. $db->quoteName('T1.id') .',
            		'. $db->quoteName('T3.name') .' client,
            		'. $db->quoteName('T3.email') .',
            		'. $db->quoteName('T3.email_payment') .',
                '. $db->quoteName('T1.price') .',
            		'. $db->quoteName('T1.discount') .',
            		'. $db->quoteName('T1.tax') .',
            		'. $db->quoteName('T1.assessment') .'
            	FROM
            		'. $db->quoteName($cfg['mainTable']) .' T1
            		JOIN '. $db->quoteName('#__envolute_projects').' T2
            		ON T2.id = T1.project_id
            		JOIN '. $db->quoteName('#__envolute_clients').' T3
            		ON T3.id = T2.client_id
            	WHERE
            		T1.id = '.$id;
            ;
          	$db->setQuery($query);
          	$inv = $db->loadObject();

            // acrescenta taxa e multa
            $totalPay = $inv->price + (float)($inv->tax + $inv->assessment);
            // desconto
            $totalPay -= ($inv->discount);
            // total geral
            $totalPay = strval(number_format($totalPay, 2));

            if($state == 1) :
              // EMAIL SENDER CONFIG
              $config = JFactory::getConfig();
              $domain = baseHelper::getDomain();
              $senderName = $config->get('fromname');
              $senderMail = 'adm@envolute.com';
              $clientEmail[] = !empty($inv->email_payment) ? $inv->email_payment : $inv->email;
              // Envia e-mail de cobrança
              $mailSubject = JText::_('TEXT_CONFIRM_PAYMENT');
              $msgContent = JText::sprintf('MSG_EMAIL_CONTENT_CONFIRM', baseHelper::nameFormat($inv->client), $inv->id, baseHelper::priceFormat($totalPay), htmlentities(urlencode(base64_encode($inv->id))));
              $msgFooter = JText::sprintf('MSG_EMAIL_FOOTER', $domain);
              $mailBody = baseHelper::mailTemplateDefault($msgContent, JText::_('TEXT_CONFIRM_PAYMENT'), $msgFooter, 'logo-news-team.png');
              $mailSend = JFactory::getMailer();
              $mailSend->sendMail($senderMail, $senderName, $clientEmail, $mailSubject, $mailBody, true);
            endif;

            $data[] = array(
              'status' => 1,
              'id' => $id,
              'msg'	=> ''
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
        if($request['project_id'] != 0 && $request['due_date'] != '0000-00-00') :

          // Prepare the insert query
    			$query  = '
          INSERT INTO '. $db->quoteName($cfg['mainTable']) .'('.
            $db->quoteName('project_id') .','.
            $db->quoteName('type') .','.
            $db->quoteName('due_date') .','.
            $db->quoteName('month') .','.
            $db->quoteName('year') .','.
            $db->quoteName('price') .','.
            $db->quoteName('create_boleto') .','.
            $db->quoteName('description') .','.
            $db->quoteName('description1') .','.
            $db->quoteName('description2') .','.
            $db->quoteName('description3') .','.
            $db->quoteName('description4') .','.
            $db->quoteName('hosting') .','.
            $db->quoteName('discount') .','.
            $db->quoteName('discount_note') .','.
            $db->quoteName('tax') .','.
            $db->quoteName('tax_note') .','.
            $db->quoteName('assessment') .','.
            $db->quoteName('assessment_note') .','.
            $db->quoteName('email_subject') .','.
            $db->quoteName('email_content') .','.
            $db->quoteName('email_subject_resend') .','.
            $db->quoteName('email_content_resend') .','.
            $db->quoteName('url_boleto') .','.
            $db->quoteName('bankAccount_info') .','.
            $db->quoteName('paid') .','.
            $db->quoteName('paid_date') .','.
            $db->quoteName('note') .','.
    				$db->quoteName('state') .','.
    				$db->quoteName('created_by')
    			.') VALUES ('.
            $request['project_id'] .','.
            $request['type'] .','.
            $db->quote($request['due_date']) .','.
    				$request['month'] .','.
    				$request['year'] .','.
            $db->quote($request['price']) .','.
    				$request['create_boleto'] .','.
            $db->quote($request['description']) .','.
            $db->quote($request['description1']) .','.
            $db->quote($request['description2']) .','.
            $db->quote($request['description3']) .','.
            $db->quote($request['description4']) .','.
    				$request['hosting'] .','.
            $db->quote($request['discount']) .','.
            $db->quote($request['discount_note']) .','.
            $db->quote($request['tax']) .','.
            $db->quote($request['tax_note']) .','.
            $db->quote($request['assessment']) .','.
            $db->quote($request['assessment_note']) .','.
            $db->quote($request['email_subject']) .','.
            $db->quote($request['email_content']) .','.
            $db->quote($request['email_subject_resend']) .','.
            $db->quote($request['email_content_resend']) .','.
            $db->quote($request['url_boleto']) .','.
            $db->quote($request['bankAccount_info']) .','.
    				$paid .','.
            $db->quote($request['paid_date']) .','.
            $db->quote($request['note']) .','.
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
              $query = '
              SELECT
                '. $db->quoteName('T2.name') .' project,
                '. $db->quoteName('T3.name') .' client,
                '. $db->quoteName('T1.due_date') .',
                '. $db->quoteName('T1.month') .',
                '. $db->quoteName('T1.year') .'
              FROM
                '. $db->quoteName($cfg['mainTable']).' T1
            		JOIN '. $db->quoteName('#__envolute_projects').' T2
            		ON T2.id = T1.project_id
            		JOIN '. $db->quoteName('#__envolute_clients').' T3
            		ON T3.id = T2.client_id
              WHERE T1.id='.$id;
              $db->setQuery($query);
              $elems = $db->loadObject();
              $elemLabel = baseHelper::dateFormat($elems->due_date, 'd.m').' - '.baseHelper::nameFormat($elems->project).' ['.baseHelper::nameFormat($elems->client).']';
            endif;

    				$data[] = array(
    					'status'=> 1,
    					'msg'	=> JText::_('MSG_SAVED'),
              'regID'	=> $id,
              'uploadError' => $fileMsg,
    					'parentField'	=> $element,
    					'parentFieldVal'	=> $elemVal,
    					'parentFieldLabel'	=> $elemLabel
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
