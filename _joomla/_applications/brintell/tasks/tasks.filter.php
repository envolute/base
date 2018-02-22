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

	// PROJECTS -> select
	$flt_project = '';
	if($pID == 0) :
		$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_projects') .' WHERE '. $db->quoteName('state') .' = 1 ORDER BY name';
		$db->setQuery($query);
		$projects = $db->loadObjectList();
		foreach ($projects as $obj) {
			$flt_project .= '<option value="'.$obj->id.'"'.($obj->id == $fProj ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
		}
		$flt_project = '
			<div class="col-sm-6 col-md-3">
				<div class="form-group">
					<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_PROJECT').'</label>
					<select name="fProj" id="fProj" class="form-control form-control-sm set-filter">
						<option value="0">- '.JText::_('TEXT_ALL').' -</option>
						'.$flt_project.'
					</select>
				</div>
			</div>
		';
	else :
		$flt_project = '<input type="hidden" name="pID" id="pID" value="'.$pID.'" />';
	endif;

	// ASSIGN TO -> select
	$flt_assign = '';
	if(!$hasExternal) :
		$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_staff') .' WHERE '. $db->quoteName('type') .' IN (0, 1) AND '. $db->quoteName('access') .' = 1 AND '. $db->quoteName('state') .' = 1 ORDER BY name';
		$db->setQuery($query);
		$assigned = $db->loadObjectList();
		foreach ($assigned as $obj) {
			$name = !empty($obj->nickname) ? $obj->nickname : $obj->name;
			$staff = ($obj->type == 1) ? '*' : '';
			$flt_assign .= '<option value="'.$obj->id.'"'.($obj->id == $fAssign ? ' selected = "selected"' : '').'>'.$staff.baseHelper::nameFormat($name).'</option>';
		}
		$flt_assign = '
			<div class="col-sm-6 col-md-3">
				<div class="form-group">
					<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_ASSIGN_TO').'</label>
					<select name="fAssign[]" id="fAssign" class="form-control form-control-sm set-filter" multiple>
						'.$flt_assign.'
					</select>
				</div>
			</div>
		';
	endif;

	// TAGS -> select
	$flt_tag = '';
	$query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable'].'_tags') .' WHERE '. $db->quoteName('state') .' = 1 ORDER BY name';
	$db->setQuery($query);
	$tags = $db->loadObjectList();
	foreach ($tags as $obj) {
		$flt_tag .= '<option value="'.$obj->name.'"'.($obj->id == $fTags ? ' selected = "selected"' : '').'>'.$obj->name.'</option>';
	}


// VIEW
$htmlFilter = '
	<form id="filter-'.$APPTAG.'" class="hidden-print collapse '.((isset($_GET[$APPTAG.'_filter']) || $cfg['openFilter']) ? 'show' : '').'" method="get">
		<fieldset class="fieldset-embed fieldset-sm pt-2 pb-0">
			<input type="hidden" name="'.$APPTAG.'_filter" value="1" />
			<div class="row">
				<div class="col-lg-10">
					<div class="row">
						'.$flt_project.$flt_assign.'
						<div class="col-sm-6 col-md-2">
							<div class="form-group">
								<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_PRIORITY').'</label>
								<select name="fPrior" id="fPrior" class="form-control form-control-sm set-filter">
									<option value="9">- '.JText::_('TEXT_ALL_F').' -</option>
									<option value="0"'.($fPrior == 0 ? ' selected' : '').'>'.JText::_('TEXT_PRIORITY_0').'</option>
									<option value="1"'.($fPrior == 1 ? ' selected' : '').'>'.JText::_('TEXT_PRIORITY_1').'</option>
									<option value="2"'.($fPrior == 2 ? ' selected' : '').'>'.JText::_('TEXT_PRIORITY_2').'</option>
								</select>
							</div>
						</div>
						<div class="col-sm-6 col-md-2">
							<div class="form-group">
								<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_VISIBILITY').'</label>
								<select name="fView" id="fView" class="form-control form-control-sm set-filter">
									<option value="2">- '.JText::_('TEXT_ALL_F').' -</option>
									<option value="1"'.($fView == 1 ? ' selected' : '').'>'.JText::_('TEXT_PRIVATE').'</option>
									<option value="0"'.($fView == 0 ? ' selected' : '').'>'.JText::_('TEXT_PROJECT').'</option>
								</select>
							</div>
						</div>
						<div class="col-sm-6 col-md-2">
							<label class="label-xs text-muted">&#160;</label>
							<span class="btn-group btn-group-justified" data-toggle="buttons">
								<label class="btn btn-sm btn-default btn-active-danger'.($active == 0 ? ' active' : '').' base-icon-box">
									<input type="checkbox" name="active" id="active" class="set-filter" value="0"'.($active == 0 ? ' checked' : '').' />
									'.JText::_('TEXT_ARCHIVE').'
								</label>
							</span>
						</div>
						<div class="col-sm-6 col-md-4 col-xl-3">
							<div class="form-group">
								<label class="label-xs text-muted text-truncate">'.implode(', ', $sLabel).'</label>
								<input type="text" name="fSearch" value="'.$search.'" class="form-control form-control-sm" />
							</div>
						</div>
						<div class="col-sm-6 col-md-4 col-xl-3">
							<div class="form-group">
								<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_DEADLINE').'</label>
								<span class="input-group input-group-sm">
									<span class="input-group-addon strong">'.JText::_('TEXT_FROM').'</span>
									<input type="text" name="dateMin" value="'.$dateMin.'" class="form-control field-date" data-width="100%" data-convert="true" />
									<span class="input-group-addon">'.JText::_('TEXT_TO').'</span>
									<input type="text" name="dateMax" value="'.$dateMax.'" class="form-control field-date" data-width="100%" data-convert="true" />
								</span>
							</div>
						</div>
						<div class="col-sm-6 col-md-4">
							<div class="form-group">
								<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_TAGS').'</label>
								<select name="fTags[]" id="fTags" class="form-control form-control-sm set-filter" multiple>
									'.$flt_tag.'
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-2 text-right">
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
