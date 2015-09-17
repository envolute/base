<?php
$base = "templates/base/";
?>
<div class="row">
	<div class="col-lg-9">
		<?php require_once('style-brands.php'); ?>
		<!-- Color Scheme
		================================================== -->
		<section id="color-scheme">

			<h1 id="base-colors" class="page-header">Color Scheme </h1>

			<div class="row bottom-space">
				<div class="col-md-3"><span class="label label-warning">BASE</span></div>
				<div class="col-md-3 col-md-offset-3 text-center"><span class="base-icon-star text-live"></span> Base</div>
			</div>
			<div class="row bottom-space">
				<div class="col-md-3"><div class="color-1-lighter valign-middle all-expand-lg border-radius"><code>.color-1-lighter</code></div></div>
				<div class="col-md-3"><div class="color-1-light valign-middle all-expand-lg border-radius"><code>.color-1-light</code></div></div>
				<div class="col-md-3"><div class="color-1 valign-middle all-expand-lg border-radius"><code>.color-1</code></div></div>
				<div class="col-md-3"><div class="color-1-dark valign-middle all-expand-lg border-radius"><code>.color-1-dark</code></div></div>
			</div>
			<div class="row bottom-space">
				<div class="col-md-3"><div class="color-2-lighter valign-middle all-expand-lg border-radius"><code>.color-2-lighter</code></div></div>
				<div class="col-md-3"><div class="color-2-light valign-middle all-expand-lg border-radius"><code>.color-2-light</code></div></div>
				<div class="col-md-3"><div class="color-2 valign-middle all-expand-lg border-radius"><code>.color-2</code></div></div>
				<div class="col-md-3"><div class="color-2-dark valign-middle all-expand-lg border-radius"><code>.color-2-dark</code></div></div>
			</div>
			<div class="row bottom-space">
				<div class="col-md-3"><div class="color-3-lighter valign-middle all-expand-lg border-radius"><code>.color-3-lighter</code></div></div>
				<div class="col-md-3"><div class="color-3-light valign-middle all-expand-lg border-radius"><code>.color-3-light</code></div></div>
				<div class="col-md-3"><div class="color-3 valign-middle all-expand-lg border-radius"><code>.color-3</code></div></div>
				<div class="col-md-3"><div class="color-3-dark valign-middle all-expand-lg border-radius"><code>.color-3-dark</code></div></div>
			</div>
			<div class="row bottom-space">
				<div class="col-md-3"><div class="color-muted-lighter valign-middle all-expand-lg border-radius"><code>.color-muted-lighter</code></div></div>
				<div class="col-md-3"><div class="color-muted-light valign-middle all-expand-lg border-radius"><code>.color-muted-light</code></div></div>
				<div class="col-md-3"><div class="color-muted valign-middle all-expand-lg border-radius"><code>.color-muted</code></div></div>
				<div class="col-md-3"><div class="color-muted-dark valign-middle all-expand-lg border-radius"><code>.color-muted-dark</code></div></div>
			</div>
			<div class="row bottom-space">
				<div class="col-md-3"><div class="color-live valign-middle all-expand-lg border-radius"><code>.color-live"</code></div></div>
			</div>
			<hr />
			<p><span class="label label-primary">BOOTSTRAP</span></p>
			<div class="row bottom-space">
				<div class="col-md-3"><div class="body-bg valign-middle all-expand-lg border-radius set-border border-default">Background Color<br /><code>$body-bg</code></div></div>
				<div class="col-md-3"><div class="text-color valign-middle all-expand-lg border-radius set-border border-default">Text Color<br /><code>$text-color</code></div></div>
				<div class="col-md-3"><div class="link-color valign-middle all-expand-lg border-radius set-border border-default">Link Color<br /><code>$link-color</code></div></div>
				<div class="col-md-3"><div class="link-hover-color valign-middle all-expand-lg border-radius set-border border-default">Link Hover Color<br /><code>$link-hover-color</code></div></div>
			</div>
			<hr class="hr-label" /><span class="label label-default">Gray</span>
			<div class="row bottom-space">
				<div class="col-md-2"><div class="gray-base valign-middle all-expand-lg border-radius"><code>$gray-base</code></div></div>
				<div class="col-md-2"><div class="gray-darker valign-middle all-expand-lg border-radius"><code>$gray-darker</code></div></div>
				<div class="col-md-2"><div class="gray-dark valign-middle all-expand-lg border-radius"><code>$gray-dark</code></div></div>
				<div class="col-md-2"><div class="gray valign-middle all-expand-lg border-radius"><code>$gray</code></div></div>
				<div class="col-md-2"><div class="gray-light valign-middle all-expand-lg border-radius"><code>$gray-light</code></div></div>
				<div class="col-md-2"><div class="gray-lighter valign-middle all-expand-lg border-radius"><code>$gray-lighter</code></div></div>
			</div>
			<div class="row bottom-space">
				<div class="col-md-3"><hr class="hr-label hr-xs" /><span class="label label-default">Brand colors</span></div>
				<div class="col-md-3"><hr class="hr-label hr-xs" /><span class="label label-default">Context BG</span></div>
				<div class="col-md-3"><hr class="hr-label hr-xs" /><span class="label label-default">Context tr/td BG</span></div>
				<div class="col-md-3"><hr class="hr-label hr-xs" /><span class="label label-default">Context Text colors</span></div>
			</div>
			<div class="row">
				<div class="col-md-3">
					<div class="btn-primary valign-middle all-expand-lg border-radius bottom-space"><code>$brand-primary</code></div>
					<div class="btn-success valign-middle all-expand-lg border-radius bottom-space"><code>$brand-success</code></div>
					<div class="btn-info valign-middle all-expand-lg border-radius bottom-space"><code>$brand-info</code></div>
					<div class="btn-warning valign-middle all-expand-lg border-radius bottom-space"><code>$brand-warning</code></div>
					<div class="btn-danger valign-middle all-expand-lg border-radius bottom-space"><code>$brand-danger</code></div>
				</div>
				<div class="col-md-3">
					<div class="valign-middle all-expand-lg border-radius bottom-space">&nbsp;</div>
					<div class="bg-success valign-middle all-expand-lg border-radius bottom-space"><code>.bg-success</code></div>
					<div class="bg-info valign-middle all-expand-lg border-radius bottom-space"><code>.bg-info</code></div>
					<div class="bg-warning valign-middle all-expand-lg border-radius bottom-space"><code>.bg-warning</code></div>
					<div class="bg-danger valign-middle all-expand-lg border-radius bottom-space"><code>.bg-danger</code></div>
				</div>
				<div class="col-md-3">
					<table class="table">
						<tr class="active"><td class="valign-middle all-expand"><code>tr/td.active</code></td></tr>
						<tr><td class="all-expand-sm"></td></tr>
						<tr class="success"><td class="valign-middle all-expand"><code>tr/td.success</code></td></tr>
						<tr><td class="all-expand-sm"></td></tr>
						<tr class="info"><td class="valign-middle all-expand"><code>tr/td.info</code></div></tr>
						<tr><td class="all-expand-sm"></td></tr>
						<tr class="warning"><td class="valign-middle all-expand"><code>tr/td.warning</code></td></tr>
						<tr><td class="all-expand-sm"></td></tr>
						<tr class="danger"><td class="valign-middle all-expand"><code>tr/td.danger</code></td></tr>
					</table>
				</div>
				<div class="col-md-3">
					<div class="text-primary valign-middle all-expand-lg border-radius bottom-space"><strong>Primary</strong><code class="pull-right">.text-active</code></div>
					<div class="text-success valign-middle all-expand-lg border-radius bottom-space"><strong>Success</strong><code class="pull-right">.text-success</code></div>
					<div class="text-info valign-middle all-expand-lg border-radius bottom-space"><strong>Info</strong><code class="pull-right">.text-info</code></div>
					<div class="text-warning valign-middle all-expand-lg border-radius bottom-space"><strong>Warning</strong><code class="pull-right">.text-warning</code></div>
					<div class="text-danger valign-middle all-expand-lg border-radius bottom-space"><strong>Danger</strong><code class="pull-right">.text-danger</code></div>
				</div>
			</div>

		</section>


		<!-- Fonts
		================================================== -->
		<section id="fonts">

			<h1 class="page-header no-margin-bottom">Fonts <span class="label label-warning text-md pull-right top-space">BASE</span></h1>

			<table class="table table-striped table-hover">
				<tbody>
					<tr>
						<th>DEFAULT</th>
						<td></td>
						<td>Grumpy wizards make toxic brew for the evil Queen and Jack.</td>
					</tr>
					<tr>
						<th colspan="3" class="warning"><span class="base-icon-star text-live"></span> FONT OPTIONS</th>
					</tr>
					<tr>
						<th>Primary</th>
						<td>.font-primary</td>
						<td class="font-primary text-lg">Grumpy wizards make toxic brew for the evil Queen and Jack.</td>
					</tr>
					<tr>
						<th>Condensed</th>
						<td>.font-condensed</td>
						<td class="font-condensed text-lg">Grumpy wizards make toxic brew for the evil Queen and Jack.</td>
					</tr>
					<tr>
						<th>Serif</th>
						<td>.font-serif</td>
						<td class="font-serif text-lg">Grumpy wizards make toxic brew for the evil Queen and Jack.</td>
					</tr>
					<tr>
						<th>Featured</th>
						<td>.font-featured</td>
						<td class="font-featured text-lg">Grumpy wizards make toxic brew for the evil Queen and Jack.</td>
					</tr>
				</tbody>
			</table>

		</section>


		<!-- Navbar
		================================================== -->
		<section id="navbar">

			<h1 class="page-header">Navbar <span class="label label-primary text-md pull-right top-space">BOOTSTRAP</span></h1>

			<nav class="navbar navbar-default">
				<div class="container-fluid">
					<div class="navbar-header">
					<button type="button" name="name" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="#">Brand</a>
					</div>

					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav">
						<li class="active"><a href="#">Link <span class="sr-only">(current)</span></a></li>
						<li><a href="#">Link</a></li>
						<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Dropdown <span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu">
							<li><a href="#">Action</a></li>
							<li><a href="#">Another action</a></li>
							<li><a href="#">Something else here</a></li>
							<li class="divider"></li>
							<li><a href="#">Separated link</a></li>
							<li class="divider"></li>
							<li><a href="#">One more separated link</a></li>
						</ul>
						</li>
					</ul>
					<form class="navbar-form navbar-left" role="search">
						<div class="form-group">
						<input type="text" name="name" class="form-control" placeholder="Search">
						</div>
						<button type="submit" name="name" class="btn btn-default">Submit</button>
					</form>
					<ul class="nav navbar-nav navbar-right">
						<li><a href="#">Link</a></li>
					</ul>
					</div>
				</div>
			</nav>

			<nav class="navbar navbar-inverse">
				<div class="container-fluid">
					<div class="navbar-header">
					<button type="button" name="name" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-2">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="#">Brand</a>
					</div>

					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-2">
					<ul class="nav navbar-nav">
						<li class="active"><a href="#">Link <span class="sr-only">(current)</span></a></li>
						<li><a href="#">Link</a></li>
						<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Dropdown <span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu">
							<li><a href="#">Action</a></li>
							<li><a href="#">Another action</a></li>
							<li><a href="#">Something else here</a></li>
							<li class="divider"></li>
							<li><a href="#">Separated link</a></li>
							<li class="divider"></li>
							<li><a href="#">One more separated link</a></li>
						</ul>
						</li>
					</ul>
					<form class="navbar-form navbar-left" role="search">
						<div class="form-group">
						<input type="text" name="name" class="form-control" placeholder="Search">
						</div>
						<button type="submit" name="name" class="btn btn-default">Submit</button>
					</form>
					<ul class="nav navbar-nav navbar-right">
						<li><a href="#">Link</a></li>
					</ul>
					</div>
				</div>
			</nav>

			<div id="source-button" class="btn btn-primary btn-xs" style="display: none;">&lt; &gt;</div>

		</section>


		<!-- Buttons
		================================================== -->
		<section id="buttons">

			<h1 class="page-header">Buttons <span class="label label-primary text-md pull-right top-space">BOOTSTRAP</span></h1>

			<div class="row">
				<div class="col-lg-6">

					<p>
						<a href="#" class="btn btn-default">Default</a>
						<a href="#" class="btn btn-primary">Primary</a>
						<a href="#" class="btn btn-success">Success</a>
						<a href="#" class="btn btn-info">Info</a>
						<a href="#" class="btn btn-warning">Warning</a>
						<a href="#" class="btn btn-danger">Danger</a>
						<a href="#" class="btn btn-link">Link</a>
					</p>

					<p>
						<a href="#" class="btn btn-default disabled">Default</a>
						<a href="#" class="btn btn-primary disabled">Primary</a>
						<a href="#" class="btn btn-success disabled">Success</a>
						<a href="#" class="btn btn-info disabled">Info</a>
						<a href="#" class="btn btn-warning disabled">Warning</a>
						<a href="#" class="btn btn-danger disabled">Danger</a>
						<a href="#" class="btn btn-link disabled">Link</a>
					</p>


					<div class="bottom-space">
						<div class="btn-group">
							<a href="#" class="btn btn-default">Default</a>
							<a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
							<ul class="dropdown-menu">
							<li><a href="#">Action</a></li>
							<li><a href="#">Another action</a></li>
							<li><a href="#">Something else here</a></li>
							<li class="divider"></li>
							<li><a href="#">Separated link</a></li>
							</ul>
						</div>

						<div class="btn-group">
							<a href="#" class="btn btn-primary">Primary</a>
							<a href="#" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
							<ul class="dropdown-menu">
							<li><a href="#">Action</a></li>
							<li><a href="#">Another action</a></li>
							<li><a href="#">Something else here</a></li>
							<li class="divider"></li>
							<li><a href="#">Separated link</a></li>
							</ul>
						</div>

						<div class="btn-group">
							<a href="#" class="btn btn-success">Success</a>
							<a href="#" class="btn btn-success dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
							<ul class="dropdown-menu">
							<li><a href="#">Action</a></li>
							<li><a href="#">Another action</a></li>
							<li><a href="#">Something else here</a></li>
							<li class="divider"></li>
							<li><a href="#">Separated link</a></li>
							</ul>
						</div>

						<div class="btn-group">
							<a href="#" class="btn btn-info">Info</a>
							<a href="#" class="btn btn-info dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
							<ul class="dropdown-menu">
							<li><a href="#">Action</a></li>
							<li><a href="#">Another action</a></li>
							<li><a href="#">Something else here</a></li>
							<li class="divider"></li>
							<li><a href="#">Separated link</a></li>
							</ul>
						</div>

						<div class="btn-group">
							<a href="#" class="btn btn-warning">Warning</a>
							<a href="#" class="btn btn-warning dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
							<ul class="dropdown-menu">
							<li><a href="#">Action</a></li>
							<li><a href="#">Another action</a></li>
							<li><a href="#">Something else here</a></li>
							<li class="divider"></li>
							<li><a href="#">Separated link</a></li>
							</ul>
						</div>
					</div>

					<a href="#" class="btn btn-primary btn-lg">Large button</a>
					<a href="#" class="btn btn-primary">Default button</a>
					<a href="#" class="btn btn-primary btn-sm">Small button</a>
					<a href="#" class="btn btn-primary btn-xs">Mini button</a>

				</div>
				<div class="col-lg-6">

					<p>
						<a href="#" class="btn btn-default btn-lg btn-block">Block level button</a>
					</p>


					<div class="bottom-space">
						<div class="btn-group btn-group-justified">
						<a href="#" class="btn btn-default">Left</a>
						<a href="#" class="btn btn-default">Middle</a>
						<a href="#" class="btn btn-default">Right</a>
						</div>
					</div>

					<div class="bottom-space">
						<div class="btn-toolbar">
						<div class="btn-group">
							<a href="#" class="btn btn-default">1</a>
							<a href="#" class="btn btn-default">2</a>
							<a href="#" class="btn btn-default">3</a>
							<a href="#" class="btn btn-default">4</a>
						</div>

						<div class="btn-group">
							<a href="#" class="btn btn-default">5</a>
							<a href="#" class="btn btn-default">6</a>
							<a href="#" class="btn btn-default">7</a>
						</div>

						<div class="btn-group">
							<a href="#" class="btn btn-default">8</a>
							<div class="btn-group">
							<a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
								Dropdown
								<span class="caret"></span>
							</a>
							<ul class="dropdown-menu">
								<li><a href="#">Dropdown link</a></li>
								<li><a href="#">Dropdown link</a></li>
								<li><a href="#">Dropdown link</a></li>
							 </ul>
							</div>
						</div>
						</div>
					</div>

					<div class="btn-group-vertical">
						<a href="#" class="btn btn-default">Button</a>
						<a href="#" class="btn btn-default">Button</a>
						<a href="#" class="btn btn-default">Button</a>
						<a href="#" class="btn btn-default">Button</a>
					</div>

				</div>
			</div>

			<div id="dropdown-hover" class="well top-space">
				<div class="row">
					<div class="col-lg-6">
						<h4 class="page-header"><span class="base-icon-star text-live"></span> Toggle Status</h4>
						<p>With the use of ".toggle-status" class on the button, we can change the "active / inactive" status when it receives the event 'click'.<br />The class can be attributed directly to the button or the container (group) buttons as a 'btn-group'. In this case, the function will be assigned to all group buttons.<p>
						<p><code>&lt;button class="btn btn-default toggle-status"&gt;Text&lt;/button&gt;</code></p>
						<p><button class="btn btn-default toggle-status">Click</button></p>
						<p><code>&lt;span class="btn-group toggle-status"&gt;... buttons ... &lt;/span&gt;</code></p>
						<p>
							<span class="btn-group">
								<button class="btn btn-primary toggle-status"> 1 </button>
								<button class="btn btn-warning toggle-status"> 2 </button>
								<button class="btn btn-danger toggle-status"> 3 </button>
							</span>
						</p>
					</div>
					<div class="col-lg-6">
						<h4 class="page-header"><span class="base-icon-star text-live"></span> Dropdown On Hover</h4>
						<p>An unofficial Bootstrap plugin to enable Bootstrap dropdowns to activate on hover and provide a nice user experience.<p>
						<h4>how to use:</h4>
						<p>Just add <code>data-hover="dropdown"</code> where you'd put <code>data-toggle="dropdown"</code></p>
						<p class="alert alert-info"><span class="base-icon-info-circled"></span> It also works with buttons and tabs</p>
						Developer's site: <a href="https://cameronspear.com/blog/bootstrap-dropdown-on-hover-plugin/" target="_blank">https://cameronspear.com/blog/bootstrap-dropdown-on-hover-plugin/</a><br />
						Demo site: <a href="https://cameronspear.com/demos/bootstrap-hover-dropdown/" target="_blank">https://cameronspear.com/demos/bootstrap-hover-dropdown/</a><br />
						Git: <a href="https://github.com/CWSpear/bootstrap-hover-dropdown" target="_blank">https://github.com/CWSpear/bootstrap-hover-dropdown</a>
					</div>
			</div>

		</section>

		<!-- Typography
		================================================== -->
		<section id="type">

			<h1 class="page-header">Typography <span class="label label-primary text-md pull-right top-space">BOOTSTRAP</span></h1>

			<!-- Headings -->

			<div class="row">
				<div class="col-lg-4">
				<h1>Heading 1</h1>
				<h2>Heading 2</h2>
				<h3>Heading 3</h3>
				<h4>Heading 4</h4>
				<h5>Heading 5</h5>
				<h6>Heading 6</h6>
				<code>&lt;p class="lead" /&gt;</code>
				<p class="lead">Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>
				<code>&lt;p class="small" /&gt;</code>
				<p class="small">Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>
				</div>
				<div class="col-lg-4">
				<h2>Example body text</h2>
				<p>Nullam quis risus eget <a href="#">urna mollis ornare</a> vel eu leo. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nullam id dolor id nibh ultricies vehicula.</p>
				<p><small>This line of text is meant to be treated as fine print.</small></p>
				<p>The following snippet of text is <strong>rendered as bold text</strong>.</p>
				<p>The following snippet of text is <em>rendered as italicized text</em>.</p>
				<p>An abbreviation of the word attribute is <abbr title="attribute">attr</abbr>.</p>
				</div>
				<div class="col-lg-4">
				<h2>Emphasis classes</h2>
				<code>&lt;p class="text-muted" /&gt;</code>
				<p class="text-muted">Fusce dapibus, tellus ac cursus commodo, tortor mauris nibh.</p>
				<code>&lt;p class="text-primary" /&gt;</code>
				<p class="text-primary">Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
				<code>&lt;p class="text-warning" /&gt;</code>
				<p class="text-warning">Etiam porta sem malesuada magna mollis euismod.</p>
				<code>&lt;p class="text-danger" /&gt;</code>
				<p class="text-danger">Donec ullamcorper nulla non metus auctor fringilla.</p>
				<code>&lt;p class="text-success" /&gt;</code>
				<p class="text-success">Duis mollis, est non commodo luctus, nisi erat porttitor ligula.</p>
				<code>&lt;p class="text-info" /&gt;</code>
				<p class="text-info">Maecenas sed diam eget risus varius blandit sit amet non magna.</p>
				</div>
			</div>
			<div class="well">
				<h4 class="page-header">Additional Options for Text Format <span class="label label-warning pull-right">BASE</span></h4>
				<div class="row">
					<div class="col-lg-3">
					<div class="bs-component">
						<code>&lt;p class="text-white" /&gt;</code>
						<p class="text-white">Maecenas sed diam eget risus varius blandit sit amet non magna.</p>
					</div>
					</div>
					<div class="col-lg-3">
					<div class="bs-component">
						<code>&lt;p class="text-live" /&gt;</code>
						<p class="text-live">Maecenas sed diam eget risus varius blandit sit amet non magna.</p>
						<span class="label label-info"><span class="base-icon-info-circled"></span> Emphasis Color</span>
					</div>
					</div>
					<div class="col-lg-3">
					<div class="bs-component">
						<code>&lt;p class="strong" /&gt;</code>
						<p class="strong">Maecenas sed diam eget risus varius blandit sit amet non magna.</p>
					</div>
					</div>
					<div class="col-lg-3">
					<div class="bs-component">
						<code>&lt;p class="text-overflow" /&gt;</code>
						<p class="text-overflow text-white bg-live">Maecenas sed diam eget risus varius blandit sit amet non magna.</p>
						<span class="label label-info"><span class="base-icon-info-circled"></span> Text limited by container</span>
					</div>
					</div>
				</div>

				<hr>

				<h4 class="page-header">Additional Options for Font Size</h4>
				<div class="row">
					<div class="col-lg-6">
					<div class="bs-component">
						<p><span class="label label-primary">FIXED FONT SIZE</span></p>
						<code>&lt;p class="text-xs" /&gt;</code>
						<p class="text-xs">Maecenas sed diam eget risus varius blandit sit amet non magna.</p>
						<code>&lt;p class="text-sm" /&gt;</code>
						<p class="text-sm">Maecenas sed diam eget risus varius blandit sit amet non magna.</p>
						<code>&lt;p class="text-md" /&gt;</code> <span class="label label-default">Default</span>
						<p class="text-md">Maecenas sed diam eget risus varius blandit sit amet non magna.</p>
						<code>&lt;p class="text-lg" /&gt;</code>
						<p class="text-lg">Maecenas sed diam eget risus varius blandit sit amet non magna.</p>
					</div>
					</div>
					<div class="col-lg-6">
					<div class="bs-component">
						<p><span class="label label-primary">CONTEXTUAL FONT SIZE</span> <span class="label label-info">Note: recommended for "Font-Sizer"</span></p>
						<code>&lt;p class="tam1" /&gt;</code>
						<p class="tam1">Maecenas sed diam eget risus varius blandit sit amet non magna.</p>
						<code>&lt;p class="tam2" /&gt;</code>
						<p class="tam2">Maecenas sed diam eget risus varius blandit sit amet non magna.</p>
						<code>&lt;p class="tam3" /&gt;</code>
						<p class="tam3">Maecenas sed diam eget risus varius blandit sit amet non magna.</p>
						<code>&lt;p class="tam4" /&gt;</code>
						<p class="tam4">Maecenas sed diam eget risus varius blandit sit amet non magna.</p>
						<code>&lt;p class="tam5" /&gt;</code>
						<p class="tam5">Maecenas sed diam eget risus varius blandit sit amet non magna.</p>
						<code>&lt;p class="tam6" /&gt;</code>
						<p class="tam6">Maecenas sed diam eget risus varius blandit sit amet non magna.</p>
						<code>&lt;p class="tam7" /&gt;</code>
						<p class="tam7">Maecenas sed diam eget risus varius blandit sit amet non magna.</p>
					</div>
					</div>
				</div>
			</div>

			<!-- Alignment -->

			<h2 id="type-alignment">Alignment</h2>

			<div class="row">
				<div class="col-lg-3">
				<p><span class="label label-primary">Left</span> <code>&lt;p class="text-left" /&gt;</code></p>
				<p class="text-left">Nullam quis risus eget <a href="#">urna mollis ornare</a> vel eu leo. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nullam id dolor id nibh ultricies vehicula.</p>
				</div>
				<div class="col-lg-3">
				<p><span class="label label-primary">Right</span> <code>&lt;p class="text-right" /&gt;</code></p>
				<p class="text-right">Nullam quis risus eget <a href="#">urna mollis ornare</a> vel eu leo. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nullam id dolor id nibh ultricies vehicula.</p>
				</div>
				<div class="col-lg-3">
				<p><span class="label label-primary">Center</span> <code>&lt;p class="text-center" /&gt;</code></p>
				<p class="text-center">Nullam quis risus eget <a href="#">urna mollis ornare</a> vel eu leo. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nullam id dolor id nibh ultricies vehicula.</p>
				</div>
				<div class="col-lg-3">
				<p><span class="label label-primary">Justify</span> <code>&lt;p class="text-justify" /&gt;</code></p>
				<p class="text-justify">Nullam quis risus eget <a href="#">urna mollis ornare</a> vel eu leo. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nullam id dolor id nibh ultricies vehicula.</p>
				</div>
			</div>

			<div class="well">
				<h4 class="page-header">Responsive Alignment <span class="label label-warning pull-right">BASE</span></h4>
				<table class="table">
					<thead>
						<tr>
						<th colspan="2">@media-xs</th>
						<th colspan="2">@media-sm to xs</th>
						</tr>
					</thead>
					<tbody>
						<tr>
						<th>.text-left-xs</th>
						<td>Set left alignment when @media -> xs</td>
						<th>.text-left-sm</th>
						<td>Set left alignment when @media -> sm (xs also)</td>
						</tr>
						<tr>
						<th>.text-right-xs</th>
						<td>Set right alignment when @media -> xs</td>
						<th>.text-right-sm</th>
						<td>Set right alignment when @media -> sm (xs also)</td>
						</tr>
						<tr>
						<th>.text-center-xs</th>
						<td>Set center alignment when @media -> xs</td>
						<th>.text-center-sm</th>
						<td>Set center alignment when @media -> sm (xs also)</td>
						</tr>
					</tbody>
				</table>
				<hr />
				<h4 class="page-header">Additional Options for Media Object alignment</h4>
				<div class="row">
					<div class="col-lg-4">
					<div class="bs-component">
						<p><span class="label label-primary">Left</span> <code>&lt;img class="obj-to-left" /&gt;</code></p>
						<p>
							<img src="http://placehold.it/60x60" width="60" height="60" class="obj-to-left" />
							Nullam quis risus eget vel eu leo. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nullam id dolor id nibh ultricies vehicula.. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.
						</p>
					</div>
					</div>
					<div class="col-lg-4">
					<div class="bs-component">
						<p><span class="label label-primary">Center</span> <code>&lt;img class="obj-to-center" /&gt;</code></p>
						<p>
							<img src="http://placehold.it/60x60" width="60" height="60" class="obj-to-center" />
							Nullam quis risus eget vel eu leo. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nullam id dolor id nibh ultricies vehicula.
						</p>
					</div>
					</div>
					<div class="col-lg-4">
					<div class="bs-component">
						<p><span class="label label-primary">Right</span> <code>&lt;img class="obj-to-right" /&gt;</code></p>
						<p>
							<img src="http://placehold.it/60x60" width="60" height="60" class="obj-to-right" />
							Nullam quis risus eget vel eu leo. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nullam id dolor id nibh ultricies vehicula.. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.
						</p>
					</div>
					</div>
				</div>
			</div>

			<!-- horizontal rule -->

			<h2 id="type-hr">Horizontal Rule</h2>

			<div>
				<code>&lt;hr&gt;</code><hr />
			</div>

			<div class="well">
				<h4 class="page-header">Additional Spacing Options for horizontal rule <span class="label label-warning pull-right">BASE</span></h4>
				<div class="row">
					<div class="col-lg-4">
					<div class="bs-component">
						<span class="label label-primary">Extra Small</span> <code>&lt;hr class="hr-xs" /&gt;</code>
						<div class="bg-gray"><hr class="hr-xs" /></div>
					</div>
					</div>
					<div class="col-lg-4">
					<div class="bs-component">
						<span class="label label-primary">Small</span> <code>&lt;hr class="hr-sm" /&gt;</code>
						<div class="bg-gray"><hr class="hr-sm" /></div>
					</div>
					</div>
					<div class="col-lg-4">
					<div class="bs-component">
						<span class="label label-primary">Large</span> <code>&lt;hr class="hr-lg" /&gt;</code>
						<div class="bg-gray"><hr class="hr-lg" /></div>
					</div>
					</div>
				</div>
				<h4>Format Options</h4>
				<div class="row">
					<div class="col-lg-4">
					<div class="bs-component">
						<span class="label label-primary">Strong</span> <code>&lt;hr class="hr-strong" /&gt;</code>
						<div class="bg-gray"><hr class="hr-strong" /></div>
					</div>
					</div>
					<div class="col-lg-4">
					<div class="bs-component">
						<span class="label label-primary">White</span> <code>&lt;hr class="hr-white" /&gt;</code>
						<div class="bg-gray"><hr class="hr-white" /></div>
					</div>
					</div>
					<div class="col-lg-4">
					<div class="bs-component">
						<span class="label label-primary">Live</span> <code>&lt;hr class="hr-live" /&gt;</code>
						<div class="bg-gray"><hr class="hr-live" /></div>
					</div>
					</div>
				</div>
				<h4>hr-label</h4>
				<div class="row">
					<div class="col-lg-6">
					<div class="bs-component">
						<hr class="hr-label" /><span class="label">default</span>
						<p><code>&lt;hr class="hr-label" /&gt;&lt;span class="label"&gt;right&lt;/span&gt;&lt;/code&gt;</code></p>
					</div>
					</div>
					<div class="col-lg-6">
					<div class="bs-component">
						<hr class="hr-label" /><span class="label pull-right">right</span>
						<p><code>&lt;hr class="hr-label" /&gt;&lt;span class="label pull-right"&gt;right&lt;/span&gt;&lt;/code&gt;</code></p>
					</div>
					</div>
				</div>
				<div class="alert alert-info">
					<span class="base-icon-info-circled"></span> To align the label right just add the class <em class="strong">.pull-right</em> to label
					<br /><span class="base-icon-info-circled"></span> The &lt;hr /&gt; tag can be used with classes <em class="strong">hr-xs, .hr-sm & hr-lg</em>
					<br /><span class="base-icon-info-circled"></span> The class ".label" can be used with or without the classes <em class="strong">.label-default, primary, warning, info & danger</em>
				</div>
			</div>

			<!-- Blockquotes -->

			<h2 id="type-blockquotes">Blockquotes</h2>

			<div>
				<code>&lt;blockquote&gt;</code>
				<code class="pull-right">&lt;blockquote class="blockquote-reverse" /&gt;</code>
			</div>
			<div class="row">
				<div class="col-lg-6">
					<blockquote>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.</p>
					<small>Someone famous in <cite title="Source Title">Source Title</cite></small>
					</blockquote>
				</div>
				<div class="col-lg-6">
					<blockquote class="pull-right">
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.</p>
					<small>Someone famous in <cite title="Source Title">Source Title</cite></small>
					</blockquote>
				</div>
			</div>

			<!-- Lists -->

			<h2 id="type-lists">Lists</h2>

			<div class="row">
				<div class="col-lg-4">
					<p><span class="label label-primary">Unordered</span> <code>&lt;ul&gt;</code></p>
					<ul>
						<li>Lorem ipsum dolor sit amet</li>
						<li>Nulla volutpat aliquam velit
						<ul>
							<li>Phasellus iaculis neque</li>
							<li>Purus sodales ultricies</li>
						</ul>
						</li>
						<li>Faucibus porta lacus fringilla vel</li>
					</ul>
				</div>
				<div class="col-lg-4">
					<p><span class="label label-primary">Ordered</span> <code>&lt;ol&gt;</code></p>
					<ol>
						<li>Lorem ipsum dolor sit amet</li>
						<li>Nulla volutpat aliquam velit
						<ol>
							<li>Phasellus iaculis neque</li>
							<li>Purus sodales ultricies</li>
						</ol>
						</li>
						<li>Faucibus porta lacus fringilla vel</li>
					</ol>
				</div>
				<div class="col-lg-4">
					<p><span class="label label-primary">Unstyled</span> <code>&lt;ul class="list-unstyled" /&gt;</code></p>
					<ul class="list-unstyled">
						<li>Lorem ipsum dolor sit amet</li>
						<li>Nulla volutpat aliquam velit
						<ul>
							<li>Phasellus iaculis neque</li>
							<li>Purus sodales ultricies</li>
						</ul>
						</li>
						<li>Faucibus porta lacus fringilla vel</li>
					</ul>
				</div>
			</div>
			<hr />
			<div class="bottom-space">
				<p><span class="label label-primary">Inline</span> <code>&lt;ul class="list-inline" /&gt;</code></p>
				<ul class="list-inline">
					<li>Lorem ipsum dolor sit amet</li>
					<li>Nulla volutpat aliquam velit</li>
					<li>Phasellus iaculis neque</li>
					<li>Purus sodales ultricies</li>
					<li>Faucibus porta lacus fringilla vel</li>
				</ul>
			</div>
			<hr />
			<div class="row">
				<div class="col-lg-6">
					<p><span class="label label-primary">DESCRIPTION</span> <code>&lt;dl&gt;</code></p>
					<dl>
						<dt>Description lists</dt>
						<dd>A description list is perfect for defining terms.</dd>
						<dt>Euismod</dt>
						<dd>Vestibulum id ligula porta felis euismod semper eget lacinia odio sem nec elit.</dd>
						<dd>Donec id elit non mi porta gravida at eget metus.</dd>
						<dt>Malesuada porta</dt>
						<dd>Etiam porta sem malesuada magna mollis euismod.</dd>
					</dl>
				</div>
				<div class="col-lg-6">
					<p><span class="label label-primary">DESCRIPTION HORIZONTAL</span> <code>&lt;dl class="dl-horizontal" /&gt;</code></p>
					<dl class="dl-horizontal">
						<dt>Description lists</dt>
						<dd>A description list is perfect for defining terms.</dd>
						<dt>Euismod</dt>
						<dd>Vestibulum id ligula porta felis euismod semper eget lacinia odio sem nec elit.</dd>
						<dd>Donec id elit non mi porta gravida at eget metus.</dd>
						<dt>Malesuada porta</dt>
						<dd>Etiam porta sem malesuada magna mollis euismod.</dd>
						<dt>Felis euismod semper eget lacinia</dt>
						<dd>Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</dd>
					</dl>
				</div>
			</div>
			<div class="well">
				<h4 class="page-header">Additional Options for Lists <span class="label label-warning pull-right">BASE</span></h4>
				<div class="row">
					<div class="col-lg-3">
						<span class="label label-primary">LIST</span>
						<br /><code>&lt;ul class="list" /&gt;</code>
						<ul class="list">
							<li>Lorem ipsum dolor sit amet</li>
							<li>Phasellus iaculis neque</li>
							<li>Purus sodales ultricies</li>
							<li>Nulla volutpat aliquam velit
							<ul>
								<li>Phasellus iaculis neque</li>
								<li>Purus sodales ultricies</li>
							</ul>
							</li>
							<li>Faucibus porta lacus fringilla vel</li>
						</ul>
					</div>
					<div class="col-lg-3">
						<span class="label label-default">SMALL</span>
						<br /><code>&lt;ul class="list list-sm" /&gt;</code>
						<ul class="list list-sm">
							<li>Lorem ipsum dolor sit amet</li>
							<li>Phasellus iaculis neque</li>
							<li>Purus sodales ultricies</li>
							<li>Nulla volutpat aliquam velit
							<ul>
								<li>Phasellus iaculis neque</li>
								<li>Purus sodales ultricies</li>
							</ul>
							</li>
							<li>Faucibus porta lacus fringilla vel</li>
						</ul>
					</div>
					<div class="col-lg-3">
						<span class="label label-default">LARGE</span>
						<br /><code>&lt;ul class="list list-lg" /&gt;</code>
						<ul class="list list-lg">
							<li>Lorem ipsum dolor sit amet</li>
							<li>Nulla volutpat aliquam velit
							<ul>
								<li>Phasellus iaculis neque</li>
								<li>Purus sodales ultricies</li>
							</ul>
							</li>
							<li>Faucibus porta lacus fringilla vel</li>
						</ul>
					</div>
					<div class="col-lg-3">
						<span class="label label-default">STRIPED</span>
						<br /><code>&lt;ul class="list list-striped" /&gt;</code>
						<ul class="list list-striped">
							<li>Lorem ipsum dolor sit amet</li>
							<li>Phasellus iaculis neque</li>
							<li>Nulla volutpat aliquam velit
							<ul>
								<li>Phasellus iaculis neque</li>
								<li>Purus sodales ultricies</li>
								<li>Purus sodales ultricies</li>
							</ul>
							</li>
							<li>Phasellus iaculis neque</li>
							<li>Faucibus porta lacus fringilla vel</li>
						</ul>
					</div>
				</div>
				<hr />
				<div class="row">
					<div class="col-lg-3">
						<span class="label label-primary">LIST MENU</span>
						<br /><code>&lt;ul class="list-menu" /&gt;</code>
						<ul class="list-menu">
							<li><a href="#">Lorem ipsum dolor sit amet</a></li>
							<li><a href="#">Phasellus iaculis neque</a></li>
							<li><a href="#">Purus sodales ultricies</a></li>
							<li><a href="#">Nulla volutpat aliquam velit</a>
							<ul>
								<li><a href="#">Phasellus iaculis neque</a></li>
								<li><a href="#">Purus sodales ultricies</a></li>
							</ul>
							</li>
							<li><a href="#">Faucibus porta lacus fringilla vel</a></li>
						</ul>
					</div>
					<div class="col-lg-3">
						<span class="label label-default">SMALL</span>
						<br /><code>&lt;ul class="list-menu list-sm" /&gt;</code>
						<ul class="list-menu list-sm">
							<li><a href="#">Lorem ipsum dolor sit amet</a></li>
							<li><a href="#">Phasellus iaculis neque</a></li>
							<li><a href="#">Purus sodales ultricies</a></li>
							<li><a href="#">Nulla volutpat aliquam velit</a>
							<ul>
								<li><a href="#">Phasellus iaculis neque</a></li>
								<li><a href="#">Purus sodales ultricies</a></li>
							</ul>
							</li>
							<li><a href="#">Faucibus porta lacus fringilla vel</a></li>
						</ul>
					</div>
					<div class="col-lg-3">
						<span class="label label-default">LARGE</span>
						<br /><code>&lt;ul class="list-menu list-lg" /&gt;</code>
						<ul class="list-menu list-lg">
							<li><a href="#">Lorem ipsum dolor sit amet</a></li>
							<li><a href="#">Nulla volutpat aliquam velit</a>
							<ul>
								<li><a href="#">Phasellus iaculis neque</a></li>
								<li><a href="#">Purus sodales ultricies</a></li>
							</ul>
							</li>
							<li><a href="#">Faucibus porta lacus fringilla vel</a></li>
						</ul>
					</div>
					<div class="col-lg-3">
						<span class="label label-default">STRIPED</span>
						<br /><code>&lt;ul class="list-menu list-striped" /&gt;</code>
						<ul class="list-menu list-striped">
							<li><a href="#">Lorem ipsum dolor sit amet</a></li>
							<li><a href="#">Phasellus iaculis neque</a></li>
							<li><a href="#">Purus sodales ultricies</a></li>
							<li><a href="#">Nulla volutpat aliquam velit</a>
							<ul>
								<li><a href="#">Phasellus iaculis neque</a></li>
								<li><a href="#">Purus sodales ultricies</a></li>
							</ul>
							</li>
							<li><a href="#">Faucibus porta lacus fringilla vel</a></li>
						</ul>
					</div>
				</div>
				<hr />
				<h4 class="page-header">Additional Options for Horizontal/Inline Lists</h4>
				<div class="row">
					<div class="col-lg-6">
					<div class="bottom-expand">
						<p><span class="label label-primary">HLIST</span> <code>&lt;ul class="hlist" /&gt;</code></p>
						<ul class="hlist">
							<li>Item 1</li>
							<li>Item 2</li>
							<li>Item 3</li>
							<li>Item 4</li>
							<li>Item 5</li>
						</ul>
					</div>
					<div class="bottom-expand">
						<p><span class="label label-default">SMALL</span> <code>&lt;ul class="hlist hlist-sm" /&gt;</code></p>
						<ul class="hlist hlist-sm">
							<li>Item 1</li>
							<li>Item 2</li>
							<li>Item 3</li>
							<li>Item 4</li>
							<li>Item 5</li>
						</ul>
					</div>
					<div class="bottom-expand">
						<p><span class="label label-default">LARGE</span> <code>&lt;ul class="hlist hlist-lg" /&gt;</code></p>
						<ul class="hlist hlist-lg">
							<li>Item 1</li>
							<li>Item 2</li>
							<li>Item 3</li>
							<li>Item 4</li>
							<li>Item 5</li>
						</ul>
					</div>
					<div class="bottom-expand">
						<p><span class="label label-default">STRIPED</span> <code>&lt;ul class="hlist list-striped" /&gt;</code></p>
						<ul class="hlist list-striped">
							<li>Item 1</li>
							<li>Item 2</li>
							<li>Item 3</li>
							<li>Item 4</li>
							<li>Item 5</li>
						</ul>
					</div>
					</div>
					<div class="col-lg-6">
					<div class="bottom-expand">
						<p><span class="label label-primary">HLIST MENU</span> <code>&lt;ul class="hlist-menu" /&gt;</code></p>
						<ul class="hlist-menu">
							<li><a href="#">Item 1</a></li>
							<li><a href="#">Item 2</a></li>
							<li><a href="#">Item 3</a></li>
							<li><a href="#">Item 4</a></li>
							<li><a href="#">Item 5</a></li>
						</ul>
					</div>
					<div class="bottom-expand">
						<p><span class="label label-default">SMALL</span> <code>&lt;ul class="hlist-menu hlist-sm" /&gt;</code></p>
						<ul class="hlist-menu hlist-sm">
							<li><a href="#">Item 1</a></li>
							<li><a href="#">Item 2</a></li>
							<li><a href="#">Item 3</a></li>
							<li><a href="#">Item 4</a></li>
							<li><a href="#">Item 5</a></li>
						</ul>
					</div>
					<div class="bottom-expand">
						<p><span class="label label-default">LARGE</span> <code>&lt;ul class="hlist-menu hlist-lg" /&gt;</code></p>
						<ul class="hlist-menu hlist-lg">
							<li><a href="#">Item 1</a></li>
							<li><a href="#">Item 2</a></li>
							<li><a href="#">Item 3</a></li>
							<li><a href="#">Item 4</a></li>
							<li><a href="#">Item 5</a></li>
						</ul>
					</div>
					<div class="bottom-expand">
						<p><span class="label label-default">STRIPED</span> <code>&lt;ul class="hlist-menu list-striped" /&gt;</code></p>
						<ul class="hlist-menu list-striped">
							<li><a href="#">Item 1</a></li>
							<li><a href="#">Item 2</a></li>
							<li><a href="#">Item 3</a></li>
							<li><a href="#">Item 4</a></li>
							<li><a href="#">Item 5</a></li>
						</ul>
					</div>
					</div>
				</div>
				<hr />
				<table class="table">
					<caption>Helper Classes for Lists</caption>
					<tbody>
						<tr>
						<th>.list-no-border</th>
						<td>Remove border of the list items</td>
						<td><code>&lt;ul class="list list-no-border" /&gt;</code></td>
						</tr>
						<tr>
						<th>.list-no-space</th>
						<td>Remove the spacing of the first (initial) and last (end) of the vertical list item</td>
						<td><code>&lt;ul class="list list-no-space" /&gt;</code></td>
						</tr>
						<tr>
						<th>.hlist-no-space</th>
						<td>Remove the spacing of the first (initial) and last (end) of the horizontal list item</td>
						<td><code>&lt;ul class="hlist hlist-no-space" /&gt;</code></td>
						</tr>
					</tbody>
				</table>

			</div>
		</section>

		<!-- Tables
		================================================== -->
		<section id="tables">

			<h1 class="page-header">Tables <span class="label label-primary text-md pull-right top-space">BOOTSTRAP</span></h1>

			<div class="row">
				<div class="col-md-6">
					<table class="table">
					<caption>Basic Table <code>&lt;table class="table"&gt;</code></caption>
					<thead>
						<tr>
						<th>#</th>
						<th>Heading</th>
						<th>Heading</th>
						<th>Heading</th>
						</tr>
					</thead>
					<tbody>
						<tr>
						<td>1</td>
						<td>Column content</td>
						<td>Column content</td>
						<td>Column content</td>
						</tr>
						<tr>
						<td>2</td>
						<td>Column content</td>
						<td>Column content</td>
						<td>Column content</td>
						</tr>
						<tr>
						<td>3</td>
						<td>Column content</td>
						<td>Column content</td>
						<td>Column content</td>
						</tr>
					</tbody>
					</table>
					<table class="table table-condensed">
					<caption>Table Condensed <code>&lt;table class="table table-condensed"&gt;</code></caption>
					<thead>
						<tr>
						<th>#</th>
						<th>Heading</th>
						<th>Heading</th>
						<th>Heading</th>
						</tr>
					</thead>
					<tbody>
						<tr>
						<td>1</td>
						<td>Column content</td>
						<td>Column content</td>
						<td>Column content</td>
						</tr>
						<tr>
						<td>2</td>
						<td>Column content</td>
						<td>Column content</td>
						<td>Column content</td>
						</tr>
						<tr>
						<td>3</td>
						<td>Column content</td>
						<td>Column content</td>
						<td>Column content</td>
						</tr>
					</tbody>
					</table>
				</div>
				<div class="col-md-6">
					<table class="table table-striped table-hover table-bordered">
					<caption>All Styles <code>&lt;table class="table table-striped table-hover table-bordered"&gt;</code></caption>
					<thead>
						<tr>
						<th>#</th>
						<th>Heading</th>
						<th>Heading</th>
						<th>Heading</th>
						</tr>
					</thead>
					<tbody>
						<tr>
						<td>1</td>
						<td>Column content</td>
						<td>Column content</td>
						<td>Column content</td>
						</tr>
						<tr>
						<td>2</td>
						<td>Column content</td>
						<td>Column content</td>
						<td>Column content</td>
						</tr>
						<tr class="info">
						<td>3</td>
						<td><code>&lt;tr/td class="info"&gt;</code></td>
						<td>Column content</td>
						<td>Column content</td>
						</tr>
						<tr class="success">
						<td>4</td>
						<td><code>&lt;tr/td class="success"&gt;</code></td>
						<td>Column content</td>
						<td>Column content</td>
						</tr>
						<tr class="danger">
						<td>5</td>
						<td><code>&lt;tr/td class="danger"&gt;</code></td>
						<td>Column content</td>
						<td>Column content</td>
						</tr>
						<tr class="warning">
						<td>6</td>
						<td><code>&lt;tr/td class="warning"&gt;</code></td>
						<td>Column content</td>
						<td>Column content</td>
						</tr>
						<tr class="active">
						<td>7</td>
						<td><code>&lt;tr/td class="active"&gt;</code></td>
						<td>Column content</td>
						<td>Column content</td>
						</tr>
					</tbody>
					</table>
					<p class="alert alert-info"><strong>Note:</strong> Contextual Colors can be applied to the row or cell</p>
				</div>
			</div>
		</section>

		<!-- Forms
		================================================== -->
		<section id="forms">

			<h1 class="page-header">Forms <span class="label label-primary text-md pull-right top-space">BOOTSTRAP</span></h1>

			<div class="row">
				<div class="col-lg-4">
					<form>
					<div class="bottom-expand"><span class="label label-primary">FORM DEFAULT</span> <code>&lt;form&gt;</code></div>
					<fieldset>
						<legend>Legend <span class="label label-default pull-right">Fieldset</span></legend>
						<div class="form-group">
						<label for="inputEmail">Email</label>
						<input type="text" class="form-control" name="name" id="inputEmail" placeholder="Email" />
						</div>
						<div class="form-group">
						<label for="inputPassword">Password</label>
						<input type="password" class="form-control" name="name" id="inputPassword" placeholder="Password" />
						</div>
						<div class="checkbox">
						<label>
							<input type="checkbox" name="name"> Checkbox
						</label>
						</div>
						<div class="form-group">
						<label for="textArea">Textarea</label>
						<textarea name="name" class="form-control" rows="3" id="textArea"></textarea>
						<span class="help-block">A longer block of help text that breaks onto a new line and may extend beyond one line.</span>
						</div>
						<div class="form-group">
						<label>Radios</label>
						<div class="radio">
							<label>
								<input type="radio" name="optionsRadios" id="optionsRadios1" value="option1" checked="">
								Option one is this
							</label>
						</div>
						<div class="radio">
							<label>
								<input type="radio" name="optionsRadios" id="optionsRadios2" value="option2">
								Option two can be something else
							</label>
						</div>
						</div>
						<div class="form-group">
						<label for="select">Select</label>
						<select name="name" class="form-control" id="select">
							<option>1</option>
							<option>2</option>
							<option>3</option>
							<option>4</option>
							<option>5</option>
						</select>
						</div>
						<div class="form-group">
						<label for="select">Select Multiple</label>
						<select name="name" multiple="" class="form-control">
							<option>1</option>
							<option>2</option>
							<option>3</option>
							<option>4</option>
							<option>5</option>
						</select>
						</div>
						<div class="form-group">
							<button type="reset" name="name" class="btn btn-default">Cancel</button>
							<button type="submit" name="name" class="btn btn-primary">Submit</button>
						</div>
					</fieldset>
					</form>
				</div>
				<div class="col-lg-7 col-lg-offset-1">
					<form class="form-horizontal">
					<div class="bottom-expand"><span class="label label-primary">FORM HORIZONTAL</span> <code>&lt;form class="form-horizontal" /&gt;</code></div>
					<fieldset>
						<legend>Legend <span class="label label-default pull-right">Fieldset</span></legend>
						<div class="form-group">
						<label for="inputEmail" class="col-lg-2 control-label">Email</label>
						<div class="col-lg-10">
							<input type="text" class="form-control" name="name" id="inputEmail" placeholder="Email">
						</div>
						</div>
						<div class="form-group">
						<label for="inputPassword" class="col-lg-2 control-label">Password</label>
						<div class="col-lg-10">
							<input type="password" class="form-control" name="name" id="inputPassword" placeholder="Password">
							<div class="checkbox">
							<label>
								<input type="checkbox" name="name" /> Checkbox
							</label>
							</div>
						</div>
						</div>
						<div class="form-group">
						<label for="textArea" class="col-lg-2 control-label">Textarea</label>
						<div class="col-lg-10">
							<textarea name="name" class="form-control" rows="3" id="textArea"></textarea>
							<span class="help-block">A longer block of help text that breaks onto a new line and may extend beyond one line.</span>
						</div>
						</div>
						<div class="form-group">
						<label class="col-lg-2 control-label">Radios</label>
						<div class="col-lg-10">
							<div class="radio">
							<label>
								<input type="radio" name="optionsRadios" id="optionsRadios1" value="option1" checked="" />
								Option one is this
							</label>
							</div>
							<div class="radio">
							<label>
								<input type="radio" name="optionsRadios" id="optionsRadios2" value="option2" />
								Option two can be something else
							</label>
							</div>
						</div>
						</div>
						<div class="form-group">
						<label for="select" class="col-lg-2 control-label">Selects</label>
						<div class="col-lg-10">
							<select name="name" class="form-control" id="select">
							<option>1</option>
							<option>2</option>
							<option>3</option>
							<option>4</option>
							<option>5</option>
							</select>
						</div>
						</div>
						<div class="form-group">
						<label for="select" class="col-lg-2 control-label">Multiple</label>
						<div class="col-lg-10">
							<select name="name" multiple="" class="form-control">
							<option>1</option>
							<option>2</option>
							<option>3</option>
							<option>4</option>
							<option>5</option>
							</select>
						</div>
						</div>
						<div class="form-group">
						<div class="col-lg-10 col-lg-offset-2">
							<button type="reset" name="name" class="btn btn-default">Cancel</button>
							<button type="submit" name="name" class="btn btn-primary">Submit</button>
						</div>
						</div>
					</fieldset>
					</form>
					<hr />
					<form class="form-inline">
					<div class="bottom-expand"><span class="label label-primary">FORM INLINE</span> <code>&lt;form class="form-inline" /&gt;</code></div>
						<div class="form-group">
						<label for="exampleInputName2">Name</label>
						<input type="text" class="form-control" name="name" id="exampleInputName2" placeholder="Jane Doe" />
						</div>
						<div class="form-group">
						<label for="exampleInputEmail2">Email</label>
						<input type="email" class="form-control" name="name" id="exampleInputEmail2" placeholder="jane.doe@example.com" />
						</div>
						<button type="submit" name="name" class="btn btn-default">Send invitation</button>
					</form>
				</div>
			</div>
			<hr />
			<div class="row">
				<div class="col-lg-3">
					<h4 class="page-header">Size</h4>
					<form>
					<div class="form-group">
						<label class="control-label" for="inputSmall">Small input</label>
						<input class="form-control input-sm" type="text" name="name" id="inputSmall" placehoder=".input-sm" />
					</div>
					<div class="form-group">
						<label class="control-label" for="inputLarge">Large input</label>
						<input class="form-control input-lg" type="text" name="name" id="inputLarge" placehoder=".input-lg" />
					</div>
					</form>
				</div>
				<div class="col-lg-3">
					<h4 class="page-header">State</h4>
					<form>
					<div class="form-group">
						<label class="control-label" for="focusedInput">Focused input</label>
						<input class="form-control" name="name" id="focusedInput" type="text" value="This is focused...">
					</div>
					<div class="form-group">
						<label class="control-label" for="disabledInput">Disabled input</label>
						<input class="form-control" name="name" id="disabledInput" type="text" placeholder="Disabled input here..." disabled="">
					</div>
					<div class="form-group">
						<label class="control-label" for="disabledInput">Readonly input</label>
						<input class="form-control" name="name" type="text" placeholder="Readonly input here..." readonly>
					</div>
					</form>
				</div>
				<div class="col-lg-3">
					<h4 class="page-header">Validation</h4>
					<form>
					<div class="form-group has-warning">
						<label class="control-label" for="inputWarning">Input warning</label>
						<input type="text" class="form-control" name="name" id="inputWarning">
					</div>
					<div class="form-group has-error">
						<label class="control-label" for="inputError">Input error</label>
						<input type="text" class="form-control" name="name" id="inputError">
					</div>
					<div class="form-group has-success">
						<label class="control-label" for="inputSuccess">Input success</label>
						<input type="text" class="form-control" name="name" id="inputSuccess">
					</div>
					</form>
				</div>
				<div class="col-lg-3">
					<h4 class="page-header">Input Group</h4>
					<form>
					<div class="form-group">
						<label class="control-label" for="disabledInput">Input Group Addon</label>
						<div class="input-group">
						<span class="input-group-addon">$</span>
						<input type="text" name="name" class="form-control">
						<span class="input-group-addon">,00</span>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label" for="disabledInput">Input Group Btn</label>
						<div class="input-group">
						<span class="input-group-btn">
							<button name="name" class="btn btn-default" type="button">Button</button>
						</span>
						<input type="text" name="name" class="form-control">
						<span class="input-group-btn">
							<button name="name" class="btn btn-default" type="button">Button</button>
						</span>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label" for="disabledInput">Both</label>
						<div class="input-group">
						<span class="input-group-addon">$</span>
						<input type="text" name="name" class="form-control">
						<span class="input-group-btn">
							<button name="name" class="btn btn-default" type="button">Button</button>
						</span>
						</div>
					</div>
					</form>
				</div>
			</div>

			<div class="well">
				<h4 id="form-base" class="page-header"><span class="base-icon-star text-live"></span> Additional Options for Forms <span class="label label-warning pull-right">BASE</span></h4>
				<div class="row">
					<div class="col-lg-6">
						<code>&lt;fieldset class="fieldset-bordered" /&gt;</code>
						<fieldset class="fieldset-bordered">
							<legend>Legend</legend>
							<div class="form-group">
								<label for="disabledInput">Field</label>
								<input type="text" name="name" class="form-control">
							</div>
							<code class="pull-right">&lt;div class="form-actions" /&gt;</code>
							<div class="form-actions">
								<button type="reset" name="name" class="btn btn-default">Cancel</button>
								<button type="submit" name="name" class="btn btn-primary">Submit</button>
							</div>
						</fieldset>
						<p class="alert alert-info"><strong>Tip:</strong> Use the <a href="#goto-expand">expand options</a> for changing the internal spacing of form-actions</p>
					</div>
					<div class="col-lg-6 top-space">
						<h4>.fieldset-bordered <code>&lt;fieldset class="fieldset-bordered" /&gt;</code></h4>
						<p>This is a option for fieldset</p>
						<h4>.form-actions <code>&lt;div class="form-actions" /&gt;</code></h4>
						<p>Use this container to group action buttons at the bottom of the form. Can be used regardless of the class "fieldset-bordered" or tagged &lt;fieldset&gt;</p>
						<div class="alert alert-info"><p><span class="base-icon-info-circled"></span> To alignment options, use this class together with the alignment classes <span class="label label-default">.text-right</span> <span class="label label-default">.text.center</span></p>This container also works well with Tabs ... see a sample tab in the <a href="#goto-custom-fields">custom fields</a></div>
					</div>
				</div>
				<h4 class="page-header top-space">Fieldset Embed <small>(It is a fieldset option to toggle button)</small></h4>
				<p>
					<button name="name" class="btn btn-primary" onclick="toggleFieldsetEmbed(this, '#fe-1', 100)">Show/Hide Fieldset</button>
					<span class="label label-primary text-md pull-right">Legend right option</span>
				</p>
				<div class="row">
					<div class="col-lg-6">
						<fieldset id="fe-1" class="fieldset-embed">
							<legend><span class="base-icon-filter"></span> Filter</legend>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group field-required">
										<label>Label</label><br />
										<input type="text" name="rg" class="form-control" />
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group field-required">
										<label>Label</label><br />
										<input type="text" name="rg" class="form-control" />
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group field-required">
										<label>Label</label><br />
										<input type="text" name="rg" class="form-control" />
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group field-required">
										<label>Label</label><br />
										<select name="occupation" class="form-control no-chosen">
											<option value="">- Selecione -</option>
											<option>Opção 1</option>
											<option>Opção 2</option>
											<option>Opção 3</option>
											<option>Opção 4</option>
											<option>Opção 5</option>
											<option>Opção 6</option>
											<option>Opção 7</option>
											<option>Opção 8</option>
											<option>Opção 9</option>
											<option>Opção 10</option>
											<option>Opção 11</option>
											<option>Opção 12</option>
											<option>Opção 13</option>
											<option>Opção 14</option>
											<option>Opção 15</option>
											<option>Opção 16</option>
											<option>Opção 17</option>
											<option>Opção 18</option>
											<option>Opção 19</option>
											<option>Opção 20</option>
										</select>
									</div>
								</div>
							</div>
							<div class="form-actions">
								<button type="reset" name="name" class="btn btn-default">Cancel</button>
								<button type="submit" name="name" class="btn btn-primary">Submit</button>
							</div>
						</fieldset>
					</div>
					<div class="col-lg-6">
						<fieldset class="fieldset-embed top-expand-lg">
							<legend class="pull-right"><span class="base-icon-filter"></span> Filter</legend>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group field-required">
										<label>Label</label><br />
										<input type="text" name="rg" class="form-control" />
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group field-required">
										<label>Label</label><br />
										<input type="text" name="rg" class="form-control" />
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group field-required">
										<label>Label</label><br />
										<input type="text" name="rg" class="form-control" />
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group field-required">
										<label>Label</label><br />
										<select name="occupation" class="form-control no-chosen">
											<option value="">- Selecione -</option>
											<option>Opção 1</option>
											<option>Opção 2</option>
											<option>Opção 3</option>
											<option>Opção 4</option>
											<option>Opção 5</option>
											<option>Opção 6</option>
											<option>Opção 7</option>
											<option>Opção 8</option>
											<option>Opção 9</option>
											<option>Opção 10</option>
											<option>Opção 11</option>
											<option>Opção 12</option>
											<option>Opção 13</option>
											<option>Opção 14</option>
											<option>Opção 15</option>
											<option>Opção 16</option>
											<option>Opção 17</option>
											<option>Opção 18</option>
											<option>Opção 19</option>
											<option>Opção 20</option>
										</select>
									</div>
								</div>
							</div>
							<div class="form-actions text-right">
								<button type="reset" name="name" class="btn btn-default">Cancel</button>
								<button type="submit" name="name" class="btn btn-primary">Submit</button>
							</div>
						</fieldset>
					</div>
				</div>
				<div class="row top-space">
					<div class="col-lg-6">
