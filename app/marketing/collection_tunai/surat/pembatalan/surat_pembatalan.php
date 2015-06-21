<?php

	require_once('../../../../../config/config.php');
	require_once('../../../../../config/terbilang.php');
	$terbilang = new Terbilang;
	
	die_login();
	$conn = conn($sess_db);
	die_conn($conn);

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
			$nomor_spp		= $obj->fields['NOMOR_SPP'];
			
			$query = "select NOMOR_SURAT_TUNAI,REG_SURAT_TUNAI from CS_REGISTER_CUSTOMER_SERVICE";
			$obj = $conn->execute($query);
			
			$no				= 1 + $obj->fields['NOMOR_SURAT_TUNAI'];
			$reg			= $obj->fields['REG_SURAT_TUNAI'];
			$nomor_surat	= $no.$reg;
			$tanggal_cetak 	= kontgl(tgltgl(date("d M Y")));
			$kode_blok		= $id_del;
			
			$pecah_kode		= explode("/",$kode_blok);
			$blok_nomor		= $pecah_kode[1];
			$pecah_blok		= explode("-",$blok_nomor);
			$blok			= $pecah_blok[0];
			$nomor			= $pecah_blok[1];
			
			$query = "SELECT UNDANGAN_PEMBATALAN FROM CS_PARAMETER_COL";
			$obj = $conn->execute($query);
			
			$waktu_denda	= $obj->fields['UNDANGAN_PEMBATALAN'];
			$denda			= $waktu_denda * (0.001 * $nilai);
			$total			= $nilai + $denda;
			$n_terbilang	= ucfirst($terbilang->eja($total));
			
			$query = 
			"SELECT *,NOMOR_PPJB = CASE WHEN f.NOMOR IS null 
				THEN '-' ELSE f.NOMOR END,
			TANGGAL_PPJB = CASE WHEN f.TANGGAL IS null 
				THEN '01-01-1990' ELSE f.TANGGAL END
			FROM
				SPP a 
				LEFT JOIN STOK b ON a.KODE_BLOK = b.KODE_BLOK
				LEFT JOIN HARGA_TANAH d ON b.KODE_SK_TANAH = d.KODE_SK
				LEFT JOIN HARGA_BANGUNAN e ON b.KODE_SK_BANGUNAN = e.KODE_SK	
				LEFT JOIN CS_PPJB f ON f.KODE_BLOK = a.KODE_BLOK	
			WHERE 
				a.KODE_BLOK ='$id_del'";
			$obj = $conn->execute($query);
			
			$nomor_ppjb		= $obj->fields['NOMOR_PPJB'];
			$tanggal_ppjb	= kontgl(tgltgl(date("d M Y", strtotime($obj->fields['TANGGAL_PPJB']))));
		
			if($tanggal_ppjb = '01 Januari 1990')
				$tanggal_ppjb = '-';
			
			$luas_bangunan	= $obj->fields['LUAS_BANGUNAN'];
			if($luas_bangunan == 0)
				$kav_bang	= 'tanah';
			else
				$kav_bang	= 'tanah dan bangunan';
			
			$tanggal_surat	= substr($tanggal_tempo,0,10);
			$pecah_tanggal	= explode("-",$tanggal_surat);
			$thn 			= $pecah_tanggal[0];
			$bln 			= $pecah_tanggal[1];
			$tgl			= $pecah_tanggal[2];
			
			if(($bln + 1) > 12)
			{
				$next_bln 	= $bln % 12;
				$next_thn 	= $thn + 1; 
			}
			else
			{
				$next_bln 	= $bln + 1;
				$next_thn 	= $thn;
			}
			
			$query = "SELECT * FROM RENCANA WHERE KODE_BLOK = '$id_del'
			AND TANGGAL >= CONVERT(DATETIME,'01-$bln-$thn',105) 
			AND TANGGAL < CONVERT(DATETIME,'01-$next_bln-$next_thn',105)";
			$obj = $conn->execute($query);
			
			$no_wanprestasi		= $obj->fields['NO_SURAT5'];
			$tgl_wanprestasi	= kontgl(tgltgl(date("d M Y", strtotime($obj->fields['TANGGAL_SURAT5']))));
			
			$query = "update CS_REGISTER_CUSTOMER_SERVICE set NOMOR_SURAT_TUNAI = NOMOR_SURAT_TUNAI + 1";
			ex_false($conn->execute($query), $query);
			
			$query = "update RENCANA set NO_SURAT4 = '$nomor_surat', TANGGAL_SURAT4 = CONVERT(DATETIME,GETDATE(),105) 
			WHERE KODE_BLOK = '$id_del'
			AND TANGGAL >= CONVERT(DATETIME,'01-$bln-$thn',105) 
			AND TANGGAL < CONVERT(DATETIME,'01-$next_bln-$next_thn',105)";
			ex_false($conn->execute($query), $query);
	

	
			// Include the PHPWord.php, all other classes were loaded by an autoloader
			include '../../../../../plugin/PHPWord.php';

			// Create a new PHPWord Object
			$PHPWord = new PHPWord();	
				
			$document = $PHPWord->loadTemplate('../../../../../surat/Surat_Pembatalan.docx');
			//header
			$document->setValue('tanggal_cetak', $tanggal_cetak);
			$document->setValue('nomor_surat', $nomor_surat);
			$document->setValue('nama_pembeli', $nama_pembeli);
			$document->setValue('tanggal_ppjb', $tanggal_ppjb);
			$document->setValue('nomor_ppjb', $nomor_ppjb);
			$document->setValue('alamat', $alamat);
			$document->setValue('telepon', $telepon);
			$document->setValue('nomor_spp', $nomor_spp);
			$document->setValue('tanggal_spp', $tanggal_spp);
			$document->setValue('tanggal_tempo', $tgl_tempo);
			$document->setValue('no_wanprestasi', $no_wanprestasi);
			$document->setValue('tgl_wanprestasi', $tgl_wanprestasi);
			$document->setValue('kode_blok', $kode_blok);
			$document->setValue('blok', $blok);
			$document->setValue('nomor', $nomor);
			$document->setValue('kav_bang', $kav_bang);
			$document->setValue('nilai', to_money($nilai).".00");
			$document->setValue('bulan', $bulan);
			$document->setValue('denda', to_money($denda).".00");
			$document->setValue('total', to_money($total).".00");
			$document->setValue('terbilang', $n_terbilang." rupiah");
			
			$namafile = "Surat Pembatalan"."_".date('d F Y').".doc";
			
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