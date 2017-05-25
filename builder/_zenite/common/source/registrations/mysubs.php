<?php
defined('_JEXEC') or die;

$app = JFactory::getApplication('site');

// load Scripts
$doc = JFactory::getDocument();
// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_BASE.'/templates/base/source/helpers/base.php');

// get current user's data
$user = JFactory::getUser();
$groups = $user->groups;

// acesso liberado sempre
$cfg['groupId'][]  = '6'; // Gerente
$cfg['groupId'][]  = '7'; // Administrador
$cfg['groupId'][]  = '8'; // Desenvolvedor

$hasAdmin = array_intersect($groups, $cfg['groupId']); // se está na lista de administradores permitidos

// verifica o acesso
if($user->guest) : // se não estiver logado
	$app->redirect(JURI::root(true).'/login?return='.urlencode(base64_encode(JURI::current())));
	exit();
endif;

$uID	= $app->input->get('uid', 0, 'int');
$userID = $uID > 0 ? $uID : $user->id;

// database connect
$db = JFactory::getDbo();

// inscrição
$query = '
  SELECT
    '. $db->quoteName('T1.id') .',
    '. $db->quoteName('T2.id') .' project_id,
    '. $db->quoteName('T2.name') .' project,
    '. $db->quoteName('T2.date') .',
    IF('. $db->quoteName('T2.date') .' < CURDATE(), 1, 0) done,
    '. $db->quoteName('T1.price') .',
		'. $db->quoteName('T1.discount') .',
    IF('. $db->quoteName('T2.start_date') .' <= NOW() AND '. $db->quoteName('T2.end_date') .' >= NOW(), 1, 0) open,
		'. $db->quoteName('T1.due_date') .',
    '. $db->quoteName('T1.created_date') .',
    '. $db->quoteName('T1.status') .',
    '. $db->quoteName('T1.state') .',
    '. $db->quoteName('T4.name') .' category,
    '. $db->quoteName('T5.name') .' disability,
    '. $db->quoteName('T3.disability_id') .' ,
    '. $db->quoteName('T3.distance') .',
    '. $db->quoteName('T3.distance_unit') .'
  FROM
    '. $db->quoteName('#__zenite_registrations') .' T1
    JOIN '. $db->quoteName('#__zenite_projects') .' T2
    ON T2.id = T1.project_id
    JOIN '. $db->quoteName('#__zenite_projects_types') .' T3
    ON T3.id = T1.projectType_id
    JOIN '. $db->quoteName('#__zenite_projects_categories') .' T4
    ON T4.id = T3.category_id
    LEFT JOIN '. $db->quoteName('#__zenite_disabilities') .' T5
    ON T4.id = T3.disability_id
  WHERE
    '. $db->quoteName('T1.user_id') .' = '.$userID.'
    AND (('. $db->quoteName('T2.start_date') .' <= NOW() AND '. $db->quoteName('T2.end_date') .' >= NOW()) OR T1.status = 2)
		AND T1.state = 1
';
$db->setQuery($query);
$db->execute();
$num_rows = $db->getNumRows();
$regs = $db->loadObjectList();

if($hasAdmin) :
	echo '
		<div class="well">
			<form>
				<strong>Filtrar pelo ID do usuário</strong> <input type="text" name="uid" value="'.($uID != 0 ? $uID : '').'" size="5" />
			</form>
		</div>
	';
endif;

if($num_rows > 0) :
?>
<ul class="list">
  <?php
  foreach($regs as $item) :
    if(($item->open == 1 || $item->status == 2) && $item->state == 1) :
			if($item->open == 1 && $item->status < 2) :
	      $status = '<span class="base-icon-attention text-danger">Venc.: '.baseHelper::dateFormat($item->due_date).'</span> <a class="btn btn-sm btn-warning" href="subsConfirm?r='.urlencode(base64_encode($item->id)).'" target="_blank">Pagar Agora <span class="base-icon-right-big"></span></a>';
	    else :
	      $status = baseHelper::dateFormat($item->created_date);
	    endif;
			$price = '';
			if($item->price > 0.00) :
				$price = '<h4 class="no-margin">Valor: R$ '.baseHelper::priceFormat(($item->price - $item->discount)).'</h4>';
			endif;
  ?>
	    <li>
	      <div class="row">
	        <div class="col-sm-8">
	          <h4 class="no-margin"><a href="subsConfirm?r=<?php echo urlencode(base64_encode($item->id))?>"><?php echo baseHelper::nameFormat($item->project)?> - (<?php echo baseHelper::dateFormat($item->date)?>)</a></h4>
	          <strong>Modalidade</strong>: <?php echo baseHelper::nameFormat($item->category).' '.$item->distance.($item->distance_unit == 0 ? ' m' : ' Km')?>
	        </div>
	        <div class="col-sm-4 text-right text-left-xs">
	          <?php echo $price.$status ?>
	        </div>
	      </div>
	    </li>
		<?php endif;?>
  <?php endforeach;?>
</ul>
<?php else : ?>
	<p class="alert alert-warning"><span class="base-icon-info-circled"></span> Até o momento não registramos nenhum cadastro seu em nossos eventos!</p>
<?php endif; ?>
