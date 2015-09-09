// Include gulp
var gulp = require('gulp');

 // Define base folders
var builder = 'builder/';
var core    = builder + 'core/';
var app     = core + '_app/';
var cms     = core + '_cms/';
var common  = core + '_common/';
var custom  = builder + 'custom/';
var tmpl    = 'templates/';
var jsCms   = tmpl + 'cms/js/';
var cssCms  = tmpl + 'cms/css/';
var jsApp   = tmpl + 'app/js/';
var cssApp  = tmpl + 'app/css/';

// CUSTOM -----------------------------
  // COMMON
    // JS files
    var commonJs = custom+'common/js/common.js';
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
  var _defaultJs = [core+'bootstrap/js/bootstrap.min.js', core+'js/bootstrap/bootstrap-tabdrop.js', core+'js/bootstrap/bootstrap-hover-dropdown.min.js', core+'js/browser/respond.min.js', core+'js/content/fontsize.js', core+'js/ie.core.js'];

// APP -----------------------------
  // JS files
  var appJs = [common+'libs/js/browser/css_browser_selector.js', common+'libs/chosen/chosen.jquery.min.js', coreJs];
  var defaultAppJs = _defaultJs.concat(appJs, formsJs, customAppJs);
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
    gulp.task('core.js', function() {
        return gulp.src(coreJs)
          .pipe(uglify())
          .pipe(gulp.dest(jsApp)) // app
          .pipe(gulp.dest(jsCms)) // cms
    });
    // forms.js
    gulp.task('forms.js', function() {
        return gulp.src(formsJs)
          .pipe(concat('forms.js'))
          .pipe(uglify())
          .pipe(gulp.dest(jsApp)) // app
          .pipe(gulp.dest(jsCms)) // cms
    });
    // validate.js
    gulp.task('validate.js', function() {
        return gulp.src(validateJs)
          .pipe(concat('validate.js'))
          .pipe(uglify())
          .pipe(gulp.dest(jsApp)) // app
          .pipe(gulp.dest(jsCms)) // cms
    });
    // style.guide.js
    gulp.task('style.guide.js', function() {
        return gulp.src(guideJs)
          .pipe(uglify())
          .pipe(gulp.dest(jsApp)) // app
          .pipe(gulp.dest(jsCms)) // cms
    });
    // default.js
      // APP: default + APP + custom
      gulp.task('app-default.js', function() {
          return gulp.src(defaultAppJs)
            .pipe(concat('default.js'))
            .pipe(uglify())
            .pipe(gulp.dest(jsApp)) // app
      });
      // CMS: default + CMS + custom
      gulp.task('cms-default.js', function() {
          return gulp.src(defaultCmsJs)
            .pipe(concat('default.js'))
            .pipe(uglify())
            .pipe(gulp.dest(jsCms)) // cms
      });

  // CSS -----------------------------
    // style.editor.css
    gulp.task('style.editor.css', function() {
        gulp.src(editorCss)
          .pipe(sass({outputStyle: 'compressed', precision: 10}))
          .pipe(gulp.dest(cssApp)) // app
          .pipe(gulp.dest(cssCms)) // cms
    });
    // style.guide.css
    gulp.task('style.guide.css', function() {
        gulp.src(guideCss)
          .pipe(sass({outputStyle: 'compressed', precision: 10}))
          .pipe(gulp.dest(cssApp)) // app
          .pipe(gulp.dest(cssCms)) // cms
    });
    // style.ie.css
    gulp.task('style.ie.css', function() {
        gulp.src(ieCss)
          .pipe(sass({outputStyle: 'compressed', precision: 10}))
          .pipe(gulp.dest(cssApp)) // app
          .pipe(gulp.dest(cssCms)) // cms
    });
    // style.css
      // APP: APP + custom
      gulp.task('app-style.css', function() {
          gulp.src(appCss)
            .pipe(sass({outputStyle: 'compressed', precision: 10}))
            .pipe(gulp.dest(cssApp)) // app
      });
      // CMS: CMS + custom
      gulp.task('cms-style.css', function() {
          gulp.src(cmsCss)
            .pipe(sass({outputStyle: 'compressed', precision: 10}))
            .pipe(gulp.dest(cssCms)) // cms
      });
    // style.print.css
      // APP: APP + custom
      gulp.task('app-style.print.css', function() {
          gulp.src(appPrintCss)
            .pipe(sass({outputStyle: 'compressed', precision: 10}))
            .pipe(gulp.dest(cssApp)) // app
      });
      // CMS: CMS + custom
      gulp.task('cms-style.print.css', function() {
          gulp.src(cmsPrintCss)
            .pipe(sass({outputStyle: 'compressed', precision: 10}))
            .pipe(gulp.dest(cssCms)) // cms
      });
    // CMS's
      // cms.frontend.navbar.css
      gulp.task('cms.frontend.navbar.css', function() {
          gulp.src(cmsNavbarCss)
            .pipe(sass({outputStyle: 'compressed', precision: 10}))
            .pipe(gulp.dest(cssCms)) // cms
      });
      // cms.admin.css
      gulp.task('cms.admin.css', function() {
          gulp.src(cmsAdminCss)
            .pipe(sass({outputStyle: 'compressed', precision: 10}))
            .pipe(gulp.dest(cssCms)) // cms
      });

  // COPY COMMON DIRECTORIES
  var source = common;
  var appDest = tmpl+'app/core';
  var cmsDest = tmpl+'cms/core';
  var tmplCleanApp = [tmpl+'app/css/**', tmpl+'app/js/**', tmpl+'app/core/**']
  var tmplCleanCms = [tmpl+'cms/css/**', tmpl+'cms/js/**', tmpl+'cms/core/**']
  var tmplClean = tmplCleanApp.concat(tmplCleanCms);
  gulp.task('_reset', function() {
    del(tmplClean)
  });
  gulp.task('build-common', function() {
    gulp.src(source + '/**/*', {base: source})
    .pipe(gulp.dest(appDest)) // app
    .pipe(gulp.dest(cmsDest)) // cms
  });
  // COPY TEMPLATE LIBS
    // APP
    var srcApp = app+'libs';
    var appLibs = tmpl+'app/libs';
    gulp.task('build-libs-app', function() {
      gulp.src(srcApp+'/**/*', {base: srcApp})
      .pipe(gulp.dest(appLibs))
    });
    // CMS
    var srcCms = cms+'libs';
    var cmsLibs = tmpl+'cms/libs';
    gulp.task('build-libs-cms', function() {
      gulp.src(srcCms+'/**/*', {base: srcCms})
      .pipe(gulp.dest(cmsLibs))
    });

