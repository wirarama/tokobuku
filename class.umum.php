<?
class umum{
	var $judul_web='Toko Buku Online';
	var $judul='Toko Buku Online';
	var $subjudul='Toko buku di mana saja';
	var $kredit='Dibuat pada tahun 2012';
	var $host='http://localhost/tokobuku/';
	var $path='/opt/lampp/htdocs/tokobuku/';
	function webtitle(){
		if(empty($_GET['p'])){
			$title = $this->judul_web;
		}else{
			$title = $_GET['p'].' '.$this->judul_web;
		}
		return $title;
	}
	function prosesquery(){
		if(!empty($_POST['addpesan'])){
			mysql_query("INSERT INTO message VALUES(null,'".$_POST['name']."','".$_POST['email']."','".$_POST['message']."',null)") or die(mysql_error());
			header('location:'.$this->host.'kontak');
		}elseif(!empty($_POST['addpage'])){
			mysql_query("INSERT INTO page VALUES(null,'".$_POST['title']."','".$_POST['initial']."','".$_POST['description']."',null)") or die(mysql_error());
			header('location:'.$this->host.'page/list');
		}elseif(!empty($_POST['editpage'])){
			mysql_query("UPDATE page SET title='".$_POST['title']."',initial='".$_POST['initial']."',description='".$_POST['description']."' WHERE id='".$_POST['edit']."'") or die(mysql_error());
			header('location:'.$this->host.'page/list');
		}elseif(!empty($_GET['pagedelete'])){
			mysql_query("DELETE FROM page WHERE id='".$_GET['pagedelete']."'") or die(mysql_error());
			header('location:'.$this->host.'page/list');
		}elseif(!empty($_POST['addproduk'])){
			if(!empty($_FILES['picture']['name'])){
				$targetFile = $this->path.'gambar/img/'.$_FILES['picture']['name'];
				$targetFileThumb = $this->path.'gambar/thumb/'.$_FILES['picture']['name'];
				include('picture.php');
				move_uploaded_file($_FILES['picture']['tmp_name'],$targetFile) or die('gagal upload gambar gan T.T');
				$d = image_validation($targetFile);
				thumbnails($targetFile,$targetFileThumb,$d,'100','40','1');
			}
			mysql_query("INSERT INTO product VALUES(null,'".$_POST['name']."','".$_POST['description']."','".$_POST['price']."','".$_FILES['picture']['name']."',null)") or die(mysql_error());
			header('location:'.$this->host.'produk/list');
		}elseif(!empty($_POST['editproduk'])){
			if(!empty($_FILES['picture']['name'])){
				$picturename = mysql_fetch_row(mysql_query("SELECT picture FROM product WHERE id='".$_POST['edit']."'"));
				if(!empty($picturename[0])){
					unlink($this->path.'gambar/img/'.$picturename[0]);
					unlink($this->path.'gambar/thumb/'.$picturename[0]);
				}
				$targetFile = $this->path.'gambar/img/'.$_FILES['picture']['name'];
				$targetFileThumb = $this->path.'gambar/thumb/'.$_FILES['picture']['name'];
				include('picture.php');
				move_uploaded_file($_FILES['picture']['tmp_name'],$targetFile) or die('gagal upload gambar gan T.T');
				$d = image_validation($targetFile);
				thumbnails($targetFile,$targetFileThumb,$d,'100','40','1');
				mysql_query("UPDATE product SET picture='".$_FILES['picture']['name']."' WHERE id='".$_POST['edit']."'") or die(mysql_error());
			}
			mysql_query("UPDATE product SET name='".$_POST['name']."',description='".$_POST['description']."',price='".$_POST['price']."' WHERE id='".$_POST['edit']."'") or die(mysql_error());
			header('location:'.$this->host.'produk/list');
		}elseif(!empty($_GET['produkdelete'])){
			$picturename = mysql_fetch_row(mysql_query("SELECT picture FROM product WHERE id='".$_GET['produkdelete']."'"));
			if(!empty($picturename[0])){
				unlink($this->path.'gambar/img/'.$picturename[0]);
				unlink($this->path.'gambar/thumb/'.$picturename[0]);
			}
			mysql_query("DELETE FROM product WHERE id='".$_GET['produkdelete']."'") or die(mysql_error());
			header('location:'.$this->host.'produk/list');
		}elseif(!empty($_POST['login'])){
			$login = mysql_fetch_row(mysql_query("SELECT id FROM admin WHERE username='".$_POST['username']."' AND password=MD5('".$_POST['password']."')"));
			if(!empty($login[0])){
				setcookie('login',$login[0]);
				header('location:'.$this->host);
			}
		}elseif($_GET['p']=='logout'){
			setcookie('login','');
			header('location:'.$this->host);
		}
	}
	function navigasi($label,$alamat){
		?>
		<a href="<? echo $this->host.$alamat; ?>"><li><? echo $label; ?></li></a>
		<?
	}
	function konten(){
		switch($_GET['p']){
			case('kontak'):
				$this->formkontak();
				break;
			case('page_list'):
				$this->daftarpage();
				break;
			case('page_add'):
			case('page_edit'):
				$this->formpage();
				break;
			case('produk_list'):
				$this->daftarproduk();
				break;
			case('produk_add'):
			case('produk_edit'):
				$this->formproduk();
				break;
			case('produk'):
			case('produk_detail'):
				$this->tampilproduk();
				break;
			case('login'):
				$this->formlogin();
				break;
		}
		$formpage = array('profil');
		if(in_array($_GET['p'],$formpage) || empty($_GET['p'])){
			if(empty($_GET['p'])) $_GET['p']='home';
			$hasil = mysql_fetch_row(mysql_query("SELECT title,description FROM page WHERE initial='".$_GET['p']."'"));
			?>
			<h3><? echo $hasil[0]; ?></h3>
			<div><p><? echo nl2br($hasil[1]); ?></p></div>
			<?
		}
	}
	function formtext($label,$nama,$panjang,$maximal,$value=null,$password=false){
		if($password==true){ $type = 'password';
		}else{ $type = 'text'; }
		?>
			<div id="label"><? echo $label; ?></div>
			<div id="isiform"><input type="<? echo $type; ?>" name="<? echo $nama; ?>" id="<? echo $nama; ?>" size="<? echo $panjang; ?>" maxlength="<? echo $maximal; ?>" value="<? echo $value; ?>"></div>
		<?
	}
	function formtextarea($label,$nama,$panjang,$tinggi,$value=null){
		?>
			<div id="label"><? echo $label; ?></div>
			<div id="isiform"><textarea name="<? echo $nama; ?>" id="<? echo $nama; ?>" cols="<? echo $panjang; ?>" rows="<? echo $tinggi; ?>"><? echo $value; ?></textarea></div>
		<?
	}
	function formhidden($nama,$value){
		?>
			<input type="hidden" name="<? echo $nama; ?>" value="<? echo $value; ?>">
		<?
	}
	function formsubmit($nama,$value){
		?>
			<div><input type="submit" name="<? echo $nama; ?>" id="<? echo $nama; ?>" value="<? echo $value; ?>"></div>
		<?
	}
	function accounting_format($number){
		$out = number_format($number,2,',','.');
		return $out;
	}
	function formkontak(){
		?>
		<h3>Pesan</h3>
		<? $this->daftarkontak(); ?>
		<form action="<? echo $this->host; ?>/kontak" method="POST">
			<?
			$this->formtext('Nama','name','50','255');
			$this->formtext('Email','email','40','50');
			$this->formtextarea('Pesan','message','60','6');
			?>
			<div><input type="submit" name="addpesan" id="addpesan" value="kirim"></div>
		</form>
		<?
	}
	function daftarkontak(){
		$q = mysql_query("SELECT name,email,date,message FROM message ORDER BY id DESC");
		while($d = mysql_fetch_row($q)){
		?>
		<div id="kontak">
			<div id="nama"><a href="mailto:<? echo $d[1]; ?>"><? echo $d[0]; ?></a></div>
			<div id="tanggal"><? echo $d[2]; ?></div>
			<div id="pesan"><? echo $d[3]; ?></div>
		</div>
		<?
		}
	}
	function formpage(){
		if(!empty($_GET['edit'])){
			$d = mysql_fetch_row(mysql_query("SELECT title,initial,description FROM page WHERE id='".$_GET['edit']."'"));
			$submit = 'editpage';
			$value = 'edit';
		}else{
			$submit = 'addpage';
			$value = 'tambah';
		}
		?>
		<h3><? echo $value; ?> Halaman</h3>
		<form action="" method="POST">
			<?
			$this->formtext('Judul','title','50','255',$d[0]);
			$this->formtext('Panggilan','initial','20','20',$d[1]);
			$this->formtextarea('Isi','description','60','15',$d[2]);
			if(!empty($_GET['edit'])) $this->formhidden('edit',$_GET['edit']);
			$this->formsubmit($submit,$value);
			?>
		</form>
		<?
	}
	function daftarpage(){
		?>
		<div><a href="<? echo $this->host; ?>page/add">Tambah Halaman Baru</a></div>
		<table width="100%">
			<tr>
				<th>Judul</th>
				<th>Panggilan</th>
				<th colspan="2"></th>
			</tr>
		<?
		$q = mysql_query("SELECT title,initial,id FROM page ORDER BY id DESC") or die(mysql_error());
		while($d = mysql_fetch_row($q)){
		?>
			<tr>
				<td width="60%"><? echo $d[0]; ?></td>
				<td width="40%"><? echo $d[1]; ?></td>
				<td><a href="<? echo $this->host; ?>page/edit/<? echo $d[2]; ?>">Edit</a></td>
				<td><a href="<? echo $this->host; ?>page/delete/<? echo $d[2]; ?>">Hapus</a></td>
			</tr>
		<?
		}
		?>
		</table>
		<?
	}
	function formproduk(){
		if(!empty($_GET['edit'])){
			$d = mysql_fetch_row(mysql_query("SELECT name,description,price FROM product WHERE id='".$_GET['edit']."'"));
			$submit = 'editproduk';
			$value = 'edit';
		}else{
			$submit = 'addproduk';
			$value = 'tambah';
		}
		?>
		<h3>Produk</h3>
		<form action="" method="POST" enctype="multipart/form-data">
			<?
			$this->formtext('Nama Produk','name','50','255',$d[0]);
			$this->formtextarea('Diskripsi','description','60','6',$d[1]);
			$this->formtext('Harga','price','20','20',$d[2]);
			?>
			<div><input type="file" name="picture" id="picture"></div>
			<?
			if(!empty($_GET['edit'])) $this->formhidden('edit',$_GET['edit']);
			$this->formsubmit($submit,$value);
			?>
		</form>
		<?
	}
	function daftarproduk(){
		?>
		<div><a href="<? echo $this->host; ?>produk/add">Tambah Produk Baru</a></div>
		<table width="100%">
			<tr>
				<th>Nama</th>
				<th>Gambar</th>
				<th>Harga</th>
				<th colspan="2"></th>
			</tr>
		<?
		$q = mysql_query("SELECT name,picture,price,id FROM product ORDER BY id DESC") or die(mysql_error());
		while($d = mysql_fetch_row($q)){
		?>
			<tr>
				<td><? echo $d[0]; ?></td>
				<td><img src="<? echo $this->host.'gambar/thumb/'.$d[1]; ?>"></td>
				<td>Rp. <? echo $this->accounting_format($d[2]); ?></td>
				<td><a href="<? echo $this->host; ?>produk/edit/<? echo $d[3]; ?>">Edit</a></td>
				<td><a href="<? echo $this->host; ?>produk/delete/<? echo $d[3]; ?>">Hapus</a></td>
			</tr>
		<?
		}
		?>
		</table>
		<?
	}
	function tampilproduk(){
		?>
		<div>
		<?
		$i=1;
		if(!empty($_GET['detail'])) $where = "WHERE id='".$_GET['detail']."'";
		$q = mysql_query("SELECT name,picture,price,id,description FROM product ".$where." ORDER BY id DESC") or die(mysql_error());
		while($d = mysql_fetch_row($q)){
			if(empty($_GET['detail'])){
				?>
						<a href="<? echo $this->host.'produk/detail/'.$d[3]; ?>">
							<div id="produk">
							<div><img src="<? echo $this->host.'gambar/thumb/'.$d[1]; ?>"></div>
							<div id="nama"><? echo $d[0]; ?></div>
							<div id="harga">Rp. <? echo $this->accounting_format($d[2]); ?></div>
							</div>
						</a>
				<?
					if($i==3){
						?><div style="clear:both;"></div>	<?
						$i=1;
					}else{
						$i++;
					}
			}else{
				?>
				<div id="produkdetail">
					<div><a href="<? echo $this->host.'gambar/img/'.$d[1]; ?>" target="_blank"><img src="<? echo $this->host.'gambar/img/'.$d[1]; ?>" width="500px"></a></div>
					<div id="nama"><? echo $d[0]; ?></div>
					<div id="harga">Rp. <? echo $this->accounting_format($d[2]); ?></div>
					<div id="keterangan"><? echo $d[4]; ?></div>
				</div>
				<?
			}
		}
		?>
		<div style="clear:both;"></div>
		</div>
		<?
	}
	function formlogin(){
		?>
		<h3>Login</h3>
		<p>Login dengan menggunakan user : admin, password :admin</p>
		<form action="" method="POST">
			<?
			$this->formtext('Nama Pengguna','username','40','40');
			$this->formtext('Kata Sandi','password','40','40',null,true);
			$this->formsubmit('login','Login');
			?>
		</form>
		<?
	}

}
?>
