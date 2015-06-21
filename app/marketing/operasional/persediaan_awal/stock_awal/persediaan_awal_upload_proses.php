<?php
	require_once('../../../../../config/config.php');
	
	// menggunakan class phpExcelReader
	require('../../../../../config/PHPExcel.php');
	require('../../../../../config/PHPExcel/IOFactory.php');
	
	
	$msg = '';
	$error = FALSE;
	$act = (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
	$id = (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';
	$tes = (isset($_REQUEST['tes'])) ? clean($_REQUEST['tes']) : '';
	$path = (isset($_FILES['file']['name'])) ? clean($_FILES['file']['name']) : '';
	
	$nama_desa			= '';
	$lokasi				= '';
	$jenis_unit			= '';
	$harga_tanah_sk		= '';
	$faktor_strategis	= '';
	$tipe_bangunan		= '';
	$harga_bangunan_sk	= '';
	$jenis_penjualan	= '';
	
	$tgl_bangunan		= '';
	$tgl_selesai		= '';
	$progress			= '';
	
	$base_harga_tanah		= 0;
	$nilai_tambah			= 0;
	$nilai_kurang			= 0;
	$fs_harga_tanah			= 0;
	$disc_harga_tanah		= 0;
	$ppn_harga_tanah		= 0;
	$harga_tanah			= 0;
	
	$base_harga_bangunan	= 0;
	$fs_harga_bangunan		= 0;
	$disc_harga_bangunan	= 0;
	$ppn_harga_bangunan		= 0;
	$harga_bangunan			= 0;
	
	$eror       = false;
	$folder     = 'upload/';
	//type file yang bisa diupload
	$file_type  = array('xls','xlsx');
	//tukuran maximum file yang dapat diupload
	$max_size   = 1000000; // 1MB
	
	if (isset($_POST["act"])) 
	{
		try
		{
			ex_login();
			//ex_app('A01');
			//ex_mod('PO01');
			$conn = conn($sess_db);
			ex_conn($conn);
			
			$conn->begintrans(); 
			
			//Mulai memorises data
			$file_name  = $_FILES['data_upload']['name'];
			$file_size  = $_FILES['data_upload']['size'];
			//cari extensi file dengan menggunakan fungsi explode
			$explode    = explode('.',$file_name);
			$extensi    = $explode[count($explode)-1];
			
			//check apakah type file sudah sesuai
			if(!in_array($extensi,$file_type)){
				$eror   = true;
				$msg .= '- Type file yang anda upload tidak sesuai<br />';
			}
			if($file_size > $max_size){
				$eror   = true;
				$msg .= '- Ukuran file melebihi batas maximum<br />';
			}
			
			// Path file upload
			move_uploaded_file($_FILES['data_upload']['tmp_name'], './' . $_FILES['data_upload']['name']);
			
			
			// Load PHPExcel
			$objPHPExcel = PHPExcel_IOFactory::load($file_name);
			foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
				$worksheetTitle = $worksheet->getTitle();
				$highestRow = $worksheet->getHighestRow();
				$highestColumn = $worksheet->getHighestColumn();
				$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
				$nrColumns = ord($highestColumn) - 64;
				// echo "<br>Worksheet " . $worksheetTitle . " memiliki ";
				// echo $nrColumns . ' kolom (A-' . $highestColumn . ') ';
				// echo ' dan ' . $highestRow . ' baris.';
				// echo '<br>Data: <table border="1"><tr>';
				for ($row = 1; $row <= $highestRow; ++$row) {
					// echo '<tr>';
					for ($col = 0; $col < $highestColumnIndex; ++$col) {
						$cell = $worksheet->getCellByColumnAndRow($col, $row);
						$val = $cell->getValue();
						$dataType = PHPExcel_Cell_DataType::dataTypeForValue($val);
						// echo '<td>' . $val . '<br>(Typ ' . $dataType . ')</td>';
					}
					// echo '</tr>';
				}
				// echo '</table>';
			}
			
			// Proses perulangan baris file excel yang diupload
			for ($row = 2; $row <= $highestRow; ++$row) {
				$val = array();
				for ($col = 0; $col < $highestColumnIndex; ++$col) {
					$cell = $worksheet->getCellByColumnAndRow($col, $row);
					$val[] = $cell->getValue();
				}
				
				// Skip data jika username sudah ada
				$kode_blok				= $val[1];
				
				// $query = "SELECT COUNT(KODE_BLOK) AS TOTAL FROM STOK WHERE KODE_BLOK = '$kode_blok'";
				// ex_found($conn->Execute($query)->fields['TOTAL'], "Kode blok \"$kode_blok\" telah terdaftar.");
				$query = "
					SELECT COUNT(KODE_BLOK) AS TOTAL FROM STOK WHERE KODE_BLOK = '$kode_blok'
					";
					$total_data = $conn->Execute($query)->fields['TOTAL'];
				
				if ($total_data == 0) {
					// Buat query insert per-baris data ke tabel user
					// $sql = "INSERT INTO user VALUES ('','" . $val[0] . "','" . $val[1] . "','" . $val[2] . "')";
					$kode_blok				= $val[0];
					$kode_unit 				= $val[1];
					$kode_desa 				= $val[2];
					$kode_lokasi 			= $val[3];
					$kode_sk_tanah 			= $val[4];
					$kode_faktor 			= $val[5];
					$kode_tipe 				= $val[6];
					$kode_sk_bangunan 		= $val[7];
					$kode_penjualan 		= $val[8];
					$luas_tanah 			= $val[9];
					$luas_bangunan 			= $val[10];
					$ppn_tanah 				= $val[11];
					$ppn_bangunan 			= $val[12];
					$disc_tanah 			= $val[13];
					$disc_bangunan 			= $val[14];
					$progress 				= $val[15];
					$class					= $val[16];
					$status_stok 			= $val[17];
					$terjual 				= $val[18];
					$program 				= $val[19];
					$status_gambar_siteplan = $val[20];
					$status_gambar_lapangan = $val[21];
					$status_gambar_gs 		= $val[22];
					
					$query = "
					INSERT INTO STOK
					(
					KODE_BLOK, KODE_UNIT, KODE_DESA, KODE_LOKASI, KODE_SK_TANAH, 
					KODE_FAKTOR, KODE_TIPE, KODE_SK_BANGUNAN, KODE_PENJUALAN, 
					
					LUAS_TANAH, LUAS_BANGUNAN, 
					PPN_TANAH, PPN_BANGUNAN, 
					DISC_TANAH, DISC_BANGUNAN, 
					
					PROGRESS, 
					
					CLASS, STATUS_STOK, TERJUAL, PROGRAM,
					
					STATUS_GAMBAR_SITEPLAN, 
					STATUS_GAMBAR_LAPANGAN, 
					STATUS_GAMBAR_GS
					)
					VALUES
					(
					'$kode_blok', $kode_unit, $kode_desa, $kode_lokasi, $kode_sk_tanah, 
					$kode_faktor, $kode_tipe, $kode_sk_bangunan, $kode_penjualan, 
					
					$luas_tanah, $luas_bangunan, 
					$ppn_tanah, $ppn_bangunan, 
					$disc_tanah, $disc_bangunan, 
					
					0, 
					
					'$class', '0', '0', '$program', 
					
					'$status_gambar_siteplan', 
					'$status_gambar_lapangan', 
					'$status_gambar_gs'
					)		
					";
					
					ex_false($conn->Execute($query), $query);
					
				}
			}
			
			
			// Hapus file excel ketika data sudah masuk ke tabel
			@unlink($file_name);
			$msg = 'Data Stok berhasil diupload.';
			
			
		}
		catch(Exception $e)
		{
			$msg = $e->getmessage();
			$error = TRUE;
			if ($conn) { $conn->rollbacktrans(); } 
		}
		
		close($conn);
		$json = array('msg' => $msg );
		echo $msg;
		exit;
	}
	
	die_login();

?>					