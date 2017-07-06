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
<body class="site <?php echo $option.$view.$layout.$task.$itemid.$pageclass.$screen.$preloader.' '. $access; ?>">

	<?php
	// BG-LAYER -> opção para elementos de background
	if($this->countModules('bg-layer') > 0):
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
		if(!empty($navbarAccess)) {

			// PERMITIR ACESSO AO BACKEND
			// Gera o cookie para permitir o acesso ao diretório administrator
			// Esse código é definido no arquivo .htaccess
			$admin_cookie_code="425636524";
			if(!isset($_COOKIE['BaseAdminSession'])) setcookie("BaseAdminSession",$admin_cookie_code,0,JURI::root(true));

			require_once('_navbar.tpl.php');

		} else {

			// BLOQUEIA ACESSO AO BACKEND
			// remove o cookie para permitir o acesso ao diretório administrator
			if(isset($_COOKIE['BaseAdminSession'])) setcookie('BaseAdminSession', null, -1, '/');

		}
		?>

		<!-- wrapper -->
		<div id="wrapper">

			<?php
			// HEADER
			loadPosition($this, $params, 'header', 6);
			// SECTION
			loadPosition($this, $params, 'section', 6);
			?>

			<!-- Full Content -->
			<div id="full-content">
				<div class="<?php echo $params['full_content_container']?>">
					<div class="row">

						<?php if($this->countModules('full-content-top') > 0): ?>
							<!-- Full Content Top -->
							<div id="full-content-top" class="col-12 <?php echo $params['full_content_top']?>">
								<jdoc:include type="modules" name="full-content-top" style="base" />
							</div>
						<?php endif; ?>

						<?php if($this->countModules('left') > 0): ?>
							<!-- Left -->
							<div id="left" class="<?php echo grid($params['leftWidth'], $gDef);?>">
								<jdoc:include type="modules" name="left" style="base" />
							</div>
						<?php endif; ?>

						<!-- Content -->
						<div id="content" class="<?php echo grid(setOffset($params['rightWidth'] + $params['leftWidth']), $gDef);?>">

							<?php if($this->countModules('content-top') > 0): ?>
								<!-- Content Top -->
								<div id="content-top" class="<?php echo $params['content_top']?>">
									<jdoc:include type="modules" name="content-top" style="base" />
								</div>
							<?php endif; ?>

							<div class="row">

								<?php if($this->countModules('content-left') > 0) :?>
									<!-- Content Left -->
									<div id="content-left" class="<?php echo grid($params['contentLeftWidth'], $gDef);?>">
										<jdoc:include type="modules" name="content-left" style="base" />
									</div>
								<?php endif; ?>

								<!-- Component -->
								<div id="component" class="<?php echo grid(setOffset($params['contentLeftWidth'] + $params['contentRightWidth']), $gDef);?>">

                  <jdoc:include type="message" />

									<?php if($this->countModules('component-top') > 0): ?>
										<!-- Component Top -->
										<div id="component-top" class="<?php echo $params['component_top']?>">
											<jdoc:include type="modules" name="component-top" style="base" />
										</div>
									<?php endif; ?>

									<div id="component-body">
										<jdoc:include type="component" />
									</div>

                  <?php if($this->countModules('component-bottom') > 0): ?>
										<!-- Component Bottom -->
										<div id="component-bottom" class="<?php echo $params['component_bottom']?>">
											<jdoc:include type="modules" name="component-bottom" style="base" />
										</div>
									<?php endif; ?>

								</div>

                <?php if($this->countModules('content-right') > 0): ?>
									<!-- Content Right -->
									<div id="content-right" class="<?php echo grid($params['contentRightWidth'], $gDef);?>">
										<jdoc:include type="modules" name="content-right" style="base" />
									</div>
								<?php endif; ?>

							</div>

							<?php if($this->countModules('content-bottom') > 0): ?>
								<!-- Content Bottom -->
								<div id="content-bottom" class="<?php echo $params['content_bottom']?>">
									<jdoc:include type="modules" name="content-bottom" style="base" />
								</div>
							<?php endif; ?>

						</div>

						<?php if($this->countModules('right') > 0): ?>
							<!-- Right -->
							<div id="right" class="<?php echo grid($params['rightWidth'], $gDef);?>">
								<jdoc:include type="modules" name="right" style="base" />
							</div>
						<?php endif; ?>

            <?php if($this->countModules('full-content-bottom') > 0): ?>
							<!-- Full Content Footer -->
							<div id="full-content-bottom" class="col-12 <?php echo $params['full_content_bottom']?>">
								<jdoc:include type="modules" name="full-content-bottom" style="base" />
							</div>
						<?php endif; ?>

					</div>
				</div>
			</div>
      <!--/ Full Content -->

      <?php
      // SYNDICATE
      loadPosition($this, $params, 'syndicate', 6);
      // SCROLL TO TOP
			echo '<a id="scroll-to-top" href="#screen" class="go-to"></a>';
      // FOOTER
      loadPosition($this, $params, 'footer', 6);
      // HIDDEN
      if($this->countModules('hidden') > 0):
        echo '
				<div id="hidden">
					<jdoc:include type="modules" name="hidden" style="base" />
				</div>
        ';
			endif;
      ?>

			<jdoc:include type="modules" name="debug" style="base" />

		</div>
		<!--/ wrapper -->

	</div>
	<!--/ screen -->

	<div id="tmpl-loader">
		<span><img src="templates/base/images/core/loader-active.gif"></span>
	</div>

	<?php

	// Set URL base to javascript files
	echo '<input type="hidden" id="baseurl" name="baseurl" value="'.$this->baseurl.'" />';

	// Google Analytics
	if($analyticsCode != '') require_once('templates/'.$this->template.'/_analytics.php');

	?>

</body>
</html>
