<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Templates.base
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

include_once('_init.tpl.php');

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
		if($this->countModules('admin-add') || $this->countModules('admin-report') || $this->countModules('admin-menu') || $this->countModules('admin-helper') || $groups[8]) :

			// PERMITIR ACESSO AO BACKEND
			// Gera o cookie para permitir o acesso ao diretório administrator
			// Esse código é definido no arquivo .htaccess
			$admin_cookie_code="425636524";
			if(!$_COOKIE['BaseAdminSession']) setcookie("BaseAdminSession",$admin_cookie_code,0,"/");

		?>
		<nav id="cmstools" class="navbar <?php echo $navbarStyle.' '.$navbarFixed; ?>" role="navigation">
			<div class="<?php echo $container?>">
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
					<ul class="nav navbar-nav no-margin">
					<?php if($this->countModules('admin-components')): ?>
						<!-- Add -->
						<li id="add-menu" class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" href="#">
								<span class="base-icon-cogs"></span> <?php echo JText::_('TPL_BASE_COMPONENTS');?>
								<b class="caret"></b>
							</a>
							<jdoc:include type="modules" name="admin-components" style="none" />
						</li>
					<?php endif; ?>
					<?php if($this->countModules('admin-report')): ?>
						<!-- Report -->
						<li id="report-menu" class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" href="#">
								<span class="base-icon-docs"></span> <?php echo JText::_('TPL_BASE_REPORTS');?>
								<b class="caret"></b>
							</a>
							<jdoc:include type="modules" name="admin-report" style="none" />
						</li>
					<?php endif; ?>
					<?php if($this->countModules('admin-menu')): ?>
						<!-- Add -->
						<li id="admin-menu" class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" href="#">
								<span class="base-icon-tools"></span> <?php echo JText::_('TPL_BASE_TOOLS');?>
								<b class="caret"></b>
							</a>
							<jdoc:include type="modules" name="admin-menu" style="none" />
						</li>
					<?php endif; ?>
					<?php if($this->countModules('admin-base')): ?>
						<!-- Add -->
						<li id="admin-base" class="dropdown hidden-sm">
							<a class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" href="#">
								<span class="base-icon-menu"></span> Menu Base
								<b class="caret"></b>
							</a>
							<jdoc:include type="modules" name="admin-base" style="none" />
						</li>
					<?php endif; ?>
					</ul>
					<label id="toggleBtnEdit">
						<input name="toggleEdit" type="checkbox" checked />
						<?php echo JText::_('TPL_BASE_TOGGLE_BTN_EDIT'); ?>
					</label>
					<?php if($this->countModules('admin-helper')): ?>
						<!-- Helper -->
						<ul class="nav navbar-nav pull-right no-margin">
							<li id="helper-menu" class="dropdown">
								<a class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" href="#">
									<span class="base-icon-info-circled"></span> <?php echo JText::_('TPL_BASE_INFO'); ?>
									<b class="caret"></b>
								</a>
								<jdoc:include type="modules" name="admin-helper" style="none" />
							</li>
						</ul>
					<?php endif; ?>
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

		<?php
		// NAVTOP -> Dashboard
		if($this->countModules('navtop')):
		?>
		<nav id="navtop" class="<?php echo $navtopFixed; ?> clearfix">
			<div class="container-fluid">
				<div class="row">
					<div class="row-wrapper">
						<div class="row">
							<jdoc:include type="modules" name="navtop" style="base" />
							<div class="clear"></div>
						</div>
					</div>
				</div>
			</div>
		</nav>
		<?php endif; ?>

		<?php
		// NAVSIDE -> Dashboard
		if($this->countModules('navside')):
		?>
		<nav id="navside" class="clearfix">

			<?php if($this->countModules('navside-header')): ?>
				<div id="navside-header"><jdoc:include type="modules" name="navside-header" style="base" /></div>
			<?php endif; ?>

			<jdoc:include type="modules" name="navside" style="base" />

			<?php if($this->countModules('navside-footer')): ?>
				<div id="navside-footer"><jdoc:include type="modules" name="navside-footer" style="base" /></div>
			<?php endif; ?>

		</nav>
		<?php endif; ?>

		<!-- wrapper -->
		<div id="wrapper">

			<?php
			// TOP
			if($this->countModules('top')):
			?>
			<div id="top" class="clearfix">
				<div class="<?php echo $container?>">
					<div class="row">
						<div class="row-wrapper">
							<div class="row">
								<jdoc:include type="modules" name="top" style="base" />
								<div class="clear"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php endif; ?>

			<?php
			// HEADER
			if($headerCount > 0):
			?>
			<div id="header">
				<div class="<?php echo $container?>">
					<div class="row">
						<div class="row-wrapper">
							<div class="row">
								<?php if($this->countModules('header-1')): ?>
									<!-- header 1 -->
									<div id="header-1" class="<?php echo $header_1 ?>">
										<jdoc:include type="modules" name="header-1" style="mod" />
									</div>
								<?php endif; ?>
								<?php if($this->countModules('header-2')): ?>
									<!-- header 2 -->
									<div id="header-2" class="<?php echo $header_2 ?>">
										<jdoc:include type="modules" name="header-2" style="mod" />
									</div>
								<?php endif; ?>
								<?php if($this->countModules('header-3')): ?>
									<!-- header 3 -->
									<div id="header-3" class="<?php echo $header_3 ?>">
										<jdoc:include type="modules" name="header-3" style="mod" />
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php endif; ?>

			<?php
			// NAVIGATION
			if($this->countModules('navigation')):
			?>
			<div id="navigation" class="clearfix">
				<div class="<?php echo $container?>">
					<div class="row">
						<div class="row-wrapper">
							<div class="row">
								<jdoc:include type="modules" name="navigation" style="base" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php endif; ?>

			<?php
			// SHOWCASE
			if($this->countModules('showcase')):
			?>
			<div id="showcase">
				<div class="<?php echo $container?>">
					<div class="row">
						<div class="row-wrapper">
							<div class="row">
								<jdoc:include type="modules" name="showcase" style="mod" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php endif; ?>

			<?php
			// INFO TOP (breadcrumb, page-header...)
			if($this->countModules('info-top')): ?>
			<div id="info-top">
				<div class="<?php echo $container?>">
					<div class="row">
						<div class="row-wrapper">
							<div class="row">
								<jdoc:include type="modules" name="info-top" style="base" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php endif; ?>

			<?php
			// FEATURED
			if($featCount > 0 || $this->countModules('full-featured')):
			?>
			<div id="featured">

				<?php if($this->countModules('full-featured')): ?>
					<jdoc:include type="modules" name="full-featured" style="none" />
				<?php endif; ?>

				<?php if($featCount > 0): ?>
				<div class="<?php echo $container?>">
					<div class="row">
						<div class="row-wrapper">
							<div class="row">
								<?php if($this->countModules('featured-1')): ?>
									<!-- featured 1 -->
									<div id="featured-1" class="<?php echo $featured_1 ?>">
										<jdoc:include type="modules" name="featured-1" style="mod" />
									</div>
								<?php endif; ?>
								<?php if($this->countModules('featured-2')): ?>
									<!-- featured 2 -->
									<div id="featured-2" class="<?php echo $featured_2 ?>">
										<jdoc:include type="modules" name="featured-2" style="mod" />
									</div>
								<?php endif; ?>
								<?php if($this->countModules('featured-3')): ?>
									<!-- featured 3 -->
									<div id="featured-3" class="<?php echo $featured_3 ?>">
										<jdoc:include type="modules" name="featured-3" style="mod" />
									</div>
								<?php endif; ?>
								<?php if($this->countModules('featured-4')): ?>
									<!-- featured 4 -->
									<div id="featured-4" class="<?php echo $featured_4 ?>">
										<jdoc:include type="modules" name="featured-4" style="mod" />
									</div>
								<?php endif; ?>
								<?php if($this->countModules('featured-5')): ?>
									<!-- featured 5 -->
									<div id="featured-5" class="<?php echo $featured_5 ?>">
										<jdoc:include type="modules" name="featured-5" style="mod" />
									</div>
								<?php endif; ?>
								<?php if($this->countModules('featured-6')): ?>
									<!-- featured 6 -->
									<div id="featured-6" class="<?php echo $featured_6 ?>">
										<jdoc:include type="modules" name="featured-6" style="mod" />
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
				<?php endif; ?>
			</div>
			<?php endif; ?>

			<!-- Full Content -->
			<div id="full-content">

				<?php if($this->countModules('full-content')): ?>
					<jdoc:include type="modules" name="full-content" style="none" />
				<?php endif; ?>

				<div class="<?php echo $container?>">
					<div class="row">
						<div class="row-wrapper">
							<div class="row">

								<?php if($this->countModules('left')): ?>
									<!-- Left -->
									<div id="left" class="<?php echo grid($leftWidth, $gDef);?>">
										<jdoc:include type="modules" name="left" style="mod" />
									</div>
								<?php endif; ?>

								<!-- Main Content -->
								<div id="main-content" class="<?php echo grid(setOffset($rightWidth + $leftWidth), $gDef);?>">

									<?php if($this->countModules('main-content-top')): ?>
										<!-- Main Content Top -->
										<div class="row">
											<div id="main-content-top" class="col-xs-12">
												<div class="row">
													<jdoc:include type="modules" name="main-content-top" style="mod" />
												</div>
											</div>
										</div>
									<?php endif; ?>

									<div class="row">

										<?php if($this->countModules('sidebar') && $sidebarFloat == 'left'): ?>
											<!-- Sidebar if [left] -->
											<div id="sidebar" class="<?php echo grid($sidebarWidth, $gDef);?>">
												<jdoc:include type="modules" name="sidebar" style="mod" />
											</div>
										<?php endif; ?>

										<!-- Content -->
										<div id="content" class="<?php echo grid(setOffset($sidebarWidth), $gDef);?>">
											<jdoc:include type="message" />
											<?php if($this->countModules('content-top')): ?>
												<!-- Content Top -->
												<div id="content-top">
													<div class="row">
														<jdoc:include type="modules" name="content-top" style="mod" />
													</div>
												</div>
											<?php endif; ?>
											<div id="component">
												<jdoc:include type="component" />
											</div>
											<?php if($this->countModules('content-bottom')): ?>
												<!-- Content Bottom -->
												<div id="content-bottom">
													<div class="row">
														<jdoc:include type="modules" name="content-bottom" style="mod" />
													</div>
												</div>
											<?php endif; ?>
										</div>

										<?php if($this->countModules('sidebar') && $sidebarFloat == 'right'): ?>
											<!-- Sidebar if [right] -->
											<div id="sidebar" class="<?php echo grid($sidebarWidth, $gDef);?>">
												<jdoc:include type="modules" name="sidebar" style="mod" />
											</div>
										<?php endif; ?>

									</div>


									<?php if($this->countModules('main-content-bottom')): ?>
										<!-- Main Content Bottom -->
										<div class="row">
											<div id="main-content-bottom" class="col-xs-12">
												<div class="row">
													<jdoc:include type="modules" name="main-content-bottom" style="mod" />
												</div>
											</div>
										</div>
									<?php endif; ?>

								</div>

								<?php if($this->countModules('right')): ?>
									<!-- Right -->
									<div id="right" class="<?php echo grid($rightWidth, $gDef);?>">
										<jdoc:include type="modules" name="right" style="mod" />
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>

			<?php
			// INFO BOTTOM (breadcrumb, extra-content...)
			if($this->countModules('info-bottom')): ?>
			<!-- Info Bottom -->
			<div id="info-bottom">
				<div class="<?php echo $container?>">
					<div class="row">
						<div class="row-wrapper">
							<div class="row">
								<jdoc:include type="modules" name="info-bottom" style="base" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php endif; ?>

			<?php
			// SYNDICATE
			if($syndCount > 0 || $this->countModules('full-syndicate')):
			?>
			<div id="syndicate">

				<?php if($this->countModules('full-syndicate')): ?>
					<jdoc:include type="modules" name="full-syndicate" style="none" />
				<?php endif; ?>

				<?php if($syndCount > 0): ?>
				<div class="<?php echo $container?>">
					<div class="row">
						<div class="row-wrapper">
							<div class="row">
								<?php if($this->countModules('syndicate-1')): ?>
									<!-- Syndicate 1 -->
									<div id="syndicate-1" class="<?php echo $syndicate_1 ?>">
										<jdoc:include type="modules" name="syndicate-1" style="mod" />
									</div>
								<?php endif; ?>
								<?php if($this->countModules('syndicate-2')): ?>
									<!-- Syndicate 2 -->
									<div id="syndicate-2" class="<?php echo $syndicate_2 ?>">
										<jdoc:include type="modules" name="syndicate-2" style="mod" />
									</div>
								<?php endif; ?>
								<?php if($this->countModules('syndicate-3')): ?>
									<!-- Syndicate 3 -->
									<div id="syndicate-3" class="<?php echo $syndicate_3 ?>">
										<jdoc:include type="modules" name="syndicate-3" style="mod" />
									</div>
								<?php endif; ?>
								<?php if($this->countModules('syndicate-4')): ?>
									<!-- Syndicate 4 -->
									<div id="syndicate-4" class="<?php echo $syndicate_4 ?>">
										<jdoc:include type="modules" name="syndicate-4" style="mod" />
									</div>
								<?php endif; ?>
								<?php if($this->countModules('syndicate-5')): ?>
									<!-- Syndicate 5 -->
									<div id="syndicate-5" class="<?php echo $syndicate_5 ?>">
										<jdoc:include type="modules" name="syndicate-5" style="mod" />
									</div>
								<?php endif; ?>
								<?php if($this->countModules('syndicate-6')): ?>
									<!-- Syndicate 6 -->
									<div id="syndicate-6" class="<?php echo $syndicate_6 ?>">
										<jdoc:include type="modules" name="syndicate-6" style="mod" />
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
				<?php endif; ?>
			</div>
			<?php endif; ?>

			<?php
			// BOTTOM
			if($bottomCount > 0 || $this->countModules('full-bottom')):
			?>
			<div id="bottom">

				<?php if($this->countModules('full-bottom')): ?>
					<jdoc:include type="modules" name="full-bottom" style="none" />
				<?php endif; ?>

				<?php if($bottomCount > 0): ?>
				<div class="<?php echo $container?>">
					<div class="row">
						<div class="row-wrapper">
							<div class="row">
								<?php if($this->countModules('bottom-1')): ?>
									<!-- bottom 1 -->
									<div id="bottom-1" class="<?php echo $bottom_1 ?>">
										<jdoc:include type="modules" name="bottom-1" style="mod" />
									</div>
								<?php endif; ?>
								<?php if($this->countModules('bottom-2')): ?>
									<!-- bottom 2 -->
									<div id="bottom-2" class="<?php echo $bottom_2 ?>">
										<jdoc:include type="modules" name="bottom-2" style="mod" />
									</div>
								<?php endif; ?>
								<?php if($this->countModules('bottom-3')): ?>
									<!-- bottom 3 -->
									<div id="bottom-3" class="<?php echo $bottom_3 ?>">
										<jdoc:include type="modules" name="bottom-3" style="mod" />
									</div>
								<?php endif; ?>
								<?php if($this->countModules('bottom-4')): ?>
									<!-- bottom 4 -->
									<div id="bottom-4" class="<?php echo $bottom_4 ?>">
										<jdoc:include type="modules" name="bottom-4" style="mod" />
									</div>
								<?php endif; ?>
								<?php if($this->countModules('bottom-5')): ?>
									<!-- bottom 5 -->
									<div id="bottom-5" class="<?php echo $bottom_5 ?>">
										<jdoc:include type="modules" name="bottom-5" style="mod" />
									</div>
								<?php endif; ?>
								<?php if($this->countModules('bottom-6')): ?>
									<!-- bottom 6 -->
									<div id="bottom-6" class="<?php echo $bottom_6 ?>">
										<jdoc:include type="modules" name="bottom-6" style="mod" />
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
				<?php endif; ?>
			</div>
			<?php endif; ?>

			<!-- Scroll Top -->
			<a id="scroll-to-top" href="#goto-screen" class="nav-smooth"></a>

			<?php
			// FOOTER
			if($footerCount > 0):
			?>
			<div id="footer" class="<?php if($showFooter == 0) echo $hiddenXS; ?>">
				<div class="<?php echo $container?>">
					<div class="row">
						<div class="row-wrapper">
							<div class="row">
								<?php if($this->countModules('footer-1')): ?>
									<!-- footer 1 -->
									<div id="footer-1" class="<?php echo $footer_1 ?>">
										<jdoc:include type="modules" name="footer-1" style="mod" />
									</div>
								<?php endif; ?>
								<?php if($this->countModules('footer-2')): ?>
									<!-- footer 2 -->
									<div id="footer-2" class="<?php echo $footer_2 ?>">
										<jdoc:include type="modules" name="footer-2" style="mod" />
									</div>
								<?php endif; ?>
								<?php if($this->countModules('footer-3')): ?>
									<!-- footer 3 -->
									<div id="footer-3" class="<?php echo $footer_3 ?>">
										<jdoc:include type="modules" name="footer-3" style="mod" />
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php endif; ?>

			<?php if($this->countModules('hidden')): ?>
				<!-- Hidden -->
				<div id="hidden" class="">
					<jdoc:include type="modules" name="hidden" style="base" />
				</div>
			<?php endif; ?>

			<jdoc:include type="modules" name="debug" style="base" />

		<!-- / wrapper -->
		</div>

	<!-- / screen -->
	</div>

	<?php

	// Set URL base to javascript files
	echo '<input type="hidden" id="baseurl" name="baseurl" value="'.$this->baseurl.'" />';
	// call base javascript files
	require_once('templates/'.$this->template.'/_js.tpl.php');

	// Google Analytics
	if($analyticsCode != '') require_once('templates/'.$this->template.'/_analytics.php');

	?>

</body>
</html>