<pre class="top-space">&lt;fieldset <strong class="text-danger">class="fieldset-embed"</strong> /&gt;
&lt;legend&gt;&lt;span class="base-icon-filter"&gt;&lt;/span&gt; Filter&lt;/legend&gt;
...
&lt;div class="form-actions"&gt; ... &lt;/div&gt;
&lt;/fieldset&gt;
</pre>
					</div>
					<div class="col-lg-6 top-space">
<pre>&lt;fieldset class="fieldset-embed <strong class="text-danger">top-expand-lg</strong>" /&gt;
&lt;legend <strong class="text-danger">class="pull-right"</strong>&gt; ... &lt;/legend&gt;
...
&lt;div class="form-actions text-right"&gt; ... &lt;/div&gt;
&lt;/fieldset&gt;
</pre>
					</div>
				</div>
				<p class="alert alert-info alert-icon">To load the fieldset closed-embed, add the "closed" class. Eg. <code>&lt;fieldset class="fieldset-embed closed"&gt;</code>
				<div class="row">
					<div class="col-md-6">
						<h4>Button to toggle the fieldset</h4>
						<p>
							To switch the fieldset just call the function <strong class="text-danger">toggleFieldsetEmbed()</strong><br />
							<code>&lt;button ... <strong>onclick="toggleFieldsetEmbed(this, '#fieldset')</strong>"&gt;</code>
						</p>
						<p class="strong">toggleFieldsetEmbed(button, target, offset)</p>
						<p>
							<strong>button:</strong> when the function is called directly on the button, the value should be <em class="strong text-danger">this</em>. If the function is called without the click event of the button we have two alternatives.<br />
							1 - If exists a button must pass the ID button;<br />
							2 - If not exists a button we set value to 'null'
							<br />
							<strong>target:</strong> the ID of "fieldset-embed" target.
							<br />
							<strong>offset:</strong> When the fieldset is outside the visible area, the function scrolls the page to the fieldset. However, it may need to scroll to position the fieldset below. So the offset defines the distance between the fieldset and the top of the visible area.
						</p>
					</div>
					<div class="col-md-6">
						<h4>Fieldset Embed Group</h4>
						<p>If there is the need to switch between several fieldsets just keeping the current open, just add the 'date-group' attribute with the same value to all fieldset that must be changed.</p>
						<p><strong>Example</strong>: Click switched on the buttons below</p>
						<p>
							<a role="button" href="#" class="btn btn-default" onclick="toggleFieldsetEmbed(this, '#flt-1', 300)">Fieldset 1</a>
							<a role="button" href="#" class="btn btn-default" onclick="toggleFieldsetEmbed(this, '#flt-2', 300)">Fieldset 2</a>
						</p>
						<fieldset id="flt-1" class="fieldset-embed closed" data-group="fe-sample">
							<legend>Fieldset 1</legend>
							<code>&lt;fieldset <em class="strong text-danger">data-group="fe-sample"</em> class="fieldset-embed closed" id="flt-1"&gt;</code>
						</fieldset>
						<fieldset id="flt-2" class="fieldset-embed closed" data-group="fe-sample">
							<legend>Fieldset 2</legend>
							<code>&lt;fieldset <em class="strong text-danger">data-group="fe-sample"</em> class="fieldset-embed closed" id="flt-2"&gt;</code>
						</fieldset>
					</div>
				</div>
				<hr />
				<h4 class="page-header top-space">Form Indicators</h4>
				<div class="row">
					<div class="col-lg-6">
						<h4>Require Alert<br /><code>&lt;label class="alert-required field-required"&gt;...&lt;/label&gt;</code></h4>
						<label class="alert-required field-required">Required fields</label>
						<div class="form-group field-required">
							<label>Required Label</label>
							<input type="text" name="name" class="form-control">
						</div>
					</div>
					<div class="col-lg-6">
						<h4>.field-required</h4>
						<div class="form-group field-required">
							<label>Required field indicator for Form Group<br /><code>&lt;div class="form-group field-required"&gt;</code></label>
							<input type="text" name="name" class="form-control" />
						</div>
						<div class="form-group">
							<label class="field-required">Required field indicator for Label<br /><code>&lt;label class="field-required" /&gt;</code></label>
							<input type="text" name="name" class="form-control" />
						</div>
					</div>
				</div>
				<hr />
				<div class="row">
					<div class="col-lg-6">
						<h4>Tip for Labels</h4>
						<div class="form-group field-required">
							<p>
								<label class="iconTip hasTooltip" title="This is Tooltip in label">Tooltip Label</label>
								<br><code>&lt;label class="iconTip hasTooltip" title="..."&gt;</code><br />
							</p>
							<p>
								<label class="iconTip hasPopover" title="This is Popover in label" data-content="This is Popover content">Popover Label</label>
								<br><code>&lt;label class="iconTip hasPopover" title="..." data-content="..."&gt;</code>
							</p>
						</div>
						<hr />
						<div class="form-group field-required">
							<label>Tip for Indicator</label> <span class="iconTip hasTooltip" title="This is indicator tooltip"></span> <span class="iconTip hasPopover" title="This is indicator Popover" data-content="This is Popover content"></span>
							<p><code>&lt;span class="iconTip hasTooltip" title="..."&gt;&lt;span&gt;</code><br /><code>&lt;span class="iconTip hasPopover" title="..."&gt;&lt;span&gt;</code></p>
							<input type="text" name="name" class="" /> <span class="iconTip hasTooltip" title="This is indicator tooltip"></span> <span class="iconTip hasPopover" title="This is indicator Popover" data-content="This is Popover content"></span>
						</div>
					</div>
					<div class="col-lg-6">
						<h4>Tip for Fields</h4>
						<div class="form-group">
							<p>
								<code>&lt;input class="hasTooltip" title="..." data-content="..."&gt;</code>
								<input type="text" name="name" class="form-control hasTooltip" title="Title for Tooltip" placeholder="Set hover or focus here" />
							</p>
							<p>
								<code>&lt;input class="setTooltip" title="..." data-content="..."&gt;</code>
								<input type="text" name="name" class="form-control setTooltip" title="Title for Tooltip" placeholder="Set focus here" />
							</p>
							<p>
								<code>&lt;input class="hasPopover" title="..." data-content="..."&gt;</code>
								<input type="text" name="name" class="form-control hasPopover" title="Title for Popover" data-content="This is Popover content" placeholder="Set hover or focus here" />
							</p>
							<p>
								<code>&lt;input class="setPopover" title="..." data-content="..."&gt;</code>
								<input type="text" name="name" class="form-control setPopover" title="Title for Popover" data-content="This is Popover content" placeholder="Set focus here" />
							</p>
						</div>
					</div>
				</div>
				<div class="well well-sm">
					<h4 class="page-header no-margin"><span class="base-icon-comment-square right-space-xs"></span> Custom Classes to Set Tooltips & Popovers</h4>
					<table class="table table-striped table-hover no-margin">
						<tbody>
							<tr>
								<th>.hasTooltip</th>
								<td>Set Tooltip default { container: 'body', html: true }<br /><code>&lt;... class="hasTooltip" ...&gt;</code></td>
							</tr>
							<tr>
								<th>.hasTooltip</th>
								<td>Set Tooltip on Focus { container: 'body', html: true, trigger: 'focus' }<br /><code>&lt;... class="setTooltip" ...&gt;</code></td>
							</tr>
							<tr>
								<th>.hasPopover</th>
								<td>Set Popover on Hover { container: 'body', html: true, trigger: 'hover focus' }<br /><code>&lt;... class="hasPopover" ...&gt;</code></td>
							</tr>
							<tr>
								<th>.setPopover</th>
								<td>Set Popover on Focus { container: 'body', html: true, trigger: 'focus' }<br /><code>&lt;... class="setPopover" ...&gt;</code></td>
							</tr>
						</tbody>
					</table>
					<p class="alert alert-info">
						<span class="base-icon-info-circled"></span> Use attribute <em class="strong">data-placement</em> for change Tooltip and Popover position. <em><strong>Values</strong>: 'top', 'bottom, 'right' & 'left'</em>
						<br /><code>&lt;input class="hasPopover" title="..." <em class="strong">data-placement="right"</em>&gt;</code>
					</p>
					<h4 class="alert alert-warning">
						<span class="base-icon-attention-circled"></span> To add content to Popover use the attribute <strong>data-content</strong>. See more about Bootstrap <a href="http://getbootstrap.com/javascript/#tooltips">Tooltip</a> and <a href="http://getbootstrap.com/javascript/#popovers">Popover</a>...
					</h4>
				</div>
				<h4 id="custom-fields" class="page-header"><span class="base-icon-star text-live"></span> Custom Fields</h4>
				<!-- Nav tabs -->
				<ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class="active"><a href="#masks" aria-controls="masks" role="tab" data-toggle="tab">Masks</a></li>
					<li role="presentation"><a href="#group" aria-controls="group" role="tab" data-toggle="tab">Auto-tab & Conditions</a></li>
					<li role="presentation"><a href="#validation" aria-controls="validation" role="tab" data-toggle="tab">Validation</a></li>
					<li role="presentation"><a href="#bootstrap-context" aria-controls="bootstrap-context" role="tab" data-toggle="tab">Bootstrap Context</a></li>
					<li role="presentation"><a href="#restrictions" aria-controls="restrictions" role="tab" data-toggle="tab">Restrictions</a></li>
					<li role="presentation"><a href="#form-template" aria-controls="form-template" role="tab" data-toggle="tab">Template</a></li>
				</ul>
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="masks">
						<div class="row">
							<div class="col-lg-6">
								<label>Phone Number<br /><code>&lt;input class="field-phone" data-width="12em" /&gt;</code></label><br />
								<p><input type="text" name="name" class="form-control field-phone" data-width="12em" /></p>

								<label>Date<br /><code>&lt;input class="field-date" data-width="8.5em" /&gt;</code></label><br />
								<p><input type="text" name="name" class="form-control field-date" data-width="8.5em" /></p>

								<label>Time<br /><code>&lt;input class="field-time" data-width="4.5em" /&gt;</code></label><br />
								<p><input type="text" name="name" class="form-control field-time" data-width="4.5em" /></p>

								<label>Price Format<br /><code>&lt;input class="field-price" data-width="12em" /&gt;</code></label><br />
								<p><input type="text" name="name" class="form-control field-price" data-width="12em" /></p>

								<p><span class="label label-default">Special Masks</span></p>

								<label>CEP<br /><code>&lt;input class="field-cep" data-width="7.2em" /&gt;</code></label><br />
								<p><input type="text" name="name" class="form-control field-cep" data-width="7.2em" /></p>

								<label>CPF<br /><code>&lt;input class="field-cpf" data-width="9.5em" /&gt;</code></label><br />
								<p><input type="text" name="name" class="form-control field-cpf" data-width="9.5em" /></p>

								<label>CNPJ<br /><code>&lt;input class="field-cnpj" data-width="12em" /&gt;</code></label><br />
								<p><input type="text" name="name" class="form-control field-cnpj" data-width="12em" /></p>
								<div class="alert alert-warning strong top-space">
									<span class="base-icon-attention-alt-inv"></span> All fields above accept the <strong>"data-width"</strong> attribute to define the width of the field. Ex.: <code>&lt;input class="field-time" data-width="6em" /&gt;</code>
								</div>
							</div>
							<div class="col-lg-6">
								<h5 class="page-header"><span class="base-icon-calendar"></span> Date Options</h5>

								<label>Min Date<br /><code>&lt;input class="field-date" data-mindate="0" /&gt;</code></label>
								<p><input type="text" name="name" class="form-control field-date" data-width="8.5em" data-mindate="0" placeholder="Min Date" /></p>

								<label>Max Date<br /><code>&lt;input class="field-date" data-maxdate="0" /&gt;</code></label>
								<p><input type="text" name="name" class="form-control field-date" data-width="8.5em" data-maxdate="0" placeholder="Max Date" /></p>
								<p class="text-info">
									<strong>Tip:</strong> To get <em class="strong">birthday</em>, set<br /><code class="strong">... data-mindate="-100Y" data-maxdate="0" data-year-range="c-100:c" /&gt;</code>.
								</p>
								<p class="alert alert-info">
									<span class="base-icon-info-circled"></span> The date fields using the plugin <a href="https://jqueryui.com/datepicker/" target="_blank">jquery Datepicker</a><br />To learn more about the possible values for "<a href="http://api.jqueryui.com/datepicker/#option-minDate" target="_blank">minDate</a>", "<a href="http://api.jqueryui.com/datepicker/#option-maxDate" target="_blank">maxDate</a>" and "<a href="http://api.jqueryui.com/datepicker/#option-yearRange" target="_blank">yearRange</a>" see the <a href="http://api.jqueryui.com/datepicker/" target="_blank">API documentation for that plugin</a>.
								</p>

								<h5 class="page-header"><span class="base-icon-clock"></span> Time Options</h5>

								<label>Time with seconds<br /><code>&lt;input class="field-time" data-seconds="true" data-width="6em" /&gt;</code></label><br />
								<p><input type="text" name="name" class="form-control field-time" data-seconds="true" data-width="6em" placeholder="" /></p>

								<h5 class="page-header"><strong>$</strong> Price Options</h5>

								<label>Price decimal (,00...)<br /><code>&lt;input class="field-price" data-decimal="3" /&gt;</code></label><br />
								<p>
									<input type="text" name="name" class="field-price" data-decimal="3" data-width="12em" placeholder="data-decimal=&quot;3&quot;" />&nbsp;
									<input type="text" name="name" class="field-price" data-decimal="0" data-width="12em" placeholder="data-decimal=&quot;0&quot;" />
									<br /><span class="text-info"><strong>Tip:</strong> To disable cents use <strong>data-decimal="0"</strong>.</span>
								</p>
								<label>American(US) Format (1,000.00)<br /><code>&lt;input class="field-price" data-format="us" /&gt;</code></label><br />
								<p><input type="text" name="name" class="field-price" data-format="us" data-width="12em" placeholder="data-decimal=&quot;0&quot;" /></p>
								<p class="alert alert-info">
									<span class="base-icon-info-circled"></span> The price fields using the plugin <a href="https://plugins.jquery.com/priceformat/" target="_blank">jquery Price Format</a>.
								</p>

							</div>
						</div>
						<div class="well">
							<h4 class="page-header" id="data-convert">Data Convert</h4>
							<p>
								When we store values in the database, the most appropriate format can not be the same as we use in our form. In this case, to use the mask we need to convert the value when we send and converted again when we bring the database.<br />In the fields with predefined mask (above) we have two that usually happens. They are the fields "<strong>Date</strong>" and "<strong>Price</strong>" (currency).<br />The most appropriate formed to fields of type <strong>Date</strong> is the '<strong>yyyy-mm-dd</strong>', as the format for currency is the <strong>decimal (10.2)</strong>. Thus, we implemented the data-convert attribute, which converts the values in these fields for the value used in the database.
							</p>
							<hr class="hr-sm" />
							<div class="row">
								<div class="col-md-7">
									<form class="form-inline" action="<?php echo JURI::current().'#data-convert'?>" method="get">
										<div class="bottom-expand"><span class="label label-primary">TEST DATA-CONVERT</span></div>
										  <div class="form-group">
											<label for="test-date">Date</label>
											<input type="text" id="test-date" name="date_converted" class="form-control field-date" data-width="8.5em" data-convert="true" value="<?php echo $_GET['date_converted']; ?>" />
										  </div>
										  <div class="form-group">
											<label for="test-price">Price</label>
											<input type="text" id="teste-price" name="price_converted" class="form-control field-price" data-width="10em" data-convert="true" value="<?php echo $_GET['price_converted']; ?>" />
										  </div>
										  <button type="submit" class="btn btn-default">Convert</button>
									</form>
									<?php
									if($_GET['date_converted'] || $_GET['price_converted'])
									echo '<span class="help-block"><span class="base-icon-flag text-live"></span> The received parameter is converted back to the default mask</span>';
									?>
								</div>
								<div class="col-md-5">
									<?php
									if($_GET['date_converted'])
									echo '<h4 class="bottom-space-xs"><span class="base-icon-flag text-live"></span> the converted data is <em class="text-live">'.$_GET['date_converted'].'</em></h4>';
									if($_GET['price_converted'])
									echo '<h4 class="no-margin"><span class="base-icon-flag text-live"></span> the converted price is <em class="text-live">'.$_GET['price_converted'].'</em></h4>';
									?>
								</div>
							</div>
						</div>
						<h4 class="page-header top-expand-lg">Form Actions</h4>
						<div class="form-actions">
							<code class="pull-right">&lt;div class="form-actions" /&gt;</code>
							<button type="reset" name="name" class="btn btn-default">Cancel</button>
							<button type="submit" name="name" class="btn btn-primary">Submit</button>
							<span class="label label-primary">INSIDE TAB-PANE</span>
						</div>
					</div>
					<div role="tabpanel" class="tab-pane" id="group">
						<div class="row">
							<div class="col-lg-4">
								<h5 class="page-header">CHECKBOX</h5>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="check_autoTab" class="auto-tab" data-target="check-target-3" data-target-display="true" checked />
										Auto Tab Hidden
									</label>
									<input type="text" id="check-target-3" name="check-target-2" class="form-control" placeholder="visible" />
								</div>
								<div class="form-group">
									<label>Auto Tab</label><br />
									<div class="input-group">
										<span class="input-group-addon">
											<input type="checkbox" name="check_autoTab" class="auto-tab" data-target="check-target-1" />
										</span>
										<input type="text" id="check-target-1" name="check-target-1" class="form-control" />
									</div>
								</div>
								<div class="form-group">
									<label>Auto Tab Disabled</label><br />
									<div class="input-group">
										<span class="input-group-addon">
											<input type="checkbox" name="check_autoTab" class="auto-tab" data-target="check-target-2" data-target-disabled="false" />
										</span>
										<input type="text" id="check-target-2" name="check-target-2" class="form-control" disabled="disabled" />
									</div>
								</div>
								<div class="form-group">
									<label>Group</label><br />
									<div class="btn-group" data-toggle="buttons">
										<label class="btn btn-default active-primary">
											<input type="checkbox" name="field" class="auto-tab" data-target="check1" data-target-display="true"> Check 1
										</label>
										<label class="btn btn-default active-info">
											<input type="checkbox" name="field" class="auto-tab" data-target="check2" data-target-display="true"> Check 2
										</label>
										<label class="btn btn-default active-warning">
											<input type="checkbox" name="field" class="auto-tab" data-target="check3" data-target-display="true"> Check 3
										</label>
										<label class="btn btn-default active-danger">
											<input type="checkbox" name="field" class="auto-tab" data-target="check4" data-target-display="true"> Check 4
										</label>
									</div>
								</div>
								<div class="form-group">
									<p><input type="text" name="field" id="check1" class="form-control hide" placeholder="Primary" /></p>
									<p><input type="text" name="field" id="check2" class="form-control hide" placeholder="Info" /></p>
									<p><input type="text" name="field" id="check3" class="form-control hide" placeholder="Warning" /></p>
									<p><input type="text" name="field" id="check4" class="form-control hide" placeholder="Danger" /></p>
								</div>
							</div>
							<div class="col-lg-4">
								<h5 class="page-header">RADIO</h5>
								<div class="row">
									<div class="col-md-6 width-auto">
										<div class="form-group">
											<label>Default</label><br />
											<label class="radio-inline">
												<input type="radio" name="options0" id="option01" class="auto-tab" data-target="radio-target-0" value="0" /> No
											</label>
											<label class="radio-inline">
												<input type="radio" name="options0" id="option02" class="auto-tab" data-target="radio-target-0" value="1" /> Yes
											</label>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Target</label><br />
											<input type="text" id="radio-target-0" name="radio-target-0" class="form-control" />
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6 width-auto">
										<div class="form-group">
											<label>Btn group</label><br />
											<div class="btn-group" data-toggle="buttons">
												<label class="btn btn-default active-danger">
													<input type="radio" name="options1" id="option1" class="auto-tab" data-target="radio-target-1" value="0" /> No
												</label>
												<label class="btn btn-default">
													<input type="radio" name="options1" id="option2" class="auto-tab" data-target="radio-target-1" value="1" /> Yes
												</label>
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Target</label><br />
											<input type="text" id="radio-target-1" name="radio-target-1" class="form-control" />
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6 width-auto">
										<div class="form-group">
											<label>Btn group</label><br />
											<div class="btn-group" data-toggle="buttons">
												<label class="btn btn-default active active-danger">
													<input type="radio" name="options2" id="option3" class="auto-tab" data-target="radio-target-2" data-target-disabled="true" value="0" checked /> No
												</label>
												<label class="btn btn-default">
													<input type="radio" name="options2" id="option4" class="auto-tab" data-target="radio-target-2" data-target-disabled="false" value="1" /> Yes
												</label>
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Target Disabled</label><br />
											<input type="text" id="radio-target-2" name="radio-target-2" class="form-control" disabled="disabled" />
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6 width-auto">
										<div class="form-group">
											<label>Btn group</label><br />
											<div class="btn-group" data-toggle="buttons">
												<label class="btn btn-default active active-danger">
													<input type="radio" name="options3" id="option5" class="auto-tab" data-target="radio-target-3" data-target-display="false" value="0" checked /> No
												</label>
												<label class="btn btn-default">
													<input type="radio" name="options3" id="option6" class="auto-tab" data-target="radio-target-3" data-target-display="true" value="1" /> Yes
												</label>
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Target Hidden</label><br />
											<input type="text" id="radio-target-3" name="radio-target-3" class="form-control hide" />
										</div>
									</div>
								</div>
							</div>
							<div class="col-lg-4">
								<h5 class="page-header">SELECT</h5>
								<div class="form-group">
									<label>Auto Tab</label><br />
									<select name="areas" class="form-control auto-tab" data-target="select-target-1">
										<option value="" selected>- Select -</option>
										<option value="" data-target-value="I Get Focus">Tab to Target</option>
										<option value="" data-target-value="I Get Focus Again">Tab to Target Too</option>
									</select>
								</div>
								<div class="form-group">
									<label>Target</label>
									<span class="base-icon-down text-danger pull-right"></span>
									<input type="text" id="select-target-1" name="select-target-1" class="form-control" />
								</div>
								<div class="form-group">
									<label>Individual Options</label><br />
									<select name="areas" class="form-control auto-tab" data-target="select-target-2">
										<option value="" data-target-disabled="true"  data-target-display="true" selected>- Select -</option>
										<option value="" data-target-disabled="false">Target Enabled</option>
										<option value="" data-target-disabled="true">Target Disabled</option>
										<option value="" data-target-disabled="false" data-target-value="Target Enabled">Target Enabled with Value</option>
										<option value="" data-target-disabled="true" data-target-value="Target Disabled">Target Disabled with Value</option>
										<option value="" data-target-display="false">Target Hidden</option>
										<option value="" data-target-display="true">Target Visible</option>
										<option value="" data-target-display="true" data-target-value="Target Visible">Target Visible with Value</option>
									</select>
								</div>
								<div class="form-group">
									<label>Target</label>
									<span class="base-icon-down text-danger pull-right"></span>
									<input type="text" id="select-target-2" name="select-target-2" class="form-control" disabled="disabled" />
								</div>
							</div>
						</div>
						<h4 class="page-header">Set Click or Focus to Button on key press 'Enter'</h4>
						Attributes a click (default) or focus to target button through of the key 'enter' when the focus is in the field (input)
						<div class="row">
							<div class="col-sm-6">
								<hr class="hr-label" /><span class="label">Set Click</span>
								<div class="form-inline">
									<input type="text" name="name" class="set-btn-action" data-target="btn-click" />
									<button id="btn-click" name="name" class="btn btn-default" onclick="alert('You clicked in the button!')">Click</button>
								</div>
							</div>
							<div class="col-sm-6">
								<hr class="hr-label" /><span class="label">Set Focus</span>
								<div class="form-inline">
									<input type="text" name="name" class="set-btn-action" data-target="btn-focus" data-action="focus" />
									<button id="btn-focus" name="name" class="btn btn-default">Focus</button>
								</div>
							</div>
						</div>
						<hr />
						<h4>The general rule is:</h4>
						<ol>
							<li>Add Class "auto-tab" in the element (checkbox, radio or select).</li>
							<li>
								Enter the target object ID using the attribute "data-target"
							</li>
						</ol>
						Ex: <code>&lt;input type="checkbox" class="auto-tab" <strong>data-target="my-target"</strong> /&gt;</code>
						<h4>To enable or disable the target object do:</h4>
						<p>
							Add the <strong>data-target-disabled</strong> attribute on the element (checkbox, radio or select -> option).
							<br /><strong>The options value are:</strong>
							<ul>
								<li>true = Disabled</li>
								<li>false = Enabled</li>
							</ul>
							Ex: <code>&lt;input type="checkbox" class="auto-tab" data-target="my-target" <strong>data-target-disabled="false"</strong> /&gt;</code>
						</p>
						<h4>To show or hide the target object do:</h4>
						<p>
							Add the <strong>data-target-display</strong> attribute on the element (checkbox, radio or select -> option).
							<br /><strong>The options value are:</strong>
							<ul>
								<li>true = Show</li>
								<li>false = Hide</li>
							</ul>
							Ex: <code>&lt;input type="checkbox" class="auto-tab" data-target="my-target" <strong>data-target-display="true"</strong> /&gt;</code>
						</p>
						<h4>To assign a value to the target object do:</h4>
						<p>
							Add the <strong>data-target-value</strong> attribute on the element (checkbox, radio or select-> option).
							<br /><span class="base-icon-ok text-success"></span> The value will be assigned to the target object when the option is selected.
							<br />Ex: <code>&lt;input type="checkbox" class="auto-tab" data-target="my-target" <strong>data-target-value="new value for target"</strong> /&gt;</code>
						</p>
						<div class="alert alert-warning strong top-space"><span class="base-icon-attention-alt-inv"></span> To enable/disable, show/hide or assign a value through &lt;select&gt;element, the attributes should be added to the &lt;option&gt; element</div>
					</div>
					<div role="tabpanel" class="tab-pane" id="validation">
						<div class="row">
							<div class="col-md-6">
								<form id="form-validator" onsubmit="return false">
									<fieldset class="fieldset-bordered">
										<legend>Default Validations</legend>
										<div class="alert alert-success hide"><span class="base-icon-ok"></span> Success!</div>
										<label class="alert-required">Required Fields</label>
										<div class="form-horizontal">
											<div class="form-group field-required">
												<label for="inputEmail3" class="col-sm-3 control-label">Name</label>
												<div class="col-sm-9">
													<input type="text" id="nome" name="nome" class="form-control" />
												</div>
											</div>
											<div class="form-group field-required">
												<label for="inputEmail3" class="col-sm-3 control-label">E-mail</label>
												<div class="col-sm-9">
													<input type="email" id="email" name="email" class="form-control field-email" />
												</div>
											</div>
											<div class="form-group field-required">
												<label for="inputEmail3" class="col-sm-3 control-label">CPF</label>
												<div class="col-sm-9">
													<input type="text" id="cpf" name="cpf" class="field-cpf" />
												</div>
											</div>
											<div class="form-group">
												<label for="inputEmail3" class="col-sm-3 control-label">URL</label>
												<div class="col-sm-9">
													<input type="url" id="website" name="website" class="form-control field-url" placeholder="http://" />
												</div>
											</div>
											<div class="form-group field-required">
												<label for="inputEmail3" class="col-sm-3 control-label">Select</label>
												<div class="col-sm-9">
													<select id="select" name="select" class="form-control input-sm">
														<option value="">- Select -</option>
														<option value="item1">Item 1</option>
														<option value="item2">Item 2</option>
														<option value="item2">Item 3</option>
														<option value="item2">Item 4</option>
														<option value="item2">Item 5</option>
														<option value="item2">Item 6</option>
														<option value="item2">Item 7</option>
														<option value="item2">Item 8</option>
														<option value="item2">Item 9</option>
														<option value="item2">Item 10</option>
														<option value="item2">Item 11</option>
													</select>
												</div>
											</div>
											<div class="form-group field-required">
												<label for="inputEmail3" class="col-sm-3 control-label">Multiple</label>
												<div class="col-sm-9">
													<select id="multiple" name="multiple" class="form-control" multiple>
														<option value="item1">Item 1</option>
														<option value="item2">Item 2</option>
														<option value="item2">Item 3</option>
														<option value="item2">Item 4</option>
														<option value="item2">Item 5</option>
														<option value="item2">Item 6</option>
														<option value="item2">Item 7</option>
														<option value="item2">Item 8</option>
														<option value="item2">Item 9</option>
														<option value="item2">Item 10</option>
														<option value="item2">Item 11</option>
													</select>
												</div>
											</div>
											<div class="form-group field-required">
												<label for="inputPassword3" class="col-sm-3 control-label">Password</label>
												<div class="col-sm-9">
													<input type="password" id="password" name="password" class="field-password" />
												</div>
											</div>
											<div class="form-group field-required">
												<label for="inputPassword3" class="col-sm-3 control-label">Repeat</label>
												<div class="col-sm-9">
													<input type="password" id="repassword" name="repassword" class="field-password" />
												</div>
											</div>
											<div class="form-group field-required">
												<div class="col-sm-offset-2 col-sm-9">
													<div class="checkbox">
														<label>
															<input id="qwe" name="qwe" type="checkbox"> Accept
														</label>
													</div>
												</div>
											</div>
											<div class="form-group">
												<div class="col-sm-offset-3 col-sm-9">
													<button type="submit" name="name" class="btn btn-primary">Validate</button>
												</div>
											</div>
										</div>
									</fieldset>
								</form>
							</div>
							<div class="col-md-6">
								<p class="alert alert-info">The validation was implemented using the <a href="http://jqueryvalidation.org/" target="_blank">jQuery Validation plugin</a>.</p>
								<p><strong class="text-danger">Important:</strong><br />To validate a form is necessary:</p>
								<p>1. To load javascript file "validate.js" to page<br /><code>&lt;script type="text/javascript" src="js/validate.js"&gt;&lt;/script&gt;</code></p>
								<p>2. To the page javascript code below:</p>

