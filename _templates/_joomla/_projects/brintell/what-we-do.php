<script type="text/javascript">
jQuery(function() {
	jQuery(window).scroll(function () {
		// dark background
		var fadeBack = (jQuery('#what-we-do').offset().top - jQuery(window).scrollTop()) / 300;
		jQuery('#back-background, #data-particles').css('opacity', fadeBack);
		// menu background
		var fadeMenu = ((jQuery('#brintell-slogan').offset().top - (jQuery(window).scrollTop() + 100)) * -1) / 100;
		jQuery('#menu-background, img.logo-def').css('opacity', fadeMenu);
	});
});
</script>

<div id="brintell-home">

	<div id="back-background"></div>

	<div id="menu-background"></div>

	<div id="data-particles">
		<div id="stars"></div>
		<div id="stars2"></div>
		<div id="stars3"></div>
	</div>

	<div id="brintell-city"></div>

	<div id="brintell-slogan">
	    <div class="container">
			<h1 class="display-3 font-serif">
				What We Do
				<div class="text-muted text-md lh-1-3">
					Brintell does a large range of services related to data, from its very acquisition to the generation of unprecedented insights related to your business. In this section, we explain in detail some of our most common activities.
				</div>
			</h1>
		</div>
	</div>

	<div id="content-container" class="container">
		<div class="row">
			<div class="col-12">
				<div id="what-we-do" class="bg-white p-3 p-lg-5 mb-5 set-shadow-lg">
					<div class="row no-gutters justify-content-between">
						<div class="col-md-6 pr-lg-5">
							<div class="row no-gutters p-3 p-lg-5">
								<div class="col-3 text-right pr-lg-4">
									<img src="images/template/ilustra-1.png" alt="Easy Data" class="img-fluid" />
								</div>
								<div class="col-2">
									<h1 class="display-4 text-center">1</h1>
								</div>
								<div class="col-7">
									<h4 class="mb-4">Where do data come from?</h4>
									<a class="btn btn-outline-primary">Easy Data</a>
								</div>
							</div>
							<div class="row no-gutters p-3 p-lg-5">
								<div class="col-3 text-right pr-lg-4">
									<img src="images/template/ilustra-2.png" alt="Intelli suite" class="img-fluid" />
								</div>
								<div class="col-2">
									<h1 class="display-4 text-center">2</h1>
								</div>
								<div class="col-7">
									<h4 class="mb-4">How do I interact with data?</h4>
									<a class="btn btn-outline-primary">Intelli suite</a>
								</div>
							</div>
						</div>
						<div class="col-md-6 pl-lg-5">
							<div class="row no-gutters p-3 p-lg-5">
								<div class="col-3 text-right pr-lg-4"><img src="images/template/ilustra-3.png" alt="Data doctor" class="img-fluid" /></div>
								<div class="col-2">
									<h1 class="display-4 text-center">3</h1>
								</div>
								<div class="col-7">
									<h4 class="mb-4">How can data help me take decisions?</h4>
									<a class="btn btn-outline-primary">Data doctor</a>
								</div>
							</div>
							<div class="row no-gutters p-3 p-lg-5">
								<div class="col-3 text-right pr-lg-4"><img src="images/template/ilustra-4.png" alt="Brintell Squad" class="img-fluid" /></div>
								<div class="col-2">
									<h1 class="display-4 text-center">4</h1>
								</div>
								<div class="col-7">
									<h4 class="mb-4">What if I need specific services?</h4>
									<a class="btn btn-outline-primary">Brintell Squad</a>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div id="easy-data">
					<div class="row no-gutters m-3 m-lg-5">
						<div class="col-2 text-right pr-4">
							<img src="images/template/ilustra-1.png" alt="Easy Data" class="img-fluid" />
						</div>
						<div class="col-10">
							<h1 class="mb-5">
								Where do data come from?
								<div class="text-md text-muted lh-1-3">
									<span class="text-primary">Easy data</span> helps you gather, enrich and store information
								</div>
							</h1>
							<p class="mb-5">
								As data may come from many different sources, such as one's own business sheets, Google Analytics, government reports and posts from users on social media, it may be hard to have them organized. Brintell's Easy Data tackles this matter.
							</p>
							<div class="row align-items-center pb-4">
								<div class="col-md-6 col-lg-5">
									<h5 class="text-dark">1. Capturing</h5>
									<p>Weather crawling documents to extract data, integrating different digital sources to a single gateway or developing a customized software to capture data, Brintell makes it.</p>
									<p class="text-dark">We are ninjas in capturing data.</p>
								</div>
								<div class="col-lg-2 d-none d-lg-block"></div>
								<div class="col-md-6 col-lg-5">
									<h5 class="text-dark">2. Preparing</h5>
									<p>The more sources are used to collect data, the more data need to be standardized. Brintell automates the way data is treated, so that its standardization goes faster.</p>
									<p class="text-dark">We make data get uniform.</p>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6 col-lg-5">
									<h5 class="text-dark">1. Capturing</h5>
									<p>Weather crawling documents to extract data, integrating different digital sources to a single gateway or developing a customized software to capture data, Brintell makes it.</p>
									<p class="text-dark">We are ninjas in capturing data.</p>
								</div>
								<div class="col-lg-2 d-none d-lg-block"></div>
								<div class="col-md-6 col-lg-5">
									<h5 class="text-dark">2. Preparing</h5>
									<p>The more sources are used to collect data, the more data need to be standardized. Brintell automates the way data is treated, so that its standardization goes faster.</p>
									<p class="text-dark">We make data get uniform.</p>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div id="intelli-suite">
					<div class="row no-gutters m-3 m-lg-5">
						<div class="col-2 text-right pr-4">
							<img src="images/template/ilustra-2.png" alt="Easy Data" class="img-fluid" />
						</div>
						<div class="col-10">
							<h1 class="mb-5">
								How do I interact with data?
								<div class="text-md text-muted lh-1-3">
									<span class="text-primary">Intelli Suite</span> makes a joy telling stories with data
								</div>
							</h1>
							<p class="mb-5">
								Brintell's most emblematic product is Intelli Suite, a group of softwares set to make data beautifully understandable. It's not only about gorgeous charts and maps, but also about custom-made dashboards designed to clarify specific needs.
							</p>
							<div class="row align-items-center pb-4 pos-relative">
								<span class="timeline"><span class="base-icon-circle text-primary"></span></span>
								<div class="col-md-7">
									<img src="images/template/what-we-do/brintel_mockup_dashboard.png" alt="Easy Data" class="img-fluid" />
								</div>
								<div class="col-md-5 text-right pos-relative pr-5">
									<h5 class="text-dark">Intelli Dashboard</h5>
									<p>We personalize dashboard panels, so they can tell live stories with data related to your business.</p>
								</div>
							</div>
							<div class="row align-items-center pb-4 pos-relative">
								<span class="timeline full"><span class="base-icon-circle text-primary"></span></span>
								<div class="col-md-7">
									<img src="images/template/what-we-do/brintell_intelli_print.png" alt="Easy Data" class="img-fluid" />
								</div>
								<div class="col-md-5 text-right pos-relative pr-5">
									<h5 class="text-dark">Intelli Printing</h5>
									<p>Not only one can visualise data on a monitor, but also on printing templates to use as reports.</p>
								</div>
							</div>
							<div class="row align-items-center pb-4 pos-relative">
								<span class="timeline full"><span class="base-icon-circle text-primary"></span></span>
								<div class="col-md-7">
									<img src="images/template/what-we-do/brintell_intelli_maps.png" alt="Easy Data" class="img-fluid" />
								</div>
								<div class="col-md-5 text-right pos-relative pr-5">
									<h5 class="text-dark">Intelli Maps</h5>
									<p>Geoprocessing data makes it possible to visually intersect relevant actions with time and space.</p>
								</div>
							</div>
							<div class="row align-items-center pb-4 pos-relative">
								<span class="timeline last"><span class="base-icon-circle text-primary"></span></span>
								<div class="col-md-7">
									<img src="images/template/what-we-do/brintell_intelli_mobile.png" alt="Easy Data" class="img-fluid" />
								</div>
								<div class="col-md-5 text-right pos-relative pr-5">
									<h5 class="text-dark">Intelli Mobile</h5>
									<p>Large monitors makes room for spaced dashboards, but Intelli Mobile is special, for it follows you anywhere.</p>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div id="data-doctor">
					<div class="row no-gutters m-3 m-lg-5">
						<div class="col-2 text-right pr-4">
							<img src="images/template/ilustra-3.png" alt="Easy Data" class="img-fluid" />
						</div>
						<div class="col-10">
							<h1 class="mb-5">
								How can data help me take decisions?
								<div class="text-md text-muted lh-1-3">
									<span class="text-primary">Data Doctor</span> gives visionary answers to visionary questions
								</div>
							</h1>
							<p>Through the means of technical and scientific methods, associated to a structure capable of processing 10 thousand bibles per second, a group of  highly qualified specialists will obtain the insights you need.</p>
							<p>The Data Doctor team is expert on mining and on correlation algorithms, as well as on descriptive, regressive, predictive and prescriptive analysis. You won't need to have specialists in a myriad of data science fields – leave the technique to us.</p>
						</div>
					</div>
				</div>

				<div id="brintell-squad">
					<div class="row no-gutters m-3 m-lg-5">
						<div class="col-2 text-right pr-4">
							<img src="images/template/ilustra-4.png" alt="Easy Data" class="img-fluid" />
						</div>
						<div class="col-10">
							<h1 class="mb-5">
								What if I need customized services?
								<div class="text-md text-muted lh-1-3">
									<span class="text-primary">Brintell Squad</span> is anxious to solve complex problems
								</div>
							</h1>
							<p>Whatever is your issue related to data and coding, we can handle. Brintell Squad is a team of specialists ready to develop custom-made solutions for business from all over the world.</p>
							<p>Some of the most common requirements are integrating systems, applications for web and mobile, big data analytics and artificial intelligence, spatial intelligence projects and many others. We also fashion incompany courses, in which we convey our expertise.</p>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>

	<div id="about-us">
		<section>
			<svg preserveAspectRatio="none" viewBox="0 0 100 10" xmlns="http://www.w3.org/2000/svg">
				<polygon points="100 0 100 10 0 10" fill="#222"></polygon>
			</svg>
			<div class="container">
				<?php
				// GET IN TOUCH
				require_once('get-in-touch.php');
				?>
			</div>
		</section>
	</div>
</div>
