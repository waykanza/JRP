<?php
	require_once('../../../../../config/config.php');
	// menggunakan class phpExcelReader
	
	// menggunakan class phpExcelReader
	require_once('../../../../../config/excel_reader2.php');
	// include "excel_reader2.php";
	
	$msg = '';
	$error = FALSE;
	$act = (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
	$id = (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';
	$xls = (isset($_FILES['xls']['tmp_name'])) ? clean($_FILES['xls']['tmp_name']) : '';
	
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
	
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		try
		{
			ex_login();
			//ex_app('A01');
			//ex_mod('PO01');
			$conn = conn($sess_db);
			ex_conn($conn);
			
			$conn->begintrans(); 
			
			if ($act == 'Upload') # Proses Tambah
			{
				//ex_ha('PO01', 'I');
				// membaca file excel yang diupload
				
				// ex_empty($userfile, 'Gagal ! Tidak ada file yang diupload.');		
				
				 // $target = ($_FILES['file']['name']) ;
				// move_uploaded_file($_FILES['file']['tmp_name'], $target);
			
				// $data = new Spreadsheet_Excel_Reader($_FILES['file']['name'],false);
				$data = new Spreadsheet_Excel_Reader($xls);
				// membaca jumlah baris dari data excel
				$baris = $data->rowcount($sheet_index=0);
				//menghitung jumlah real data. Karena kita mulai pada baris ke-2, maka jumlah baris yang sebenarnya adalah 
				//        jumlah baris data dikurangi 1. Demikian juga untuk awal dari pengulangan yaitu i juga dikurangi 1
				// $barisreal = $baris-1;
				// $k = $i-1;
				
				// menghitung persentase progress
				// $percent = intval($k/$barisreal * 100)."%";
				
				// mengupdate progress
				// echo '<script language="javascript">
				// document.getElementById("progress").innerHTML="<div style=\"width:'.$percent.'; background-color:lightblue\">&nbsp;</div>";
				// document.getElementById("info").innerHTML="'.$k.' data berhasil diinsert ('.$percent.' selesai).";
				// </script>';
				
					
				// import data excel mulai baris ke-2 (karena baris pertama adalah nama kolom
				$i=2;
				for ($i; $i<=$baris; $i++)
				{			
					$kode_blok				= $data->val($i, 1);
					$kode_unit 				= $data->val($i, 2);
					$kode_desa 				= $data->val($i, 3);
					$kode_lokasi 			= $data->val($i, 4);
					$kode_sk_tanah 			= $data->val($i, 5);
					$kode_faktor 			= $data->val($i, 6);
					$kode_tipe 				= $data->val($i, 7);
					$kode_sk_bangunan 		= $data->val($i, 8);
					$kode_penjualan 		= $data->val($i, 9);
					$luas_tanah 			= $data->val($i, 10);
					$luas_bangunan 			= $data->val($i, 11);
					$ppn_tanah 				= $data->val($i, 12);
					$ppn_bangunan 			= $data->val($i, 13);
					$disc_tanah 			= $data->val($i, 14);
					$disc_bangunan 			= $data->val($i, 15);
					$progress 				= $data->val($i, 16);
					$class					= $data->val($i, 17);
					$status_stok 			= $data->val($i, 18);
					$terjual 				= $data->val($i, 19);
					$program 				= $data->val($i, 20);
					$status_gambar_siteplan = $data->val($i, 21);
					$status_gambar_lapangan = $data->val($i, 22);
					$status_gambar_gs 		= $data->val($i, 23);

					ex_empty($kode_blok, 'Kode Blok harus diisi.');
					ex_empty($kode_desa, 'Desa harus diisi.');
					ex_empty($kode_lokasi, 'Lokasi harus diisi.');
					ex_empty($kode_unit, 'Jenis unit harus diisi.');
					ex_empty($kode_sk_tanah, 'SK tanah harus diisi.');
					ex_empty($kode_faktor, 'Faktor strategis harus diisi.');
					ex_empty($kode_tipe, 'Tipe harus diisi.');
					ex_empty($kode_sk_bangunan, 'SK bangunan harus diisi.');
					ex_empty($kode_penjualan, 'Jenis penjualan harus diisi.');
					ex_empty($class, 'Pilih class.');
					
					// $query = "SELECT COUNT(KODE_BLOK) AS TOTAL FROM STOK_BAYANGAN WHERE KODE_BLOK = '$kode_blok'";
					// ex_found($conn->Execute($query)->fields['TOTAL'], "Kode blok \"$kode_blok\" telah terdaftar.");
					
					// setelah data dibaca, sisipkan ke dalam tabel tsukses
					$query = "
					INSERT INTO STOK_BAYANGAN
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
					//    hapus file xls yang udah dibaca
						// unlink($_FILES['file']['name']);
					$msg = 'Data Stok berhasil diupload.';
						
				
			}
			
		}
		catch(Exception $e)
		{
			$msg = $e->getmessage();
			$error = TRUE;
			if ($conn) { $conn->rollbacktrans(); } 
		}
		
		close($conn);
		$json = array('act' => $act, 'error'=> $error, 'msg' => $msg );
		echo json_encode($json);
		exit;
	}
	
	die_login();
	
?>					