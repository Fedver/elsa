<!DOCTYPE HTML>
<html>
	<head>
		<title>ELSA</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" href="css/main.css" />
		<!--[if lte IE 8]><link rel="stylesheet" href="css/ie8.css" /><![endif]-->

        <!-- Scripts -->
			<script src="js/jquery.min.js"></script>
			<script src="js/jquery.dropotron.min.js"></script>
			<script src="js/jquery.scrollgress.min.js"></script>
			<script src="js/skel.min.js"></script>
			<script src="js/util.js"></script>
			<!--[if lte IE 8]><script src="js/ie/respond.min.js"></script><![endif]-->
            <!--[if lte IE 8]><script src="js/ie/html5shiv.js"></script><![endif]-->
			<script src="js/main.js"></script>

	</head>
	<body onload="checkIssues();">
		<div id="page-wrapper">

			<!-- Header -->
				<header id="header">
					<h1><a href="index.html">ELSA</a></h1>
					<nav id="nav">
						<ul>
							<li><a href="index.html">Home</a></li>
							<li>
								<a href="#" class="icon fa-angle-down">API</a>
								<ul>
									<li><a href="generic.html">Get API Key</a></li>
									<li><a href="contact.html">Documentation</a></li>
								</ul>
							</li>
							<li><a href="signup.php" class="button">Sign Up</a></li>
						</ul>
					</nav>
				</header>

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
													<li><input type="button" value="Sign up" /></li>
													<li><input type="reset" value="Reset" class="alt" onclick="checkIssues();"/></li>
												</ul>
											</div>
										</div>
									</form>
								</section>

						</div>
					</div>
					
				</section>

			<!-- Footer -->
				<footer id="footer">
					<ul class="icons">
						<li><a href="#" class="icon fa-github"><span class="label">Github</span></a></li>
					</ul>
					<ul class="copyright">
						<li>&copy; 2016. </li><li>Design: Federico Orlandi</li>
					</ul>
				</footer>

		</div>

	</body>
</html>