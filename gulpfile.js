// Include gulp
var gulp = require('gulp');

// Define Project Name
var project     = 'base';

// Define base folders
var builder     = '_templates';
var core        = builder + '/_core';
var cms         = builder + '/_joomla';
var projects    = cms + '/_projects';
// DEFINE PROJECT SITE: enter project folder
var site        = projects + '/' + project;
// ------------------------------------
var common      = core + '/common';
var commonCms   = cms + '/_core/common';
var commonSite  = site + '/common';
var tmpl        = 'template';
var tmplJS      = tmpl + '/js';
var tmplCSS     = tmpl + '/css';

// CORE -----------------------------
  // JS files

    // Bootstrap JS -> Javascript libraries from bootstrap
	// IMPORTANTE:
	// Não utilizar o arquivo 'bootstrap.min.js' (minified), pois o mesmo
	// quebra algumas funcionalidades como 'collapse'...
    var bootstrapJs = [
      core+'/bootstrap/js/bootstrap.js' // Bootstrap functionalities
    ];
    // Bootstrap Extensions
    var bootstrapExtJs = [
      core+'/bootstrap/extensions/js/_init.js',
      // Helpers
      core+'/bootstrap/extensions/js/_helpers/responsive.js',
      core+'/bootstrap/extensions/js/_helpers/modal.js',
      core+'/bootstrap/extensions/js/_helpers/setTips.js',
      core+'/bootstrap/extensions/js/_helpers/collapse.js',
      // Integrations
      core+'/bootstrap/extensions/js/_integrations/mootools.js',
	  // Alert
      core+'/bootstrap/extensions/js/alert/notify.js',
      // Browser
      core+'/bootstrap/extensions/js/browser/_ie.init.js',
      core+'/bootstrap/extensions/js/browser/SmoothScroll.js',
      // Buttons
      core+'/bootstrap/extensions/js/buttons/btn-rippleEffect.js',
      core+'/bootstrap/extensions/js/buttons/btn-toggleState.js',
      // Layout
      core+'/bootstrap/extensions/js/layout/loader.js',
      core+'/bootstrap/extensions/js/layout/loadOnView.js',
      core+'/bootstrap/extensions/js/layout/libs/affix.js',
      core+'/bootstrap/extensions/js/layout/libs/affix.remove.js',
      // Navs
      core+'/bootstrap/extensions/js/navs/navMenu.js',
      // Typography
      core+'/bootstrap/extensions/js/typography/fontSize.js',
      core+'/bootstrap/extensions/js/typography/linkActions.js',
      // Utilities
	  core+'/bootstrap/extensions/js/utilities/copyToClipboard.js',
	  core+'/bootstrap/extensions/js/utilities/elementHeight.js',
	  core+'/bootstrap/extensions/js/utilities/iframeHeight.js',
      core+'/bootstrap/extensions/js/utilities/imageRetina.js',
      core+'/bootstrap/extensions/js/utilities/isOnScreen.js',
      core+'/bootstrap/extensions/js/utilities/parentWidth.js',
      core+'/bootstrap/extensions/js/utilities/setHidden.js',
      core+'/bootstrap/extensions/js/utilities/setScroll.js',
      core+'/bootstrap/extensions/js/utilities/toggleHidden.js',
      core+'/bootstrap/extensions/js/utilities/toggleIcon.js',
      core+'/bootstrap/extensions/js/utilities/libs/jquery.actual.js',
      core+'/bootstrap/extensions/js/utilities/libs/perfect-scrollbar.js',
      // Default Libs
        // chosen
        core+'/bootstrap/extensions/js/forms/select-chosen.js',
        common+'/libs/forms/chosen/chosen.jquery.min.js', // Formats the fields of type 'select'
        // fluid embed videos
        common+'/libs/content/fitvids/jquery.fitvids.js',
      // Definitions
      core+'/bootstrap/extensions/js/coreDefinitions.js'
    ];
    var formsJs = [
      // Libs
      core+'/bootstrap/extensions/js/forms/libs/jquery.autotab-1.1b.js', // Auto tab functionality
      core+'/bootstrap/extensions/js/forms/libs/jquery.inputmask.bundle.js', // Plugin for masks in text fields
      core+'/bootstrap/extensions/js/forms/libs/jquery.price_format.min.js', // Formats the price in text fields
      // Buttons
      core+'/bootstrap/extensions/js/buttons/btn-checkState.js',
      // Form Fields
      core+'/bootstrap/extensions/js/forms/check-autoTab.js',
      core+'/bootstrap/extensions/js/forms/check-option.js',
      core+'/bootstrap/extensions/js/forms/field-cep.js',
      core+'/bootstrap/extensions/js/forms/field-cpf-cnpj.js',
      core+'/bootstrap/extensions/js/forms/field-date.js',
      core+'/bootstrap/extensions/js/forms/field-editor.js',
      core+'/bootstrap/extensions/js/forms/field-fileAction.js',
      core+'/bootstrap/extensions/js/forms/field-image.js',
      core+'/bootstrap/extensions/js/forms/field-ip.js',
      core+'/bootstrap/extensions/js/forms/field-phone.js',
      core+'/bootstrap/extensions/js/forms/field-price.js',
      core+'/bootstrap/extensions/js/forms/field-required.js',
      core+'/bootstrap/extensions/js/forms/field-selectToselect.js',
      core+'/bootstrap/extensions/js/forms/field-time.js',
      core+'/bootstrap/extensions/js/forms/input-alphanum.js',
      core+'/bootstrap/extensions/js/forms/input-fixedLength.js',
      core+'/bootstrap/extensions/js/forms/input-getFocus.js',
      core+'/bootstrap/extensions/js/forms/input-lower.js',
      core+'/bootstrap/extensions/js/forms/input-noAccents.js',
      core+'/bootstrap/extensions/js/forms/input-noBlankSpace.js',
      core+'/bootstrap/extensions/js/forms/input-noDrop.js',
      core+'/bootstrap/extensions/js/forms/input-noNumber.js',
      core+'/bootstrap/extensions/js/forms/input-noPaste.js',
      core+'/bootstrap/extensions/js/forms/input-numeric.js',
      core+'/bootstrap/extensions/js/forms/input-upper.js',
      core+'/bootstrap/extensions/js/forms/select-autoTab.js',
      core+'/bootstrap/extensions/js/forms/select-update.js',
      core+'/bootstrap/extensions/js/forms/toggle-field.js',
      // Definitions
      core+'/bootstrap/extensions/js/formDefinitions.js'
    ];
    var validateJs = [
      core+'/bootstrap/extensions/js/forms/libs/jquery-validation/jquery.validate.min.js', // jQuery validation plugin
      core+'/bootstrap/extensions/js/forms/libs/jquery-validation/additional-methods.min.js', // jQuery validation plugin addon
      core+'/bootstrap/extensions/js/formValidations.js' // Customizations for validation
    ];
    var customJs = [
      site+'/js/default.js' // Specific customizations from project
    ];