<pre>&lt;script&gt;
<strong class="text-danger">jQuery(<u>window</u>).<u>load</u>(function() {</strong>

//form validations
jQuery("#form-validator").validate({
rules: {
<span class="text-danger strong">//CUSTOM VALIDATIONS</span>
},
//don't remove this
invalidHandler: function(event, validator) {
//if there is error,
//set custom preferences
baseValidation(jQuery(this));
}
});

});
&lt;/script&gt;
</pre>
							</div>
						</div>
					</div>
					<div role="tabpanel" class="tab-pane" id="bootstrap-context">
						<div class="row">
							<div class="col-lg-4">
								<div class="form-group has-feedback has-success">
									<label>Success</label>
									<input type="text" name="name" class="form-control" />
								</div>
<pre>&lt;div class="form-group has-feedback has-success"&gt;
&lt;label&gt;Success&lt;/label&gt;
&lt;input type="text" name="name" class="form-control" /&gt;
&lt;/div&gt;
</pre>
							</div>
							<div class="col-lg-4">
								<div class="form-group has-feedback has-warning">
									<label>Warning</label>
									<input type="text" name="name" class="form-control" />
								</div>
<pre>&lt;div class="form-group has-feedback has-warning"&gt;
&lt;label&gt;Warning&lt;/label&gt;
&lt;input type="text" name="name" class="form-control" /&gt;
&lt;/div&gt;
</pre>
							</div>
							<div class="col-lg-4">
								<div class="form-group has-feedback has-error">
									<label>Error</label>
									<input type="text" name="name" class="form-control" />
									<span class="base-icon-warning form-control-feedback" aria-hidden="true"></span>
								</div>
