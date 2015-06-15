<?php
	require_once('../../../../../config/config.php');
	require_once('../../../../../config/terbilang.php');
	require_once('ppjb_proses.php');

	$query = "
	SELECT *
	FROM
	CS_PARAMETER_PPJB";
	$obj = $conn->execute($query);
	
	//DATA PEMBELI
	$NAMA_PT 			= $obj->fields['NAMA_PT'];
	$NAMA_DEP 			= $obj->fields['NAMA_DEP'];
	$NAMA_PEJABAT 		= $obj->fields['NAMA_PEJABAT'];
	$NAMA_JABATAN 		= $obj->fields['NAMA_JABATAN'];
	$PEJABAT_PPJB 		= $obj->fields['PEJABAT_PPJB'];
	$JABATAN_PPJB 		= $obj->fields['JABATAN_PPJB'];
	$NOMOR_SK 			= $obj->fields['NOMOR_SK'];
	$TANGGAL_SK 		= tgltgl(date("d-m-Y", strtotime($obj->fields['TANGGAL_SK'])));
	

	
	//Format Tanggal Berbahasa Indonesia 

	// Array Hari
	$array_hari = array(1=>'Senin','Selasa','Rabu','Kamis','Jumat', 'Sabtu','Minggu');
	$hari = $array_hari[date('N')];

	//Format Tanggal 
	$tanggal = date ('j');

	//Array Bulan 
	$array_bulan = array(1=>'Januari','Februari','Maret', 'April', 'Mei', 'Juni','Juli','Agustus','September','Oktober', 'November','Desember'); 
	$bulan = $array_bulan[date('n')];
	 
	//Format Tahun 
	$tahun = date('Y');
	
	$bilangan = new Terbilang;
	 
	// Include the PHPWord.php, all other classes were loaded by an autoloader
	include '../../../../../plugin/PHPWord.php';

	// Create a new PHPWord Object
	$PHPWord = new PHPWord();
		
		
	$document = $PHPWord->loadTemplate('../../../../../config/Template/PPJB.docx');
	
	$document->setValue('nomor_ppjb',$nomor);
	$document->setValue('hari',$hari);
	$document->setValue('tanggal',$tanggal);
	$document->setValue('bulan',$bulan);
	$document->setValue('tahun',$tahun);
	$document->setValue('tahun_terbilang',$bilangan -> eja($tahun));
	$document->setValue('nama_pembeli',$nama_pembeli);
	$document->setValue('alamat',$alamat);
	$document->setValue('JABATAN_PPJB',$JABATAN_PPJB);
	$document->setValue('NAMA_PT',$NAMA_PT);
	$document->setValue('NOMOR_SK',$NOMOR_SK);
	$document->setValue('TANGGAL_SK',$TANGGAL_SK);
	$document->setValue('luas_tanah',$luas_tanah);
	
	
	$path='E:\\';
	
	$nama_file="PPJB ".$nama_pembeli." ". $tanggal . " " . $bulan . " " . $tahun .".doc";
	// $document->save('E:\\andonnikahTemplate.docx');
	//$document->save('E:\\'.$nama_file);
	
	
	// At least write the document to webspace:
	$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
	
	// // save as a random file in temp file
	$temp_file = tempnam(sys_get_temp_dir(), 'PHPWord');
	$document->save($temp_file);
	
	// Your browser will name the file "myFile.docx"
	// regardless of what it's named on the server 
	
	header("Content-Disposition: attachment; filename=\"" . basename($nama_file) . "\"");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Expires: 0');
	header('Pragma: public');
	flush();
	readfile($temp_file); // or echo file_get_contents($temp_file);
	unlink($temp_file);  // remove temp file

	
	
	exit;


?>
