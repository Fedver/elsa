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
	<body onload="checkIssues();">
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
										<h3>Compile all the form fields to subscribe</h3>
										<p>Once signed up, you will be able to generate your own API Key which is mandatory for service usage.</p>
									</header>
									<form method="post" action="#">
										<div class="row uniform 50%">
											<div class="4u 12u(narrower)">
												<input type="email" name="email" id="email" value="<?php echo $_REQUEST['email']; ?>" placeholder="Email Address" />
											</div>
											<div class="4u 12u(narrower)">
												<input type="password" name="pass1" id="pass1" value="" placeholder="Password" onChange="checkIssues();" />
											</div>
                                            <div class="4u 12u(narrower)">
												<input type="password" name="pass2" id="pass2" value="" placeholder="Confirm Password" onChange="checkIssues();" />
											</div>
										</div>
                                        <div class="row uniform 50%">
											<div class="12u">
												<input type="checkbox" id="human" name="human" onChange="checkIssues();" >
												<label for="human">I am a human and not a robot</label>
											</div>
										</div>
                                        <div class="row uniform 50%">
											<div class="12u">
												<textarea name="message" id="message" placeholder="Disclaimer and legal terms." rows="6" disabled><?php echo $terms; ?></textarea>
											</div>
										</div>
										<div class="row uniform 50%">
											<div class="12u">
												<input type="checkbox" id="accept" name="accept" onChange="checkIssues();" >
												<label for="accept">I accept these conditions</label>
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
													<li><input type="button" value="Sign up" id="submit" name="submit" onclick="register();"/></li>
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