<pre>&lt;div class="form-group has-feedback has-error"&gt;
&lt;label&gt;Error&lt;/label&gt;
&lt;input type="text" name="name" class="form-control" /&gt;
&lt;/div&gt;
</pre>
							</div>
						</div>
						<h4 class="page-header"><span class="base-icon-star text-live"></span> Dynamically assigning the context</h4>
						<p>
							<button name="name" class="btn btn-success" onclick="setFieldSuccess('#field-context')">Success</button>
							<button name="name" class="btn btn-warning" onclick="setFieldWarning('#field-context')">Warning</button>
							<button name="name" class="btn btn-danger" onclick="setFieldError('#field-context')">Error</button>
							<button name="name" class="btn btn-default" onclick="unsetFieldContext('#field-context')">Clean Context</button>
						</p>
						<div class="row">
							<div class="col-lg-6">
								<div class="form-group">
									<label>Label</label>
									<input id="field-context" type="text" name="name" class="form-control" />
								</div>
								<p class="alert alert-info alert-icon">To remove the context, simply call the function <strong>unsetFieldContext(input)</strong></p>
							</div>
							<div class="col-lg-6">
								<p>To assign dynamically context simply call one of the following functions according to the context:</p>
								<p>
									<strong>Success:</strong> setFieldSuccess(input);<br />
									<strong>Warning:</strong> setFieldWarning(input);<br />
									<strong>Error:</strong> setFieldError(input);<br />
								</p>
								<p>The <strong>"input"</strong> parameter refers to the field that the context is assigned. <code>setFieldError('#my-input');</code></p>
							</div>
						</div>
					</div>
					<div role="tabpanel" class="tab-pane" id="restrictions">
						<div class="row">
							<div class="col-lg-6">
								<label>Set Focus in page load<br /><code>&lt;input class="set-focus" /&gt;</code></label>
								<p><input type="text" name="name" class="form-control set-focus" placeholder="" /></p>
								<label>Does not allow &quot;drop&quot;<br /><code>&lt;input class="no-drop" /&gt; </code></label>
								<p><input type="text" name="name" class="form-control no-drop" placeholder="" /></p>
								<label>Does not allow &quot;paste&quot;<br /><code>&lt;input class="no-paste" /&gt;</code><br /></label>
								<p><input type="text" name="name" class="form-control no-paste" /></p>
								<span class="label label-default pull-right">together</span>
								<label>Does not allow &quot;drop & paste&quot;<br /><code>&lt;input class="no-drop no-paste" /&gt;</code></label>
								<p><input type="text" name="name" class="form-control no-drop no-paste" /></p>
								<label>Uppercase<br /><code>&lt;input class="upper" /&gt;</code></label>
								<p><input type="text" name="name" class="form-control upper" placeholder="Uppercase" /></p>
								<label>Lowercase<br /><code>&lt;input class="lower" /&gt;</code></label>
								<p><input type="text" name="name" class="form-control lower" placeholder="Lowercase" /></p>
							</div>
							<div class="col-lg-6">
								<label>Does not allow &quot;number&quot;<br /><code>&lt;input class="field-nonumber" /&gt;</code></label>
								<p><input type="text" name="name" class="form-control field-nonumber" /></p>
								<label>Does not allow &quot;special character&quot;<br /><code>&lt;input class="no-special-character" /&gt;</code></label>
								<span class="label label-default pull-right">{}[]&ordf;&ordm;/\;:.,<>!@#$%&*()_-+=?"'|...</span>
								<p><input type="text" name="name" class="form-control no-special-character" /></p>
								<label>Does not allow &quot;accentuation&quot;<br /><code>&lt;input class="no-accents" /&gt;</code></label>
								<span class="label label-default pull-right">&aacute;&eacute;&iacute;&oacute;&uacute;&Aacute;&Eacute;&Iacute;&Oacute;&Uacute;&acirc;&ecirc;&icirc;&ocirc;&ucirc;&Acirc;&Ecirc;&Icirc;&Ocirc;&Ucirc;&atilde;&otilde;&Atilde;&Otilde;&agrave;&Agrave;&ccedil;&Ccedil;&acute;&#94;&#96;&#126;</span>
								<p><input type="text" name="name" class="form-control no-accents" /></p>
								<label>Does not allow &quot;blank space&quot;<br /><code>&lt;input class="no-blank-space" /&gt;</code></label>
								<p><input type="text" name="name" class="form-control no-blank-space" /></p>
								<label><span class="label label-primary">Together:</span> <span class="label label-default">field-nonumber</span> <span class="label label-default">no-special-character</span> <span class="label label-default">no-accents</span> <span class="label label-default">no-blank-space</span></label>
								<p><input type="text" name="name" class="form-control field-nonumber no-special-character no-accents no-blank-space" /></p>
								<label>Only Numbers<br /><code>&lt;input class="field-integer" /&gt;</code> or<br /><code>&lt;input class="field-number" /&gt;</code></label>
								<p><input type="text" name="name" class="form-control field-integer" /></p>
								<label>Only numbers with point<br /><code>&lt;input class="field-float" /&gt;</code></label>
								<p><input type="text" name="name" class="form-control field-float" /></p>
							</div>
						</div>
					</div>
					<div role="tabpanel" class="tab-pane" id="form-template">
						<div class="row">
							<div class="col-lg-6">
								<fieldset class="fieldset-bordered">
									<legend>Login</legend>
									<div class="form-group">
										<label>E-mail/Username</label>
										<input type="text" name="tpl-email" class="form-control" />
									</div>
									<div class="form-group">
										<label>Password</label>
										<input type="password" name="tpl-email" class="form-control" />
									</div>
									<div class="checkbox">
										<label><input type="checkbox" name="tpl-email" /> Remember Me</label>
									</div>
									<div class="form-actions">
										<input type="submit" name="submit" class="btn btn-primary btn-block" />
										<a href="#">Forget you password?</a>
									</div>
								</fieldset>
								<a href="#"><span class="base-icon-user-add"></span> Create Account</a>
							</div>
							<div class="col-lg-6">
								<fieldset class="fieldset-bordered">
									<legend>Address [Brazilian Format]</legend>
									<div class="form-group">
										<label>CEP</label><br />
										<input type="text" name="cep" size="10" class="field-cep field-search search-cep" />
									</div>
									<div class="row">
										<div class="col-sm-10">
											<label>Logradouro</label>
											<input type="text" name="address" class="form-control field-address upper" />
										</div>
										<div class="col-sm-2">
											<div class="form-group">
												<label>N&uacute;mero</label>
												<input type="text" name="address_number" class="form-control field-address-number upper" />
											</div>
										</div>
									</div>
									<div class="form-group">
										<label>Complemento</label>
										<input type="text" name="address_info" class="form-control" />
									</div>
									<div class="row">
										<div class="col-sm-2">
											<div class="form-group">
												<label>UF</label><br />
												<select name="uf" class="form-control field-uf">
													<option>-</option>
													<option value="PB">PB</option>
													<option value="PE">PE</option>
													<option value="RN">RN</option>
												</select>
											</div>
										</div>
										<div class="col-sm-5">
											<div class="form-group">
												<label>Cidade</label>
												<input type="text" name="city" class="form-control field-cidade upper" />
											</div>
										</div>
										<div class="col-sm-5">
											<div class="form-group">
												<label>Bairro</label>
												<input type="text" name="district" class="form-control field-district upper" />
											</div>
										</div>
									</div>
								</fieldset>
							</div>
						</div>
					</div>
				</div>
				<div class="form-actions">
					<code class="pull-right">&lt;div class="form-actions" /&gt;</code>
					<button type="reset" name="name" class="btn btn-default">Cancel</button>
					<button type="submit" name="name" class="btn btn-primary">Submit</button>
					<span class="label label-primary">AFTER TAB-CONTENT</span>
				</div>
			</div>
		</section>

		<!-- Navs
		================================================== -->
		<section id="nav">

			<h1 class="page-header">Navs <span class="label label-primary text-md pull-right top-space">BOOTSTRAP</span></h1>

			<div class="row">
				<div class="col-lg-6">
					<h2 id="nav-tabs">Tabs</h2>
					<ul class="nav nav-tabs">
						<li class="active"><a href="#home" data-toggle="tab">Home</a></li>
						<li class="disabled"><a>Disabled</a></li>
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#">
							Dropdown <span class="caret"></span>
							</a>
							<ul class="dropdown-menu">
							<li><a href="#dropdown1" data-toggle="tab">Action</a></li>
							<li class="divider"></li>
							<li><a href="#dropdown2" data-toggle="tab">Another action</a></li>
							</ul>
						</li>
						<li><a href="#profile" data-toggle="tab">Profile</a></li>
						<li><a>Item 5</a></li>
						<li><a>Item 6</a></li>
						<li><a href="#profile" data-toggle="tab">Item 7</a></li>
						<li><a>Item 8</a></li>
						<li><a>Item 9</a></li>
						<li><a>Item 10</a></li>
					</ul>
					<div id="myTabContent" class="tab-content">
						<div class="tab-pane fade active in" id="home">
							<h4><span class="base-icon-star text-live"></span> TabDrop</h4>
							<p>Very usefull script when your tabs do not fit in a  single row. This script takes the not fitting tabs and makes a new dropdown tab. In the dropdown there are all the tabs that do not fit.</p>
							<ul>
							<li>works only with horizontal tabs and pills</li>
							<li>custom text for the dropdown tab</li>
							<li>works with responsive design</li>
							</ul>
							Original site and examples: <a href="http://www.eyecon.ro/bootstrap-tabdrop/" target="_blank">http://www.eyecon.ro/bootstrap-tabdrop/</a>
						</div>
						<div class="tab-pane fade" id="profile">
							<p>Food truck fixie locavore, accusamus mcsweeney's marfa nulla single-origin coffee squid. Exercitation +1 labore velit, blog sartorial PBR leggings next level wes anderson artisan four loko farm-to-table craft beer twee. Qui photo booth letterpress, commodo enim craft beer mlkshk aliquip jean shorts ullamco ad vinyl cillum PBR. Homo nostrud organic, assumenda labore aesthetic magna delectus mollit.</p>
						</div>
						<div class="tab-pane fade" id="dropdown1">
							<p>Etsy mixtape wayfarers, ethical wes anderson tofu before they sold out mcsweeney's organic lomo retro fanny pack lo-fi farm-to-table readymade. Messenger bag gentrify pitchfork tattooed craft beer, iphone skateboard locavore carles etsy salvia banksy hoodie helvetica. DIY synth PBR banksy irony. Leggings gentrify squid 8-bit cred pitchfork.</p>
						</div>
						<div class="tab-pane fade" id="dropdown2">
							<p>Trust fund seitan letterpress, keytar raw denim keffiyeh etsy art party before they sold out master cleanse gluten-free squid scenester freegan cosby sweater. Fanny pack portland seitan DIY, art party locavore wolf cliche high life echo park Austin. Cred vinyl keffiyeh DIY salvia PBR, banh mi before they sold out farm-to-table VHS viral locavore cosby sweater.</p>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<h2 id="nav-pills">Pills</h2>
					<ul class="nav nav-pills">
						<li class="active"><a href="#">Home</a></li>
						<li class="disabled"><a href="#">Disabled</a></li>
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#">
							Dropdown <span class="caret"></span>
							</a>
							<ul class="dropdown-menu">
							<li><a href="#">Action</a></li>
							<li><a href="#">Another action</a></li>
							<li><a href="#">Something else here</a></li>
							<li class="divider"></li>
							<li><a href="#">Separated link</a></li>
							</ul>
						</li>
						<li><a href="#">Profile</a></li>
						<li><a>Item 5</a></li>
						<li><a>Item 6</a></li>
						<li><a>Item 7</a></li>
						<li><a>Item 8</a></li>
						<li><a>Item 9</a></li>
						<li><a>Item 10</a></li>
					</ul>
					<hr class="hr-label" /><span class="label">NAV-STACKED</span>
					<ul class="nav nav-pills nav-stacked">
						<li class="active"><a href="#">Home</a></li>
						<li><a href="#">Profile</a></li>
						<li class="disabled"><a href="#">Disabled</a></li>
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#">
							Dropdown <span class="caret"></span>
							</a>
							<ul class="dropdown-menu">
							<li><a href="#">Action</a></li>
							<li><a href="#">Another action</a></li>
							<li><a href="#">Something else here</a></li>
							<li class="divider"></li>
							<li><a href="#">Separated link</a></li>
							</ul>
						</li>
					</ul>
				</div>
			</div>
			<div class="well top-space">
				<h4 id="nav-menu" class="page-header">
					<span class="base-icon-star text-live"></span> Nav Pill Menu <small>Horizontal Menu Option</small> <span class="label label-warning pull-right">BASE</span>
				</h4>
				<div class="row">
					<div class="col-lg-8">
						<ul class="nav nav-pills menu">
						<li class="active"><a href="#">Home</a></li>
						<li><a href="#">Profile</a></li>
						<li class="disabled"><a href="#">Disabled</a></li>
						<li>
							<a href="#">Sub Levels</a>
							<ul class="nav-child dropdown-menu">
							<li><a href="#">Action</a></li>
							<li>
								<a href="#">Sub Levels</a>
								<ul class="nav-child dropdown-menu">
								<li><a href="#">Action</a></li>
								<li><a href="#">Another action</a></li>
								<li><a href="#">Something else here</a></li>
								<li class="divider"></li>
								<li><a href="#">Separated link</a></li>
								</ul>
							</li>
							<li><a href="#">Something else here</a></li>
							<li class="divider"></li>
							<li><a href="#">Separated link</a></li>
							</ul>
						</li>
						<li><a href="#">Item 5</a></li>
						<li class="child-reverse">
							<a href="#">Child Reverse</a>
							<ul class="nav-child dropdown-menu">
								<li><a href="#">Action</a></li>
								<li>
									<a href="#">Sub Levels</a>
									<ul class="nav-child dropdown-menu">
										<li><a href="#">Action</a></li>
										<li><a href="#">Another action</a></li>
										<li><a href="#">Something else here</a></li>
										<li><a href="#">Separated link</a></li>
									</ul>
								</li>
								<li><a href="#">Something else here</a></li>
								<li class="divider"></li>
								<li><a href="#">Separated link</a></li>
							</ul>
						</li>
						</ul>
						<hr class="hr-sm" />
						<p>To set the child menu to reverse side, just add "child-reverse" class to 'li' <code>&lt;li class="child-reverse"&gt;</code> parent on level 1 (root). To flexibility this option, the class can also to be add into 'li(root) > <a>'. eg <code>&lt;li&gt;&lt;a class="child-reverse"&gt;</code></p>
						<div class="alert alert-info top-space">
							<span class="base-icon-info-circled"></span> The menu can also be used without the class <strong>.nav-pills</strong>. However, the menu classes should be customized.
						</div>
					</div>
					<div class="col-lg-4">
