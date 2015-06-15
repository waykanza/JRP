<?php

	require_once('../../../../config/config.php');
	require_once('../../../../config/terbilang.php');
	require_once('spp_proses.php');
	$terbilang = new Terbilang;
	
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
			
			// Include the PHPWord.php, all other classes were loaded by an autoloader
			include '../../../../plugin/PHPWord.php';

			// Create a new PHPWord Object
			$PHPWord = new PHPWord();	
				
			$document = $PHPWord->loadTemplate('../../../../surat/spp/SPP.docx');
			//header
			$document->setValue('nomor_spp', $no_spp);
			$document->setValue('nama', $nama);
			$document->setValue('alamat_ktp_1', $alamat_rumah);
			$document->setValue('alamat_surat_2', $alamat_surat);
			$document->setValue('alamat_email', $email);
			$document->setValue('telepon', $tlp_lain);
			$document->setValue('npwp', $npwp);
			// $document->setValue('tanggal_tempo', $tgl_tempo);
			// $document->setValue('kode_blok', $kode_blok);
			// $document->setValue('nilai', $nilai);
			// $document->setValue('bulan', $bulan);
			// $document->setValue('denda', $denda.".00");
			// $document->setValue('total', $total.".00");
			// $document->setValue('terbilang', $n_terbilang." rupiah");
			
			$namafile = "SPP"."_".$id." ".date('d F Y').".doc";
			
			// At least write the document to webspace:
			$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
			
			// // save as a random file in temp file
			$temp_file = tempnam(sys_get_temp_dir(), 'PHPWord');
			$document->save($temp_file);
			
			header('Content-Disposition: attachment; filename="' .rawurlencode($namafile) . '"');
			header('Content-Transfer-Encoding: binary');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Expires: 0');
			header('Pragma: public');
			flush();
			readfile($temp_file); // or echo file_get_contents($temp_file);
			unlink($temp_file);  // remove temp file
		
	
	exit;


?>
