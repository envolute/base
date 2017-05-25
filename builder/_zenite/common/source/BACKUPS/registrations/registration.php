<?php
defined('_JEXEC') or die;

$app = JFactory::getApplication('site');

// get current user's data
$user = JFactory::getUser();

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
  $request['pid']           = $input->get('pid', 0, 'int');
  // User data
  $request['sent']          = $input->get('sent', 0, 'int');
  $request['id']            = $input->get('itemId', 0, 'int');
  $request['user_id']       = $input->get('user_id', 0, 'int');
  $request['cpf']           = $input->get('cpf', '', 'string');
  $request['birthday']      = $input->get('birthday', '', 'string');
  $request['gender']        = $input->get('gender', '', 'string');
  $request['phone_number']  = $input->get('phone_number', '', 'string');
  $request['deficient']     = $input->get('deficient', '', 'string');
  $request['deficient_type']= $input->get('deficient_type', '', 'string');
  // Registration data$request['sent']          = $input->get('sent', 0, 'int');
  $request['pType']         = $input->get('pType', 0, 'int');

// dados do usuário
$query = 'SELECT * FROM '. $db->quoteName('#__zenite_user_info') .' WHERE user_id = '.$user->id;
$db->setQuery($query);
$item = $db->loadObject();
// deficiências
$query = 'SELECT * FROM '. $db->quoteName('#__zenite_disabilities') .' WHERE state = 1 AND id != 1 ORDER BY name';
$db->setQuery($query);
$disabilities = $db->loadObjectList();
// eventos disponíveis
$query = 'SELECT * FROM '. $db->quoteName('#__zenite_projects') .' WHERE date >= NOW() AND state = 1 ORDER BY date ASC';
$db->setQuery($query);
$projects = $db->loadObjectList();
// verifica se o evento existe e está disponível
if($request['pid'] > 0) :
  $query = 'SELECT * FROM '. $db->quoteName('#__zenite_projects') .' WHERE date >= NOW() AND state = 1 AND id = '.$request['pid'];
  $db->setQuery($query);
  $project = $db->loadObject();
endif;

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
        $db->quoteName('deficient_type') .'
      ) VALUES ('.
        $request['user_id'] .','.
        $db->quote($request['cpf']) .','.
        $db->quote($request['birthday']) .','.
        $db->quote($request['gender']) .','.
        $db->quote($request['phone_number']) .','.
        $db->quote($request['deficient']) .','.
        $db->quote($request['deficient_type']) .'
      )
    ';
    $txt = 'cadastrados';
  elseif($request['cpf']) :
    $query  = '
      UPDATE '.$db->quoteName('#__zenite_user_info').' SET '.
        $db->quoteName('user_id') 	.'='. $request['user_id'] .','.
        $db->quoteName('cpf') 	.'='. $db->quote($request['cpf']) .','.
        $db->quoteName('birthday') 	.'='. $db->quote($request['birthday']) .','.
        $db->quoteName('gender') 	.'='. $db->quote($request['gender']) .','.
        $db->quoteName('phone_number') 	.'='. $db->quote($request['phone_number']) .','.
        $db->quoteName('deficient') 	.'='. $db->quote($request['deficient']) .','.
        $db->quoteName('deficient_type') 	.'='. $db->quote($request['deficient_type']) .'
      WHERE '.
        $db->quoteName('id') .'='. $request['id']
    ;
    $txt = 'atualizados';
  endif;

  try {

    $db->setQuery($query);
    $db->execute();
    $isUpdated = true;

  } catch (RuntimeException $e) {

    echo '
    <div class="alert alert-error">
      <h4><span class="base-icon-attention"></span> Erro no envio dos dados!</h4>
      <p>Por favor, verifique as informações e tente novamente.</p>
    </div>
    ';
    $isUpdated = false;
  }

elseif(!empty($project->name) && $request['pType'] > 0) :
  $query = '
    INSERT INTO '. $db->quoteName('#__zenite_registrations') .'('.
      $db->quoteName('user_id') .','.
      $db->quoteName('project_id') .','.
      $db->quoteName('projectType_id') .','.
      $db->quoteName('created_by') .'
    ) VALUES ('.
      $user->id .','.
      $request['pid'] .','.
      $request['pType'] .','.
      $user->id .'
    )
  ';
  try {
    $db->setQuery($query);
    $db->execute();

    echo '
      <div class="alert alert-success">
        <h4>Encaminhado para o sistema de pagamento</h4>
      </div>
    ';

  } catch (RuntimeException $e) {

    echo '
    <div class="alert alert-error">
      <h4><span class="base-icon-attention"></span> Erro no envio dos dados!</h4>
    </div>
    ';
  }
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
      '. $db->quoteName('T1.description') .',
      '. $db->quoteName('T1.min_age') .',
      '. $db->quoteName('T1.max_age') .',
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
    T1.project_id = '.$request['pid'].' AND T1.state = 1 ORDER BY T2.name, T1.distance_unit, T1.distance;
  ';
  $db->setQuery($query);
  $db->execute();
  $num_rows = $db->getNumRows();
  $types = $db->loadObjectList();
endif;

if(!empty($item->id)) :
  $request['cpf']           = $item->cpf;
  $request['birthday']      = $item->birthday;
  $request['gender']        = $item->gender;
  $request['phone_number']  = $item->phone_number;
  $request['deficient']     = $item->deficient;
  $request['deficient_type']= $item->deficient_type;
endif;

?>

