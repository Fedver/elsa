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
	<body onload="checkLoginIssues();">
		<div id="page-wrapper">

			<!-- Header -->
				<?php echo $header_page.$header_general; ?>

			<!-- Spinner -->
				<div id="spinner_target"></div>

			<!-- Main -->
				<section id="main" class="container">
					<header>
						<h2>ELSA</h2>
						<p>Enhanced cross-Lingual Semantic Annotator</p>
					</header>
					<div class="row">
						<div class="12u">

							<!-- Form -->
								<section class="box">
                                    <header>
										<p>Login with your personal account to generate an API Key. You need to be signed up first.</p>
									</header>
									<form method="post" action="#">
										<div class="row uniform 50%">
											<div class="6u 12u(narrower)">
												<input type="email" name="email" id="email" value="" placeholder="Email Address" onchange="checkLoginIssues();" />
											</div>
											<div class="6u 12u(narrower)">
												<input type="password" name="pass1" id="pass1" value="" placeholder="Password" onchange="checkLoginIssues();" />
											</div>
										</div>
										<div class="row uniform 50%">
											<div class="12u">
												<span id="error" class="icon fa-check">There are no issues so far.</span>
											</div>
										</div>
										<div class="row uniform">
											<div class="12u">
												<ul class="actions">
													<li><input type="button" value="Login" id="submit" name="submit" onclick="login();"/></li>
													<li><input type="reset" value="Reset" class="alt" /></li>
												</ul>
											</div>
										</div>
									</form>
								</section>

						</div>
					</div>
					
				</section>

			<!-- Footer -->
				<?php echo $footer; ?>

		</div>

	</body>
</html>