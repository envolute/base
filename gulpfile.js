// Include gulp
var gulp = require('gulp');

 // Define base folders
var builder = 'builder/';
var core    = builder + 'core/';
var app     = builder + 'app/';
var cms     = builder + 'cms/';
var common  = builder + 'common/';
var custom  = builder + 'custom/';
var tmpl    = 'templates/';
var jsCms   = tmpl + 'cms/js/';
var cssCms  = tmpl + 'cms/css/';
var jsApp   = tmpl + 'app/js/';
var cssApp  = tmpl + 'app/css/';

// CUSTOM -----------------------------
  // COMMON
    // JS files
    var commonJs = custom+'common/js/custom.js';
    var guideJs = custom+'common/js/guide.js';
      // APP
      var customAppJs = [commonJs, custom+'app/js/custom.js'];
      // CMS
      var customCmsJs = [commonJs, custom+'cms/js/custom.js'];

    // CSS files
    var guideCss = custom+'common/sass/style.guide.scss';
    var editorCss = custom+'common/sass/style.editor.scss';
    var ieCss = custom+'common/sass/style.ie.scss';

// CORE -----------------------------
  // JS files
  var coreJs = core+'js/core.js';
  var formsJs = [core+'js/core.forms.js', core+'js/forms/jquery.autotab-1.1b.js', core+'js/forms/jquery.maskedinput.min.js', core+'js/forms/jquery.price_format.min.js'];
  var validateJs = [core+'js/forms/jquery-validation/jquery.validate.min.js', core+'js/forms/jquery-validation/additional-methods.min.js', core+'js/core.validation.js'];
  // Default JS -> Javascript libraries loaded by default
  var _defaultJs = [core+'bootstrap/js/bootstrap.min.js', core+'js/bootstrap/bootstrap-tabdrop.js', core+'js/bootstrap/bootstrap-hover-dropdown.min.js', core+'js/browser/respond.min.js', core+'js/template/isInViewport.min.js', core+'js/content/fontsize.js', core+'js/ie.core.js'];

// APP -----------------------------
  // JS files
  var appJs = [coreJs, common+'libs/js/browser/css_browser_selector.js'];
  var defaultAppJs = appJs.concat(_defaultJs, customAppJs);
  // CSS files
  var appCss = custom+'app/style.scss';
  var appPrintCss = custom+'app/style.print.scss';

// CMS -----------------------------
  // JS files
  var cmsJs = [cms+'js/cms.frontend.js'];
  var defaultCmsJs = cmsJs.concat(_defaultJs, customCmsJs);
  // CSS files
  var cmsCss = custom+'cms/style.scss';
  var cmsPrintCss = custom+'cms/style.print.scss';
  var cmsNavbarCss = cms+'sass/cms.frontend.navbar.scss';
  var cmsAdminCss = cms+'sass/cms.admin.scss';

 // Include plugins
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var sass = require('gulp-sass');
var del = require('del');
// updating browser
var browserSync = require('browser-sync');
gulp.task('browserSync', function() {
  browserSync({
    server: {
      baseDir: 'builder'
    },
  })
})

