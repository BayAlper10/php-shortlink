<?php
if(isset($_POST["redirect"])){
    header("Location: " . $_POST["redirect"]);

    return;
}
include "config.php";
$mysql = new mysqli($db_host, $db_user, $db_pass, $db_schema, $db_port);
if($mysql->connect_error){
    header("Location: ./");

    return;
}

$parts = explode("/", $_SERVER["REQUEST_URI"]);
if(empty($parts)){
    header("Location: ./");

    return;
}
$code = array_pop($parts);

$stmt = $mysql->prepare("SELECT url, clicks FROM links WHERE code=?");
$stmt->bind_param("s", $code);
$stmt->execute();
if(($result = $stmt->get_result()) === null){
    header("Location: ./");

    return;
}
$data = $result->fetch_assoc();
if($data === null){
    header("Location: ./");

    return;
}
$stmt->free_result();

$newClicks = $data["clicks"] + 1;
$stmt = $mysql->prepare("UPDATE links SET clicks=? WHERE code=?");
$stmt->bind_param("is", $newClicks, $code);
$stmt->execute();

$url = $data["url"];
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
		<meta name="description" content="Long links made shorter!">

		<title>Short Link</title>

		<link rel="stylesheet"
			  href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
		<link rel="stylesheet" href="assets/css/styles.min.css">

		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
	</head>
	<body>
		<div>
			<nav class="navbar navbar-light navbar-expand-md bg-primary navigation-clean">
				<div class="container">
					<a class="navbar-brand text-nowrap text-dark" href="#">Short Link</a>
				</div>
			</nav>
		</div>
		<div class="card"></div>
		<div class="newsletter-subscribe">
			<div class="container">
				<div class="intro">
					<h2 class="text-center">Link kısaltma!</h2>
					<p class="text-center">Yönlendirildiğin site <strong><?php echo $url ?></strong>.<br>
						Tıkla ve bağlantıya ilerle.</p>
				</div>
				<form class="form-inline" method="post" action="redirect.php">
					<input name="redirect" type="hidden" value="<?php echo $url ?>">
					<div class="form-group">
						<button class="btn btn-primary" type="submit">Devam</button>
					</div>
				</form>
			</div>
		</div>
		<div class="footer-basic">
			<footer>
				<div class="social">
          <a href="https://github.com/BayAlper10"><i class="icon ion-social-github"></i></a>
					<a href="https://youtube.com/BayAlper10"><i class="icon ion-social-youtube"></i></a>
				</div>
				<p class="copyright">Alper Dursun © 2019</p>
			</footer>
		</div>
	</body>
</html>
