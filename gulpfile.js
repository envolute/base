// Include gulp
var gulp = require('gulp');

 // Define base folders
var builder   = 'builder';
var core      = builder + '/core';
var app       = builder + '/app';
var cms       = builder + '/cms';
var common    = core + '/common';
var commonApp = app + '/common';
var commonCms = cms + '/common';
var tmpl      = 'template';
var tmplJS    = tmpl + '/js';
var tmplCSS   = tmpl + '/css';

// CORE -----------------------------
  // JS files
  var coreJs = core+'/js/core.js';
  var formsJs = [core+'/js/core.forms.js', core+'/js/forms/jquery.autotab-1.1b.js', core+'/js/forms/jquery.maskedinput.min.js', core+'/js/forms/jquery.price_format.min.js', core+'/js/bootstrap/bootstrap-field-context.js'];
  var validateJs = [core+'/js/forms/jquery-validation/jquery.validate.min.js', core+'/js/forms/jquery-validation/additional-methods.min.js', core+'/js/core.validation.js'];
  // Default JS -> Javascript libraries loaded by default
  var _defaultJs = [core+'/bootstrap/js/bootstrap.min.js', core+'/js/bootstrap/bootstrap-tabdrop.js', core+'/js/bootstrap/bootstrap-hover-dropdown.min.js', core+'/js/browser/respond.min.js', core+'/js/content/fontsize.js', core+'/js/ie.core.js'];

// APP -----------------------------
  // JS files
  var customAppJs = [app+'/js/custom.js'];
  var appJs = [core+'/common/libs/js/browser/css_browser_selector.js', core+'/common/libs/chosen/chosen.jquery.min.js', coreJs];
  var defaultAppJs = _defaultJs.concat(appJs, formsJs, customAppJs);
  var guideAppJs = app+'/js/guide.js';
  // CSS files
  var appCss = app+'/sass/style.scss';
  var appPrintCss = app+'/sass/style.print.scss';
  var appIeCss = app+'/sass/style.ie.scss';
  var appEditorCss = app+'/sass/style.editor.scss';
  var appGuideCss = app+'/sass/style.guide.scss';

// CMS -----------------------------
  // JS files
  var customCmsJs = [app+'/js/custom.js'];
  var cmsJs = [cms+'/core/js/cms.frontend.js'];
  var defaultCmsJs = _defaultJs.concat(cmsJs, customCmsJs);
  var guideCmsJs = cms+'/js/guide.js';
  // CSS files
  var cmsCss = cms+'/sass/style.scss';
  var cmsPrintCss = cms+'/sass/style.print.scss';
  var cmsIeCss = cms+'/sass/style.ie.scss';
  var cmsEditorCss = cms+'/sass/style.editor.scss';
  var cmsGuideCss = cms+'/sass/style.guide.scss';

  var cmsNavbarCss = cms+'/core/sass/cms.frontend.navbar.scss';
  var cmsAdminCss = cms+'/core/sass/cms.admin.scss';

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
gulp.task('_builder-cms', ['_reset', 'build-common', 'cms-build-common', 'cms-builder-js', 'cms-builder-css']);
