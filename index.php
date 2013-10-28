<?
	require_once 'class.umum.php';
	require_once 'connect.php';
	$umum = new umum();
	$umum->prosesquery();
?>
<html>
	<head>
		<title><? echo $umum->webtitle(); ?></title>
		<link type="text/css" href="<? echo $umum->host; ?>css/style.css" rel="stylesheet" />
	</head>
	<body>
		<div align="center">
			<div id="utama">
				<div id="kepala">
					<h1><? echo $umum->judul; ?></h1>
					<h2><? echo $umum->subjudul; ?></h2>
					<ul id="nav">
						<?
						$umum->navigasi('Home','');
						$umum->navigasi('Produk','produk');
						$umum->navigasi('Profil','profil');
						$umum->navigasi('Kontak','kontak');
						if(!empty($_COOKIE['login'])){
							$umum->navigasi('Halaman','page/list');
							$umum->navigasi('Produk','produk/list');
							$umum->navigasi('Logout','logout');
						}else{
							$umum->navigasi('Login','login');
						}
						?>
					</ul>
				</div>
				<div id="konten">
					<? $umum->konten(); ?>
				</div>
				<div id="kredit">
					<h5><? echo $umum->kredit; ?></h5>
				</div>
			</div>
		</div>
	</body>
</html>
