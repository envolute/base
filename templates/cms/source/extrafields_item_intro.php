<?php
/* EXTRAFIELDS -> FIELDSATTACH
 * Configuração dos campos extras na listagem de itens da categoria.
*/
defined('_JEXEC') or die;

$efields = '';

/* PARA HABILITAR O CÓDIGO ABAIXO REMOVA ESSE COMENTÁRIO. (OBS: O MODELO ABAIXO FOI UTILIZADO NO PROJETO "ALBERTO MOREIRA")

// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_BASE.'/templates/base/core/helpers/base.php');

// load API
JLoader::register('fieldattach', 'components/com_fieldsattach/helpers/fieldattach.php');

//content ID
$itemID = $this->item->id;
			
// CUSTOM FIELDS
	
	// status
	$tecnica = fieldattach::getValue($itemID, 1 , false);
	
	// Preço
	$dimensoes = fieldattach::getValue($itemID, 2 , false);
	
	// Motor
	$ano = fieldattach::getValue($itemID, 3 , false);

// CUSTOM CONTENT
if(!empty($tecnica) || !empty($dimensoes) || !empty($ano)) :
	
	$efields .= '<div id="customFields">';
	
		if(!empty($tecnica)) $efields .= '<span class="efield-tecnica">'.$tecnica.'</span>';
		if(!empty($dimensoes)) $efields .= '<span class="efield-dimensoes">'.$dimensoes.'</span>';
		if(!empty($ano)) $efields .= '<span class="efield-ano">'.$ano.'</span>';
	
	$efields .= '</div>';
	
endif;

*/