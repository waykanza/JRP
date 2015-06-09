<?php

	require_once('../../../../../config/config.php');
	require_once('../../../../../config/terbilang.php');
	$terbilang = new Terbilang;
	ex_login();

	$conn = conn($sess_db);
	ex_conn($conn);

	$conn->begintrans(); 

	$act	= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
	$id		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';	
	$tanggal_tempo		= (isset($_REQUEST['tanggal_tempo'])) ? clean($_REQUEST['tanggal_tempo']) : '';	
	$tgl_tempo 			= fm_date(date("Y-m-d", strtotime($tanggal_tempo)));
	
	$array_bulan = array(1=>'Januari','Februari','Maret', 'April', 'Mei', 'Juni','Juli','Agustus','September','Oktober', 'November','Desember'); 
	
	
	if($act == 'Surat') #Proses Hapus
	{
		$act = array();
		$cb_data = $_REQUEST['cb_data'];
		
		foreach ($cb_data as $id_del)
		{		
			$query = "SELECT *, B.TANGGAL AS TGL_TEMPO FROM SPP A JOIN RENCANA B ON A.KODE_BLOK = B.KODE_BLOK
				WHERE A.KODE_BLOK = '$id_del'";
			$obj = $conn->execute($query);
			
			$nama_pembeli	= $obj->fields['NAMA_PEMBELI'];
			$alamat			= $obj->fields['ALAMAT_SURAT'];
			
			$TELP_KANTOR=(trim($obj->fields["TELP_KANTOR"])!="")?trim(strtoupper($obj->fields["TELP_KANTOR"])):"";
			$TELP_LAIN=(trim($obj->fields["TELP_LAIN"])!="")?",".trim(strtoupper($obj->fields["TELP_LAIN"])):"";
			$TELP_RUMAH=(trim($obj->fields["TELP_RUMAH"])!="")?",".trim(strtoupper($obj->fields["TELP_RUMAH"])):"";
			
			$telepon		= $TELP_KANTOR.$TELP_LAIN.$TELP_RUMAH;
			$tanggal_spp	= fm_date(date("Y-m-d", strtotime($obj->fields['TANGGAL_SPP'])));
			$bulan			= $array_bulan[date("n", strtotime($tanggal_tempo))];
			$nilai			= $obj->fields['NILAI'];
			
			$query = "select NOMOR_SURAT_TEGURAN_1,REG_SURAT_TEGURAN_1 from CS_REGISTER_CUSTOMER_SERVICE";
			$obj = $conn->execute($query);
			
			$no				= 1 + $obj->fields['NOMOR_SURAT_TEGURAN_1'];
			$reg			= $obj->fields['REG_SURAT_TEGURAN_1'];
			$tahun 			= date('Y');
			$nomor_surat	= $no.$reg.$tahun;
			$tanggal_cetak 	= kontgl(tgltgl(date("d M Y")));
			$kode_blok		= $id_del;
			
			$query = "SELECT SOMASI_SATU FROM CS_PARAMETER_COL";
			$obj = $conn->execute($query);
			
			$waktu_denda	= $obj->fields['SOMASI_SATU'];
			$denda			= $waktu_denda * (0.001 * $nilai);
			$total			= $nilai - $denda;
			$n_terbilang	= ucfirst($terbilang->eja($total));
			
			
			// Include the PHPWord.php, all other classes were loaded by an autoloader
			include '../../../../../plugin/PHPWord.php';

			// Create a new PHPWord Object
			$PHPWord = new PHPWord();	
				
			$document = $PHPWord->loadTemplate('../../../../../surat/Surat_Somasi_1.docx');
			//header
			$document->setValue('tanggal_cetak', $tanggal_cetak);
			$document->setValue('nomor_surat', $nomor_surat);
			$document->setValue('nama_pembeli', $nama_pembeli);
			$document->setValue('alamat', $alamat);
			$document->setValue('telepon', $telepon);
			$document->setValue('tanggal_spp', $tanggal_spp);
			$document->setValue('tanggal_tempo', $tgl_tempo);
			$document->setValue('kode_blok', $kode_blok);
			$document->setValue('nilai', $nilai);
			$document->setValue('bulan', $bulan);
			$document->setValue('denda', $denda.".00");
			$document->setValue('total', $total.".00");
			$document->setValue('terbilang', $n_terbilang." rupiah");
			
			$namafile = "Surat Somasi I"."_".date('d F Y').".doc";
			
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
		}
		
	}		
	
	exit;


?>