<div class="row">
  <div class="col-md-4 col-lg-6">

    <div class="alert alert-<?php echo ($isUpdated ? 'success' : 'info')?> strong small">
      <span class="<?php echo ($isUpdated ? 'base-icon-ok text-success' : 'base-icon-circle text-live')?>"> Passo 1</span>
      <span class="base-icon-angle-double-right"></span> <?php echo (empty($item->id) ? 'Informe seus dados pessoais' : ($isUpdated ? 'Dados '.$txt.' com sucesso!' : 'Verifique seus dados pessoais'))?>
    </div>

    <form name="form-userInfo" id="form-userInfo" method="post">
      <input type="hidden" name="pid" id="userInfo-pid" value="<?php echo $request['pid']?>" />
      <input type="hidden" name="sent" id="userInfo-sent" value="1" />
      <input type="hidden" name="itemId" id="userInfo-itemId" value="<?php echo $item->id?>" />
      <input type="hidden" name="user_id" id="userInfo-user_id" value="<?php echo $user->id?>" />
    	<fieldset class="fieldset-embed">
        <legend>Dados Pessoais</legend>
    		<div class="row">
    			<div class="col-sm-4 col-md-6 col-lg-4">
    				<div class="form-group field-required">
    					<label>CPF</label>
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
        <div class="form-actions text-right">
          <button type="submit" name="btn-userInfo-save" id="btn-userInfo-save" class="btn btn-primary no-margin">
            <?php echo (empty($item->id) ? '<span class="base-icon-right-big"></span> Salvar' : '<span class="base-icon-arrows-cw"></span> Atualizar')?> dados pessoais
          </button>
        </div>
    	</fieldset>
    </form>
  </div>
  <?php if($isUpdated || !empty($item->id)) : ?>
    <div class="col-md-8 col-lg-6">
      <hr class="visible-xs visible-sm"/>

      <?php if(!empty($item->id)) : ?>
        <div class="alert alert-info strong small">
          <span class="<?php echo ($isUpdated ? 'base-icon-circle' : 'base-icon-circle-empty')?> text-live"> Passo 2</span> <span class="base-icon-angle-double-right"></span> Selecione a modalidade que deseja participar.
        </div>
      <?php endif?>

      <form action="<?php echo JURI::root().'subsConfirm'?>" name="form-setItem" id="form-setItem" method="post">
        <?php if($request['pid'] == 0) :?>
          <div class="form-group field-required">
            <label>Selecione o Evento</label>
            <select name="pid" id="setItem-pid" class="form-control input-lg">
              <option value="0">- Selecione -</option>
              <?php
                foreach ($projects as $obj) {
                  echo '<option value="'.$obj->id.'"'.($request['pid'] == $obj->id ? ' selected' : '').'>'.baseHelper::nameFormat($obj->name).' - '.baseHelper::dateFormat($obj->date).'</option>';
                }
              ?>
            </select>
          </div>
        <?php else :?>
          <div class="well well-sm no-margin field-required">
            <h4 class="no-margin text-success"><span class="base-icon-check text-live"></span> <?php echo baseHelper::nameFormat($project->name)?></h4>
            <input type="hidden" name="pid" id="setItem-pid" value="<?php echo $request['pid']?>">
          </div>
        <?php endif;?>
        <?php if($request['pid'] > 0) : ?>
          <?php if($num_rows > 0) : ?>
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th></th>
                  <th>Modalidade</th>
                  <th>Distância</th>
                  <th>Grupo</th>
                  <th>Valor</th>
                </tr>
              </thead>
              <tbody>
                <?php
                foreach ($types as $item) {

                  // verifica se o usuário está dentro da faixa etária
                  $ageLimit = ($item->max_age == 0 ? 1000 : $item->max_age);
                  $hasAge = (baseHelper::getAge($request['birthday']) >= $item->min_age && baseHelper::getAge($request['birthday']) <= $ageLimit) ? true : false;
                  // verifica se o usuário faz parte da modalidade
                  $hasCategory = $hasCat = 0;
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

                  if($hasAge && $hasCat) :

                    $hasCategory = 1;
                    $distance_unit = ($item->distance_unit == 0 ? ' m' : ' Km');
                		if($item->max_age == 0) :
                			$faixa = 'À partir dos '.$item->min_age.' anos';
                		else :
                			$faixa = 'Dos '.$item->min_age.' aos '.$item->max_age.' anos';
                		endif;

                    echo '
                      <tr>
                        <td><input type="radio" name="pType" value="'.$item->id.'" /></td>
                        <td>
                          '.baseHelper::nameFormat($item->category).'
                          '.($item->limit == 0 ? '' : '<br /><small class="text-muted font-featured cursor-help hasTooltip" title="Limite de inscrições na modalidade">Limite: '.$item->limit.'</small>').'
                        </td>
                        <td>
                          '.$item->distance.$distance_unit.'
                        </td>
                        <td>
                          '.(!empty($item->disability) ? '<div>'.baseHelper::nameFormat($item->disability).'</div>' : '').'
                          <small class="text-muted font-featured">'.$faixa.'</small>
                        </td>
                        <td class="strong">R$ '.baseHelper::priceFormat($item->price).'</td>
                      </tr>
                    ';

                  endif;

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
            <?php if($hasCategory) : ?>
              <div class="form-actions text-right">
                <button type="submit" name="btn-setItem-save" id="btn-setItem-save" class="btn btn-success">
                  Realizar Pagamento <span class="base-icon-right-big"></span>
                </button>
              </div>
            <?php endif; ?>
          <?php else : ?>
            <div class="alert alert-warning">Não existem modalidades cadastradas para esse evento!<div>
          <?php endif; ?>
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
    location.href = '<?php echo JURI::current()?>?pid='+jQuery(this).val();
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
