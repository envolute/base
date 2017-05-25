<?php
defined('_JEXEC') or die;

$app = JFactory::getApplication('site');

// load Scripts
$doc = JFactory::getDocument();
// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_BASE.'/templates/base/source/helpers/base.php');

// acesso liberado sempre
$cfg['groupId'][]  = '6'; // Gerente
$cfg['groupId'][]  = '7'; // Administrador
$cfg['groupId'][]  = '8'; // Desenvolvedor

// get current user's data
$user = JFactory::getUser();
$groups = $user->groups;

// database connect
$db = JFactory::getDbo();

//joomla get request data
$input      = $app->input;

// fields 'Form' requests
$request            = array();
// default
// registration
$request['pid']       = $input->post->get('pid', 0, 'int');
$request['pType']     = $input->post->get('pType', 0, 'int');
$request['discount']  = $input->post->get('discount', 0, 'int');
$request['discountCounpon']  = $input->post->get('discountCounpon', 0, 'int');
$request['coupon']    = $input->post->get('coupon', '', 'string');
$request['extraField'] = $input->post->get('extraField', '', 'string');
$request['sizeShirt'] = $input->post->get('sizeShirt', '', 'string');
$request['team']      = $input->post->get('team', '', 'string');
$request['confirm']   = $input->post->get('confirm', 0, 'int');
// view registration
$request['r']         = $input->get('r', '', 'string');
$request['rid']       = base64_decode($input->get('r', '', 'string'));
// confirm
$request['c']         = base64_decode($input->get('c', '', 'string'));
$isConfirm = ($request['c'] == '1') ? true : false;

$id = $insert = 0;
// verifica se o evento existe e está disponível
if($request['pid'] > 0) :
  $query = 'SELECT * FROM '. $db->quoteName('#__zenite_projects') .' WHERE '. $db->quoteName('start_date') .' <= NOW() AND '. $db->quoteName('end_date') .' >= NOW() AND date > CURDATE() AND state = 1 AND id = '.$request['pid'];
  $db->setQuery($query);
  $project = $db->loadObject();
  $type = $request['pType'];
  $insert = 1;
elseif($request['rid'] > 0) :
  // inscrição
  $query = 'SELECT * FROM '. $db->quoteName('#__zenite_registrations') .' WHERE id = '.$request['rid'].' AND state = 1';
  $db->setQuery($query);
  $reg = $db->loadObject();
  $id = $reg->id;
  if($id == null) :
    $app->enqueueMessage('Inscrição cancelada!', 'warning');
    $app->redirect(JURI::root());
	 exit();
  endif;
  // projeto
  $query = 'SELECT * FROM '. $db->quoteName('#__zenite_projects') .' WHERE state = 1 AND id = '.$reg->project_id;
  $db->setQuery($query);
  $project = $db->loadObject();
  $type = $reg->projectType_id;
endif;

// verifica o limite do projeto
$query = 'SELECT COUNT(*) FROM '. $db->quoteName('#__zenite_registrations') .' WHERE project_id = '.$project->id.' AND state = 1';
$db->setQuery($query);
$subsTotal = $db->loadResult();

// verifica o acesso
$hasGroup = array_intersect($groups, $cfg['groupId']); // se está na lista de grupos permitidos
if($user->guest) : // se não estiver logado
	$app->redirect(JURI::root(true).'/login?return='.urlencode(base64_encode(JURI::current())));
	exit();
// se o registro já foi criado, não é um administrador nem o usuário que realizou a inscrição
elseif($insert == 0 && empty($hasGroup) && $reg->user_id != $user->id) :
	$app->redirect(JURI::root().'error');
	exit();
endif;

