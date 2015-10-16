// Include gulp
var gulp = require('gulp');

 // Define base folders
var builder     = 'builder';
var core        = builder + '/_core';
var base        = builder + '/_baseCore';
// DEFINE PROJECT APP: enter project folder
var app         = builder + '/digivox';
// DEFINE PROJECT SITE: enter project folder
var cms         = builder + '/_base';
// ------------------------------------
var common      = core + '/common';
var commonApp   = app + '/common';
var commonBase  = base + '/common';
var commonCms   = cms + '/common';
var tmpl        = 'template';
var tmplJS      = tmpl + '/js';
var tmplCSS     = tmpl + '/css';

// CORE -----------------------------
  // JS files
  var coreJs = core+'/js/core.js'; // General implementations of CORE
  var menuJs = core+'/common/libs/jquery-mmenu/jquery.mmenu.min.all.js'; // jQuery menu mobile 'mmenu' plugin
  var formsJs = [
    core+'/js/core.forms.js', // Implementations of CORE for forms
    core+'/js/forms/jquery.autotab-1.1b.js', // Auto tab functionality
    core+'/js/forms/jquery.maskedinput.min.js', // Plugin for masks in text fields
    core+'/js/forms/jquery.price_format.min.js', // Formats the price in text fields
    core+'/js/bootstrap/bootstrap-field-context.js' // Method for assigning the field validation classes of bootstrap
  ];
  var validateJs = [
    core+'/js/forms/jquery-validation/jquery.validate.min.js', // jQuery validation plugin
    core+'/js/forms/jquery-validation/additional-methods.min.js', // jQuery validation plugin addon
    core+'/js/core.validation.js' // Customizations for validation
  ];
  // Default JS -> Javascript libraries loaded by default
  var _defaultJs = [
    core+'/bootstrap/js/bootstrap.min.js', // Bootstrap functionalities
    core+'/js/bootstrap/bootstrap-tabdrop.js', // Plugin to compress the tabs or pills when it exceeds the maximum width
    core+'/js/bootstrap/bootstrap-hover-dropdown.min.js', // Plugin to display/hide the dropdown in Hover event
    core+'/js/bootstrap/bootstrap-table.js', // Option for dynamic table with order, pagination, etc...
    core+'/js/content/jquery.actual.js', // Get dimensions of the hidden elements
    core+'/common/libs/chosen/chosen.jquery.min.js', // Formats the fields of type 'select'
    core+'/js/content/fontsize.js', // Own method to increase/decrease the font size on the page body
    core+'/js/browser/respond.min.js', // script to enable responsive web designs in browsers that don't support CSS3 Media Queries - in particular, Internet Explorer 8 and under
    core+'/js/ie.core.js' // General implementations of CORE
  ];

// APP -----------------------------
  // JS files
  var customAppJs = [app+'/js/custom.js'];
  var appJs = [
    core+'/common/libs/js/browser/css_browser_selector.js', // Plugin to set the user browser used
    menuJs,
    coreJs
  ];
  var defaultAppJs = _defaultJs.concat(appJs, formsJs, customAppJs);
  var guideAppJs = app+'/js/guide.js'; // Methods and specific style guide features
  // CSS files
  var appCss = app+'/sass/style.scss'; // General css stylesheet
  var appPrintCss = app+'/sass/style.print.scss'; // Stylesheet for printing
  var appIeCss = app+'/sass/style.ie.scss'; // Specific style sheet for IE
  var appEditorCss = app+'/sass/style.editor.scss'; // Stylesheet to be used in text editors
  var appGuideCss = app+'/sass/style.guide.scss'; // Specific style sheet for style guide

