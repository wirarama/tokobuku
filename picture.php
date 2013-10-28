<?
//----------------------------------------------------------------
//		thumbsnails
//----------------------------------------------------------------
function thumbnails($img_src,$img_th,$thumb_on,$thumb_size,$quality,$crop=null){
	$img_size = GetImageSize($img_src);
	$img_in = ImageCreateFromJPEG($img_src);
	//resize
	if($thumb_on == 'y'){
		$img_x = ($thumb_size/$img_size[1])*$img_size[0];
		$img_y = $thumb_size;
	}else{
		$img_y = ($thumb_size/$img_size[0])*$img_size[1];
		$img_x = $thumb_size;
	}
	$img_out = ImageCreateTrueColor($img_x, $img_y);
	ImageCopyResampled ($img_out, $img_in, 0, 0, 0, 0, $img_x, $img_y, $img_size[0], $img_size[1]);
	ImageJPEG($img_out,$img_th,$quality);
	ImageDestroy($img_out);
	ImageDestroy($img_in);
	if(!empty($crop)){
		crop($thumb_size,$thumb_size,$img_th);
	}
}
//----------------------------------------------------------------------------------------------
//		crop
//----------------------------------------------------------------------------------------------
function crop($h,$w,$filename){
	$gambar = imagecreatefromjpeg($filename); 
	$crop = imagecreatetruecolor($w,$h);
	imagecopy($crop,$gambar,0,0,0,0,$w,$h);
	ImageJPEG($crop,$filename,70);
}
//----------------------------------------------------------------
//		image validation
//----------------------------------------------------------------
function image_validation($img){
	list($w,$h) = getimagesize($img);
	if($w > $h){
		$d = 'y';
	}else{
		$d = 'x';
	}
	return $d;
}
?>