// TASKS
  // JS -----------------------------
    // core.js
    gulp.task('coreJs', function() {
        return gulp.src(coreJs)
          .pipe(uglify())
          .pipe(gulp.dest(jsApp)) // app
          .pipe(gulp.dest(jsCms)) // cms
    });
    // forms.js
    gulp.task('formsJs', function() {
        return gulp.src(formsJs)
          .pipe(concat('forms.js'))
          .pipe(uglify())
          .pipe(gulp.dest(jsApp)) // app
          .pipe(gulp.dest(jsCms)) // cms
    });
    // validate.js
    gulp.task('validateJs', function() {
        return gulp.src(validateJs)
          .pipe(concat('validate.js'))
          .pipe(uglify())
          .pipe(gulp.dest(jsApp)) // app
          .pipe(gulp.dest(jsCms)) // cms
    });
    // style.guide.js
    gulp.task('guideJs', function() {
        return gulp.src(guideJs)
          .pipe(uglify())
          .pipe(gulp.dest(jsApp)) // app
          .pipe(gulp.dest(jsCms)) // cms
    });
    // default.js
      // APP: default + APP + custom
      gulp.task('defaultAppJs', function() {
          return gulp.src(defaultAppJs)
            .pipe(concat('default.js'))
            .pipe(uglify())
            .pipe(gulp.dest(jsApp)) // app
      });
      // CMS: default + CMS + custom
      gulp.task('defaultCmsJs', function() {
          return gulp.src(defaultCmsJs)
            .pipe(concat('default.js'))
            .pipe(uglify())
            .pipe(gulp.dest(jsCms)) // cms
      });

  // CSS -----------------------------
    // style.editor.css
    gulp.task('editorCss', function() {
        gulp.src(editorCss)
          .pipe(sass({outputStyle: 'compressed'}))
          .pipe(gulp.dest(cssApp)) // app
          .pipe(gulp.dest(cssCms)) // cms
    });
    gulp.task('guideCss', function() {
        gulp.src(guideCss)
          .pipe(sass({outputStyle: 'compressed'}))
          .pipe(gulp.dest(cssApp)) // app
          .pipe(gulp.dest(cssCms)) // cms
    });
    gulp.task('ieCss', function() {
        gulp.src(ieCss)
          .pipe(sass({outputStyle: 'compressed'}))
          .pipe(gulp.dest(cssApp)) // app
          .pipe(gulp.dest(cssCms)) // cms
    });
    // style.css
      // APP: APP + custom
      gulp.task('appCss', function() {
          gulp.src(appCss)
            .pipe(sass({outputStyle: 'compressed'}))
            .pipe(gulp.dest(cssApp)) // app
      });
      // CMS: CMS + custom
      gulp.task('cmsCss', function() {
          gulp.src(cmsCss)
            .pipe(sass({outputStyle: 'compressed'}))
            .pipe(gulp.dest(cssCms)) // cms
      });
    // style.print.css
      // APP: APP + custom
      gulp.task('appPrintCss', function() {
          gulp.src(appPrintCss)
            .pipe(sass({outputStyle: 'compressed'}))
            .pipe(gulp.dest(cssApp)) // app
      });
      // CMS: CMS + custom
      gulp.task('cmsPrintCss', function() {
          gulp.src(cmsPrintCss)
            .pipe(sass({outputStyle: 'compressed'}))
            .pipe(gulp.dest(cssCms)) // cms
      });
    // CMS's
      // cms.frontend.navbar.css
      gulp.task('cmsNavbarCss', function() {
          gulp.src(cmsNavbarCss)
            .pipe(sass({outputStyle: 'compressed'}))
            .pipe(gulp.dest(cssCms)) // cms
      });
      // cms.admin.css
      gulp.task('cmsAdminCss', function() {
          gulp.src(cmsAdminCss)
            .pipe(sass({outputStyle: 'compressed'}))
            .pipe(gulp.dest(cssCms)) // cms
      });

  // COPY COMMON DIRECTORIES
  var source = common;
  var appDest = tmpl+'app';
  var cmsDest = tmpl+'cms';
  var cleanDest = [tmpl+'fonts/**', tmpl+'images/docs/**', tmpl+'libs/**']
  gulp.task('build-common', function() {
    gulp.src(source + '/**/*', {base: source})
    .pipe(del(cleanDest))
    //.pipe(gulp.dest(appDest)) // app
    //.pipe(gulp.dest(cmsDest)) // cms
  });
  // COPY TEMPLATE LIBS
    // APP
    var srcApp = app+'libs';
    var appLibs = tmpl+'app/libs';
    gulp.task('build-app-libs', function() {
      gulp.src(srcApp+'/**/*', {base: srcApp})
      .pipe(gulp.dest(appLibs))
    });
    // CMS
    var srcCms = cms+'libs';
    var cmsLibs = tmpl+'cms/libs';
    gulp.task('build-cms-libs', function() {
      gulp.src(srcCms+'/**/*', {base: srcCms})
      .pipe(gulp.dest(cmsLibs))
    });

// Watch for changes in files
gulp.task('watch', function() {
  // Watch .js files
  gulp.watch(coreJs, ['coreJs']);
  gulp.watch(formsJs, ['formsJs']);
  gulp.watch(validateJs, ['validateJs']);
  gulp.watch(guideJs, ['guideJs']);
  gulp.watch(defaultJs, ['defaultAppJs']);
  gulp.watch(defaultJs, ['defaultCmsJs']);
  // Watch .scss files
  gulp.watch(editorCss, ['editorCss']);
  gulp.watch(guideCss, ['guideCss']);
  gulp.watch(ieCss, ['ieCss']);
  gulp.watch(appCss, ['appCss']);
  gulp.watch(cmsCss, ['cmsCss']);
  gulp.watch(appPrintCss, ['appPrintCss']);
  gulp.watch(cmsPrintCss, ['cmsPrintCss']);
  gulp.watch(cmsNavbarCss, ['cmsNavbarCss']);
  gulp.watch(cmsAdminCss, ['cmsAdminCss']);
  // Watch builder files
  //gulp.watch(appDests, ['build-fonts']);
});

// Default Task
gulp.task('default', ['coreJs', 'formsJs', 'validateJs', 'guideJs', 'defaultAppJs', 'defaultCmsJs', 'editorCss', 'guideCss', 'styleAppCss', 'styleCmsCss', 'appPrintCss', 'cmsPrintCss', 'cmsNavbarCss', 'cmsAdminCss']);