// FILES DEFINITIONS -----------------------------
  // JS files
  var defaultJs   = bootstrapJs.concat(bootstrapExtJs, customJs);
  // CSS files
  var styleCss    = site+'/scss/style.scss'; // General css stylesheet
  var appCss      = site+'/scss/style.app.scss'; // General css stylesheet
  var printCss    = site+'/scss/style.print.scss'; // Stylesheet for printing
  var ieCss       = site+'/scss/style.ie.scss'; // Specific style sheet for IE
  var basicCss    = site+'/scss/style.basic.scss'; // Stylesheet to be used in text editors

// Include plugins
var concat        = require('gulp-concat');
var uglify        = require('gulp-uglify');
var rename        = require('gulp-rename');
var sass          = require('gulp-sass');
var autoprefixer  = require('gulp-autoprefixer');
var babel         = require('gulp-babel');
var del           = require('del');

// TASKS
  // JS -----------------------------
    // default.js
    gulp.task('default.js', function() {
        return gulp.src(defaultJs)
          .pipe(concat('default.js'))
          .pipe(babel())
          .pipe(uglify())
          .pipe(gulp.dest(tmplJS))
    });
    // forms.js
    gulp.task('forms.js', function() {
        return gulp.src(formsJs)
          .pipe(concat('forms.js'))
          .pipe(babel())
          .pipe(uglify())
          .pipe(gulp.dest(tmplJS))
    });
    // validate.js
    gulp.task('validate.js', function() {
        return gulp.src(validateJs)
          .pipe(concat('validate.js'))
          .pipe(babel())
          .pipe(uglify())
          .pipe(gulp.dest(tmplJS))
    });

  // CSS -----------------------------
    // style.css
    gulp.task('style.css', function() {
        gulp.src(styleCss)
          .pipe(sass({outputStyle: 'compressed', precision: 10}))
          .pipe(autoprefixer({browsers: ['last 1 version']}))
          .pipe(gulp.dest(tmplCSS))
    });
    // style.basic.css
    gulp.task('style.basic.css', function() {
        gulp.src(basicCss)
          .pipe(sass({outputStyle: 'compressed', precision: 10}))
          .pipe(autoprefixer({browsers: ['last 1 version']}))
          .pipe(gulp.dest(tmplCSS))
    });
    // style.ie.css
    gulp.task('style.ie.css', function() {
        gulp.src(ieCss)
          .pipe(sass({outputStyle: 'compressed', precision: 10}))
          .pipe(autoprefixer({browsers: ['last 1 version']}))
          .pipe(gulp.dest(tmplCSS))
    });
    // style.print.css
    gulp.task('style.print.css', function() {
        gulp.src(printCss)
          .pipe(sass({outputStyle: 'compressed', precision: 10}))
          .pipe(autoprefixer({browsers: ['last 1 version']}))
          .pipe(gulp.dest(tmplCSS))
    });
    // style.app.css
    gulp.task('style.app.css', function() {
        gulp.src(appCss)
          .pipe(sass({outputStyle: 'compressed', precision: 10}))
          .pipe(autoprefixer({browsers: ['last 1 version']}))
          .pipe(gulp.dest(tmplCSS))
    });

  // COPY TEMPLATE LIBS
    // CORE
    gulp.task('build-common', function() {
      return gulp.src(common+'/**/*', {base: common})
      .pipe(gulp.dest(tmpl))
    });
    // BASE
    gulp.task('cms-build-common', function() {
      return gulp.src(commonCms+'/**/*', {base: commonCms})
      .pipe(gulp.dest(tmpl))
    });
    // SITE
    gulp.task('site-build-common', function() {
      return gulp.src(commonSite+'/**/*', {base: commonSite})
      .pipe(gulp.dest(tmpl))
    });

// CLEAN PROJECT
var tmplClean = [tmpl+'/**', '!'+tmpl];
gulp.task('_reset', function() { del(tmplClean) });

// Builder JS
gulp.task('builder-js', ['default.js', 'forms.js', 'validate.js']);
// Builder CSS
gulp.task('builder-css', ['style.css', 'style.basic.css', 'style.ie.css', 'style.print.css', 'style.app.css']);
// Builder Template
gulp.task('_builder-core', ['builder-css', 'builder-js', 'build-common', 'cms-build-common', 'site-build-common']);
gulp.task('_builder-tmpl', ['site-build-common']);
gulp.task('_builder', ['_builder-core', '_builder-tmpl']);