<pre class="no-margin">&lt;ul class="nav nav-pills <em class="strong text-danger">menu</em>"&gt;
&lt;li&gt;...&lt;/li&gt;
&lt;li&gt;
&lt;a&gt;...&lt;/a&gt;
&lt;ul <em class="strong text-danger">class="nav-child dropdown-menu"</em>&gt;
&lt;li&gt;...&lt;/li&gt;
&lt;/ul&gt;
&lt;/li&gt;
&lt;/ul&gt;
</pre>
					</div>
				</div>
				<h4 id="nav-menu" class="page-header">
					<span class="base-icon-star text-live"></span> Nav Menu List <small>Vertical Menu Option</small>
				</h4>
				<div class="row">
					<div class="col-lg-4">
						<ul class="nav list-menu">
						<li class="active"><a href="#">Home</a></li>
						<li><a href="#">Profile</a></li>
						<li class="active">
							<a href="#">Sub Levels</a>
							<ul class="nav-child">
							<li><a href="#">Action</a></li>
							<li>
								<a href="#">Sub Levels</a>
								<ul class="nav-child">
								<li><a href="#">Action</a></li>
								<li><a href="#">Another action</a></li>
								<li><a href="#">Something else here</a></li>
								<li class="divider"></li>
								<li><a href="#">Separated link</a></li>
								</ul>
							</li>
							<li><a href="#">Something else here</a></li>
							<li class="divider"></li>
							<li><a href="#">Separated link</a></li>
							</ul>
						</li>
						<li><a href="#">Item 5</a></li>
						<li><a href="#">Item 6</a></li>
						</ul>
					</div>
					<div class="col-lg-4">
						<ul class="nav list-menu set-border border-default">
						<li class="active"><a href="#">Home</a></li>
						<li><a href="#">Profile</a></li>
						<li class="active">
							<a href="#">Sub Levels</a>
							<ul class="nav-child">
							<li><a href="#">Action</a></li>
							<li>
								<a href="#">Sub Levels</a>
								<ul class="nav-child">
								<li><a href="#">Action</a></li>
								<li><a href="#">Another action</a></li>
								<li><a href="#">Something else here</a></li>
								<li><a href="#">Separated link</a></li>
								</ul>
							</li>
							<li><a href="#">Something else here</a></li>
							<li class="divider"></li>
							<li><a href="#">Separated link</a></li>
							</ul>
						</li>
						<li><a href="#">Item 5</a></li>
						<li><a href="#">Item 6</a></li>
						</ul>
						<span class="label label-primary">Bordered Option</span>
					</div>
					<div class="col-lg-4">
