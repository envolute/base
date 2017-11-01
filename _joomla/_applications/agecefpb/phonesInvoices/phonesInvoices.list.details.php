<?php
/* SISTEMA PARA CADASTRO DE TELEFONES
 * AUTOR: IVO JUNIOR
 * EM: 18/02/2016
*/
defined('_JEXEC') or die;
$ajaxRequest = false;
require('config.php');

// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_CORE.DS.'helpers/base.php');
JLoader::register('baseAppHelper', JPATH_CORE.DS.'helpers/apps.php');

$app = JFactory::getApplication('site');

// GET CURRENT USER'S DATA
$user = JFactory::getUser();
$groups = $user->groups;

// init general css/js files
require(JPATH_CORE.DS.'apps/_init.app.php');

// Carrega o arquivo de tradução
// OBS: para arquivos externos com o carregamento do framework '_init.joomla.php' (geralmente em 'ajax')
// a language 'default' não é reconhecida. Sendo assim, carrega apenas 'en-GB'
// Para possibilitar o carregamento da language 'default' de forma dinâmica,
// é necessário passar na sessão ($_SESSION[$APPTAG.'langDef'])
if(isset($_SESSION[$APPTAG.'langDef'])) :
$lang->load('base_apps', JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);
$lang->load('base_'.$APPNAME, JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);
endif;

// DATABASE CONNECT
$db = JFactory::getDbo();
?>

<script>
	jQuery(function() {
		// lista completa
		var formFilter	= jQuery("#filter-<?php echo $APPTAG?>");
		var formLimit	= jQuery("#form-limit-<?php echo $APPTAG?>");
		<?php
			// SET FILTER -> Submit o filtro no evento 'onchange'
			require(JPATH_CORE.DS.'apps/snippets/list/setFilter.js.php');
			// LIST LIMIT -> Altera o limite de itens visualizados na listagem
			require(JPATH_CORE.DS.'apps/snippets/list/listLimit.js.php');
		?>
	});
</script>

<?php
// LOAD FILTER
require($PATH_APP_FILE.'.filter.details.php');

// LIST

	// PAGINATION VAR's
	require(JPATH_CORE.DS.'apps/layout/list/pagination.vars.php');

	$query = '
		SELECT SQL_CALC_FOUND_ROWS T1.*,
		T2.due_date
		FROM '. $db->quoteName('#__'.$cfg['project'].'_phones_invoices_details') .' T1
			JOIN '. $db->quoteName($cfg['mainTable']) .' T2
			ON T2.id = T1.invoice_id
		WHERE '.$where.'
		ORDER BY T1.tel, T1.sub_secao, T1.secao';
	;
	try {

		$db->setQuery($query, $lim0, $lim);
		$db->execute();
		$num_rows = $db->getNumRows();
		$res = $db->loadObjectList();

	} catch (RuntimeException $e) {
		 echo $e->getMessage();
		 return;
	}

// VIEW
$html = '
	<table class="table table-striped table-hover table-sm">
		<thead>
			<tr>
				<th>'.JText::_('FIELD_LABEL_DUE_DATE').'</th>
				<th>'.JText::_('FIELD_LABEL_PHONE').'</th>
				<th>'.JText::_('FIELD_LABEL_SUB_SECTION').'</th>
				<th class="d-none d-md-table-cell">'.JText::_('FIELD_LABEL_SECTION').'</th>
				<th>'.JText::_('TEXT_VALUE').'</th>
			</tr>
		</thead>
		<tbody>
';

if($num_rows) : // verifica se existe

	// pagination
	$db->setQuery('SELECT FOUND_ROWS();');  //no reloading the query! Just asking for total without limit
	jimport('joomla.html.pagination');
	$found_rows = $db->loadResult();
	$pageNav = new JPagination($found_rows , $lim0, $lim );

	$total = 0;
	foreach($res as $item) {

		if($cfg['hasUpload']) :
			JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
			$files[$item->id] = uploader::getFiles($cfg['fileTable'], $item->id);
			$listFiles = '';
			for($i = 0; $i < count($files[$item->id]); $i++) {
				if(!empty($files[$item->id][$i]->filename)) :
					$listFiles .= '
						<a href="'.JURI::root(true).'/apps/get-file?fn='.base64_encode($files[$item->id][$i]->filename).'&mt='.base64_encode($files[$item->id][$i]->mimetype).'&tag='.base64_encode($APPNAME).'">
							<span class="base-icon-attach hasTooltip" title="'.$files[$item->id][$i]->filename.'<br />'.((int)($files[$item->id][$i]->filesize / 1024)).'kb"></span>
						</a>
					';
				endif;
			}
		endif;

		$total += $item->valor;

		// Resultados
		$html .= '
			<tr id="'.$APPTAG.'-item-'.$item->id.'">
				<td>'.$note.baseHelper::dateFormat($item->due_date).'</td>
				<td>'.$item->tel.'</td>
				<td class="d-none d-md-table-cell">'.$item->sub_secao.'</td>
				<td class="d-none d-md-table-cell">'.$item->secao.'</td>
				<td>'.baseHelper::priceFormat($item->valor).'</td>
			</tr>
		';
	}

	// TOTAL
	if($showTotal) :
		$html .= '
			<tr class="table-warning">
				<td colspan="4"><strong>TOTAL</strong></td>
				<td colspan="1"><strong>'.baseHelper::priceFormat($total).'</strong></td>
			</tr>
		';
	endif;

else : // num_rows = 0

	$html .= '
		<tr>
			<td colspan="10">
				<div class="alert alert-warning alert-icon m-0">'.JText::_('MSG_LISTNOREG').'</div>
			</td>
		</tr>
	';

endif;

$html .= '
		</tbody>
	</table>
';

if($num_rows) :

	// PAGINATION
	require(JPATH_CORE.DS.'apps/layout/list/pagination.php');

	// PAGE LIMIT
	require(JPATH_CORE.DS.'apps/layout/list/pageLimit.php');

endif;

echo $htmlFilter.$html;
