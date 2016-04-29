<?php

    require "elsa.config.php";

?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>ELSA</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<?php echo $src_files; ?>
	</head>
	<body class="landing">
		<div id="page-wrapper">

			<!-- Header -->
                <?php echo $header_index.$header_general; ?>
			
			<!-- Spinner -->
				<div id="spinner_target"></div>

			<!-- Banner -->
				<section id="banner">
					<h2>ELSA</h2>
					<p>Embeddable cross-Lingual Semantic Annotator</p>
					<ul class="actions">
						<li><a href="signup.php" class="button special">Sign Up</a></li>
						<li><a href="#main" class="button">Learn More</a></li>
					</ul>
				</section>

			<!-- Main -->
				<section id="main" class="container">

					<section class="box special">
						<header class="major">
							<h2>All the information on the Web in your language,
							<br />
							available for everyone.</h2>
							<p>ELSA is a cross-lingual table annotation service: it lets you annotate table headers and RDB field names. The service is provided via Application Programming Interface,
                                through an HTTP interface returning a JSON array. </p>
						</header>
					</section>

					<section class="box special features">
						<div class="features-row">
							<section>
								<span class="icon major fa-bolt accent2"></span>
								<h3>Magna etiam</h3>
								<p>Integer volutpat ante et accumsan commophasellus sed aliquam feugiat lorem aliquet ut enim rutrum phasellus iaculis accumsan dolore magna aliquam veroeros.</p>
							</section>
							<section>
								<span class="icon major fa-area-chart accent3"></span>
								<h3>Ipsum dolor</h3>
								<p>Integer volutpat ante et accumsan commophasellus sed aliquam feugiat lorem aliquet ut enim rutrum phasellus iaculis accumsan dolore magna aliquam veroeros.</p>
							</section>
						</div>
						<div class="features-row">
							<section>
								<span class="icon major fa-cloud accent4"></span>
								<h3>Sed feugiat</h3>
								<p>Integer volutpat ante et accumsan commophasellus sed aliquam feugiat lorem aliquet ut enim rutrum phasellus iaculis accumsan dolore magna aliquam veroeros.</p>
							</section>
							<section>
								<span class="icon major fa-lock accent5"></span>
								<h3>Enim phasellus</h3>
								<p>Integer volutpat ante et accumsan commophasellus sed aliquam feugiat lorem aliquet ut enim rutrum phasellus iaculis accumsan dolore magna aliquam veroeros.</p>
							</section>
						</div>
					</section>

				</section>

			<!-- CTA -->
				<section id="cta">

					<h2>Sign up and get your API Key</h2>
					<p>Blandit varius ut praesent nascetur eu penatibus nisi risus faucibus nunc.</p>

					<form action="signup.php" method="post" name="frm_email" id="frm_email">
						<div class="row uniform 50%">
							<div class="8u 12u(mobilep)">
								<input type="email" name="email" id="email" placeholder="Email Address" />
							</div>
							<div class="4u 12u(mobilep)">
								<input type="submit" value="Sign Up" class="fit" />
							</div>
						</div>
					</form>

				</section>

			<!-- Footer -->
				<?php echo $footer; ?>

		</div>

	</body>
</html>