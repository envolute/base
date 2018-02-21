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

	// GROUPS -> select
	$flt_group = '';
	$query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable'].'_groups') .' ORDER BY name';
	$db->setQuery($query);
	$groups = $db->loadObjectList();
	foreach ($groups as $obj) {
		$flt_group .= '<option value="'.$obj->id.'"'.($obj->id == $fGroup ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
	}

// VISIBILITY
// Elementos visíveis apenas quando uma consulta é realizada

	$hasFilter = $app->input->get($APPTAG.'_filter', 0, 'int');
	// Estado inicial dos elementos
	$btnClearFilter		= ''; // botão de resetar
	$textResults		= ''; // Texto informativo
	// Filtro ativo
	if($hasFilter || $cfg['ajaxFilter']) :
		$btnClearFilter = '
			<a href="'.JURI::current().'" class="btn btn-sm btn-danger base-icon-cancel-circled btn-icon">
				'.JText::_('TEXT_CLEAR').' '.JText::_('TEXT_FILTER').'
			</a>
		';
		$textResults = '<span class="base-icon-down-big text-muted d-none d-sm-inline"> '.JText::_('TEXT_SEARCH_RESULTS').'</span>';
	endif;

	$filterActive = '';
	if($hasAdmin) {
		$filterActive = '
			<div class="col-sm-4 col-md-3 col-lg-2">
				<div class="form-group">
					<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_ITEM_STATE').'</label>
					<select name="active" id="active" class="form-control form-control-sm set-filter">
						<option value="2">- '.JText::_('TEXT_ALL').' -</option>
						<option value="1"'.($active == 1 ? ' selected' : '').'>'.JText::_('TEXT_ACTIVES').'</option>
						<option value="0"'.($active == 0 ? ' selected' : '').'>'.JText::_('TEXT_INACTIVES').'</option>
					</select>
				</div>
			</div>
		';
	}

// VIEW
$htmlFilter = '
	<form id="filter-'.$APPTAG.'" class="hidden-print collapse '.((isset($_GET[$APPTAG.'_filter']) || $cfg['openFilter']) ? 'show' : '').'" method="get">
		<fieldset class="fieldset-embed fieldset-sm pt-3 pb-0">
			<input type="hidden" name="'.$APPTAG.'_filter" value="1" />

			<div class="row">
				<div class="col-sm-8 md-6 col-lg-2">
					<div class="form-group">
						<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_GROUP').'</label>
						<select name="fGroup" id="fGroup" class="form-control form-control-sm set-filter">
							<option value="0">- '.JText::_('TEXT_SELECT').' -</option>
							'.$flt_group.'
						</select>
					</div>
				</div>
				<div class="col-sm-8 col-md-6 col-lg">
					<div class="form-group">
						<label class="label-xs text-muted text-truncate">'.implode(', ', $sLabel).'</label>
						<input type="text" name="fSearch" value="'.$search.'" class="form-control form-control-sm" />
					</div>
				</div>
				'.$filterActive.'
				<div class="col-sm-8 col-md-6 col-lg-3 text-right">
					<div class="form-group">
						<label class="label-xs d-none d-md-block">&#160;</label>
						<button '.$btnAction.' id="'.$APPTAG.'-submit-filter" class="btn btn-sm btn-primary base-icon-search btn-icon">
							'.JText::_('TEXT_SEARCH').'
						</button>
						'.$btnClearFilter.'
					</div>
				</div>
			</div>
		</fieldset>
	</form>
';

?>