<pre class="no-margin">&lt;ul class="nav <em class="strong text-danger">list-menu</em>"&gt;
&lt;li&gt;...&lt;/li&gt;
&lt;li&gt;
&lt;a&gt;...&lt;/a&gt;
&lt;ul <em class="strong text-danger">class="nav-child"</em>&gt;
&lt;li&gt;...&lt;/li&gt;
&lt;/ul&gt;
&lt;/li&gt;
&lt;/ul&gt;
</pre>
&nbsp;
<pre class="no-margin">&lt;ul class="nav <em class="strong text-danger">list-menu set-border border-default</em>"&gt;
&lt;li&gt;...&lt;/li&gt;
&lt;li&gt;
&lt;a&gt;...&lt;/a&gt;
&lt;ul <em class="strong text-danger">class="nav-child"</em>&gt;
&lt;li&gt;...&lt;/li&gt;
&lt;/ul&gt;
&lt;/li&gt;
&lt;/ul&gt;
</pre>
					</div>
				</div>
			</div>
			<h2 id="nav-collapse">Accordion {Collapse}</h2>
			<div class="row">
				<div class="col-lg-6">
					<div class="panel-group collapse-indicator" id="accordion" role="tablist" aria-multiselectable="true">
						<div class="panel panel-default">
						<div class="panel-heading" role="tab" id="headingOne">
							<h4 class="panel-title">
							<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
								Collapsible Group Item #1
							</a>
							</h4>
						</div>
						<div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
							<div class="panel-body">
							Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
							</div>
						</div>
						</div>
						<div class="panel panel-default">
						<div class="panel-heading" role="tab" id="headingTwo">
							<h4 class="panel-title">
							<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
								Collapsible Group Item #2
							</a>
							</h4>
						</div>
						<div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
							<div class="panel-body">
							Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
							</div>
						</div>
						</div>
						<div class="panel panel-default">
						<div class="panel-heading" role="tab" id="headingThree">
							<h4 class="panel-title">
							<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
								Collapsible Group Item #3
							</a>
							</h4>
						</div>
						<div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
							<div class="panel-body">
							Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
							</div>
						</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
				<div class="well well-sm">
					<h4 class="page-header top-expand-sm"><span class="base-icon-star text-live"></span> Collapse Indicator <span class="label label-warning pull-right">BASE</span></h4>
					<p>
						To add indicator icons <span class="base-icon-down-open"></span> <span class="base-icon-up-open"></span> in "tabs" from element "accordion", simply assign the class "collapse-indicator" along with class "panel-group".
						<br />ex: <code>&lt;div class="panel-group collapse-indicator" ...&gt;</code>
					</p>
					<div class="panel-group collapse-indicator" id="accordion2" role="tablist" aria-multiselectable="true">
						<div class="panel panel-default">
						<div class="panel-heading" role="tab" id="headingOne">
							<h4 class="panel-title">
							<a role="button" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne2" aria-expanded="true" aria-controls="collapseOne2">
								Collapsible Group Item #1
							</a>
							</h4>
						</div>
						<div id="collapseOne2" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
							<div class="panel-body">
							Default Opened...
							</div>
						</div>
						</div>
						<div class="panel panel-default">
						<div class="panel-heading" role="tab" id="headingTwo">
							<h4 class="panel-title">
							<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo2" aria-expanded="false" aria-controls="collapseTwo2">
								Collapsible Group Item #2
							</a>
							</h4>
						</div>
						<div id="collapseTwo2" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
							<div class="panel-body">
							Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus...
							</div>
						</div>
						</div>
						<div class="panel panel-default">
						<div class="panel-heading" role="tab" id="headingThree">
							<h4 class="panel-title">
							<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion2" href="#collapseThree2" aria-expanded="false" aria-controls="collapseThree2">
								Collapsible Group Item #3
							</a>
							</h4>
						</div>
						<div id="collapseThree2" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
							<div class="panel-body">
							Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus...
							</div>
						</div>
						</div>
					</div>
				</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-4">
				<h2 id="nav-breadcrumbs">Breadcrumbs</h2>
				<ul class="breadcrumb">
					<li class="active">Home</li>
				</ul>

				<ul class="breadcrumb">
					<li><a href="#">Home</a></li>
					<li class="active">Library</li>
				</ul>

				<ul class="breadcrumb">
					<li><a href="#">Home</a></li>
					<li><a href="#">Library</a></li>
					<li class="active">Data</li>
				</ul>
				</div>
				<div class="col-lg-4">
				<h2 id="nav-pagination">Pagination</h2>
				<ul class="pagination">
					<li class="disabled"><a href="#">&laquo;</a></li>
					<li class="active"><a href="#">1</a></li>
					<li><a href="#">2</a></li>
					<li><a href="#">3</a></li>
					<li><a href="#">4</a></li>
					<li><a href="#">5</a></li>
					<li><a href="#">&raquo;</a></li>
				</ul>

				<ul class="pagination pagination-lg">
					<li class="disabled"><a href="#">&laquo;</a></li>
					<li class="active"><a href="#">1</a></li>
					<li><a href="#">2</a></li>
					<li><a href="#">3</a></li>
					<li><a href="#">&raquo;</a></li>
				</ul>

				<ul class="pagination pagination-sm">
					<li class="disabled"><a href="#">&laquo;</a></li>
					<li class="active"><a href="#">1</a></li>
					<li><a href="#">2</a></li>
					<li><a href="#">3</a></li>
					<li><a href="#">4</a></li>
					<li><a href="#">5</a></li>
					<li><a href="#">&raquo;</a></li>
				</ul>
				</div>
				<div class="col-lg-4">
				<h2 id="nav-pager">Pager</h2>
				<ul class="pager">
					<li><a href="#">Previous</a></li>
					<li><a href="#">Next</a></li>
				</ul>

				<ul class="pager">
					<li class="previous disabled"><a href="#">&larr; Older</a></li>
					<li class="next"><a href="#">Newer &rarr;</a></li>
				</ul>
				</div>
			</div>
			<div id="nav-steps" class="well">
				<h3 class="page-header no-margin-top"><span class="base-icon-star text-live"></span> Steps <span class="label label-warning text-sm pull-right">BASE</span></h3>
				<div class="row">
					<div class="col-lg-6">
					<h4 id="nav-steps">Step</h4>
					<p><span class="nav-step">1</span> <span class="nav-step active">2</span> <span class="nav-step done">3</span></p>
					<code>&lt;span class="nav-step"&gt;1&lt;/span&gt;</code><br /><code>&lt;span class="nav-step active"&gt;2&lt;/span&gt;</code><br /><code>&lt;span class="nav-step done"&gt;3&lt;/span&gt;</code>
					<hr class="hr-label" /><span class="label hr-label">size options</span>
					<p>
						<span class="nav-step step-sm">1</span> <span class="nav-step step-sm">2</span> <span class="nav-step step-sm">3</span>
						<span class="nav-step step-lg">1</span> <span class="nav-step step-lg">2</span> <span class="nav-step step-lg">3</span></p>
					<code>&lt;span class="nav-step step-sm"&gt;sm&lt;/span&gt;</code><br /><code>&lt;span class="nav-step step-lg"&gt;lg&lt;/span&gt;</code>
					</div>
					<div class="col-lg-6">
					<h4 id="nav-wizard">Wizard</h4>
					<ul class="nav-wizard">
						<li><span class="nav-step done">1</span><label>Step 1</label></li>
						<li><span class="nav-step active">2</span><label>Step 2</label></li>
						<li><span class="nav-step">3</span><label>Step 3</label></li>
						<li><span class="nav-step">4</span><label>Step 4</label></li>
					</ul>