$priceTotal = $price = $discount = (float)0.00;
if(!empty($project->name)) :
  // dados da modalidade
  $query = '
    SELECT
      '. $db->quoteName('T1.id') .',
      '. $db->quoteName('T2.name') .' category,
      '. $db->quoteName('T3.name') .' disability,
      '. $db->quoteName('T1.disability_id') .' ,
      '. $db->quoteName('T1.distance') .',
      '. $db->quoteName('T1.distance_unit') .',
      '. $db->quoteName('T1.price') .',
      '. $db->quoteName('T1.limit') .'
    FROM
      '. $db->quoteName('#__zenite_projects_types') .' T1
      JOIN '. $db->quoteName('#__zenite_projects_categories') .' T2
      ON T2.id = T1.category_id
      LEFT JOIN '. $db->quoteName('#__zenite_disabilities') .' T3
      ON T3.id = T1.disability_id
    WHERE
    T1.id = '.$type.' AND T1.state = 1
  ';
  $db->setQuery($query);
  $categ = $db->loadObject();

  // dados do usuário
  $query = '
    SELECT
      '. $db->quoteName('T1.id') .',
      '. $db->quoteName('T1.cpf') .',
      '. $db->quoteName('T1.birthday') .',
      '. $db->quoteName('T1.gender') .',
      '. $db->quoteName('T1.phone_number') .',
      '. $db->quoteName('T1.address') .',
      '. $db->quoteName('T1.address_number') .',
      '. $db->quoteName('T1.address_info') .',
      '. $db->quoteName('T1.zip_code') .',
      '. $db->quoteName('T1.address_district') .',
      '. $db->quoteName('T1.address_city') .',
      '. $db->quoteName('T1.address_uf') .',
      '. $db->quoteName('T1.deficient') .',
      '. $db->quoteName('T2.name') .' deficient_type
    FROM
      '. $db->quoteName('#__zenite_user_info') .' T1
      LEFT JOIN '. $db->quoteName('#__zenite_disabilities') .' T2
      ON T2.id = T1.deficient_type
    WHERE
    T1.user_id = '.$user->id.'
  ';
  $db->setQuery($query);
  $userInfo = $db->loadObject();

  // valor
  $price = (isset($reg->price) && !empty($reg->price) && $reg->price != '0.00') ? $reg->price : $categ->price;

  // descontos
  if($request['discount'] > 0) :
    $discount = $price * ($request['discount'] / 100);
  else :
    $hasCoupon = false;
    if(!empty($request['coupon'])) : // cupom
      $query = '
        SELECT '. $db->quoteName('price') .'
        FROM '. $db->quoteName('#__zenite_projects_coupons') .'
        WHERE project_id = '.$project->id.' AND cod = '.$request['coupon'].' AND status = 0 AND state = 1
      ';
      $db->setQuery($query);
      $couponPrice = $db->loadResult();
      if($couponPrice != null) :
        $discount = ($couponPrice != '0.00') ? (float)$couponPrice : (float)($price * ($request['discountCounpon'] / 100));
        $hasCoupon = true;
      else :
        $discount = (float)0.00;
      endif;
    endif;
  endif;

  // total
  $discount = (isset($reg->discount) && !empty($reg->discount) && $reg->discount != '0.00') ? $reg->discount : $discount;
  $priceTotal = (float)($price - $discount);

else :

  $app->enqueueMessage('Este evento não existe ou não está mais disponível!', 'warning');
  $app->redirect(JURI::root(true));
  exit();

endif;

