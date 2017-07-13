<?php

// load Joomla's framework
require_once('../load.joomla.php');
$app = JFactory::getApplication('site');

defined('_JEXEC') or die;
$ajaxRequest = true;
require('config.php');
// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_BASE.'/templates/base/source/helpers/base.php');

// Carrega o arquivo de tradução
// OBS: para arquivos externos com o carregamento do framework 'load.joomla.php' (geralmente em 'ajax')
// a language 'default' não é reconhecida. Sendo assim, carrega apenas 'en-GB'
// Para possibilitar o carregamento da language 'default' de forma dinâmica,
// é necessário passar na sessão ($_SESSION[$APPTAG.'langDef'])
if(isset($_SESSION[$APPTAG.'langDef']))
$lang->load('base_'.$APPNAME, JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);

//joomla get request data
$input      = $app->input;

// params requests
$P			= $input->get('p', 0, 'int');
$S			= $input->get('s', 0, 'int');

// get current user's data
$user = JFactory::getUser();
$groups = $user->groups;

// verifica o acesso
$hasGroup = array_intersect($groups, $cfg['groupId']['viewer']); // se está na lista de grupos permitidos
$hasAdmin = array_intersect($groups, $cfg['groupId']['admin']); // se está na lista de administradores permitidos

// database connect
$db = JFactory::getDbo();

// GET DATA
$noReg = true;
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
  WHERE T2.id = '.$P.' AND T1.state = '.$S.'
  ORDER BY '. $db->quoteName('T1.created_date') .' ASC';

try {

	$db->setQuery($query);
	$db->execute();
	$num_rows = $db->getNumRows();
	$res = $db->loadObjectList();

} catch (RuntimeException $e) {
	 echo $e->getMessage();
	 return;
}

if($num_rows) : // verifica se existe
	$html .= '<table>';
	$html .= '<tr>';
	$html .= '<td>NOME</td>';
	$html .= '<td>CPF</td>';
	$html .= '<td>SEXO</td>';
	$html .= '<td>DATA DE NASCIMENTO</td>';
	$html .= '<td>TELEFONE</td>';
	$html .= '<td>EMAIL</td>';
	$html .= '<td>EQUIPE</td>';
	$html .= '<td>MODALIDADE</td>';
	$html .= '<td>DISTÂNCIA</td>';
	$html .= '<td>DEFICIÊNCIA</td>';
	$html .= '<td>TAM. DA CAMISA</td>';
	$html .= '<td>VALOR</td>';
	$html .= '<td>DATA DA INSCRIÇÃO</td>';
	$html .= '</tr>';
	$html .= '<tr>';
	foreach($res as $item) {
		$html .= '<td>'.$item->nome.'</td>';
		$html .= '<td>'.$item->cpf.'</td>';
		$html .= '<td>'.$item->sexo.'</td>';
		$html .= '<td>'.$item->aniversario.'</td>';
		$html .= '<td>'.$item->telefone.'</td>';
		$html .= '<td>'.$item->email.'</td>';
		$html .= '<td>'.$item->equipe.'</td>';
		$html .= '<td>'.$item->modalidade.'</td>';
		$html .= '<td>'.$item->distancia.'</td>';
		$html .= '<td>'.$item->deficiencia.'</td>';
		$html .= '<td>'.$item->camisa.'</td>';
		$html .= '<td>'.$item->valor.'</td>';
		$html .= '<td>'.$item->data.'</td>';
	}
	$html .= '</tr>';
	$html .= '</table>';

	$arquivo = 'planilha-nao-inscritos-'.$P.'.xls';

	// Configurações header para forçar o download
	header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
	header ("Cache-Control: no-cache, must-revalidate");
	header ("Pragma: no-cache");
	header ("Content-type: application/x-msexcel; charset=utf-8");
	header ("Content-Disposition: attachment; filename=\"{$arquivo}\"" );
	header ("Content-Description: PHP Generated Data" );

	echo $html;

else :
	header('status: 400 Bad Request', true, 400);
endif;

?>
