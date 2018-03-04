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
  require(__DIR__.'/../../libraries/envolute/_init.joomla.php');
	$app = JFactory::getApplication('site');

  defined('_JEXEC') or die;
  $ajaxRequest = true;
  require('config.php');
  // IMPORTANTE: Carrega o arquivo 'helper' do template
  JLoader::register('baseHelper', JPATH_CORE.DS.'helpers/base.php');

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
    // OBS: para arquivos externos com o carregamento do framework '_init.joomla.php' (geralmente em 'ajax')
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

    // upload actions
    $fileMsg  = '';
    if($cfg['hasUpload']) :
      $fname    = $input->get('fname', '', 'string');
      $fileId   = $input->get('fileId', 0, 'int');
      // load 'uploader' class
      JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
    endif;

  	// fields 'Form' requests
    $request                 = array();
    // default
    $request['relationId']   = $input->get('relationId', 0, 'int');
  	$request['state']        = $input->get('state', 1, 'int');
    // app
  	$request['title']        = $input->get('title', '', 'string');
  	$request['description']  = $input->get('description', '', 'raw');
  	$request['coverType']    = $input->get('coverType', '', 'int');
  	$request['videoEmbed']   = $input->get('videoEmbed', '', 'raw');
  	$request['date']         = $input->get('date', '', 'string');
  	$request['month']        = $input->get('month', 0, 'int');
  	$request['year']         = $input->get('year', 0, 'int');
  	$request['contentType']  = $input->get('contentType', 0, 'int');
  	$request['url']          = $input->get('url', '', 'string');
  	$request['target']       = $input->get('target', '', 'string');
  	$request['content']      = $input->get('content', '', 'raw');
  	$request['tag']          = $input->get('tag', '', 'string');
    $request['ordering']     = $input->get('ordering', 0, 'int');
    $request['cordering']    = $input->get('cordering', 0, 'int');
  	$request['urlList']      = $input->get('urlList', '', 'string');
  	$request['urlContent']   = $input->get('urlContent', '', 'string');
    // app config
  	$request['element_class']           = $input->get('element_class', '', 'string');
  	$request['element_dateFormat']      = $input->get('element_dateFormat', '', 'string');
  	$request['element_layout']          = $input->get('element_layout', '', 'raw');
  	$request['element_imageWidth']      = $input->get('element_imageWidth', '', 'string');
  	$request['element_imageHeight']     = $input->get('element_imageHeight', '', 'string');
  	$request['element_imageClass']      = $input->get('element_imageClass', '', 'string');
  	$request['element_downloadLabel']   = $input->get('element_downloadLabel', '', 'string');
  	$request['element_downloadClass']   = $input->get('element_downloadClass', '', 'string');
  	$request['element_contentModal']    = $input->get('element_contentModal', '', 'string');
  	$request['element_modalHeader']     = $input->get('element_modalHeader', '', 'string');
  	$request['element_modalSize']       = $input->get('element_modalSize', '', 'string');
  	$request['content_galleryGrid']     = $input->get('content_galleryGrid', '', 'string');
  	$request['content_galImageWidth']   = $input->get('content_galImageWidth', '', 'string');
  	$request['content_galImageHeight']  = $input->get('content_galImageHeight', '', 'string');
  	$request['content_galImageClass']   = $input->get('content_galImageClass', '', 'string');
  	$request['content_galleryCaption']  = $input->get('content_galleryCaption', '', 'string');
  	$request['content_layout']          = $input->get('content_layout', '', 'raw');
  	$request['content_imageWidth']      = $input->get('content_imageWidth', '', 'string');
  	$request['content_imageHeight']     = $input->get('content_imageHeight', '', 'string');
  	$request['content_imageClass']      = $input->get('content_imageClass', '', 'string');

    // CUSTOM -> Set Order
    function setOrder($ID, $tag, $ctag, $ord, $cord, $cfg) {
      if(!empty($ID) && $ID != 0) :
        // database connect
      	$db = JFactory::getDbo();
        // last item
        $query = 'SELECT MAX('.$db->quoteName('ordering').') FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '.$db->quoteName('id').' != '.$ID.' AND '. $db->quoteName('tag') .' = '. $db->quote($tag);
    		$db->setQuery($query);
        $max = $db->loadResult();
        if(!empty($ord) && $ord != 0 && ($ord != $cord || $tag != $ctag)) :
          // this item position
          $query = 'SELECT COUNT(*) FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '.$db->quoteName('ordering').' = '.$ord.' AND  '. $db->quoteName('id') .' != '. $ID .' AND '. $db->quoteName('tag') .' = '. $db->quote($tag);
      		$db->setQuery($query);
          $exist = $db->loadResult();
          if($ord <= $max) :
            // define a posição quando a ordem é definida no insert, altera todos a partir da nova ordem
            if($cord == 0 || $tag != $ctag) :
              $query = '
              UPDATE '.$db->quoteName($cfg['mainTable']).' SET '.
              $db->quoteName('ordering').' = ('.$db->quoteName('ordering').' + 1)
              WHERE '.
              $db->quoteName('ordering') .' >= ' .$ord.' AND '. // ordem ocupada
              $db->quoteName('id') .' != '. $ID.' AND '.
              $db->quoteName('tag') .' = '. $db->quote($tag);
          		$db->setQuery($query);
              $db->execute();
              return true;
            // se o item subir na ordem, altera todos os que estão entre a nova ordem e a anterior
            elseif($exist && $ord < $cord) :
              $query = '
              UPDATE '.$db->quoteName($cfg['mainTable']).' SET '.
              $db->quoteName('ordering').' = ('.$db->quoteName('ordering').' + 1)
              WHERE '.
              $db->quoteName('ordering') .' >= ' .$ord.' AND '. // ordem ocupada
              $db->quoteName('ordering') .' < ' .$cord.' AND '. // ordem anterior -> abaixo
              $db->quoteName('id') .' != '. $ID.' AND '.
              $db->quoteName('tag') .' = '. $db->quote($tag);
          		$db->setQuery($query);
              $db->execute();
              return true;
            // se o item descer na ordem, altera todos os que estão entre a nova ordem e a anterior
            elseif($exist && $ord > $cord) :
              $query = '
              UPDATE '.$db->quoteName($cfg['mainTable']).' SET '.
                $db->quoteName('ordering').' = ('.$db->quoteName('ordering').' - 1)
              WHERE '.
                $db->quoteName('ordering') .' <= ' .$ord.' AND '. // ordem ocupada
                $db->quoteName('ordering') .' > ' .$cord.' AND '.  // ordem anterior -> acima
                $db->quoteName('id') .' != '. $ID.' AND '.
                $db->quoteName('tag') .' = '. $db->quote($tag);
          		$db->setQuery($query);
              $db->execute();
              return true;
            else :
              return false;
            endif;
          // se o item for maior do que o máx, seta o máximo e define a ordem
          else :
            $nord = ($cord >= $max) ? $max + 1 : $max; // caso exista outro item na mesma posição
            $query = '
            UPDATE '.$db->quoteName($cfg['mainTable']).' SET '.
              $db->quoteName('ordering').' = '.$nord.'
            WHERE '.
              $db->quoteName('id') .' = '. $ID;
        		$db->setQuery($query);
            $db->execute();
            if($cord < $max) setOrder($ID, $tag, $ctag, $nord, $cord, $cfg);
            return true;
          endif;
        elseif(empty($ord) || $ord == 0) :
          $query = 'UPDATE '.$db->quoteName($cfg['mainTable']).' SET '.$db->quoteName('ordering').' = ('.$max.' + 1) WHERE '. $db->quoteName('id') .' = '. $ID;
      		$db->setQuery($query);
          $db->execute();
          if($ord != $cord && $cord <= $max) :// this item position
            // verifica se existe outro item na mesma posição
            $query = 'SELECT COUNT(*) FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '.$db->quoteName('ordering').' = '.$cord.' AND '. $db->quoteName('id') .' != '. $ID .' AND '. $db->quoteName('tag') .' = '. $db->quote($tag);
            $db->setQuery($query);
            $cexist = $db->loadResult();
            if(!$cexist) setOrder($ID, $tag, $tag, $max + 1, $cord, $cfg);
          endif;
          return true;
        else :
          return false;
        endif;
      else :
        return false;
      endif;
    }
    // CUSTOM -> Re-Order after delete item
    function reOrder($ID, $tag, $ord, $cfg) {
      if(!empty($ID) && $ID != 0) :
        // database connect
      	$db = JFactory::getDbo();
        // last item
        $query = 'SELECT MAX('.$db->quoteName('ordering').') FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '.$db->quoteName('id').' != '.$ID.' AND '. $db->quoteName('tag') .' = '. $db->quote($tag);
    		$db->setQuery($query);
        $max = $db->loadResult();
        if(!empty($ord) && $ord != 0 && $ord < $max) :
          // altera todos os que estão abaixo
          $query = '
          UPDATE '.$db->quoteName($cfg['mainTable']).' SET '.
            $db->quoteName('ordering').' = ('.$db->quoteName('ordering').' - 1)
          WHERE '.
            $db->quoteName('ordering') .' > ' .$ord.' AND '. // ordem ocupada
            $db->quoteName('tag') .' = '. $db->quote($tag);
      		$db->setQuery($query);
          $db->execute();
          return true;
        else :
          return false;
        endif;
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
        // CUSTOM -> tag filter
        // mostra apenas os itens com a mesma 'tag' no formulário
        $fltTagged = $_SESSION[$RTAG.'ListAllTags'] ? '' : ' AND tag = '.$db->quote($APPTAG);
        // get previous ID
        $query = 'SELECT MAX(id) FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('id') .' < '. $id.$fltTagged;
    		$db->setQuery($query);
    		$prev = $db->loadResult();
        $prev = !empty($prev) ? $prev : 0;
        // get next ID
        $query = 'SELECT MIN(id) FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('id') .' > '. $id.$fltTagged;
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
      				'title'	      => $item->title,
      				'description'	=> $item->description,
              'coverType'	  => $item->coverType,
              'videoEmbed'	=> $item->videoEmbed,
      				'date'	      => $item->date,
      				'month'	      => $item->month,
      				'year'	      => $item->year,
      				'contentType' => $item->contentType,
      				'url'	        => $item->url,
      				'target'	    => $item->target,
      				'content'	    => $item->content,
      				'tag'	        => $item->tag,
      				'ordering'    => $item->ordering,
      				'urlList'     => $item->urlList,
      				'urlContent'  => $item->urlContent,
      				'element_class'         => $item->element_class,
      				'element_dateFormat'    => $item->element_dateFormat,
      				'element_layout'        => $item->element_layout,
      				'element_imageWidth'    => $item->element_imageWidth,
      				'element_imageHeight'   => $item->element_imageHeight,
      				'element_imageClass'    => $item->element_imageClass,
      				'element_downloadLabel' => $item->element_downloadLabel,
      				'element_downloadClass' => $item->element_downloadClass,
      				'element_contentModal'  => $item->element_contentModal,
      				'element_modalHeader'   => $item->element_modalHeader,
      				'element_modalSize'     => $item->element_modalSize,
      				'content_galleryGrid'   => $item->content_galleryGrid,
      				'content_galImageWidth' => $item->content_galImageWidth,
      				'content_galImageHeight'=> $item->content_galImageHeight,
      				'content_galImageClass' => $item->content_galImageClass,
      				'content_galleryCaption'=> $item->content_galleryCaption,
      				'content_layout'        => $item->content_layout,
      				'content_imageWidth'    => $item->content_imageWidth,
      				'content_imageHeight'   => $item->content_imageHeight,
      				'content_imageClass'    => $item->content_imageClass,
              'files'                 => $listFiles
      			);
      		}

  			// UPDATE
  			elseif($task == 'save' && $id) :

  				$query  = 'UPDATE '.$db->quoteName($cfg['mainTable']).' SET ';
  				$query .=
  					$db->quoteName('title') .'='. $db->quote($request['title']) .','.
            $db->quoteName('description') .'='. $db->quote($request['description']) .','.
  					$db->quoteName('coverType') .'='. $request['coverType'] .','.
            $db->quoteName('videoEmbed') .'='. $db->quote($request['videoEmbed']) .','.
            $db->quoteName('date') .'='. $db->quote($request['date']) .','.
            $db->quoteName('month') .'='. $request['month'] .','.
  					$db->quoteName('year') .'='. $request['year'] .','.
  					$db->quoteName('contentType') .'='. $request['contentType'] .','.
            $db->quoteName('url') .'='. $db->quote($request['url']) .','.
            $db->quoteName('target') .'='. $db->quote($request['target']) .','.
            $db->quoteName('content') .'='. $db->quote($request['content']) .','.
            $db->quoteName('tag') .'='. $db->quote($request['tag']) .','.
  					$db->quoteName('ordering') .'='. $request['ordering'] .','.
  					$db->quoteName('urlList') .'='. $db->quote($request['urlList']) .','.
  					$db->quoteName('urlContent') .'='. $db->quote($request['urlContent']) .','.
  					$db->quoteName('element_class') .'='. $db->quote($request['element_class']) .','.
  					$db->quoteName('element_dateFormat') .'='. $db->quote($request['element_dateFormat']) .','.
  					$db->quoteName('element_layout') .'='. $db->quote($request['element_layout']) .','.
  					$db->quoteName('element_imageWidth') .'='. $db->quote($request['element_imageWidth']) .','.
  					$db->quoteName('element_imageHeight') .'='. $db->quote($request['element_imageHeight']) .','.
  					$db->quoteName('element_imageClass') .'='. $db->quote($request['element_imageClass']) .','.
  					$db->quoteName('element_downloadLabel') .'='. $db->quote($request['element_downloadLabel']) .','.
  					$db->quoteName('element_downloadClass') .'='. $db->quote($request['element_downloadClass']) .','.
  					$db->quoteName('element_contentModal') .'='. $db->quote($request['element_contentModal']) .','.
  					$db->quoteName('element_modalHeader') .'='. $db->quote($request['element_modalHeader']) .','.
  					$db->quoteName('element_modalSize') .'='. $db->quote($request['element_modalSize']) .','.
  					$db->quoteName('content_galleryGrid') .'='. $db->quote($request['content_galleryGrid']) .','.
  					$db->quoteName('content_galImageWidth') .'='. $db->quote($request['content_galImageWidth']) .','.
  					$db->quoteName('content_galImageHeight') .'='. $db->quote($request['content_galImageHeight']) .','.
  					$db->quoteName('content_galImageClass') .'='. $db->quote($request['content_galImageClass']) .','.
  					$db->quoteName('content_galleryCaption') .'='. $db->quote($request['content_galleryCaption']) .','.
  					$db->quoteName('content_layout') .'='. $db->quote($request['content_layout']) .','.
  					$db->quoteName('content_imageWidth') .'='. $db->quote($request['content_imageWidth']) .','.
  					$db->quoteName('content_imageHeight') .'='. $db->quote($request['content_imageHeight']) .','.
  					$db->quoteName('content_imageClass') .'='. $db->quote($request['content_imageClass']) .','.
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
              $query = 'SELECT '. (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $_SESSION[$RTAG.'TableField']) ? $db->quoteName($_SESSION[$RTAG.'TableField']) : $_SESSION[$RTAG.'TableField']) .' FROM '. $db->quoteName($cfg['mainTable']).' WHERE id='.$id.' AND state = 1';
              $db->setQuery($query);
              $elemLabel = $db->loadResult();
            endif;

            // SET ORDER
            setOrder($id, $request['tag'], $request['ctag'], $request['ordering'], $request['cordering'], $cfg);
            if($request['tag'] != $request['ctag']) :
              // RE-ORDER
              reOrder($id, $request['ctag'], $request['cordering'], $cfg);
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

          $Ids = explode(',', $ids);

          // RE-ORDER ITEMS
          for($i = 0; $i < count($Ids); $i++) {
            // GET PARENT INFO
            $queryOrder = 'SELECT tag, ordering FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('id') .' = '.$Ids[$i];
            $db->setQuery($queryOrder);
            $obj = $db->loadObject();
            // RE-ORDER
            reOrder($Ids[$i], $obj->tag, $obj->ordering, $cfg);
          }

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
                // [RELACIONAMENTO] -> remove os registros relacionados aos [relacionados]
                // $query = 'DELETE FROM '. $db->quoteName('??') .' WHERE '. $db->quoteName('??') .' IN ('.$ids.')';
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
          $query = 'UPDATE '. $db->quoteName($cfg['mainTable']) .' SET '. $db->quoteName('state') .' = '.$stateVal.', '. $db->quoteName('alter_date') .' = NOW() WHERE '. $db->quoteName('id') .' IN ('.$ids.')';

          try {
            $db->setQuery($query);
            $db->execute();

            // UPDATE FIELD
            // executa apenas com valores individuais
            $element = $elemVal = $elemLabel = '';
            if(!empty($_SESSION[$RTAG.'FieldUpdated']) && !empty($_SESSION[$RTAG.'TableField'])) :
              $element = $_SESSION[$RTAG.'FieldUpdated'];
              $elemVal = $ids;
              $query = 'SELECT '. (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $_SESSION[$RTAG.'TableField']) ? $db->quoteName($_SESSION[$RTAG.'TableField']) : $_SESSION[$RTAG.'TableField']) .' FROM '. $db->quoteName($cfg['mainTable']).' WHERE '. $db->quoteName('id') .' = '.$ids;
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
        if(!empty($request['title'])) :

          // Prepare the insert query
    			$query  = '
          INSERT INTO '. $db->quoteName($cfg['mainTable']) .'('.
    				$db->quoteName('title') .','.
    				$db->quoteName('description') .','.
    				$db->quoteName('coverType') .','.
    				$db->quoteName('videoEmbed') .','.
    				$db->quoteName('date') .','.
    				$db->quoteName('month') .','.
    				$db->quoteName('year') .','.
    				$db->quoteName('contentType') .','.
    				$db->quoteName('url') .','.
    				$db->quoteName('target') .','.
    				$db->quoteName('content') .','.
    				$db->quoteName('tag') .','.
    				$db->quoteName('ordering') .','.
    				$db->quoteName('urlList') .','.
    				$db->quoteName('urlContent') .','.
    				$db->quoteName('element_class') .','.
    				$db->quoteName('element_dateFormat') .','.
    				$db->quoteName('element_layout') .','.
    				$db->quoteName('element_imageWidth') .','.
    				$db->quoteName('element_imageHeight') .','.
    				$db->quoteName('element_imageClass') .','.
    				$db->quoteName('element_downloadLabel') .','.
    				$db->quoteName('element_downloadClass') .','.
    				$db->quoteName('element_contentModal') .','.
    				$db->quoteName('element_modalHeader') .','.
    				$db->quoteName('element_modalSize') .','.
    				$db->quoteName('content_galleryGrid') .','.
    				$db->quoteName('content_galImageWidth') .','.
    				$db->quoteName('content_galImageHeight') .','.
    				$db->quoteName('content_galImageClass') .','.
    				$db->quoteName('content_galleryCaption') .','.
    				$db->quoteName('content_layout') .','.
    				$db->quoteName('content_imageWidth') .','.
    				$db->quoteName('content_imageHeight') .','.
    				$db->quoteName('content_imageClass') .','.
    				$db->quoteName('state') .','.
    				$db->quoteName('created_by')
    			.') VALUES ('.
            $db->quote($request['title']) .','.
            $db->quote($request['description']) .','.
    				$request['coverType'] .','.
            $db->quote($request['videoEmbed']) .','.
            $db->quote($request['date']) .','.
    				$request['month'] .','.
    				$request['year'] .','.
    				$request['contentType'] .','.
            $db->quote($request['url']) .','.
            $db->quote($request['target']) .','.
            $db->quote($request['content']) .','.
            $db->quote($request['tag']) .','.
    				$request['ordering'] .','.
            $db->quote($request['urlList']) .','.
            $db->quote($request['urlContent']) .','.
            $db->quote($request['element_class']) .','.
            $db->quote($request['element_dateFormat']) .','.
            $db->quote($request['element_layout']) .','.
            $db->quote($request['element_imageWidth']) .','.
            $db->quote($request['element_imageHeight']) .','.
            $db->quote($request['element_imageClass']) .','.
            $db->quote($request['element_downloadLabel']) .','.
            $db->quote($request['element_downloadClass']) .','.
            $db->quote($request['element_contentModal']) .','.
            $db->quote($request['element_modalHeader']) .','.
            $db->quote($request['element_modalSize']) .','.
            $db->quote($request['content_galleryGrid']) .','.
            $db->quote($request['content_galImageWidth']) .','.
            $db->quote($request['content_galImageHeight']) .','.
            $db->quote($request['content_galImageClass']) .','.
            $db->quote($request['content_galleryCaption']) .','.
            $db->quote($request['content_layout']) .','.
            $db->quote($request['content_imageWidth']) .','.
            $db->quote($request['content_imageHeight']) .','.
            $db->quote($request['content_imageClass']) .','.
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
              $query = 'SELECT '. (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $_SESSION[$RTAG.'TableField']) ? $db->quoteName($_SESSION[$RTAG.'TableField']) : $_SESSION[$RTAG.'TableField']) .' FROM '. $db->quoteName($cfg['mainTable']).' WHERE id='.$id.' AND state = 1';
              $db->setQuery($query);
              $elemLabel = $db->loadResult();
            endif;

            // SET ORDER
            setOrder($id, $request['tag'], $request['ctag'], $request['ordering'], $request['cordering'], $cfg);

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
