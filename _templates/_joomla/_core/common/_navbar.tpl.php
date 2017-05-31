<?php if($navbarContainer == 1) echo '<div class="container">'; ?>

  <nav id="cmstools" class="navbar <?php echo 'navbar-expand-'.$navbarToggleable.' '.$navbarStyle.' '.$navbarFixed; ?> bg-faded">
    <button class="navbar-toggler <?php echo 'navbar-toggler-'.$navbarTogglerSide; ?>" type="button" data-toggle="collapse" data-target="#cmstoolsCollapsing" aria-controls="cmstoolsCollapsing" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse flex-row justify-content-center" id="cmstoolsCollapsing">
      <?php if($this->countModules('system-menu') > 0): ?>
        <jdoc:include type="modules" name="system-menu" style="none" />
      <?php endif; ?>
      <?php if($this->countModules('base-menu') > 0): ?>
        <ul class="nav menu stacked-<?php echo $navbarToggleable?>">
          <li>
            <a id="base-menu" href="#"><span class="base-icon-menu text-live"></span> Base Menu</a>
            <jdoc:include type="modules" name="base-menu" style="none" />
          </li>
        </ul>
      <?php endif; ?>
    </div>
  </nav>

<?php if($navbarContainer == 1) echo '</div>'; ?>
