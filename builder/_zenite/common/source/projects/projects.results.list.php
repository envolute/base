<?php
defined('_JEXEC') or die;

$ajaxRequest = false;
require('config.php');

// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_BASE.'/templates/base/source/helpers/base.php');

$app = JFactory::getApplication('site');

// get current user's data
$user = JFactory::getUser();
$groups = $user->groups;

// database connect
$db = JFactory::getDbo();

// LOAD FILTER
require('projects.results.filter.php');

echo '
<script>
  jQuery(function() {
    jQuery("#filter-result").find(".set-filter").change(function() {
      setTimeout(function() { jQuery("#filter-result").submit() }, 100);
    });
		window.results_setListOrder = function(col, type) {
			if(col) {
				formOrder.find("input#'.$APPTAG.'ResultsoF").val(col);
				formOrder.find("input#'.$APPTAG.'ResultsoF").val(type);
			}
			formOrder.submit();
		};
		window.results_setListLimit = function() {
			jQuery("#form-limit-results").submit();
		};
  });
</script>
';

if(!isset($pID) || empty($pID) || $pID == 0) :

	echo $htmlFilter.'<h4 class="alert alert-info no-margin"><span class="base-icon-up-hand"></span> Selecione o Evento</h4>';

elseif(!isset($cID) || empty($cID) || $cID == 0) :

	echo $htmlFilter.'<h4 class="alert alert-warning no-margin">Selecione a Modalidade <span class="base-icon-up-hand"></span></h4>';

else :

	// LIST

		$query = '
    SELECT
				'. $db->quoteName('T1.id') .',
				'. $db->quoteName('T2.name') .' project,
				'. $db->quoteName('T2.date') .' projectDate,
				'. $db->quoteName('T1.nome') .',
				'. $db->quoteName('T1.sexo') .',
				'. $db->quoteName('T1.colocacao') .',
				'. $db->quoteName('T1.numero') .',
				'. $db->quoteName('T1.idade') .',
				'. $db->quoteName('T1.faixa') .',
				'. $db->quoteName('T1.colocacao_faixa') .',
				'. $db->quoteName('T1.equipe') .',
				'. $db->quoteName('T1.tempo_bruto') .',
				'. $db->quoteName('T1.tempo_liquido') .',
				'. $db->quoteName('T1.state') .'
			FROM
				'. $db->quoteName('#__zenite_results') .' T1
				JOIN '. $db->quoteName('#__zenite_projects') .' T2
				ON T2.id = T1.project_id
				JOIN '. $db->quoteName('#__zenite_projects_results') .' T3
				ON T3.id = T1.projectResult_id
			WHERE
				'.$where.$orderList;
		;
		try {

			$db->setQuery($query);
			$db->execute();
			$num_rows = $db->getNumRows();
			$res = $db->loadObjectList();

		} catch (RuntimeException $e) {
			 echo $e->getMessage();
			 return;
		}

    // Nome do Projeto
  	$query = 'SELECT name FROM '. $db->quoteName('#__zenite_projects') .' WHERE id = '.$pID;
  	$db->setQuery($query);
  	$project = $db->loadResult();

	// VIEW
	$html = '
		<form id="form-list-'.$APPTAG.'" method="post">
			<table class="table table-striped table-hover table-condensed">
				<thead>
					<tr>
            <th colspan="5" class="strong text-live"><span class="base-icon-award"></span> '.$project.'</th>
            <th colspan="2" class="info text-center"><span class="base-icon-clock"></span> Tempo</th>
						<th></th>
					</tr>
					<tr>
            <th class="warning text-center">Geral</th>
            <th>Sexo</th>
            <th>N&ordm;</th>
            <th>Nome</th>
            <th>Faixa</th>
            <th class="info text-center">Bruto</th>
            <th class="info text-center">Liquido</th>
            <th>Equipe</th>
					</tr>
				</thead>
				<tbody>
	';

	if($num_rows) : // verifica se existe

		foreach($res as $item) {

      $cFaixa = ($item->colocacao_faixa == '0' || $item->colocacao_faixa == '-') ? '<div class="small text-success font-featured">Premiado em '.$item->colocacao.'&ordm; na Colocação Geral</div>' : '<div class="small text-live font-featured">Colocação na faixa: '.$item->colocacao_faixa.'&ordm;</div>';
      $equipe = !empty($item->equipe) ? $item->equipe : '-';
      $genero = $item->sexo == 'M' ? 'Masc.' : 'Fem.';
      $genColor = $item->sexo == 'M' ? 'primary' : 'live';
			$html .= '
				<tr id="'.$APPTAG.'-item-'.$item->id.'">
          <td class="warning text-center text-'.$genColor.'">'.$item->colocacao.'&ordm;</td>
          <td class="text-'.$genColor.'">'.$genero.'</td>
          <td>'.$item->numero.'</td>
          <td>'.baseHelper::nameFormat($item->nome).'<div class="small text-muted font-featured">'.$item->idade.' anos</div></td>
          <td>'.$item->faixa.$cFaixa.'</td>
          <td class="info text-center">'.$item->tempo_bruto.'</td>
          <td class="info text-center">'.$item->tempo_liquido.'</td>
          <td>'.baseHelper::nameFormat($equipe).'</td>
				</tr>
			';
		}

	else : // num_rows = 0

		$html .= '
			<tr>
				<td colspan="8">
					<div class="alert alert-warning alert-icon no-margin">Não foram registrados resultados para essa modalidade!</div>
				</td>
			</tr>
		';

	endif;

	$html .= '
				</tbody>
			</table>
		</form>
    <div class="top-expand strong">Visualizando um total de <span class="text-live">'.$num_rows.'</span> resultados</div>
	';

	echo $htmlFilter.$html;

endif;

?>
