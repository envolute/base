<?php
/* EXTRAFIELDS -> FIELDSATTACH
 * Configuração dos campos extras no conteúdo do artigo.
*/
defined('_JEXEC') or die;

/* PARA HABILITAR O CÓDIGO ABAIXO REMOVA ESSE COMENTÁRIO. (OBS: O MODELO ABAIXO FOI UTILIZADO NO PROJETO "JACARÉ NÁUTICA")

// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_BASE.'/templates/base/core/libs/php/helper.php');

// load API
JLoader::register('fieldattach', 'components/com_fieldsattach/helpers/fieldattach.php');

// load Joomla Libs
$app = JFactory::getApplication(site);

//content ID
$itemID = $app->input->getInt('id');
			
// CUSTOM FIELDS
	
	// status
	$status = fieldattach::getValue($itemID, 1 , false);
	
	// Preço
	$preco = fieldattach::getValue($itemID, 5 , false);
	
	// Motor
	$motor = nl2br(fieldattach::getValue($itemID, 3 , false));
	
	// lista -> Características
	$field = array();
	// Ano
	$field['label'][] = fieldattach::getName($itemID, 2 , false);
	$field['value'][] = fieldattach::getValue($itemID, 2 , false);
	// Cor
	$field['label'][] = fieldattach::getName($itemID, 4 , false);
	$field['value'][] = fieldattach::getValue($itemID, 4 , false);
	
	// lista -> dados técnicos (Item:valor -> separados por vírgula)
	$dados = fieldattach::getValue($itemID, 6 , false);
	$dados_array = explode("\n", str_replace("\n\r","\n",$dados));
	foreach($dados_array as $dado) {
		// O replace abaixo permite o uso de ':' no resultado (após o 1º ':')
		// pois usa apenas a 1ª ocorrencia de ':' -> alterando-a para '|'
		// Ex: Label : -> '|' valor1:valor2:valor3...
		$item = preg_replace('/:/', '|', $dado, 1);
		$item = explode('|',$item);
		$field['label'][] = $item[0];
		$field['value'][] = $item[1];
	
	}

// CUSTOM CONTENT
$html = '';
if(array_filter($field['value']) || !empty($status) || !empty($preco)) :
	
	if(!empty($status)) :
		switch ($status) {
			case 'Vendido' :
				$status = '<span class="pull-left label label-danger">'.$status.'</span>';
				break;
			default :
				$status = '<span class="pull-left label label-success">'.$status.'</span>';
		}
	endif;
	
	$html .= '<hr class="hidden-lg" />';
	
	if(!empty($preco) || !empty($status)) :
		$html .= '<h3 class="text-right no-margin-top bottom-expand set-border-bottom border-default clearfix">';
		$html .= !empty($status) ? $status : '';
		$html .= !empty($preco) ? '<small>R$ </small><span>'.$preco.'</span>':'';
		$html .= '</h3>';
	endif;
	
	$html .= '
	<h4 class="no-margin-top">Características</h4>
	<table class="produto-detalhes table table-striped table-hover table-condensed small">
		<tbody>
	';
	if(!empty($motor)) :
		$html .= '
		<tr>
			<td colspan="2">
				<strong>Motorização</strong><h4 class="no-margin">'.$motor.'</h4>
			</td>
		</tr>
		';
	endif;
	
	for ($i = 0; $i < count($field['value']); $i++) {
		if(!empty($field['value'][$i])) :
			$html .= '
			<tr>
				<th>'.$field['label'][$i].'</th>
				<td class="text-right">'.$field['value'][$i].'</td>
			</tr>
			';
		endif;
	}
	
	$html .= '
		</tbody>
	</table>
	';
	
	echo $html;
endif;

/* EXEMPLO DE UTILIZAÇÃO DESSE ARQUIVO ATRAVÉS DO PLUGIN 'SOURCERER (NoNumber)'...
 * DICA: CASO SEJA CONFIGURADO COM UM "SNIPPET (Elemento)", PODEM SER GERADOS OUTROS ARQUIVOS/SNIPPETS PARA USAR EM POSIÇÕES DIFERENTES DO CONTEÚDO
 * -----------------------------------------------
 
{source}
<?php
// Por segurança, utilize um arquivo para armazenar o código
// IMPORTANTE: Para pegar o ID do item utilize -> JRequest::getVar('id')
include_once JPATH_BASE.'/templates/base/source/extrafields.php';
?>
{/source}

 * -----------------------------------------------
 */