if($request['confirm'] == 1) : // recebe os dados do form

  if($request['pid'] != 0 && $request['pType'] != 0) :

    // verifica quantos dias faltam para o encerramento das inscrições
    $days = baseHelper::dateDiff('now', $project->end_date);
    // Prazo máx. de 2 dias, exceto se o encerramento for antes. Assim, fica a data do encerramento...
    $prazo = ($days['D'] < 2) ? $days['D'] : 2;
    $dueDate = date('Y-m-d', strtotime("now +$prazo days"));

    $query = '
      INSERT INTO '. $db->quoteName('#__zenite_registrations') .'('.
        $db->quoteName('user_id') .','.
        $db->quoteName('project_id') .','.
        $db->quoteName('projectType_id') .','.
        $db->quoteName('price') .','.
        $db->quoteName('extra_field') .','.
        $db->quoteName('sizeShirt') .','.
        $db->quoteName('team') .','.
        $db->quoteName('discount') .','.
        $db->quoteName('due_date') .','.
        $db->quoteName('created_by') .'
      ) VALUES ('.
        $user->id .','.
        $request['pid'] .','.
        $request['pType'] .','.
        $db->quote($price) .','.
        $db->quote($request['extraField']) .','.
        $db->quote($request['sizeShirt']) .','.
        $db->quote($request['team']) .','.
        $db->quote($discount) .','.
        $db->quote($dueDate) .','.
        $user->id .'
      )
    ';

    try {

      $db->setQuery($query);
      $db->execute();
      $id = $db->insertid();
      // disable coupon
      if($hasCoupon) :
        $query = 'UPDATE '. $db->quoteName('#__zenite_projects_coupons') .' SET '.$db->quoteName('status').' = 1, '.$db->quoteName('status_date').' = NOW(), '.$db->quoteName('user_id').' = '.$user->id.' WHERE cod = '.$db->quote($request['coupon']);
        $db->setQuery($query);
        $db->execute();
      endif;
      // page confirm
      $app->redirect(JURI::root().'subsConfirm?r='.urlencode(base64_encode($id)).'&c='.urlencode(base64_encode('1')));
  		exit();

    } catch (RuntimeException $e) {

      $app->enqueueMessage('<strong><span class="base-icon-attention"></span> Erro no envio dos dados!</strong><br />Por favor, verifique as informações e tente novamente.', 'warning');
  		$app->redirect(JURI::root().'eventos/inscricoes?p='.urlencode(base64_encode($request['pid'])));
  		exit();
    }
  else :
    $app->enqueueMessage('<strong><span class="base-icon-attention"></span> Erro no envio dos dados!</strong><br />Por favor, verifique as informações e tente novamente.', 'warning');
		$app->redirect(JURI::root().'eventos/inscricoes?p='.urlencode(base64_encode($request['pid'])));
		exit();
  endif;
endif;
?>

