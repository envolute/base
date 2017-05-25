<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Templates.base
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once('_init.tpl.php');

?>
<body class="site <?php echo $option.$view.$layout.$task.$itemid.$pageclass.$screen.' '. $access; ?>">

	<?php
	// BG-LAYER -> opção para elementos de background
	if($this->countModules('bg-layer')):
	?>
	<div id="base-bg-layer">
		<jdoc:include type="modules" name="bg-layer" style="none" />
	</div>
	<?php endif; ?>

	<a href="#main-content" class="sr-only">Skip to content</a>

	<!-- Screen -->
	<div id="screen" class="clearfix">

		<?php
		// NAVBAR
		if(!empty($navbarAccess)) :

			// PERMITIR ACESSO AO BACKEND
			// Gera o cookie para permitir o acesso ao diretório administrator
			// Esse código é definido no arquivo .htaccess
			$admin_cookie_code="425636524";
			if(!isset($_COOKIE['BaseAdminSession'])) setcookie("BaseAdminSession",$admin_cookie_code,0,JURI::root(true));

		?>
  		<nav id="cmstools" class="navbar <?php echo $navbarStyle.' '.$navbarFixed; ?>" role="navigation">
  			<div class="<?php echo $navbarContainer?>">
  				<div class="navbar-header">
  					<button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target=".navbar-collapse">
  						<span class="sr-only">Toggle navigation</span>
  						<span class="icon-bar"></span>
  						<span class="icon-bar"></span>
  						<span class="icon-bar"></span>
  					</button>
  					<a class="navbar-brand" href="administrator" target="_blank">
  						<span class="base-icon-cog"></span> <?php echo JText::_('TPL_BASE_ADMINISTRATOR'); ?>
  					</a>
  				</div>
  				<div class="collapse navbar-collapse">
  					<?php if($this->countModules('admin-menu')): ?>
  						<jdoc:include type="modules" name="admin-menu" style="none" />
  					<?php endif; ?>
  					<!-- Helper -->
  					<ul class="nav navbar-nav pull-right no-margin">
  						<li id="helper-menu" class="dropdown">
  							<a class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" href="#">
  								<span class="base-icon-info-circled"></span> <?php echo JText::_('TPL_BASE_INFO'); ?>
  								<b class="caret"></b>
  							</a>
  						</li>
  					</ul>
  				</div>
  			</div>
  		</nav>
		<?php
		else :

			// BLOQUEIA ACESSO AO BACKEND
			// remove o cookie para permitir o acesso ao diretório administrator
			if(isset($_COOKIE['BaseAdminSession'])) setcookie('BaseAdminSession', null, -1, '/');

		endif;
		?>

		<!-- wrapper -->
		<div id="wrapper">

			<?php
			// HEADER
			if(!$hidePos) loadPosition($this, $params, 'header', 8);
      // SECTION
			if(!$hidePos) loadPosition($this, $params, 'section', 12);
      ?>

			<!-- Full Content -->
			<div id="full-content">

				<div class="<?php echo $params['full_content_container']?>">
					<div class="row">
						<div class="row-wrapper">
							<div class="row">

								<?php if($this->countModules('full-content-header') && !$hidePos): ?>
									<!-- Full Content Header -->
									<div id="full-content-header" class="<?php echo $params['full_content_header']?>">
										<jdoc:include type="modules" name="full-content-header" style="mod" />
									</div>
								<?php endif; ?>

								<?php if($this->countModules('left') && !$hidePos): ?>
									<!-- Left -->
									<div id="left" class="<?php echo grid($params['leftWidth'], $gDef);?>">
										<jdoc:include type="modules" name="left" style="mod" />
									</div>
								<?php endif; ?>

								<!-- Content -->
								<div id="content" class="<?php echo grid(setOffset($params['rightWidth'] + $params['leftWidth']), $gDef);?>">

									<?php
                  // CONTENT HEADER
            			if(!$hidePos) loadPosition($this, $params, 'content-header', 4);
                  ?>

									<div class="row">

										<?php if($this->countModules('content-left') && !$hidePos) :?>
											<!-- Content Left -->
											<div id="content-left" class="<?php echo grid($params['contentLeftWidth'], $gDef);?>">
												<jdoc:include type="modules" name="content-left" style="mod" />
											</div>
										<?php endif; ?>

										<!-- Component -->
										<div id="component" class="<?php echo grid(setOffset($params['contentLeftWidth'] + $params['contentRightWidth']), $gDef);?>">

                      <jdoc:include type="message" />

                      <?php
                      // COMPONENT HEADER
                			if(!$hidePos) loadPosition($this, $params, 'component-header', 3);
                      ?>

											<div id="component-body">
												<jdoc:include type="component" />
											</div>

                      <?php
                      // COMPONENT FOOTER
                			if(!$hidePos) loadPosition($this, $params, 'component-footer', 3);
                      ?>

										</div>

                    <?php if($this->countModules('content-right') && !$hidePos): ?>
											<!-- Content Right -->
											<div id="content-right" class="<?php echo grid($params['contentRightWidth'], $gDef);?>">
												<jdoc:include type="modules" name="content-right" style="mod" />
											</div>
										<?php endif; ?>

									</div>

                  <?php
                  // CONTENT FOOTER
                  if(!$hidePos) loadPosition($this, $params, 'content-footer', 4);
                  ?>

								</div>

								<?php if($this->countModules('right') && !$hidePos): ?>
									<!-- Right -->
									<div id="right" class="<?php echo grid($params['rightWidth'], $gDef);?>">
										<jdoc:include type="modules" name="right" style="mod" />
									</div>
								<?php endif; ?>

                <?php if($this->countModules('full-content-footer') && !$hidePos): ?>
									<!-- Full Content Footer -->
									<div id="full-content-footer" class="<?php echo $params['full_content_footer']?>">
										<jdoc:include type="modules" name="full-content-footer" style="mod" />
									</div>
								<?php endif; ?>

							</div>
						</div>
					</div>
				</div>

			</div>
      <!-- End Full Content -->

      <?php
      // BOTTOM
      if(!$hidePos) loadPosition($this, $params, 'bottom', 12);
      // SCROLL TO TOP
			echo '<a id="scroll-to-top" href="#goto-screen" class="nav-smooth"></a>';
      // FOOTER
      if(!$hidePos) loadPosition($this, $params, 'footer', 8);
      // HIDDEN
      if($this->countModules('hidden')):
        echo '
				<div id="hidden">
					<jdoc:include type="modules" name="hidden" style="base" />
				</div>
        ';
			endif;
      ?>

			<jdoc:include type="modules" name="debug" style="base" />

		<!-- / wrapper -->
		</div>

	<!-- / screen -->
	</div>

	<div id="loader">
		<img src="templates/base/core/images/loader-active.gif">
	</div>

	<?php

	// Set URL base to javascript files
	echo '<input type="hidden" id="baseurl" name="baseurl" value="'.$this->baseurl.'" />';

	// Google Analytics
	if($analyticsCode != '') require_once('templates/'.$this->template.'/_analytics.php');

	?>

</body>
</html>
