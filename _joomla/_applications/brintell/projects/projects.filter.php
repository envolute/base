<?php
defined('_JEXEC') or die;

// AJAX FILTER
// Para que seja possível utilizar o filtro numa listagem ajax é necessário que
// o formulário esteja no corpo da página (fora do resultado da listagem "list.ajax")
// Dessa forma, o formulário é carregado no arquivo principal através da variável
// $cfg['ajaxFilter'] ou do parâmetro ${$APPTAG.'AjaxFilter'}
// Já o script de tratamento do filtro deve estar "incluso" na listagem ajax.
// => require($PATH_APP_FILE.'.filter.query.php');
// Para isso, é criado o arquivo "[project].filter.query.php" para conter
// o script de tratamento dos parâmetros e montagem da variável "where"
// Obs: Caso não haja a necessidade de filtro em listagem ajax, o código contido em
// "[project].filter.query.php" pode ser colocado aqui e o arquivo deletado,
// mantendo assim um único arquivo "[project].filter.php" para filtragem
// Ou seja, caso não exista o arquivo "[project].filter.query.php" no diretório
// da aplicação é porque o filro ajax não é utilizado!

// LOAD FILTER QUERY
require($PATH_APP_FILE.'.filter.query.php');

// ACTION

	$btnAction = $cfg['listFull'] ? 'type="submit"' : 'type="button" onclick="'.$APPTAG.'_listReload(false);"';

// FILTER'S DINAMIC FIELDS

	// CLIENTS -> select
	$flt_client = '';
	$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_clients') .' WHERE '. $db->quoteName('state') .' = 1 ORDER BY name';
	$db->setQuery($query);
	$clients = $db->loadObjectList();
	foreach ($clients as $obj) {
		$flt_client .= '<option value="'.$obj->id.'"'.($obj->id == $clientID ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
	}


// VIEW
$htmlFilter = '
	<form id="filter-'.$APPTAG.'" class="hidden-print collapse '.((isset($_GET[$APPTAG.'_filter']) || $cfg['showFilter']) ? 'show' : '').'" method="get">
		<fieldset class="fieldset-embed fieldset-sm pt-2 pb-0">
			<input type="hidden" name="'.$APPTAG.'_filter" value="1" />

			<div class="row">
				<div class="col-sm-6 col-md-4 col-lg-3">
					<div class="form-group">
						<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_CLIENT').'</label>
						<select name="clientID" id="clientID" class="form-control form-control-sm set-filter">
							<option value="0">- '.JText::_('TEXT_ALL').' -</option>
							'.$flt_client.'
						</select>
					</div>
				</div>
				<div class="col-sm-6 col-md-2">
					<div class="form-group">
						<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_ITEM_STATE').'</label>
						<select name="active" id="active" class="form-control form-control-sm set-filter">
							<option value="2">- '.JText::_('TEXT_ALL').' -</option>
							<option value="1"'.($active == 1 ? ' selected' : '').'>'.JText::_('TEXT_ACTIVES').'</option>
							<option value="0"'.($active == 0 ? ' selected' : '').'>'.JText::_('TEXT_INACTIVES').'</option>
						</select>
					</div>
				</div>
				<div class="col-sm-6 col-md">
					<div class="form-group">
						<label class="label-xs text-muted text-truncate">'.implode(', ', $sLabel).'</label>
						<input type="text" name="fSearch" value="'.$search.'" class="form-control form-control-sm" />
					</div>
				</div>
				<div class="col-sm-6 col-lg-4 col-xl-3">
					<div class="form-group">
						<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_START_DATE').'</label>
						<span class="input-group input-group-sm">
							<span class="input-group-addon strong">'.JText::_('TEXT_FROM').'</span>
							<input type="text" name="dateMin" value="'.$dateMin.'" class="form-control field-date" data-width="100%" data-convert="true" />
							<span class="input-group-addon">'.JText::_('TEXT_TO').'</span>
							<input type="text" name="dateMax" value="'.$dateMax.'" class="form-control field-date" data-width="100%" data-convert="true" />
						</span>
					</div>
				</div>
				<div class="col-sm col-lg-2 text-right">
					<div class="form-group">
						<label class="label-xs text-muted">&#160;</label>
						<button '.$btnAction.' id="'.$APPTAG.'-submit-filter" class="btn btn-sm btn-primary base-icon-search btn-icon">
							'.JText::_('TEXT_SEARCH').'
						</button>
						<a href="'.JURI::current().'" class="btn btn-sm btn-danger base-icon-cancel-circled hasTooltip" title="'.JText::_('TEXT_RESET').'"></a>
					</div>
				</div>
			</div>
		</fieldset>
	</form>
';

?>