<form action="subsPayment" method="post" target="_blank">
  <input type="hidden" name="rid" value="<?php echo $id?>">
  <?php if($isConfirm && $priceTotal != 0.00) : ?>
    <div class="alert alert-success bottom-space-sm alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="base-icon-clock"> Sua Inscrição foi registrada. <span class="text-live">Mas ainda não foi confirmada!</span></h4>
      <p class="small top-space-xs">A sua inscrição só será confirmada após registrarmos o pagamento.<br />Para realizar o pagamento clique no botão "<strong class="text-success">Realizar Pagamento</strong>" localizado logo após dos dados do evento.</p>
    </div>
    <p class="alert alert-warning text-sm alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <strong>Importante:</strong><br />Em caso de boleto bancário, a confirmação poderá levar até 24hs após o pagamento para ser registrada no sistema e o pagamento não poderá ser realizado após o vencimento.
    </p>
  <?php else : ?>
    <?php if($reg->status == 2) : ?>
      <div class="alert alert-success bottom-space-sm alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="base-icon-ok"> Inscrição Realizada!</h4>
      </div>
      <?php $btnPay = ''; ?>
    <?php elseif($priceTotal != 0.00) : ?>
      <div class="alert alert-warning bottom-space-sm alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="base-icon-clock"> Estamos aguardando a confirmação o seu pagamento!</h4>
        <p class="small top-space-xs">Lembrando que a sua inscrição só será confirmada após registrarmos o pagamento.<br />Para realizar o pagamento clique no botão "<strong class="text-success">Realizar Pagamento</strong>" localizado logo após dos dados do evento.</p>
      </div>
    <?php elseif($priceTotal == 0.00) : ?>
      <?php if($reg->status < 2) : ?>
        <div class="alert alert-warning bottom-space-sm alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="base-icon-attention"> Você ainda não finalizou a sua inscrição!</h4>
          <p>Para finalizar a sua inscrição, verifique se os dados estão corretos e clique no botão "<strong>Finalizar</strong>".</p>
        </div>
      <?php else : ?>
        <div class="alert alert-warning bottom-space-sm alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="base-icon-attention"> Quase lá!</h4>
          <p>Para confirmar a sua inscrição, verifique se os dados estão corretos e clique no botão "<strong>Finalizar</strong>".</p>
        </div>
      <?php endif;?>
    <?php endif;?>
  <?php endif;?>

  <div class="row">
    <div class="col-md-5 bottom-space set-border-right border-default">
      <h4 class="base-icon-user strong text-sucess"> <?php echo baseHelper::nameFormat($user->name)?></h4>
      <ul class="list">
        <li><strong>CPF</strong>: <?php echo $userInfo->cpf?></li>
        <li><strong>Idade</strong>: <?php echo baseHelper::getAge($userInfo->birthday)?></li>
        <li><strong>Sexo</strong>: <?php echo ($userInfo->gender == 'M' ? 'Masculino' : 'Feminino')?></li>
        <li><strong>Celular</strong>: <?php echo $userInfo->phone_number?></li>
        <?php
        if($userInfo->deficient) :
          echo '<li><strong>Deficiência Física</strong>: '.$userInfo->deficient_type.'</li>';
        endif;
        ?>
        <li>
          <strong>Endereço</strong>:
          <?php echo baseHelper::nameFormat($userInfo->address).', '.$userInfo->address_number?>
          <?php echo !empty($userInfo->address_info) ? ', '.$userInfo->address_info : ''?><br />
          <?php echo !empty($userInfo->zip_code) ? $userInfo->zip_code.', ' : ''?>
          <?php echo !empty($userInfo->address_district) ? baseHelper::nameFormat($userInfo->address_district).', ' : ''?>
          <?php echo baseHelper::nameFormat($userInfo->address_city).', '.$userInfo->address_uf?>
        </li>
      </ul>
    </div>
    <div class="col-md-7">
      <h4 class="base-icon-check strong text-sucess"> <?php echo baseHelper::nameFormat($project->name)?></h4>
      <div class="row">
        <div class="col-sm-8">
          <ul class="list">
            <li><strong>Data</strong>: <?php echo baseHelper::dateFormat($project->date)?></li>
            <li><strong>Modalidade</strong>: <?php echo baseHelper::nameFormat($categ->category).' '.$categ->distance.($categ->distance_unit == 0 ? ' m' : ' Km')?></li>
            <?php
            if(!empty($reg->sizeShirt) || !empty($reg->team)) :
              echo '<li>';
              $float = 0;
              if(!empty($reg->sizeShirt)) :
                echo '<strong>Tam. da Camisa</strong>: '.$reg->sizeShirt;
                $float = 1;
              endif;
              if(!empty($reg->team)) :
                echo '<span class="cursor-help hasTooltip'.($float ? ' pull-right' : '').'" title="Equipe"><span class="base-icon-users text-live"></span> '.baseHelper::nameFormat($reg->team).'</span>';
              endif;
              echo '</li>';
            endif;
            ?>
          </ul>
        </div>
        <div class="col-sm-4 text-right set-border-left border-default">
          <ul class="list">
            <li><span class="pull-left">Valor:</span>R$ <?php echo baseHelper::priceFormat($price)?></li>
            <li><span class="pull-left">Desconto:</span>R$ <?php echo baseHelper::priceFormat($discount)?></li>
            <li class="strong text-sucess"><span class="pull-left">Total:</span>R$ <?php echo baseHelper::priceFormat($priceTotal)?></li>
          </ul>
        </div>
      </div>
      <?php if($priceTotal != 0.00) : ?>
        <div class="strong top-space text-danger"><span class="base-icon-attention text-live"></span> Inscrição válida até: <?php echo baseHelper::dateFormat($reg->due_date);?></div>
      <?php endif; ?>
      <hr class="hr-sm" />
      <div class="row">
        <div class="col-xs-3">
          <?php if($reg->status < 2) : ?>
            <a class="base-icon-left-big btn btn-default" href="eventos/inscricoes?p=<?php echo urlencode(base64_encode($project->id))?>"> Voltar</a>
          <?php else : ?>
            <a class="base-icon-print btn btn-success hidden-print" href="javascript:print()"> Imprimir</a>
          <?php endif; ?>
        </div>
        <div class="col-xs-9 text-right">
          <?php if($reg->status < 2) : ?>
            <button type="submit" name="btn-pay" id="btn-pay" class="btn btn-lg btn-success no-margin">
              <?php if($priceTotal != 0.00) : ?>
                Realizar Pagamento <span class="base-icon-right-big hidden-xs"></span>
              <?php else : ?>
                Finalizar <span class="base-icon-right-big hidden-xs"></span>
              <?php endif; ?>
            </button>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</form>