// WATCH
  // Watch .js files
  // gulp.task('watch-js', function() {
  //   gulp.watch(coreJs, ['core.js']);
  //   gulp.watch(formsJs, ['forms.js']);
  //   gulp.watch(validateJs, ['validate.js']);
  //   gulp.watch(guideJs, ['style.guide.js']);
  //   gulp.watch(defaultJs, ['app-default.js']);
  //   gulp.watch(defaultJs, ['cms-default.js']);
  // });
  // // Watch .scss files
  // gulp.task('watch-css', function() {
  //   gulp.watch(editorCss, ['style.editor.css']);
  //   gulp.watch(guideCss, ['style.guide.css']);
  //   gulp.watch(ieCss, ['style.ie.css']);
  //   gulp.watch(appCss, ['app-style.css']);
  //   gulp.watch(cmsCss, ['cms-style.css']);
  //   gulp.watch(appPrintCss, ['app-style.print.css']);
  //   gulp.watch(cmsPrintCss, ['cms-style.print.css']);
  //   gulp.watch(cmsNavbarCss, ['cms.frontend.navbar.css']);
  //   gulp.watch(cmsAdminCss, ['cms.admin.css']);
  // });

// Default Task
gulp.task('builder-js', ['core.js', 'forms.js', 'validate.js', 'style.guide.js', 'app-default.js', 'cms-default.js']);
gulp.task('builder-css', ['style.editor.css', 'style.guide.css', 'style.ie.css', 'app-style.css', 'cms-style.css', 'app-style.print.css', 'cms-style.print.css', 'cms.frontend.navbar.css', 'cms.admin.css']);
gulp.task('builder', ['_reset', 'build-common', 'build-libs-app', 'build-libs-cms', 'builder-js', 'builder-css']);
