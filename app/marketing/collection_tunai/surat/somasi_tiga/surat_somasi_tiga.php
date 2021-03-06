<?php

	include '../../../../../plugin/PHPWord.php';
	require_once('../../../../../config/config.php');
	require_once('../../../../../config/terbilang.php');
	$terbilang = new Terbilang;
	
	
	ob_clean();
	die_login();
	$conn = conn($sess_db);
	die_conn($conn);

	$act	= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
	$id		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';	
	$cb_data = array();
	$cb_data= (isset($_REQUEST['cb_data'])) ? ($_REQUEST['cb_data']) : '';
	
	$tanggal_tempo		= (isset($_REQUEST['tanggal_tempo'])) ? clean($_REQUEST['tanggal_tempo']) : '';	
	$tgl_tempo 			= fm_date(date("Y-m-d", strtotime($tanggal_tempo)));
	
	$array_bulan = array(1=>'Januari','Februari','Maret', 'April', 'Mei', 'Juni','Juli','Agustus','September','Oktober', 'November','Desember'); 
	
	
	if($act == 'Surat') #Proses Hapus
	{
		$act = array();
		
		foreach ((array) $cb_data as $id)
		{	
			$query = "SELECT *, B.TANGGAL AS TGL_TEMPO FROM SPP A JOIN RENCANA B ON A.KODE_BLOK = B.KODE_BLOK
				WHERE A.KODE_BLOK = '$id'";
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
			
			$query = "select STATUS_KOMPENSASI FROM SPP WHERE KODE_BLOK = '$id'";
			$obj = $conn->execute($query);
			$status			= $obj->fields['STATUS_KOMPENSASI'];
			
			$query = "select * from CS_REGISTER_CUSTOMER_SERVICE";
			$obj = $conn->execute($query);
			
			if($status == 2)
			{
				$no			= 1 + $obj->fields['NOMOR_SURAT_TUNAI'];
				$reg		= $obj->fields['REG_SURAT_TUNAI'];
			}
			else if($status == 1)
			{
				$no			= 1 + $obj->fields['NOMOR_SURAT_KPR'];
				$reg		= $obj->fields['REG_SURAT_KPR'];
			}
			
			$nomor_surat	= $no.$reg;
			$tanggal_cetak 	= kontgl(tgltgl(date("d M Y")));
			$kode_blok		= $id;
			
			$pecah_kode		= explode("/",$kode_blok);
			$blok_nomor		= $pecah_kode[1];
			$pecah_blok		= explode("-",$blok_nomor);
			$blok			= $pecah_blok[0];
			$nomor			= $pecah_blok[1];
			
			$query = "SELECT SOMASI_TIGA FROM CS_PARAMETER_COL";
			$obj = $conn->execute($query);
			
			$waktu_denda	= $obj->fields['SOMASI_TIGA'];
			$denda			= $waktu_denda * (0.001 * $nilai);
			$total			= $nilai + $denda;
			$n_terbilang	= ucfirst($terbilang->eja($total));
			
			$query = 
			"SELECT *
			FROM
				SPP a 
				LEFT JOIN STOK b ON a.KODE_BLOK = b.KODE_BLOK
				LEFT JOIN HARGA_TANAH d ON b.KODE_SK_TANAH = d.KODE_SK
				LEFT JOIN HARGA_BANGUNAN e ON b.KODE_SK_BANGUNAN = e.KODE_SK	
			WHERE 
				a.KODE_BLOK ='$id'";
			$obj = $conn->execute($query);
			
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
			
			$query = "SELECT * FROM RENCANA WHERE KODE_BLOK = '$id'
			AND TANGGAL >= CONVERT(DATETIME,'01-$bln-$thn',105) 
			AND TANGGAL < CONVERT(DATETIME,'01-$next_bln-$next_thn',105)";
			$obj = $conn->execute($query);
			
			$nomor_surat_dahulu		= $obj->fields['NO_SURAT3'];
			$tanggal_surat_dahulu	= kontgl(tgltgl(date("d M Y", strtotime($obj->fields['TANGGAL_SURAT3']))));
			
			$query = "update CS_REGISTER_CUSTOMER_SERVICE set NOMOR_SURAT_TUNAI = NOMOR_SURAT_TUNAI + 1";
			ex_false($conn->execute($query), $query);
			
			$query = "update RENCANA set NO_SURAT4 = '$nomor_surat', TANGGAL_SURAT4 = CONVERT(DATETIME,GETDATE(),105) 
			WHERE KODE_BLOK = '$id'
			AND TANGGAL >= CONVERT(DATETIME,'01-$bln-$thn',105) 
			AND TANGGAL < CONVERT(DATETIME,'01-$next_bln-$next_thn',105)";
			ex_false($conn->execute($query), $query);
	

			// Create a new PHPWord Object
			$PHPWord = new PHPWord();	
				
			$document = $PHPWord->loadTemplate('../../../../../surat/Surat_Somasi_3.docx');
			//header
			$document->setValue('tanggal_cetak', $tanggal_cetak);
			$document->setValue('nomor_surat', $nomor_surat);
			$document->setValue('nama_pembeli', $nama_pembeli);
			$document->setValue('alamat', $alamat);
			$document->setValue('telepon', $telepon);
			$document->setValue('tanggal_spp', $tanggal_spp);
			$document->setValue('tanggal_tempo', $tgl_tempo);
			$document->setValue('nomor_surat_dahulu', $nomor_surat_dahulu);
			$document->setValue('tanggal_surat_dahulu', $tanggal_surat_dahulu);
			$document->setValue('blok', $blok);
			$document->setValue('nomor', $nomor);
			$document->setValue('kav_bang', $kav_bang);
			$document->setValue('nilai', to_money($nilai).".00");
			$document->setValue('bulan', $bulan);
			$document->setValue('denda', to_money($denda).".00");
			$document->setValue('total', to_money($total).".00");
			$document->setValue('terbilang', $n_terbilang." rupiah");
			
			$kode_blok = explode("/",$kode_blok);
			$kode_blok = implode("",$kode_blok);
			
			$nama_file = "Surat_Somasi_III_".trim($kode_blok)."_".date('d F Y').".doc";
			
			// At least write the document to webspace:
			$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
			
			
			// buat pathnya di C..jika belum ada, sistem langsung dibikin
			$path = 'C:Surat_Somasi_III/';
			if (!file_exists($path)) {
				mkdir($path, 0777, true);
			}
			
			//simpan file di komputer
			$document->save($path ."/". $nama_file );
			
			//simpan ke dalam array
			$file_names[]= $nama_file;
			
		}
	
		//persiapan file zip dan memanggil fungsi generate zip
		$zip_file_name="Surat_Somasi_III_".date('d F Y').".zip";
		$file_path= getcwd(). '/Surat_Somasi_III/';
		zipFilesAndDownload($file_names,$zip_file_name,$path);
	
	}		
	
	//fungsi untuk membuat zip
	function zipFilesAndDownload($file_names,$archive_file_name,$file_path){
		$zip = new ZipArchive();
		//create the file and throw the error if unsuccessful
		if ($zip->open($archive_file_name, ZIPARCHIVE::CREATE )!==TRUE) {
			exit("cannot open <$archive_file_name>\n");
		}
		//add each files of $file_name array to archive
		foreach((array)$file_names as $files)  {
			$zip->addFile($file_path.$files,$files);     
		}
		$zip->close();
		$zipped_size = filesize($archive_file_name);
		header("Content-Description: File Transfer");
		header("Content-type: application/zip"); 
		header("Content-Type: application/force-download");// some browsers need this
		header("Content-Disposition: attachment; filename=\"" . basename($archive_file_name) . "\"");
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header("Content-Length:". " $zipped_size");
		ob_clean();
		flush();
		readfile("$archive_file_name");
		unlink("$archive_file_name"); // Now delete the temp file (some servers need this option)
		exit;   
	}
?>