<pre class="no-margin">&lt;ul class="nav-wizard"&gt;
&lt;li&gt;
&lt;span class="nav-step done"&gt;1&lt;/span&gt;
&lt;label&gt;Step 1&lt;/label&gt;
&lt;/li&gt;
&lt;li&gt;
&lt;span class="nav-step active"&gt;2&lt;/span&gt;
&lt;label&gt;Step 2&lt;/label&gt;
&lt;/li&gt;
&lt;li&gt;
&lt;span class="nav-step"&gt;3&lt;/span&gt;
&lt;label&gt;Step 3&lt;/label&gt;
&lt;/li&gt;
...
&lt;/ul&gt;
</pre>
					</div>
				</div>
			</div>
		</section>

		<!-- Images
		================================================== -->
		<section id="images">

			<h1 class="page-header">Images <span class="label label-primary text-md pull-right top-space">BOOTSTRAP</span></h1>

			<div class="row text-center">
				<div class="col-lg-4">
				<p><img src="http://placehold.it/200x200" width="200" height="200" class="img-rounded" /></p>
				<code>&lt;img ... class="img-rounded" /&gt;</code>
				</div>
				<div class="col-lg-4">
				<p><img src="http://placehold.it/200x200" width="200" height="200" class="img-circle" /></p>
				<code>&lt;img ... class="img-circle" /&gt;</code>
				</div>
				<div class="col-lg-4">
				<p><img src="http://placehold.it/200x200" width="200" height="200" class="img-thumbnail" /></p>
				<code>&lt;img ... class="img-thumbnail" /&gt;</code>
				</div>
			</div>
		</section>

		<!-- Indicators
		================================================== -->
		<section id="indicators">

			<h1 class="page-header">Indicators <span class="label label-primary text-md pull-right top-space">BOOTSTRAP</span></h1>

			<div class="row">
				<div class="col-lg-12">
				<h2>Alerts</h2>
				<div class="text-right"><code>class="alert <em class="strong">alert-warning</em>"</code></div>
				<div class="alert alert-dismissible alert-warning">
					<button type="button" name="name" class="close" data-dismiss="alert">&times;</button>
					<h4>Warning!</h4>
					<p>Best check yo self, you're not looking too good. Nulla vitae elit libero, a pharetra augue. Praesent commodo cursus magna, <a href="#" class="alert-link">vel scelerisque nisl consectetur et</a>.</p>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-4">
					<div class="text-right"><code>class="alert <em class="strong">alert-danger</em>"</code></div>
					<div class="alert alert-dismissible alert-danger">
						<button type="button" name="name" class="close" data-dismiss="alert">&times;</button>
						<strong>Oh snap!</strong> <a href="#" class="alert-link">Change a few things up</a> and try submitting again.
					</div>
				</div>
				<div class="col-lg-4">
					<div class="text-right"><code>class="alert <em class="strong">alert-success</em>"</code></div>
					<div class="alert alert-dismissible alert-success">
						<button type="button" name="name" class="close" data-dismiss="alert">&times;</button>
						<strong>Well done!</strong> You successfully read <a href="#" class="alert-link">this important alert message</a>.
					</div>
				</div>
				<div class="col-lg-4">
					<div class="text-right"><code>class="alert <em class="strong">alert-info</em>"</code></div>
					<div class="alert alert-dismissible alert-info">
						<button type="button" name="name" class="close" data-dismiss="alert">&times;</button>
						<strong>Heads up!</strong> This <a href="#" class="alert-link">alert needs your attention</a>, but it's not super important.
					</div>
				</div>
			</div>
			<div class="well">
				<h4 id="alerts-context-icons" class="page-header"><span class="base-icon-star text-live"></span> Alerts Context Icons<span class="label label-warning pull-right">BASE</span><span class="label label-primary pull-right right-space-xs">BOOTSTRAP</span></h2>
				<div class="row">
					<div class="col-lg-6">
						<p class="text-info">To set context icons just add the class <em class="strong">.alert-icon</em></p>
						<code>class="alert alert-warning <em class="strong">alert-icon</em>"</code>
						<div class="alert alert-dismissible alert-warning alert-icon">
							<button type="button" name="name" class="close" data-dismiss="alert">&times;</button>
							<h4>Warning!</h4>
							<p>Best check yo self, you're not looking too good. Nulla vitae elit libero, a pharetra augue. Praesent commodo cursus magna, <a href="#" class="alert-link">vel scelerisque nisl consectetur et</a>.</p>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="alert alert-dismissible alert-danger alert-icon">
							<button type="button" name="name" class="close" data-dismiss="alert">&times;</button>
							<strong>Oh snap!</strong> <a href="#" class="alert-link">Change a few things up</a> and try submitting again.
						</div>
						<div class="alert alert-dismissible alert-success alert-icon">
							<button type="button" name="name" class="close" data-dismiss="alert">&times;</button>
							<strong>Well done!</strong> You successfully read <a href="#" class="alert-link">this important alert message</a>.
						</div>
						<div class="alert alert-dismissible alert-info alert-icon">
							<button type="button" name="name" class="close" data-dismiss="alert">&times;</button>
							<strong>Heads up!</strong> This <a href="#" class="alert-link">alert needs your attention</a>, but it's not super important.
						</div>
					</div>
				</div>
				<h4 class="page-header">Extra Contexts</h4>
				<div class="row">
					<div class="col-lg-6">
						<div>
							<span class="label label-default">Help</span>
							<code class="pull-right">... alert-icon <em class="strong">help</em>"</code>
						</div>
						<div class="alert alert-dismissible alert-warning alert-icon help">
							<button type="button" name="name" class="close" data-dismiss="alert">&times;</button>
							<strong>Oh snap!</strong> <a href="#" class="alert-link">Change a few things up</a> and try submitting again.
						</div>
					</div>
					<div class="col-lg-6">
						<div>
							<span class="label label-default">Note, Tips</span>
							<code class="pull-right">... alert-icon <em class="strong">note</em>"</code>
						</div>
						<div class="alert alert-dismissible alert-info alert-icon note">
							<button type="button" name="name" class="close" data-dismiss="alert">&times;</button>
							<strong>Well done!</strong> You successfully read <a href="#" class="alert-link">this important alert message</a>.
						</div>
					</div>
				</div>
				<h4 id="alerts-balloon" class="page-header"><span class="base-icon-star text-live"></span> Alerts Ballon</h2>
				<div class="row">
					<div class="col-lg-6">
						<p class="text-info">It shows warning messages on a fixed position of the window and the time option to close</p>
						<p><button name="name" class="btn btn-primary" onclick="showAlertBalloon()">Click to see the "balloon" in the top position and right</button></p>

						<div class="set-alert-balloon" data-offset-top="80" data-offset-right="30" data-seconds="5" data-show="true">
							<div class="alert alert-dismissible alert-success alert-icon">
								<button type="button" name="name" class="close" data-dismiss="alert">&times;</button>
								<h4>Success!</h4>
								<p>Best check yo self, you're not looking too good. Nulla vitae elit libero, a pharetra augue. Praesent commodo cursus magna, <a href="#" class="alert-link">vel scelerisque nisl consectetur et</a>.</p>
							</div>
						</div>
<pre>&lt;div class="set-alert-balloon" data-offset-top="90" data-offset-right="30" data-seconds="5" data-show="true"&gt;
&lt;div class="alert alert-dismissible alert-success alert-icon"&gt;
&lt;button type="button" class="close" data-dismiss="alert"&gt;&times;&lt;/button&gt;
&lt;h4&gt;Success!&lt;/h4&gt;
&lt;p&gt;Best check yo self, you're not looking too good. Nulla vitae elit libero, a pharetra augue. Praesent commodo cursus magna, &lt;a href="#" class="alert-link"&gt;vel scelerisque nisl consectetur et&lt;/a&gt;.&lt;/p&gt;
&lt;/div&gt;
&lt;/div&gt;
</pre>
					</div>
					<div class="col-lg-6">
						<p>The way the "balloon" will be displayed is defined by <strong>setAlertBalloon()</strong> function or through the property <strong>"data"</strong> directly in the element. The parameters for the function, as well as the types of "data" are respectively:</p>
						<ol>
							<li><strong>elem:</strong> Selector that defines the element. Required only in the function.</li>
							<li><strong>seconds:</strong> Time, in seconds, that the "balloon" remains visible. The value '0' (zero), default, indicates that the balloon will not be closed automatically. Just click on the "x" or using the "ESC".</li>
							<li><strong>offsetTop:</strong> Distance to the top of the window</li>
							<li><strong>offsetRight:</strong> Distance to the right side of the window</li>
							<li><strong>show:</strong> True (default) defines the "balloon" is displayed at loading. While "false" indicates that it is hidden in the load being driven by showAlertBalloon() function;</li>
						</ol>
						<p class="strong">showAlertBalloon(elem, seconds);</p>
						<p>defines the "balloon" element that will be displayed and, if necessary, the time it will be visible.</p>
						<p class="alert alert-info alert-icon">If you can be seen more than one message at the same time, should be avoided directly calling the message, but put them in a container as in the example here.</p>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-4">
				<h2>Labels</h2>
				<div style="margin-bottom: 40px;">
					<span class="label label-default">Default</span>
					<span class="label label-primary">Primary</span>
					<span class="label label-success">Success</span>
					<span class="label label-warning">Warning</span>
					<span class="label label-danger">Danger</span>
					<span class="label label-info">Info</span>
				</div>
				</div>
				<div class="col-lg-4">
				<h2>Badges</h2>
				<ul class="nav nav-pills">
				<li class="active"><a href="#">Home <span class="badge">42</span></a></li>
				<li><a href="#">Profile <span class="badge"></span></a></li>
				<li><a href="#">Messages <span class="badge">3</span></a></li>
				</ul>
				</div>
			</div>
		</section>

		<!-- Progress bars
		================================================== -->
		<section>

			<h1 id="progress" class="page-header">Progress bars <span class="label label-primary text-md pull-right top-space">BOOTSTRAP</span></h1>

			<h3 id="progress-basic">Basic</h3>
			<div class="bs-component">
				<div class="progress">
				<div class="progress-bar" style="width: 60%;"></div>
				</div>
			</div>

			<h3 id="progress-alternatives">Contextual alternatives</h3>
			<div class="bs-component">
				<div class="progress">
				<div class="progress-bar progress-bar-info" style="width: 20%"></div>
				</div>

				<div class="progress">
				<div class="progress-bar progress-bar-success" style="width: 40%"></div>
				</div>

				<div class="progress">
				<div class="progress-bar progress-bar-warning" style="width: 60%"></div>
				</div>

				<div class="progress">
				<div class="progress-bar progress-bar-danger" style="width: 80%"></div>
				</div>
			</div>

			<h3 id="progress-striped">Striped</h3>
			<div class="bs-component">
				<div class="progress progress-striped">
				<div class="progress-bar progress-bar-info" style="width: 20%"></div>
				</div>

				<div class="progress progress-striped">
				<div class="progress-bar progress-bar-success" style="width: 40%"></div>
				</div>

				<div class="progress progress-striped">
				<div class="progress-bar progress-bar-warning" style="width: 60%"></div>
				</div>

				<div class="progress progress-striped">
				<div class="progress-bar progress-bar-danger" style="width: 80%"></div>
				</div>
			</div>

			<h3 id="progress-animated">Animated</h3>
			<div class="bs-component">
				<div class="progress progress-striped active">
				<div class="progress-bar" style="width: 45%"></div>
				</div>
			</div>

			<h3 id="progress-stacked">Stacked</h3>
			<div class="bs-component">
				<div class="progress">
				<div class="progress-bar progress-bar-success" style="width: 35%"></div>
				<div class="progress-bar progress-bar-warning" style="width: 20%"></div>
				<div class="progress-bar progress-bar-danger" style="width: 10%"></div>
				</div>
			</div>

			<div class="well">
				<h4 id="load-activity" class="page-header"><span class="base-icon-star text-live"></span> Load Activity<span class="label label-warning pull-right">BASE</span><span class="label label-primary pull-right right-space-xs">BOOTSTRAP</span></h4>
				<div class="row">
					<div class="col-lg-6">
						<p>Processing activity indicator.It shows the user that a process is generally "Ajax" is running.</p>
						<p><button name="name" class="btn btn-primary" onclick="loaderFull(false)">Show/Hide Loader in the bottom of the screen</button></p>
						<p><button name="name" class="btn btn-primary" onclick="loaderFull(true)">Show/Hide Loader Fullscreen</button></p>
						<script>
							function loaderFull(full) {
								toggleLoader(full);
								setTimeout(function() { toggleLoader(full) }, 5000);
							}
						</script>
						<div id="loader" class="progress progress-striped">
							<img src="<?php $base?>core/images/loader-active.gif">
							<div class="progress-bar progress-bar-success"></div>
						</div>
<pre>&lt;div id="loader" class="progress progress-striped"&gt;
&lt;img src="core/images/loader-active.gif"&gt;
&lt;div class="progress-bar progress-bar-success"&gt;&lt;/div&gt;
&lt;/div&gt;
</pre>
					</div>
					<div class="col-lg-6">
						<p>After defining the element in html. Just call the function <strong>toggleLoader()</strong> both to show how to hide the loader.</p>
						<p class="alert alert-warning alert-icon">If the image is not loaded. Check the correct path according to the directory structure used in the project.</p>
						<h4>Loader Fullscreen</h4>
						<p>It occupies the entire screen area making it not take any action while the loader is active.</p>
						<p>To assign the loader in full screen we have two options:</p>
						<p>1 - add the class <code>fullScreen</code> directly into the loader object. So fullscreen becomes the default format.<br /><code>&lt;div id="loader" class="progress progress-striped <strong>fullScreen</strong>"&gt;</code></p>
						<p>2 - Assign "true" to parameter in the function "toggleLoader". Eg <code>toggleLoader(true);</code>
						<p class="alert alert-info alert-icon">
							If the class "fullScreen" is assigned to the object, we can load the loader in the initial format, giving "false" parameter to the function.<br />Eg <code>toggleLoader(false);</code>.
							To load the set as the default format, simply not pass any value to the parameter of the function. Eg <code>toggleLoader();</code>
						</p>
					</div>
			</div>

		</section>

		<!-- list groups
		================================================== -->
		<section id="list-groups">

			<h1 class="page-header">List groups <span class="label label-primary text-md pull-right top-space">BOOTSTRAP</span></h1>

			<div class="row">
				<div class="col-lg-4">
					<ul class="list-group">
					<li class="list-group-item">
						<span class="badge">14</span>
						Cras justo odio
					</li>
					<li class="list-group-item">
						<span class="badge">2</span>
						Dapibus ac facilisis in
					</li>
					<li class="list-group-item">
						<span class="badge">1</span>
						Morbi leo risus
					</li>
					</ul>
				</div>
				<div class="col-lg-4">
					<div class="list-group">
					<a href="#" class="list-group-item active">
						Cras justo odio
					</a>
					<a href="#" class="list-group-item">Dapibus ac facilisis in
					</a>
					<a href="#" class="list-group-item">Morbi leo risus
					</a>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="list-group">
					<a href="#" class="list-group-item">
						<h4 class="list-group-item-heading">List group item heading</h4>
						<p class="list-group-item-text">Donec id elit non mi porta gravida at eget metus. Maecenas sed diam eget risus varius blandit.</p>
					</a>
					<a href="#" class="list-group-item">
						<h4 class="list-group-item-heading">List group item heading</h4>
						<p class="list-group-item-text">Donec id elit non mi porta gravida at eget metus. Maecenas sed diam eget risus varius blandit.</p>
					</a>
					</div>
				</div>
			</div>

		</section>

		<!-- panels
		================================================== -->
		<section id="panels">

			<h1 class="page-header">Panels <span class="label label-primary text-md pull-right top-space">BOOTSTRAP</span></h1>

			<div class="row">
				<div class="col-lg-4">
					<div class="panel panel-default">
					<div class="panel-body">
						Basic panel
					</div>
					</div>

					<div class="panel panel-default">
					<div class="panel-heading">Panel heading</div>
					<div class="panel-body">
						Panel content
					</div>
					</div>

					<div class="panel panel-default">
					<div class="panel-body">
						Panel content
					</div>
					<div class="panel-footer">Panel footer</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="panel panel-primary">
					<div class="panel-heading">
						<h3 class="panel-title">Panel primary</h3>
					</div>
					<div class="panel-body">
						Panel content
					</div>
					</div>

					<div class="panel panel-success">
					<div class="panel-heading">
						<h3 class="panel-title">Panel success</h3>
					</div>
					<div class="panel-body">
						Panel content
					</div>
					</div>

					<div class="panel panel-warning">
					<div class="panel-heading">
						<h3 class="panel-title">Panel warning</h3>
					</div>
					<div class="panel-body">
						Panel content
					</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="panel panel-danger">
					<div class="panel-heading">
						<h3 class="panel-title">Panel danger</h3>
					</div>
					<div class="panel-body">
						Panel content
					</div>
					</div>

					<div class="panel panel-info">
					<div class="panel-heading">
						<h3 class="panel-title">Panel info</h3>
					</div>
					<div class="panel-body">
						Panel content
					</div>
					</div>
				</div>
			</div>

		</section>

		<!-- well
		================================================== -->
		<section id="well">

			<h1 class="page-header">Wells <span class="label label-primary text-md pull-right top-space">BOOTSTRAP</span></h1>

			<div class="row">
				<div class="col-lg-4">
				<div class="well">
					Look, I'm in a well!
				</div>
				</div>
				<div class="col-lg-4">
				<div class="well well-sm">
					Look, I'm in a small well!
				</div>
				</div>
				<div class="col-lg-4">
				<div class="well well-lg">
					Look, I'm in a large well!
				</div>
				</div>
			</div>

		</section>

		<!-- Dialogs
		================================================== -->
		<section id="dialogs">

			<h1 class="page-header">Dialogs <span class="label label-primary text-md pull-right top-space">BOOTSTRAP</span></h1>

			<div class="row">
				<div class="col-lg-6">
				<h2 id="modals">Modals</h2>
				<button type="button" name="name" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">
					Launch demo modal
				</button>
				<button type="button" name="name" class="btn btn-warning btn-lg" data-toggle="modal" data-target="#myModal2">
					Modal without header
				</button>
				<p class="alert alert-info top-space">
					<span class="base-icon-info-circled"></span> To hide the modal header without removing it directly on the code, just add the class "<em class="strong">.no-header</em>" along with "<em class="strong">.modal</em>" class.
				</p>
				<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
					<div class="modal-dialog">
						<div class="modal-content">
						<div class="modal-header">
							<button type="button" name="name" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title">Modal title</h4>
						</div>
						<div class="modal-body">
							<p>One fine body...</p>
						</div>
						<div class="modal-footer">
							<button type="button" name="name" class="btn btn-default" data-dismiss="modal">Close</button>
							<button type="button" name="name" class="btn btn-primary">Save changes</button>
						</div>
						</div>
					</div>
				</div>
				<div class="modal no-header fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
					<div class="modal-dialog">
						<div class="modal-content">
						<div class="modal-header">
							<button type="button" name="name" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title">Modal title</h4>
						</div>
						<div class="modal-body">
							<p class="text-info">
							<span class="base-icon-info-circled"></span> To hide the modal header without removing it directly on the code, just add the class "<em class="strong">.no-header</em>" along with "<em class="strong">.modal</em>" class.
							</p>
							ex: <code>&lt;div class="modal fade <em class="strong text-danger">no-header</em>" ...&gt;</code>
						</div>
						<div class="modal-footer">
							<button type="button" name="name" class="btn btn-default" data-dismiss="modal">Close</button>
							<button type="button" name="name" class="btn btn-primary">Save changes</button>
						</div>
						</div>
					</div>
				</div>
				</div>
				<div class="col-lg-6">

				<h2 id="popovers">Popovers</h2>
				<button type="button" name="name" class="btn btn-default" title="Header" data-container="body" data-toggle="popover" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus.">Default</button>

				<button type="button" name="name" class="btn btn-default" data-container="body" data-toggle="popover" data-placement="left" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus." data-original-title="" title="">Left</button>

				<button type="button" name="name" class="btn btn-default" data-container="body" data-toggle="popover" data-placement="top" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus." data-original-title="" title="">Top</button>

				<button type="button" name="name" class="btn btn-default" data-container="body" data-toggle="popover" data-placement="bottom" data-content="Vivamus
				sagittis lacus vel augue laoreet rutrum faucibus." data-original-title="" title="">Bottom</button>

				<button type="button" name="name" class="btn btn-default" data-container="body" data-toggle="popover" data-placement="right" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus." data-original-title="" title="">Right</button>

				<h2 id="tooltips">Tooltips</h2>
				<button type="button" name="name" class="btn btn-default" data-toggle="tooltip" title="" data-original-title="Tooltip default position">Default</button>

				<button type="button" name="name" class="btn btn-default" data-toggle="tooltip" data-placement="left" title="" data-original-title="Tooltip on left">Left</button>

				<button type="button" name="name" class="btn btn-default" data-toggle="tooltip" data-placement="top" title="" data-original-title="Tooltip on top">Top</button>

				<button type="button" name="name" class="btn btn-default" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Tooltip on bottom">Bottom</button>

				<button type="button" name="name" class="btn btn-default" data-toggle="tooltip" data-placement="right" title="" data-original-title="Tooltip on right">Right</button>

				</div>
			</div>
		</section>

		<!-- module
		================================================== -->
		<section id="module">

			<h1 class="page-header">
				<span class="base-icon-star text-live"></span> Module <span class="label label-warning text-md pull-right top-space">BASE</span>
			</h1>

			<div class="row">
				<div class="col-lg-4">
				<div  class="module clearfix">
					<h3 class="page-header clearfix ">
						<span class="head-container">Header</span>
						<span class="head-tag"></span>
					</h3>
					<div class="module-body">
						<div class="module-content">
							Content...
						</div>
					</div>
				</div>
				</div>
				<div class="col-lg-8">