// CMS -----------------------------
  // JS files
  var customCmsJs = [cms+'/js/custom.js']; // Specific customizations from froject
  var cmsJs = [base+'/core/js/cms.frontend.js']; // Specific customizations from CMS
  var defaultCmsJs = _defaultJs.concat(cmsJs, customCmsJs);
  var guideCmsJs = cms+'/js/guide.js'; // Methods and specific style guide features
  // CSS files
  var cmsCss = cms+'/sass/style.scss'; // General css stylesheet
  var cmsPrintCss = cms+'/sass/style.print.scss'; // Stylesheet for printing
  var cmsIeCss = cms+'/sass/style.ie.scss'; // Specific style sheet for IE
  var cmsEditorCss = cms+'/sass/style.editor.scss'; // Stylesheet to be used in text editors
  var cmsGuideCss = cms+'/sass/style.guide.scss'; // Specific style sheet for style guide

  var cmsNavbarCss = base+'/core/sass/cms.frontend.navbar.scss'; // Stylesheet for administrator navbar in frontend
  var cmsAdminCss = base+'/core/sass/cms.admin.scss'; // Specific customizations from CMS Administration area

 // Include plugins
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var sass = require('gulp-sass');
var del = require('del');

// TASKS
  // JS -----------------------------
    // core.js
      // APP
      gulp.task('app-core.js', function() {
          return gulp.src(coreJs)
            .pipe(uglify())
            .pipe(gulp.dest(tmplJS))
      });
      // CMS
      gulp.task('cms-core.js', function() {
          return gulp.src(coreJs)
            .pipe(uglify())
            .pipe(gulp.dest(tmplJS))
      });
    // forms.js
      // APP
      gulp.task('app-forms.js', function() {
          return gulp.src(formsJs)
            .pipe(concat('forms.js'))
            .pipe(uglify())
            .pipe(gulp.dest(tmplJS))
      });
      // CMS
      gulp.task('cms-forms.js', function() {
          return gulp.src(formsJs)
            .pipe(concat('forms.js'))
            .pipe(uglify())
            .pipe(gulp.dest(tmplJS))
      });
    // validate.js
      // APP
      gulp.task('app-validate.js', function() {
          return gulp.src(validateJs)
            .pipe(concat('validate.js'))
            .pipe(uglify())
            .pipe(gulp.dest(tmplJS))
      });
      // CMS
      gulp.task('cms-validate.js', function() {
          return gulp.src(validateJs)
            .pipe(concat('validate.js'))
            .pipe(uglify())
            .pipe(gulp.dest(tmplJS))
      });
    // style.guide.js
      // APP
      gulp.task('app-style.guide.js', function() {
          return gulp.src(guideAppJs)
            .pipe(uglify())
            .pipe(gulp.dest(tmplJS))
      });
      // CMS
      gulp.task('cms-style.guide.js', function() {
          return gulp.src(guideCmsJs)
            .pipe(uglify())
            .pipe(gulp.dest(tmplJS))
      });
    // default.js
      // APP
      gulp.task('app-default.js', function() {
          return gulp.src(defaultAppJs)
            .pipe(concat('default.js'))
            .pipe(uglify())
            .pipe(gulp.dest(tmplJS))
      });
      // CMS
      gulp.task('cms-default.js', function() {
          return gulp.src(defaultCmsJs)
            .pipe(concat('default.js'))
            .pipe(uglify())
            .pipe(gulp.dest(tmplJS))
      });

  // CSS -----------------------------
    // style.editor.css
      // APP
      gulp.task('app-style.editor.css', function() {
          gulp.src(appEditorCss)
            .pipe(sass({outputStyle: 'compressed', precision: 10}))
            .pipe(gulp.dest(tmplCSS))
      });
      // CMS
      gulp.task('cms-style.editor.css', function() {
          gulp.src(cmsEditorCss)
            .pipe(sass({outputStyle: 'compressed', precision: 10}))
            .pipe(gulp.dest(tmplCSS))
      });
    // style.guide.css
      // APP
      gulp.task('app-style.guide.css', function() {
          gulp.src(appGuideCss)
            .pipe(sass({outputStyle: 'compressed', precision: 10}))
            .pipe(gulp.dest(tmplCSS))
      });
      // CMS
      gulp.task('cms-style.guide.css', function() {
          gulp.src(cmsGuideCss)
            .pipe(sass({outputStyle: 'compressed', precision: 10}))
            .pipe(gulp.dest(tmplCSS))
      });
    // style.ie.css
      // APP
      gulp.task('app-style.ie.css', function() {
          gulp.src(appIeCss)
            .pipe(sass({outputStyle: 'compressed', precision: 10}))
            .pipe(gulp.dest(tmplCSS))
      });
      // CMS
      gulp.task('cms-style.ie.css', function() {
          gulp.src(cmsIeCss)
            .pipe(sass({outputStyle: 'compressed', precision: 10}))
            .pipe(gulp.dest(tmplCSS))
      });
    // style.css
      // APP
      gulp.task('app-style.css', function() {
          gulp.src(appCss)
            .pipe(sass({outputStyle: 'compressed', precision: 10}))
            .pipe(gulp.dest(tmplCSS))
      });
      // CMS
      gulp.task('cms-style.css', function() {
          gulp.src(cmsCss)
            .pipe(sass({outputStyle: 'compressed', precision: 10}))
            .pipe(gulp.dest(tmplCSS))
      });
    // style.print.css
      // APP
      gulp.task('app-style.print.css', function() {
          gulp.src(appPrintCss)
            .pipe(sass({outputStyle: 'compressed', precision: 10}))
            .pipe(gulp.dest(tmplCSS))
      });
      // CMS
      gulp.task('cms-style.print.css', function() {
          gulp.src(cmsPrintCss)
            .pipe(sass({outputStyle: 'compressed', precision: 10}))
            .pipe(gulp.dest(tmplCSS))
      });
    // CMS's
      // cms.frontend.navbar.css
      gulp.task('cms.frontend.navbar.css', function() {
          gulp.src(cmsNavbarCss)
            .pipe(sass({outputStyle: 'compressed', precision: 10}))
            .pipe(gulp.dest(tmplCSS))
      });
      // cms.admin.css
      gulp.task('cms.admin.css', function() {
          gulp.src(cmsAdminCss)
            .pipe(sass({outputStyle: 'compressed', precision: 10}))
            .pipe(gulp.dest(tmplCSS))
      });

  // CLEAN PROJECT
  var tmplClean = [tmpl+'/**', '!'+tmpl, '!'+tmpl+'/_dev', '!'+tmpl+'/_dev/**']; //['template/**', '!template', '!template/_dev', '!template/_dev/**']; //[tmpl+'/**', '!'+tmpl, '!'+tmpl+'/_dev'];
  gulp.task('_reset', function() { del(tmplClean) });

  // COPY TEMPLATE LIBS
    // CORE
    gulp.task('build-common', function() {
      gulp.src(common+'/**/*', {base: common})
      .pipe(gulp.dest(tmpl+'/core'))
    });
    // APP
    gulp.task('app-build-common', function() {
      gulp.src(commonApp+'/**/*', {base: commonApp})
      .pipe(gulp.dest(tmpl))
    });
    // BASE
    gulp.task('base-build-common', function() {
      gulp.src(commonBase+'/**/*', {base: commonBase})
      .pipe(gulp.dest(tmpl))
    });
    // CMS
    gulp.task('cms-build-common', function() {
      gulp.src(commonCms+'/**/*', {base: commonCms})
      .pipe(gulp.dest(tmpl))
    });

// Builder JS
gulp.task('app-builder-js', ['app-core.js', 'app-forms.js', 'app-validate.js', 'app-style.guide.js', 'app-default.js']);
gulp.task('cms-builder-js', ['cms-core.js', 'cms-forms.js', 'cms-validate.js', 'cms-style.guide.js', 'cms-default.js']);
// Builder CSS
gulp.task('app-builder-css', ['app-style.editor.css', 'app-style.guide.css', 'app-style.ie.css', 'app-style.css', 'app-style.print.css']);
gulp.task('cms-builder-css', ['cms-style.editor.css', 'cms-style.guide.css', 'cms-style.ie.css', 'cms-style.css', 'cms-style.print.css', 'cms.frontend.navbar.css', 'cms.admin.css']);
// Builder Template
gulp.task('_builder-app', ['_reset', 'build-common', 'app-build-common', 'app-builder-js', 'app-builder-css']);
gulp.task('_builder-cms', ['_reset', 'build-common', 'base-build-common', 'cms-build-common', 'cms-builder-js', 'cms-builder-css']);
