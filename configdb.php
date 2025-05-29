<?php
	@session_start();
	$_SESSION['judul'] = 'SPK';
	$_SESSION['welcome'] = 'SISTEM PENDUKUNG KEPUTUSAN BERBASIS WEB DENGAN METODE WEIGHT PRODUCT';
	$_SESSION['by'] = '© Kelompok 9';
	$mysqli = new mysqli('localhost','root','','scpk');
	if($mysqli->connect_errno){
		echo $mysqli->connect_errno." - ".$mysqli->connect_error;
		exit();
	}
?>
