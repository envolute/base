<?php
defined('_JEXEC') or die;
?>

<!-- css template -->
<link rel="stylesheet" href="templates/base/css/style.css" type="text/css" />
<?php if(!empty($navbarAccess)) echo '<link rel="stylesheet" href="templates/base/css/cms.frontend.navbar.css" type="text/css" />'; ?>
<?php if($loadDev) echo '<link rel="stylesheet" href="templates/base/_dev/custom.css" type="text/css" />'; ?>

<!-- css print -->
<link rel="stylesheet" href="templates/base/css/style.print.css" type="text/css" media="print" />

<!-- For IE support -->
<!--[if IE]>
	<link href="templates/base/css/style.ie.css" rel="stylesheet">
<![endif]-->
