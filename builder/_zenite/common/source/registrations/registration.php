<?php
defined('_JEXEC') or die;

$app = JFactory::getApplication('site');

// get current user's data
$user = JFactory::getUser();
if($user->guest) :

  $app->redirect(JURI::root(true).'/login?return='.urlencode(base64_encode(JURI::current())));
  exit();

else :

  // load Scripts
  $doc = JFactory::getDocument();
  $doc->addScript(JURI::base().'templates/base/js/forms.js');
  $doc->addScript(JURI::base().'templates/base/js/validate.js');
  // carrega Jquery.ui datepicker;
  $doc->addStyleSheet(JURI::base().'templates/base/core/libs/jquery/jquery-ui.min.css');
  $doc->addScript(JURI::base().'templates/base/core/libs/jquery/jquery-ui.min.js');
  // IMPORTANTE: Carrega o arquivo 'helper' do template
  JLoader::register('baseHelper', JPATH_BASE.'/templates/base/source/helpers/base.php');

  // database connect
  $db = JFactory::getDbo();

  //joomla get request data
  $input      = $app->input;

    // fields 'Form' requests
    $request                  = array();
    // default
    $request['p']             = $input->get('p', '', 'string');
    $request['m']             = $input->get('m', '', 'string');
    $request['pr']            = $input->get('pr', 0, 'int'); // seleciona o projeto
    $pid = ($request['pr'] > 0) ? $request['pr'] : base64_decode($request['p']);
    // User data
    $request['sent']          = $input->post->get('sent', 0, 'int');
    $request['id']            = $input->post->get('itemId', 0, 'int');
    $request['user_id']       = $input->post->get('user_id', 0, 'int');
    $request['cpf']           = $input->post->get('cpf', '', 'string');
    $request['birthday']      = $input->post->get('birthday', '', 'string');
    $request['gender']        = $input->post->get('gender', '', 'string');
    $request['phone_number']  = $input->post->get('phone_number', '', 'string');
    $request['deficient']     = $input->post->get('deficient', '', 'string');
    $request['deficient_type']= $input->post->get('deficient_type', '', 'string');
    $request['zip_code']      = $input->post->get('zip_code', '', 'string');
    $request['address']       = $input->post->get('address', '', 'string');
    $request['address_number']  = $input->post->get('address_number', '', 'string');
    $request['address_info']    = $input->post->get('address_info', '', 'string');
    $request['address_district']= $input->post->get('address_district', '', 'string');
    $request['address_city']    = $input->post->get('address_city', '', 'string');
    $request['address_uf']      = $input->post->get('address_uf', '', 'string');
    // Registration data
    $request['sent']          = $input->post->get('sent', 0, 'int');
    $request['pType']         = $input->post->get('pType', 0, 'int');

  $isUpdated = false;
  if($request['sent'] == 1) : // recebe os dados do form

    if($request['id'] == 0) :
      $query = '
        INSERT INTO '. $db->quoteName('#__zenite_user_info') .'('.
          $db->quoteName('user_id') .','.
          $db->quoteName('cpf') .','.
          $db->quoteName('birthday') .','.
          $db->quoteName('gender') .','.
          $db->quoteName('phone_number') .','.
          $db->quoteName('deficient') .','.
          $db->quoteName('deficient_type') .','.
          $db->quoteName('zip_code') .','.
          $db->quoteName('address') .','.
          $db->quoteName('address_number') .','.
          $db->quoteName('address_info') .','.
          $db->quoteName('address_district') .','.
          $db->quoteName('address_city') .','.
          $db->quoteName('address_uf') .'
        ) VALUES ('.
          $request['user_id'] .','.
          $db->quote($request['cpf']) .','.
          $db->quote($request['birthday']) .','.
          $db->quote($request['gender']) .','.
          $db->quote($request['phone_number']) .','.
          $db->quote($request['deficient']) .','.
          $db->quote($request['deficient_type']) .','.
          $db->quote($request['zip_code']) .','.
          $db->quote($request['address']) .','.
          $db->quote($request['address_number']) .','.
          $db->quote($request['address_info']) .','.
          $db->quote($request['address_district']) .','.
          $db->quote($request['address_city']) .','.
          $db->quote($request['address_uf']) .'
        )
      ';
      $msg = '1Srt';
    elseif($request['cpf']) :
      $query  = '
        UPDATE '.$db->quoteName('#__zenite_user_info').' SET '.
          $db->quoteName('user_id') 	.'='. $request['user_id'] .','.
          $db->quoteName('cpf') 	.'='. $db->quote($request['cpf']) .','.
          $db->quoteName('birthday') 	.'='. $db->quote($request['birthday']) .','.
          $db->quoteName('gender') 	.'='. $db->quote($request['gender']) .','.
          $db->quoteName('phone_number') 	.'='. $db->quote($request['phone_number']) .','.
          $db->quoteName('deficient') 	.'='. $db->quote($request['deficient']) .','.
          $db->quoteName('deficient_type') 	.'='. $db->quote($request['deficient_type']) .','.
          $db->quoteName('zip_code') 	.'='. $db->quote($request['zip_code']) .','.
          $db->quoteName('address') 	.'='. $db->quote($request['address']) .','.
          $db->quoteName('address_number') 	.'='. $db->quote($request['address_number']) .','.
          $db->quoteName('address_info') 	.'='. $db->quote($request['address_info']) .','.
          $db->quoteName('address_district') 	.'='. $db->quote($request['address_district']) .','.
          $db->quoteName('address_city') 	.'='. $db->quote($request['address_city']) .','.
          $db->quoteName('address_uf') 	.'='. $db->quote($request['address_uf']) .'
        WHERE '.
          $db->quoteName('id') .'='. $request['id']
      ;
      $msg = '2Udt';
    endif;

    try {

      $db->setQuery($query);
      $db->execute();

      $app->redirect(JURI::current().'?p='.urlencode(base64_encode($request['pr'])).'&m='.$msg.'#modalidade');
      exit();

    } catch (RuntimeException $e) {

      echo '
      <div class="alert alert-error">
        <h4><span class="base-icon-attention"></span> Erro no envio dos dados!</h4>
        <p>Por favor, verifique as informações e tente novamente.</p>
      </div>
      ';
      $isUpdated = false;
    }

  endif;

  // dados do usuário
  $query = 'SELECT * FROM '. $db->quoteName('#__zenite_user_info') .' WHERE user_id = '.$user->id;
  $db->setQuery($query);
  $item = $db->loadObject();
  // deficiências
  $query = 'SELECT * FROM '. $db->quoteName('#__zenite_disabilities') .' WHERE state = 1 AND id != 1 ORDER BY name';
  $db->setQuery($query);
  $disabilities = $db->loadObjectList();
  // eventos disponíveis
  $query = 'SELECT * FROM '. $db->quoteName('#__zenite_projects') .' WHERE date >= CURDATE() AND start_date <= NOW() AND end_date > NOW() AND state = 1 ORDER BY date ASC';
  $db->setQuery($query);
  $projects = $db->loadObjectList();
  // verifica se o evento existe e está disponível
  if($pid > 0) :
    $query = 'SELECT * FROM '. $db->quoteName('#__zenite_projects') .' WHERE '. $db->quoteName('start_date') .' <= NOW() AND '. $db->quoteName('end_date') .' >= NOW() AND date >= CURDATE() AND state = 1 AND id = '.$pid;
    $db->setQuery($query);
    $project = $db->loadObject();

    if(!empty($project->url_registration)) :
      echo '
        <div class="alert alert-info">
          <h4>'.$project->name.'</h4>
          Para realizar a inscrição nesse evento, acesse o endereço abaixo:<hr class="hr-sm" />
          <a href="'.$project->url_registration.'" target="_blank" class="strong new-window">'.$project->url_registration.'</a>
        </div>
      ';
      return;
    endif;
  endif;

  if(!empty($project->name)) :
    $query = '
      SELECT
        '. $db->quoteName('T1.id') .',
        '. $db->quoteName('T2.name') .' category,
        '. $db->quoteName('T3.name') .' project,
        '. $db->quoteName('T3.date') .' projectDate,
        '. $db->quoteName('T4.name') .' disability,
        '. $db->quoteName('T1.disability_id') .' ,
        '. $db->quoteName('T1.distance') .',
        '. $db->quoteName('T1.distance_unit') .',
        '. $db->quoteName('T1.gender') .',
        '. $db->quoteName('T1.description') .',
        '. $db->quoteName('T1.min_age') .',
        '. $db->quoteName('T1.max_age') .',
        '. $db->quoteName('T1.age_type') .',
        '. $db->quoteName('T1.price') .',
        '. $db->quoteName('T1.limit') .',
        '. $db->quoteName('T1.link_map') .',
        '. $db->quoteName('T1.state') .'
      FROM
        '. $db->quoteName('#__zenite_projects_types') .' T1
        JOIN '. $db->quoteName('#__zenite_projects_categories') .' T2
        ON T2.id = T1.category_id
        JOIN '. $db->quoteName('#__zenite_projects') .' T3
        ON T3.id = T1.project_id
        LEFT JOIN '. $db->quoteName('#__zenite_disabilities') .' T4
        ON T4.id = T1.disability_id
      WHERE
      T1.project_id = '.$pid.' AND T1.state = 1 ORDER BY T2.name, T1.distance_unit, T1.distance;
    ';
    $db->setQuery($query);
    $db->execute();
    $num_rows = $db->getNumRows();
    $types = $db->loadObjectList();
  endif;

  if(!empty($item->id)) :
    $request['cpf']             = $item->cpf;
    $request['birthday']        = $item->birthday;
    $request['gender']          = $item->gender;
    $request['phone_number']    = $item->phone_number;
    $request['deficient']       = $item->deficient;
    $request['deficient_type']  = $item->deficient_type;
    $request['zip_code']        = $item->zip_code;
    $request['address']         = $item->address;
    $request['address_number']  = $item->address_number;
    $request['address_info']    = $item->address_info;
    $request['address_district']= $item->address_district;
    $request['address_city']    = $item->address_city;
    $request['address_uf']      = $item->address_uf;
  endif;

  ?>

  <script>
  jQuery(function() {

    // coupon
    // verifica o cupom
    window.validaCoupon = function(pID) {

      var pCOD = jQuery('#user-coupon').val();
      if(pCOD != '') {
        cod = '&pID='+pID+'&pCOD='+pCOD;
      } else {
        alert('Por favor, informe o código do cupom!');
        return false;
      }
      jQuery('#coupon-status').empty(); // reseta a mensagem de validação
      jQuery('#coupon-loader').removeClass('hide'); // inicia o loader

      jQuery.ajax({
        url: "<?php echo JURI::root()?>templates/base/source/projectsCoupons/projectsCoupons.model.php?aTag=projectsCoupons&task=verify"+cod,
        dataType: 'json',
        type: 'POST',
        cache: false,
        success: function(data){
          jQuery('#coupon-loader').addClass('hide'); // encerra o loader
          jQuery.map( data, function( res ) {
            if(res.status == 1) {
              jQuery('#coupon-status').html('<span class="base-icon-ok text-success"> O código do cupom é válido!</span>');
            } else {
              jQuery('#coupon-status').html('<span class="base-icon-cancel text-danger"> O código do cupom é inválido!</span>');
              console.log(res.msg);
            }
          });
        },
        error: function(xhr, status, error) {
          console.log(xhr);
          console.log(status);
          console.log(error);
          jQuery('#coupon-loader').addClass('hide'); // encerra o loader
        }
      });
      return false;
    };

    // valida o form de confirmação
    window.validaForm = function() {
      var ptype = jQuery('input:radio[name=pType]');
      if(!ptype.is(':checked')) {
        alert('Por favor, informe a modalidade!');
        return false;
      }
      var sizeShirt = jQuery('#user-sizeShirt');
      if(sizeShirt.length && sizeShirt.val() == '') {
        alert('Por favor, informe o tamanho da camisa!');
        sizeShirt.focus();
        return false;
      }
    };

  });
  </script>

  <div class="row">
    <div class="col-md-5 col-lg-6">
      <?php
      $txt = '';
      if(!empty($request['m'])) :
        $txt = ($request['m'] == '1Srt') ? 'cadastrados' : 'atualizados';
      endif;
      ?>
      <div class="alert alert-<?php echo (!empty($request['m']) ? 'success' : 'info')?> strong small">
        <span class="<?php echo (!empty($request['m']) ? 'base-icon-ok text-success' : 'base-icon-circle text-live')?>"> Passo 1</span>
        <span class="base-icon-angle-double-right"></span> <?php echo (empty($item->id) ? 'Informe seus dados pessoais' : (!empty($request['m']) ? 'Dados '.$txt.' com sucesso!' : 'Verifique seus dados pessoais'))?>
      </div>

      <form action="<?php echo JURI::root()?>eventos/inscricoes" name="form-userInfo" id="form-userInfo" method="post">
        <input type="hidden" name="pr" id="userInfo-pr" value="<?php echo $pid?>" />
        <input type="hidden" name="sent" id="userInfo-sent" value="1" />
        <input type="hidden" name="itemId" id="userInfo-itemId" value="<?php echo $item->id?>" />
        <input type="hidden" name="user_id" id="userInfo-user_id" value="<?php echo $user->id?>" />
      	<fieldset class="fieldset-embed">
          <legend>Dados Pessoais</legend>
      		<div class="row">
      			<div class="col-sm-4 col-md-6 col-lg-4">
      				<div class="form-group field-required">
      					<label>CPF</label><br />
      					<input type="text" name="cpf" id="userInfo-cpf" value="<?php echo $request['cpf']?>" class="field-cpf" />
      				</div>
      			</div>
      			<div class="col-sm-4 col-md-6 col-lg-4">
      				<div class="form-group field-required">
      					<label>Data de Nascimento</label>
      					<input type="text" name="birthday" id="userInfo-birthday" value="<?php echo $request['birthday']?>" class="form-control field-date" data-convert="true" />
      				</div>
      			</div>
            <div class="col-sm-4 col-md-6 col-lg-4">
      				<div class="form-group field-required">
      					<label>Sexo</label>
      					<span class="btn-group btn-group-justified" data-toggle="buttons">
      						<label class="btn btn-default btn-active-success <?php echo ($request['gender'] == 'M' ? 'active' : '')?>">
      							<input type="radio" name="gender" id="userInfo-gender-0" <?php echo ($request['gender'] == 'M' ? 'checked' : '')?> value="M" class="auto-tab" data-target="userInfo-phone_number" />
      							Masc.
      						</label>
      						<label class="btn btn-default btn-active-success <?php echo ($request['gender'] == 'F' ? 'active' : '')?>">
      							<input type="radio" name="gender" id="userInfo-gender-1" <?php echo ($request['gender'] == 'F' ? 'checked' : '')?> value="F" class="auto-tab" data-target="userInfo-phone_number" />
      							Fem.
      						</label>
      					</span>
      				</div>
      			</div>
            <div class="col-sm-4 col-md-6 col-lg-4">
      				<div class="form-group field-required">
      					<label>Celular</label>
      					<input type="text" name="phone_number" id="userInfo-phone_number" value="<?php echo $request['phone_number']?>" class="form-control field-phone" data-toggle-mask="false" />
      				</div>
      			</div>
            <div class="col-sm-4 col-md-6 col-lg-4">
      				<div class="form-group field-required">
      					<label>Deficiente?</label>
      					<span class="btn-group btn-group-justified" data-toggle="buttons">
      						<label class="btn btn-default btn-active-success <?php echo ($request['deficient'] == 0 ? 'active' : '')?>" onclick="clearUserInfoValidator()">
      							<input type="radio" name="deficient" id="userInfo-deficient-0" <?php echo ($request['deficient'] == 0 ? 'checked' : '')?> value="0" checked class="auto-tab" data-target="deficientType-group" data-target-display="false" data-target-value="0" />
      							Não
      						</label>
      						<label class="btn btn-default btn-active-success <?php echo ($request['deficient'] == 1 ? 'checked' : '')?>">
      							<input type="radio" name="deficient" id="userInfo-deficient-1" <?php echo ($request['deficient'] == 1 ? 'checked' : '')?> value="1" class="auto-tab" data-target="deficientType-group" data-target-display="true" />
      							Sim
      						</label>
      					</span>
      				</div>
      			</div>
      			<div class="col-sm-4 col-md-6 col-lg-4">
      				<div id="deficientType-group" class="form-group field-required<?php echo ($request['deficient'] == 0 ? ' hide' : '')?>">
      					<label>Tipo de Deficiência</label>
                <select name="deficient_type" id="userInfo-deficient_type" class="form-control">
  								<option value="0">- Selecione -</option>
  								<?php
  									foreach ($disabilities as $obj) {
  										echo '<option value="'.$obj->id.'"'.($request['deficient_type'] == $obj->id ? ' selected' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
  									}
  								?>
  							</select>
      				</div>
      			</div>
          </div>
          <hr class="hr-sm hr-label" />
          <span class="label label-warning">Endereço</span>
          <div class="row">
            <div class="col-sm-3 col-md-4 col-lg-3">
    					<div class="form-group">
    						<label>CEP</label>
    						<input type="text" name="zip_code" id="<?php echo $APPTAG?>-zip_code" value="<?php echo $request['zip_code']?>" class="form-control field-cep" />
    					</div>
    				</div>
    				<div class="col-sm-6 col-md-8 col-lg-9">
    					<div class="form-group field-required">
    						<label>Logradouro</label>
    						<input type="text" name="address" id="<?php echo $APPTAG?>-address" value="<?php echo $request['address']?>" class="form-control upper" />
    					</div>
    				</div>
    				<div class="col-sm-3 col-md-4 col-lg-3">
    					<div class="form-group field-required">
    						<label>N&ordm;</label>
    						<input type="text" name="address_number" id="<?php echo $APPTAG?>-address_number" value="<?php echo $request['address_number']?>" class="form-control upper" />
    					</div>
    				</div>
    				<div class="col-sm-12 col-md-8 col-lg-9">
    					<div class="form-group">
    						<label>Complemento</label>
    						<input type="text" name="address_info" id="<?php echo $APPTAG?>-address_info" value="<?php echo $request['address_info']?>" class="form-control" />
    					</div>
    				</div>
    				<div class="col-sm-3 col-md-4 col-lg-3">
    					<div class="form-group field-required">
    						<label>UF</label>
    						<input type="text" name="address_uf" id="<?php echo $APPTAG?>-address_uf" value="<?php echo $request['address_uf']?>" class="form-control upper" />
    					</div>
    				</div>
    				<div class="col-sm-5 col-md-8 col-lg-5">
    					<div class="form-group field-required">
    						<label>Cidade</label>
    						<input type="text" name="address_city" id="<?php echo $APPTAG?>-address_city" value="<?php echo $request['address_city']?>" class="form-control upper" />
    					</div>
    				</div>
    				<div class="col-sm-4 col-md-12 col-lg-4">
    					<div class="form-group">
    						<label>Bairro</label>
    						<input type="text" name="address_district" id="<?php echo $APPTAG?>-address_district" value="<?php echo $request['address_district']?>" class="form-control upper" />
    					</div>
    				</div>
      		</div>
          <div class="form-actions text-right">
            <span class="base-icon-down-big visible-xs pull-left top-space-sm"></span>
            <span class="base-icon-down-big visible-sm pull-left top-space-sm"> Caso seus estejam atualizados siga para o <strong>passo 2</strong></span>
            <button type="submit" name="btn-userInfo-save" id="btn-userInfo-save" class="btn btn-primary no-margin">
              <?php echo (empty($item->id) ? '<span class="base-icon-right-big"></span> Salvar' : '<span class="base-icon-arrows-cw"></span> Atualizar')?> dados pessoais
            </button>
          </div>
      	</fieldset>
      </form>
    </div>
    <?php if($isUpdated || !empty($item->id)) : ?>
      <div class="col-md-7 col-lg-6">
        <hr class="visible-xs visible-sm"/>

        <?php if(!empty($item->id)) : ?>
          <div id="modalidade" class="alert alert-info strong small">
            <span class="<?php echo ($isUpdated ? 'base-icon-circle' : 'base-icon-circle-empty')?> text-live"> Passo 2</span> <span class="base-icon-angle-double-right"></span> Selecione a modalidade
          </div>
        <?php endif?>

        <form action="<?php echo JURI::root().'subsConfirm'?>" name="form-setItem" id="form-setItem" method="post" onsubmit="return validaForm()">
          <input type="hidden" name="confirm" value="1" />
          <?php if(empty($pid) || $pid == 0 || $request['pr'] > 0) :?>
            <div class="form-group field-required">
              <label>Selecione o Evento</label>
              <select name="pid" id="setItem-pid" class="form-control input-lg">
                <option value="0">- Selecione -</option>
                <?php
                  foreach ($projects as $obj) {
                    echo '<option value="'.$obj->id.'"'.($pid == $obj->id ? ' selected' : '').'>'.baseHelper::nameFormat($obj->name).' - '.baseHelper::dateFormat($obj->date).'</option>';
                  }
                ?>
              </select>
            </div>
          <?php elseif(!empty($project->name)) :?>
            <div class="well well-sm no-margin field-required">
              <h4 class="no-margin text-success"><span class="base-icon-check text-live"></span> <?php echo baseHelper::nameFormat($project->name)?> <small class="font-featured">(<?php echo baseHelper::dateFormat($project->date)?>)</small></h4>
              <input type="hidden" name="pid" id="setItem-pid" value="<?php echo $pid?>">
            </div>
          <?php endif;?>
          <?php if($pid > 0 && !empty($project->name)) : ?>

            <?php
            // verifica o limite do projeto
            $query = 'SELECT COUNT(*) FROM '. $db->quoteName('#__zenite_registrations') .' WHERE project_id = '.$pid.' AND state = 1';
            $db->setQuery($query);
            $subsTotal = $db->loadResult();
            if($subsTotal < $project->limit) :
              if($num_rows > 0) :
            ?>
                <div class="table-responsive">
                  <table class="table table-striped table-hover bottom-space-sm">
                    <thead>
                      <tr>
                        <th width="30"><span class="base-icon-down-big"></span></th>
                        <th>Selecione a Modalidade</th>
                        <th>Grupo</th>
                        <th width="100">Valor</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $hasCategory = $counter = 0;
                      foreach ($types as $item) {

                        // verifica se o usuário está dentro da faixa etária
                        $ageLimit = ($item->max_age == 0 ? 1000 : $item->max_age);
                        if($item->age_type == 1) :
                          $age = baseHelper::dateDiff($request['birthday'], $project->date);
                        else :
                          $dt = explode('-', $project->date);
                          $age = baseHelper::dateDiff($request['birthday'], $dt[0].'-12-31');
                        endif;
                        $hasAge = ($age['y'] >= $item->min_age && $age['y'] <= $ageLimit) ? true : false;
                        // verifica se o usuário faz parte da modalidade
                        $hasCat = 0;
                        // se o usuário for deficiente o tipo for para deficientes
                        if($request['deficient'] == 1 && $item->disability_id != 0) :
                          // se for para qualquer tipo de deficiência
                          if($item->disability_id == 1) $hasCat = 1;
                          // se for a mesma deficiência do usuário
                          elseif($request['deficient_type'] == $item->disability_id) $hasCat = 1;
                          else $hasCat = 0;
                        // se não for deficiente e o tipo não for para deficientes
                        elseif($request['deficient'] == 0 && $item->disability_id == 0) :
                          $hasCat = 1;
                        endif;
                        // verifica o sexo do participante
                        $gender = ($request['gender'] == 'M') ? 1 : 2;
                        $hasGender = ($item->gender == 0 || $item->gender == $gender) ? true : false;

                        $price = (float)$item->price;
                        $discount = (float)0.00;
                        $discountDeficient = $discountAge = false;
                        // verifica se há desconto por deficiencia
                        if($project->discount_deficient > 0 && $request['deficient'] == 1) :
                          $discount = $price * ($project->discount_deficient / 100);
                          $price -= $discount;
                          $discountDeficient = true;
                        endif;
                        // verifica se há desconto por idade
                        if($project->discount_age > 0 && $age['y'] >= 60) :
                          $discount = $price * ($project->discount_age / 100);
                          $price -= $discount;
                          $discountAge = true;
                        endif;

                        // verifica se já está registrado
                        $query = 'SELECT * FROM '. $db->quoteName('#__zenite_registrations') .' WHERE project_id = '.$project->id.' AND projectType_id = '.$item->id.' AND user_id = '.$user->id.' AND state = 1';
                        $db->setQuery($query);
                        $reg = $db->loadObject();
                        $class = '';
                        if($reg->id == null) :
                          $val = '<strong>R$ '.baseHelper::priceFormat($price).'</strong>';
                        else :
                          if($reg->status < 2) :
                            $lbl = '<span class="base-icon-clock btn-icon"></span> Pagar';
                            $ttp = 'Aguardando Confirmação<br />do Pagamento';
                            if($price == 0.00) :
                              $lbl = 'Finalizar';
                              $ttp = 'Você ainda não finalizou a sua inscrição.<br />Clique aqui para finalizar';
                            endif;
                            $val = '<a href="'.JURI::root().'subsConfirm?r='.urlencode(base64_encode($reg->id)).'" class="btn btn-sm btn-warning btn-block hasTooltip" title="'.$ttp.'">'.$lbl.'</a>';
                            $class = ' class="warning text-live"';
                          else :
                            $val = '<div class="base-icon-ok text-success strong"> Inscrito!</div>';
                            $class = ' class="success text-success"';
                          endif;
                        endif;

                        if($hasAge && $hasCat && $hasGender) :

                          $hasCategory = 1;
                          $distance_unit = ($item->distance_unit == 0 ? ' m' : ' Km');
                      		if($item->max_age == 0) :
                      			$faixa = 'À partir dos '.$item->min_age.' anos';
                      		else :
                      			$faixa = 'Dos '.$item->min_age.' aos '.$item->max_age.' anos';
                      		endif;

                          $check = ($reg->id == null) ? '<input type="radio" name="pType" id="user-pType-'.$counter.'" value="'.$item->id.'" class="auto-tab" data-target="#setItem-confirm-group" data-target-display="true" />' : '<span class="base-icon-ok"></span>';

                          // verifica o limite da modalidade
                          $query = 'SELECT COUNT(*) FROM '. $db->quoteName('#__zenite_registrations') .' WHERE projectType_id = '.$item->id.' AND state = 1';
                          $db->setQuery($query);
                          $subsTotal = $db->loadResult();
                          if($item->limit > 0 && $subsTotal >= $item->limit) :

                            echo '
                              <tr>
                                <td><span class="base-icon-cancel text-danger"></span></td>
                                <td colspan="3">
                                  '.baseHelper::nameFormat($item->category).' - '.$item->distance.$distance_unit.
                                  '<br /><small class="text-danger strong font-featured cursor-help">Inscrições Esgotadas</small>
                                </td>
                              </tr>
                            ';

                          else :

                            echo '
                              <tr'.$class.'>
                                <td>'.$check.'</td>
                                <td>
                                  '.baseHelper::nameFormat($item->category).' - '.$item->distance.$distance_unit.'
                                  '.($item->limit == 0 ? '' : '<br /><small class="text-muted font-featured cursor-help hasTooltip" title="Limite de inscrições na modalidade">Limite: '.$item->limit.'</small>').'
                                </td>
                                <td>
                                  '.(!empty($item->disability) ? '<div>'.baseHelper::nameFormat($item->disability).'</div>' : '').'
                                  <small class="text-muted font-featured">'.$faixa.'</small>
                                </td>
                                <td>'.$val.'</td>
                              </tr>
                            ';

                          endif;

                        endif;
                        $counter++;

                      }
                      if(!$hasCategory && $request['deficient'] == 1) :
                        echo '
                          <tr>
                            <td colspan="5">
                              <div class="alert alert-warning text-sm no-margin">
                                <p class="base-icon-attention strong bottom-space-xs"> Desculpe-nos!</p>
                                Não há modalidades disponíveis para seu tipo de deficiência.
                              </div>
                            </td>
                          </tr>
                        ';
                      endif;
                    ?>
                    </tbody>
                  </table>
                </div>
                <?php if($hasCategory) : ?>
                  <?php if($discountAge) : ?>
                    <div class="alert alert-success small no-margin">
                      <span class="base-icon-award"></span> A partir dos <strong>60 anos</strong>, você recebe um desconto especial de <strong><?php echo $project->discount_age?>%</strong> no valor da inscrição!
                      <input type="hidden" name="discount" name="user-discount" value="<?php echo $project->discount_age?>" />
                    </div>
                  <?php elseif($discountDeficient) : ?>
                    <div class="alert alert-success small no-margin">
                      <span class="base-icon-award"></span> Por ser deficiente, você recebe um desconto especial de <strong><?php echo $project->discount_deficient?>%</strong> no valor da inscrição!
                      <input type="hidden" name="discount" name="user-discount" value="<?php echo $project->discount_deficient?>" />
                    </div>
                  <?php endif; ?>
                  <div id="setItem-confirm-group" class="hide">
                    <?php if($project->extra_field == 1) : ?>
                      <hr class="hr-xs" />
                      <div class="row">
                        <div class="col-sm-6">
                          <div class="form-group<?php echo ($project->extra_field_required == 1 ? ' field-required' : '')?> bottom-space-xs">
                            <label><?php echo $project->extra_field_label?></label>
                            <input type="text" name="extraField" id="extra-field" class="form-control upper" maxlength="50" />
                          </div>
                        </div>
                      </div>
                    <?php endif; ?>
                    <hr class="hr-xs" />
                    <div class="row">
                      <?php if(!empty($project->sizeShirts)) : ?>
                        <div class="col-sm-5">
                          <div class="form-group bottom-space-xs">
                            <label class="field-required">Tamanho da camiseta</label>
                            <div class="input-group">
                              <select name="sizeShirt" id="user-sizeShirt" style="width:70px">
                                <option value="">- Selecione -</option>
                                <?php
                                $sizes = explode(',', $project->sizeShirts);
                                foreach($sizes as $size) {
                                  echo '<option value="'.$size.'">'.$size.'</option>';
                                }
                                ?>
                              </select>
                            </div>
                          </div>
                        </div>
                      <?php endif; ?>
                      <div class="col-sm-7">
                        <?php if(!$discountAge && !$discountDeficient) : ?>
                          <div class="form-group bottom-space-xs">
                            <label>Participa por alguma equipe?</label>
                            <div class="row">
                              <div class="col-sm-5">
                                <span class="btn-group btn-group-justified" data-toggle="buttons">
                      						<label class="btn btn-default btn-active-danger active">
                      							<input type="radio" name="isTeam" id="user-isTeam-0" checked value="0" class="auto-tab" data-target="user-team" data-target-display="false" data-target-value="" />
                      							Não
                      						</label>
                      						<label class="btn btn-default btn-active-success auto-tab" data-target="" data-target-display="false">
                      							<input type="radio" name="isTeam" id="user-isTeam-1" value="1" class="auto-tab" data-target="user-team" data-target-display="true" data-target-value="" />
                      							Sim
                      						</label>
                      					</span>
                              </div>
                              <div class="col-sm-7">
                                <input type="text" name="team" id="user-team" class="form-control upper hide" maxlength="50" placeholder="Nome da equipe" />
                              </div>
                            </div>
                          </div>
                        <?php endif; ?>
                      </div>
                    </div>
                    <hr class="hr-xs" />
                    <div class="row">
                      <div class="col-sm-6">
                        <?php if(!$discountAge && !$discountDeficient) : ?>
                          <div class="form-group bottom-space-xs">
                            <label>Possui Cupom de Desconto?</label>
                            <div class="input-group">
                              <input type="text" name="coupon" id="user-coupon" class="form-control" placeholder="N&ordm; do Cupom" />
                              <input type="hidden" name="discountCounpon" name="user-discountCounpon" value="<?php echo $project->discount_coupon?>" />
                              <span class="input-group-btn">
                                <button type="button" class="btn btn-primary hasTooltip" onclick="validaCoupon('<?php echo $project->id?>')" title="Clique para verificar se o cupom é válido">
                                  Verificar
                                </button>
                              </span>
                            </div>
                          </div>
                          <span id="coupon-loader" class="ajax-loader hide"></span>
                          <span id="coupon-status"></span>
                        <?php endif; ?>
                      </div>
                      <div class="col-sm-6">
                        <div class="form-actions text-right">
                          <button type="submit" name="btn-setItem-confirm" id="btn-setItem-confirm" class="btn btn-lg btn-success">
                            Confirmar <span class="base-icon-right-big"></span>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
              <?php else : ?>
                <div class="base-icon-attention alert alert-warning small top-space-xs"> Não existem modalidades cadastradas para esse evento!</div>
              <?php endif; ?>
            <?php else : ?>
              <div class="base-icon-attention alert alert-warning top-space"> Inscrições esgotadas para esse evento!</div>
            <?php endif; ?>
          <?php else : ?>
            <div class="base-icon-attention alert alert-warning top-space"> Por favor, selecione o evento!</div>
          <?php endif; ?>
        </form>
      </div>
    <?php endif;?>
  </div>
  <script>
  jQuery(window).load(function() {

    jQuery("#form-userInfo").on('submit', function() {
      dateConvert();
    });

    jQuery("#setItem-pid").on('change', function() {
      location.href = '<?php echo JURI::current()?>?pr='+jQuery(this).val();
    });

    // Validate User Data
    window.userInfoValidator = jQuery("#form-userInfo").validate({
      rules: {
        //CUSTOM VALIDATIONS
        // deficient_type: {
  			// 	requiredId: function(element) {
        //     return false;
        //   }
  			// }
      },
      //don't remove this
      invalidHandler: function(event, validator) {
        //if there is error,
        //set custom preferences
      }
    });
    window.clearUserInfoValidator = function() {
      userInfoValidator.resetForm();
    };

    // Validate Registration
    jQuery("#form-setItem").validate({
      rules: {
        //CUSTOM VALIDATIONS
      },
      //don't remove this
      invalidHandler: function(event, validator) {
        //if there is error,
        //set custom preferences
      }
    });

  });
  </script>
<?php endif; ?>