<pre>&lt;div  class="module clearfix"&gt;
&lt;h3 class="page-header clearfix "&gt;
&lt;span class="head-container"&gt;Header&lt;/span&gt;
&lt;span class="head-tag"&gt;Tag&lt;/span&gt;
&lt;/h3&gt;
&lt;div class="module-body"&gt;
&lt;div class="module-content"&gt;
Content...
&lt;/div&gt;
&lt;/div&gt;
&lt;/div&gt;
</pre>
				</div>
			</div>
			<h4 class="page-header">Additional Options for Font Size</h4>
			<div class="row">
				<div class="col-lg-4">
				<code>&lt;div class="module <em class="strong text-danger">text-xs</em>" ...&gt;</code>
				<div  class="module text-xs clearfix">
					<h3 class="page-header clearfix ">
						<span class="head-container">Header</span>
						<span class="head-tag"><span class="label label-warning text-xs">Extra Small</span></span>
					</h3>
					<div class="module-body">
						<div class="module-content">
							Nullam quis risus eget vel eu leo. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nullam id dolor id nibh ultricies vehicula...
						</div>
					</div>
				</div>
				</div>
				<div class="col-lg-4">
				<code>&lt;div class="module <em class="strong text-danger">text-sm</em>" ...&gt;</code>
				<div  class="module text-sm clearfix">
					<h3 class="page-header clearfix ">
						<span class="head-container">Header</span>
						<span class="head-tag"><span class="label label-warning text-xs">Small</span></span>
					</h3>
					<div class="module-body">
						<div class="module-content">
							Nullam quis risus eget vel eu leo. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nullam id dolor id nibh ultricies vehicula...
						</div>
					</div>
				</div>
				</div>
				<div class="col-lg-4">
				<code>&lt;div class="module <em class="strong text-danger">text-lg</em>" ...&gt;</code>
				<div  class="module text-lg clearfix">
					<h3 class="page-header clearfix ">
						<span class="head-container">Header</span>
						<span class="head-tag"><span class="label label-warning text-xs">Large</span></span>
					</h3>
					<div class="module-body">
						<div class="module-content">
							Nullam quis risus eget vel eu leo. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nullam id dolor id nibh ultricies vehicula...
						</div>
					</div>
				</div>
				</div>
			</div>
		</section>

		<!-- Helper Classes BASE
		================================================== -->
		<section id="base-helper">

			<h1 class="page-header">
				<span class="base-icon-star text-live"></span> Helper Classes from Base <span class="label label-warning text-md pull-right top-space">BASE</span>
			</h1>

			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th width="180">Class</th>
						<th>Description</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th colspan="2" class="active">DISPLAY</th>
					</tr>
					<tr>
						<td>.display-block</td>
						<td>Assigns display "block"</td>
					</tr>
					<tr>
						<td>.display-inline</td>
						<td>Assigns display "inline-block"</td>
					</tr>
					<tr>
						<td>.display-inline-block</td>
						<td>Assigns display "inline-block"</td>
					</tr>
					<tr>
						<th colspan="2" class="active">POSITION</th>
					</tr>
					<tr>
						<td>.pos-relative</td>
						<td>Assigns position "relative"</td>
					</tr>
					<tr>
						<td>.pos-absolute</td>
						<td>Assigns position "absolute"</td>
					</tr>
					<tr>
						<td>.pos-static</td>
						<td>Assigns position "static"</td>
					</tr>
					<tr>
						<th colspan="2" class="active">VERTICAL ALIGNMENT</th>
					</tr>
					<tr>
						<td>.valign-top</td>
						<td>Assigns vertical align "top"</td>
					</tr>
					<tr>
						<td>.valign-middle</td>
						<td>Assigns vertical align "middle"</td>
					</tr>
					<tr>
						<td>.valign-bottom</td>
						<td>Assigns vertical align "bottom"</td>
					</tr>
					<tr>
						<th colspan="2" class="active">VISIBILITY</th>
					</tr>
					<tr>
						<td>.hidden-logged <span class="label label-warning pull-right">JOOMLA</span></td>
						<td>Hides the element when the user is logged in</td>
					</tr>
					<tr>
						<td>.hidden-public <span class="label label-warning pull-right">JOOMLA</span></td>
						<td>Hides the element when the user is not logged in</td>
					</tr>
					<tr>
						<td>.element-invisible</td>
						<td>hides the object with the visibility property. Not with display</td>
					</tr>
					<tr>
						<td>.element-view</td>
						<td>Trigger for checking the presence element in the viewable area of the screen. The element that has this class is assisted during the loading and scroll event. When it is first displayed is added to class' viewed '' and when he, as well as displayed, is the visible screen area (viewport) is added to class "on-screen". When the element is outside the visible area, class "on-screen" is removed</td>
					</tr>
					<tr>
						<td>.element-view.view</td>
						<td>Displayed element</td>
					</tr>
					<tr>
						<td>.element-view.on-screen</td>
						<td>Element is present in the visible area of the screen (viewport). When the element is outside the visible area, class "on-screen" is removed</td>
					</tr>
					<tr>
						<th colspan="2" class="active">WIDTH</th>
					</tr>
					<tr>
						<td>.width-auto</td>
						<td>Assigns width "auto"</td>
					</tr>
					<tr>
						<td>.width-full</td>
						<td>Assigns width "100%"</td>
					</tr>
					<tr>
						<td>.width-auto</td>
						<td>Assigns width "50%"</td>
					</tr>
					<tr>
						<td>.width-responsive</td>
						<td>Assigns max-width "100%"</td>
					</tr>
					<tr>
						<th colspan="2" class="active">FLOAT</th>
					</tr>
					<tr>
						<td>.no-float</td>
						<td>Assigns float "none" to element</td>
					</tr>
					<tr>
						<td>.pull-none</td>
						<td>Alias to ".no-float" class. <em>Complements the classes pull-[left|right] bootstrap</em></td>
					</tr>
					<tr>
						<th colspan="2" class="warning"><span class="base-icon-resize-horizontal text-live"></span> Responsive Options</th>
					</tr>
					<tr>
						<td>.no-float-xs</td>
						<td>Assigns float "none" to element when the resolution is <em class="strong">@media -> xs</em></td>
					</tr>
					<tr>
						<td>.no-float-sm</td>
						<td>Assigns float "none" to element when the resolution is <em class="strong">@media -> sm</em></td>
					</tr>
					<tr>
						<th colspan="2" class="active">SPACING <span class="label label-warning">MARGIN</span></th>
					</tr>
					<tr>
						<td>.all-space</td>
						<td>Assigns margin "20px" to the element</td>
					</tr>
					<tr>
						<td>.top-space</td>
						<td>Assigns margin "20px" above the element</td>
					</tr>
					<tr>
						<td>.bottom-space</td>
						<td>Assigns margin "20px" below the element</td>
					</tr>
					<tr>
						<td>.right-space</td>
						<td>Assigns margin "20px" on the right side of the element</td>
					</tr>
					<tr>
						<td>.left-space</td>
						<td>Assigns margin "20px" on the left side of the element</td>
					</tr>
					<tr>
						<th colspan="2" class="warning"><span class="base-icon-right-dir"></span> Prefix</th>
					</tr>
					<tr>
						<td>-xs</td>
						<td>Set spacing to <strong>5px</strong>. Eg: <em class="strong text-danger">top-space-xs</em> assigns the margin of <em class="strong text-danger">5px</em> above the element</td>
					</tr>
					<tr>
						<td>-sm</td>
						<td>Set spacing to <strong>10px</strong>. Eg: <em class="strong text-danger">top-space-sm</em> assigns the margin of <em class="strong text-danger">10px</em> above the element</td>
					</tr>
					<tr>
						<th colspan="2" class="warning"><span class="base-icon-block text-danger"></span> Remove Spacing <span class="label label-warning">MARGIN</span></th>
					</tr>
					<tr>
						<td>.no-margin</td>
						<td>Remove margin to the element</td>
					</tr>
					<tr>
						<td>.no-margin-top</td>
						<td>Remove margin above the element</td>
					</tr>
					<tr>
						<td>.no-margin-bottom</td>
						<td>Remove margin below the element</td>
					</tr>
					<tr>
						<td>.no-margin-right</td>
						<td>Remove margin on the right side of the element</td>
					</tr>
					<tr>
						<td>.no-margin-left</td>
						<td>Remove margin on the left side of the element</td>
					</tr>
					<tr>
						<th colspan="2" class="active">EXPAND <span class="label label-warning">PADDING</span></th>
					</tr>
					<tr>
						<td>.all-expand</td>
						<td>Assigns padding "10px" to the element</td>
					</tr>
					<tr>
						<td>.top-expand</td>
						<td>Assigns padding "10px" above the element</td>
					</tr>
					<tr>
						<td>.bottom-expand</td>
						<td>Assigns padding "10px" below the element</td>
					</tr>
					<tr>
						<td>.right-expand</td>
						<td>Assigns padding "10px" on the right side of the element</td>
					</tr>
					<tr>
						<td>.left-expand</td>
						<td>Assigns padding "10px" on the left side of the element</td>
					</tr>
					<tr>
						<th colspan="2" class="warning"><span class="base-icon-right-dir"></span> Prefix</th>
					</tr>
					<tr>
						<td>-xs</td>
						<td>Set expand to <strong>5px</strong>. Eg: <em class="strong text-danger">top-expand-sm</em> assigns the padding of <em class="strong text-danger">5px</em> above the element</td>
					</tr>
					<tr>
						<td>-sm</td>
						<td>Set expand to <strong>20px</strong>. Eg: <em class="strong text-danger">top-expand-lg</em> assigns the padding of <em class="strong text-danger">20px</em> above the element</td>
					</tr>
					<tr>
						<th colspan="2" class="warning"><span class="base-icon-block text-danger"></span> Remove Expand <span class="label label-warning">PADDING</span></th>
					</tr>
					<tr>
						<td>.no-padding</td>
						<td>Remove padding to the element</td>
					</tr>
					<tr>
						<td>.no-padding-top</td>
						<td>Remove padding above the element</td>
					</tr>
					<tr>
						<td>.no-padding-bottom</td>
						<td>Remove padding below the element</td>
					</tr>
					<tr>
						<td>.no-padding-right</td>
						<td>Remove padding on the right side of the element</td>
					</tr>
					<tr>
						<td>.no-padding-left</td>
						<td>Remove padding on the left side of the element</td>
					</tr>
					<tr>
						<th colspan="2" class="active">BORDER</th>
					</tr>
					<tr>
						<td>.set-border</td>
						<td>Assigns border "default" to the element</td>
					</tr>
					<tr>
						<td>.set-border-top</td>
						<td>Assigns border "default" above the element</td>
					</tr>
					<tr>
						<td>.set-border-bottom</td>
						<td>Assigns border "default" below the element</td>
					</tr>
					<tr>
						<td>.set-border-right</td>
						<td>Assigns border "default" on the right side of the element</td>
					</tr>
					<tr>
						<td>.set-border-left</td>
						<td>Assigns border "default" on the left side of the element</td>
					</tr>
					<tr>
						<th colspan="2" class="warning"><span class="base-icon-right-dir"></span> Border Style</th>
					</tr>
					<tr>
						<td>border-dotted</td>
						<td>Assigns border color "default" to the element. Eg: <code>&lt;div class="set-border <em class="strong text-danger">border-dotted</em>" ...&gt;</code></td>
					</tr>
					<tr>
						<td>border-dashed</td>
						<td>Assigns border "dashed" to the element. Eg: <code>&lt;div class="set-border <em class="strong text-danger">border-dashed</em>" ...&gt;</code></td>
					</tr>
					<tr>
						<th colspan="2" class="warning"><span class="base-icon-right-dir"></span> Border Color</th>
					</tr>
					<tr>
						<td>border-default</td>
						<td>assigns the "default" color to the element. Eg: <code>&lt;div class="set-border <em class="strong text-danger">border-default</em>" ...&gt;</code></td>
					</tr>
					<tr>
						<td>border-white</td>
						<td>assigns the "white" color to the element. Eg: <code>&lt;div class="set-border <em class="strong text-danger">border-white</em>" ...&gt;</code></td>
					</tr>
					<tr>
						<td>border-live</td>
						<td>assigns the "live" color to the element. Eg: <code>&lt;div class="set-border <em class="strong text-danger">border-live</em>" ...&gt;</code></td>
					</tr>
					<tr>
						<th colspan="2" class="warning"><span class="base-icon-block text-danger"></span> Remove Border</th>
					</tr>
					<tr>
						<td>.no-border</td>
						<td>Remove border to the element</td>
					</tr>
					<tr>
						<th colspan="2" class="warning"><span class="base-icon-right-dir"></span> Border Radius</th>
					</tr>
					<tr>
						<td>.border-radius</td>
						<td>assigns the border-radius "4px" to the element. Eg: <code>&lt;div class="<em class="strong text-danger">border-radius</em>" ...&gt;</code></td>
					</tr>
					<tr>
						<td>.border-radius-sm</td>
						<td>assigns the border-radius "2px" to the element. Eg: <code>&lt;div class="<em class="strong text-danger">border-radius-sm</em>" ...&gt;</code></td>
					</tr>
					<tr>
						<td>.border-radius-lg</td>
						<td>assigns the border-radius "6px" to the element. Eg: <code>&lt;div class="<em class="strong text-danger">border-radius-lg</em>" ...&gt;</code></td>
					</tr>
					<tr>
						<td>.border-radius-circle</td>
						<td>Assigns the element a circle format. Eg: <code>&lt;div class="<em class="strong text-danger">border-radius-circle</em>" ...&gt;</code></td>
					</tr>
					<tr>
						<th colspan="2" class="warning"><span class="base-icon-block text-danger"></span> Remove Border Radius</th>
					</tr>
					<tr>
						<td>.border-radius-none</td>
						<td>Remove border-radius to the element</td>
					</tr>
					<tr>
						<th colspan="2" class="active">BACKGROUND COLOR</th>
					</tr>
					<tr>
						<td>.bg-white</td>
						<td>Assigns "white" background-color to the element</td>
					</tr>
					<tr>
						<td>.bg-gray</td>
						<td>Assigns "gray" background-color to the element</td>
					</tr>
					<tr>
						<th colspan="2" class="warning"><span class="base-icon-block text-danger"></span> Remove background Color</th>
					</tr>
					<tr>
						<td>.bg-transparent</td>
						<td>Remove background-color to the element</td>
					</tr>
					<tr>
						<th colspan="2" class="active">BOX-SHADOW</th>
					</tr>
					<tr>
						<td>.set-shadow</td>
						<td>Assigns "default" box-shadow to the element</td>
					</tr>
					<tr>
						<td>.set-shadow-sm</td>
						<td>Optional small size for Box Shadow</td>
					</tr>
					<tr>
						<td>.set-shadow-lg</td>
						<td>Optional large size for Box Shadow</td>
					</tr>
					<tr>
						<th colspan="2" class="warning"><span class="base-icon-resize-horizontal text-live"></span> Direction Options</th>
					</tr>
					<tr>
						<td>.to-left</td>
						<td>directs the shadow to the left of the element. Eg: <code>&lt;div class="set-shadow<em class="strong text-danger">to-left</em>" ...&gt;</code></td>
					</tr>
					<tr>
						<td>.to-right</td>
						<td>directs the shadow to the right of the element. Eg: <code>&lt;div class="set-shadow<em class="strong text-danger">to-right</em>" ...&gt;</code></td>
					</tr>
					<tr>
						<th colspan="2" class="warning"><span class="base-icon-block text-danger"></span> Remove Box Shadow</th>
					</tr>
					<tr>
						<td>.no-shadow</td>
						<td>Remove box-shadow to the element</td>
					</tr>
					<tr>
						<th colspan="2" class="active">CLEAR</th>
					</tr>
					<tr>
						<td>.clear</td>
						<td>No floating elements allowed on the left or the right side of a specified element</td>
					</tr>
					<tr>
						<th colspan="2" class="warning"><span class="base-icon-resize-horizontal text-live"></span> Responsive Options</th>
					</tr>
					<tr>
						<td>.clear-xs</td>
						<td>"clear" the element when the resolution is <em class="strong">@media -> xs</em></td>
					</tr>
					<tr>
						<td>.clear-sm</td>
						<td>"clear" the element when the resolution is <em class="strong">@media -> sm</em></td>
					</tr>
					<tr>
						<th colspan="2" class="active">CURSOR</th>
					</tr>
					<tr>
						<td>.cursor-pointer</td>
						<td>Assigns cursor "pointer" to the element</td>
					</tr>
					<tr>
						<th colspan="2" class="active">NEW WINDOW - LINK INDICATOR</th>
					</tr>
					<tr>
						<td>.new-window</td>
						<td>Assigns a link <a href="#" class="new-window">indicator</a> to alert that a new window will is opened on click</td>
					</tr>
					<tr>
						<td>.cursor-help</td>
						<td>Assigns cursor "help" to the element</td>
					</tr>
					<tr>
						<th colspan="2" class="active">LINE BREAK</th>
					</tr>
					<tr>
						<td>.word-break</td>
						<td>Allow long words to be able to break and wrap onto the next line</td>
					</tr>
					<tr>
						<th colspan="2" class="active">PAGE BREAK <span class="label label-warning">ONLY IN PRINT</span></th>
					</tr>
					<tr>
						<td>.page-break</td>
						<td>Always insert a page break after each &lt;footer&gt; element (when printing)</td>
					</tr>
				</tbody>
			</table>
		</section>
	</div>
	<div class="col-lg-3">
		<!-- Nav Bar -->
		<nav id="guide-menu" class="set-border border-default color-muted-lighter text-sm set-parent-width">
			<ul class="nav list-menu">
				<li><a href="#brands">Brands</a></li>
				<li>
					<a href="#color-scheme">Color Scheme</a>
					<ul class="nav-child">
						<li><a href="#base-colors">Base Colors</a></li>
						<li><a href="#bootstrap-colors">Bootstrap Colors</a></li>
					</ul>
				</li>
				<li><a href="#fonts">Fonts</a></li>
				<li><a href="#navbar">Navbar</a></li>
				<li><a href="#buttons">Buttons</a></li>
				<li>
					<a href="#type">Typography</a>
					<ul class="nav-child">
						<li><a href="#type-alignment">Alignment</a></li>
						<li><a href="#type-hr">Horizontal rule</a></li>
						<li><a href="#type-blockquotes">Blockquotes</a></li>
						<li><a href="#type-lists">Lists</a></li>
					</ul>
				</li>
				<li><a href="#tables">Tables</a></li>
				<li>
					<a href="#forms">Forms</a>
					<ul class="nav-child">
						<li><a href="#form-base">Additional Options</a></li>
						<li><a href="#custom-fields">Custom Fields</a></li>
					</ul>
				</li>
				<li>
					<a href="#navs">Navs</a>
					<ul class="nav-child">
						<li><a href="#nav-tabs">Tabs</a></li>
						<li><a href="#nav-pills">Pills</a></li>
						<li><a href="#nav-menu">Nav Menu</a></li>
						<li><a href="#nav-collapse">Collapse</a></li>
						<li><a href="nav-breadcrumbs">Breadcrumbs</a></li>
						<li><a href="nav-pagination">Pagination</a></li>
						<li><a href="nav-pager">Pager</a></li>
						<li><a href="nav-steps">Steps</a></li>
					</ul>
				</li>
				<li><a href="#images">Images</a></li>
				<li>
					<a href="#indicators">Alerts, Labels & Badges</a>
					<ul class="nav-child">
						<li><a href="#alerts-context-icons">Alerts Context Icons</a></li>
						<li><a href="#alerts-balloon">Alerts Balloon</a></li>
					</ul>
				</li>
				<li>
					<a href="#progress">Progress bars</a>
					<ul class="nav-child">
						<li><a href="#load-activity">Load Activity</a></li>
					</ul>
				</li>
				<li><a href="#list-groups">List Groups</a></li>
				<li><a href="#panels">Panels</a></li>
				<li><a href="#well">Wells</a></li>
				<li>
					<a href="#dialogs">Dialogs</a>
					<ul class="nav-child">
						<li><a href="#modals">Modals</a></li>
						<li><a href="#popovers">Popovers</a></li>
						<li><a href="#tooltips">Tooltips</a></li>
					</ul>
				</li>
				<li><a href="#module">Module</a></li>
				<li><a href="#base-helper">Base Utils</a></li>
			</ul>
		</nav>
	</div>
</div>
