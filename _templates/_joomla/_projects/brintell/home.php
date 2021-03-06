<script type="text/javascript">

jQuery(function() {

	jQuery(window).scroll(function () {
		// dark background
		var fadeBack = ((jQuery('#what-we-do').offset().top - jQuery(window).scrollTop()) + 600) / 400;
		jQuery('#back-background, #data-particles').css('opacity', fadeBack);
		// menu background
		var fadeMenu = ((jQuery('#brintell-slogan').offset().top - (jQuery(window).scrollTop() + 100)) * -1) / 100;
		jQuery('#menu-background, img.logo-def').css('opacity', fadeMenu);
		// clients
		var clientsPos = jQuery('#our-clients').offset().top - jQuery(window).scrollTop();
		if(clientsPos < 100 && clientsPos > -200) {
			jQuery('#our-clients').addClass('showLogos');
			setTips();
		} else {
			jQuery('#our-clients').removeClass('showLogos');
		}
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
				<span>The Data Revolution has begun</span>
				<div class="pt-3 text-md lh-1-3">Today 98% of all stored information is already digital. Data is everything and everything is data. Bearing this in mind, is your company making data-based decisions?</div>
			</h1>
		</div>
	</div>

	<div id="content-container" class="container">
		<div class="row">
			<div class="col-12">
				<div class="next-section text-center"><a class="text-xl text-primary-lighter base-icon-down go-to" href="#what-we-do"></a></div>
				<div id="what-we-do" class="bg-white p-3 p-lg-5 mb-5 set-shadow-lg">
					<h1 class="display-3 font-serif text-center pt-5">We help solve data problems</h1>
					<div class="row no-gutters justify-content-between py-5">
						<div class="col-md-6 pr-lg-5">
							<div class="row no-gutters m-3 m-lg-5" style="min-height: 185px;">
								<div class="col-3">
									<img src="images/template/ilustra-1.png" alt="Easy Data" class="img-fluid" />
								</div>
								<div class="col-2">
									<h1 class="display-4 text-center">1</h1>
								</div>
								<div class="col-7">
									<h4 class="mb-4">Where do data come from?</h4> Data come from files, public sources, social media etc – but they are usually messy. Data need to be properly captured, classified and stored.
								</div>
							</div>
							<!--
								<div class="row no-gutters pb-5">
									<div class="col-7 ml-auto">
										<a href="what-we-do/#easy-data" class="btn btn-outline-primary go-to">Easy Data</a>
									</div>
								</div>
							-->
							<div class="row no-gutters m-3 m-lg-5" style="min-height: 185px;">
								<div class="col-3">
									<img src="images/template/ilustra-2.png" alt="Intelli suite" class="img-fluid" />
								</div>
								<div class="col-2">
									<h1 class="display-4 text-center">2</h1>
								</div>
								<div class="col-7">
									<h4 class="mb-4">How do I interact with data?</h4> Most companies use disconnected tools for chart visualization. But there are beautiful, easy-to-use and customizable softwares out there.
								</div>
							</div>
							<!--
								<div class="row no-gutters pb-5">
									<div class="col-7 ml-auto">
										<a href="what-we-do/#intelli-suite" class="btn btn-outline-primary go-to">Intelli suite</a>
									</div>
								</div>
							-->
						</div>
						<div class="col-md-6 pl-lg-5">
							<div class="row no-gutters m-3 m-lg-5" style="min-height: 185px;">
								<div class="col-3"><img src="images/template/ilustra-4.png" alt="Data doctor" class="img-fluid" /></div>
								<div class="col-2">
									<h1 class="display-4 text-center">3</h1>
								</div>
								<div class="col-7">
									<h4 class="mb-4">How can data help me take decisions?</h4> Data is the new oil. It's valuable, but if unrefined it cannot be used. Predictive analysis and artificial intelligence are good ways to go.
								</div>
							</div>
							<!--
								<div class="row no-gutters pb-5">
									<div class="col-7 ml-auto">
										<a href="what-we-do/#data-doctor" class="btn btn-outline-primary go-to">Data doctor</a>
									</div>
								</div>
							-->
							<div class="row no-gutters m-3 m-lg-5" style="min-height: 185px;">
								<div class="col-3"><img src="images/template/ilustra-3.png" alt="Brintell Squad" class="img-fluid" /></div>
								<div class="col-2">
									<h1 class="display-4 text-center">4</h1>
								</div>
								<div class="col-7">
									<h4 class="mb-4">What if I need specific services?</h4> Every company has different problems and its own needs. If these needs are related to data, Brintell can handle them.
								</div>
							</div>
							<!--
								<div class="row no-gutters pb-5">
									<div class="col-7 ml-auto">
										<a href="what-we-do/#brintell-squad" class="btn btn-outline-primary go-to">Brintell Squad</a>
									</div>
								</div>
							-->
						</div>
					</div>
				</div>

				<!--
					<div id="our-clients" class="py-5">
						<div class="text-right mb-4">
							<h1 class="display-3 font-serif pt-5">Our Clients</h1>
							<p>Brintell has been supporting companies of all shapes and sizes.<br />Here are selected companies from our personal pool of experience.</p>
						</div>
						<div class="logos">
							<div id="client-lausd">
								<a href="#" class="d-block hasPopover" title="Prince George County Public Schools (PGCPS)" data-placement="top" data-content="Los Angeles Unified District (LAUSD) Brintell integrated ESRI and Oracle technology to generate a single panel that raised managers and tech staff interest in dashboards to 100%"><img src="images/template/clients/LAUSD.png" /></a>
								<div></div>
							</div>
							<div id="client-brb">
								<a href="#" class="d-block hasPopover" title="BRB" data-placement="top" data-content="Brintell has been supporting companies of all shapes and sizes"><img src="images/template/clients/BRB.png" /></a>
								<div></div>
							</div>
							<div id="client-fed">
								<a href="#" class="d-block hasPopover" title="Federal Reserve" data-placement="top" data-content="Brintell has been supporting companies of all shapes and sizes"><img src="images/template/clients/US-Federal-reserve.png" /></a>
								<div></div>
							</div>
							<div id="client-gl">
								<a href="#" class="d-block hasPopover" title="GL Engenharia" data-placement="top" data-content="Brintell has been supporting companies of all shapes and sizes"><img src="images/template/clients/GL-Engenharia.png" /></a>
								<div></div>
							</div>
							<div id="client-mastec">
								<a href="#" class="d-block hasPopover" title="Mastec" data-placement="top" data-content="Brintell has been supporting companies of all shapes and sizes"><img src="images/template/clients/mastec.png" /></a>
								<div></div>
							</div>
							<div id="client-pgcps">
								<a href="#" class="d-block hasPopover" title="PGCPS" data-placement="top" data-content="Brintell has been supporting companies of all shapes and sizes"><img src="images/template/clients/pgcps.png" /></a>
								<div></div>
							</div>
						</div>
					</div>
				-->

				<div id="methodology" class="py-5">
					<h1 class="display-3 font-serif pt-5 mb-4">Methodology</h1>
					<p>
						In the past couple of years, Brintell has been developing solutions for different countries, with defying cultures and conflicting time zones, in a process of sheer experimentation.
						<br />Amidst errors and hits, we have devised a method we call Intelli Cycles, which aims to deliver what have been requested with the minimum amount of gaps and rework. The following steps explain how it works.
					</p>
					<div class="row">
						<div class="col-sm-4 col-md-3">
							<div id="method-1" class="method-card load-onView reveal-to-top">
								<h2 class="m-0 lh-1">1</h2>
								<center>
									<img src="images/template/methodology/ilustra-anotacoes.png" class="img-fluid" />
									<hr class="m-3" />
								</center>
								<div class="text-sm text-center">Our analysts understand the client's major pains</div>
							</div>
						</div>
						<div class="col-sm-4 col-md-3">
							<div id="method-2" class="method-card load-onView reveal-to-top">
								<h2 class="m-0 lh-1">2</h2>
								<center>
									<img src="images/template/methodology/ilustra-prototipo.png" class="img-fluid" />
									<hr class="m-3" />
								</center>
								<div class="text-sm text-center">A design team build high resolution prototypes to solve the matter</div>
							</div>
						</div>
						<div class="col-sm-4 col-md-3">
							<div id="method-3" class="method-card load-onView reveal-to-top">
								<h2 class="m-0 lh-1">3</h2>
								<center>
									<img src="images/template/methodology/ilustra-checklist.png" class="img-fluid" />
									<hr class="m-3" />
								</center>
								<div class="text-sm text-center">System architects validate the expected functionalities</div>
							</div>
						</div>
						<div class="col-sm-4 col-md-3">
							<div id="method-1" class="method-card load-onView reveal-to-top">
								<h2 class="m-0 lh-1">4</h2>
								<center>
									<img src="images/template/methodology/ilustra-calendario.png" class="img-fluid" />
									<hr class="m-3" />
								</center>
								<div class="text-sm text-center">Development sprints are drafted and a deadline is estimated</div>
							</div>
						</div>
						<div class="col-sm-4 col-md-3">
							<div id="method-1" class="method-card load-onView reveal-to-top">
								<h2 class="m-0 lh-1">5</h2>
								<center>
									<img src="images/template/methodology/ilustra-codigo.png" class="img-fluid" />
									<hr class="m-3" />
								</center>
								<div class="text-sm text-center">The prototypes are programmed with agile methodologies</div>
							</div>
						</div>
						<div class="col-sm-4 col-md-3">
							<div id="method-1" class="method-card load-onView reveal-to-top">
								<h2 class="m-0 lh-1">6</h2>
								<center>
									<img src="images/template/methodology/ilustra-lupa.png" class="img-fluid" />
									<hr class="m-3" />
								</center>
								<div class="text-sm text-center">A multidisciplinary team revise every aspect of the components</div>
							</div>
						</div>
						<div class="col-sm-4 col-md-3">
							<div id="method-1" class="method-card load-onView reveal-to-top">
								<h2 class="m-0 lh-1">7</h2>
								<center>
									<img src="images/template/methodology/ilustra-computador.png" class="img-fluid" />
									<hr class="m-3" />
								</center>
								<div class="text-sm text-center">The product is uploaded to work on the internet</div>
							</div>
						</div>
						<div class="col-sm-4 col-md-3">
							<div id="method-1" class="method-card load-onView reveal-to-top">
								<h2 class="m-0 lh-1">8</h2>
								<center>
									<img src="images/template/methodology/ilustra-suporte.png" class="img-fluid" />
									<hr class="m-3" />
								</center>
								<div class="text-sm text-center">We offers professional support</div>
							</div>
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
				<div class="row mb-4">
					<div class="col-md-6">
						<div class="about-graphic">
							<div class="what hasPopover" data-placement="top" data-content=""><span>What</span></div>
							<div class="how hasPopover" data-placement="top" data-content=""><span>How</span></div>
							<div class="why hasPopover" data-placement="top" data-content="Foster innovation in business in the world"><span>Why</span></div>
						</div>
					</div>
					<div class="col-md-6 text-justify">
						<h1 class="display-3 font-serif pt-5 mb-4">About us</h1>
						<p>Founded on February 2016, Brintell maintains operations in Brazil and in the US, having its headquarter in Dover, DE. The company has started after solving a big data and visualization challenge for an American client. Such was the success, Brintell's demand has only increased since then, as well as its services have been constantly improved.</p>
						<p>The company now counts on a multidisciplinary team, composed by engineers, economists, mathematicians, statisticians, developers and designers, who shares the drive to solve ever-growing challenges for our clients.</p>
					</div>
				</div>

				<?php
				// GET IN TOUCH
				require_once('get-in-touch.php');
				?>

			</div>
		</section>
	</div>
</div>
