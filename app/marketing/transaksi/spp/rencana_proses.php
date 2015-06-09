<?php
	require_once('../../../../config/config.php');
	
	$msg 	= '';
	$error	= FALSE;
	
	$act	= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
	$id		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';
	$kode_blok	= (isset($_REQUEST['kode_blok'])) ? clean($_REQUEST['kode_blok']) : '';
	
	$jumlah					= '';
	$tanggal				= '';
	$kode_bayar				= '';
	$keterangan				= '';
	
	$total_harga			= (isset($_REQUEST['total_harga'])) ? clean($_REQUEST['total_harga']) : '';
	$tanda_jadi				= (isset($_REQUEST['tanda_jadi'])) ? clean($_REQUEST['tanda_jadi']) : '';
	$jumlah					= (isset($_REQUEST['jumlah'])) ? clean($_REQUEST['jumlah']) : '';	
	$tanggal_input			= (isset($_REQUEST['tanggal'])) ? clean($_REQUEST['tanggal']) : '';
	$kode_bayar				= (isset($_REQUEST['kode_bayar'])) ? clean($_REQUEST['kode_bayar']) : '';
	$keterangan				= (isset($_REQUEST['keterangan'])) ? clean($_REQUEST['keterangan']) : '';
	
	
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		try
		{
			ex_login();
			//ex_app('');
			//ex_mod('');
			$conn = conn($sess_db);
			ex_conn($conn);
			
			$conn->begintrans(); 
			
			if ($act == 'Apply') # Proses Ubah
			{
				//ex_ha('', 'U');
				//CONVERT(DATETIME,'$tanggal',105),
				$nilai					= ($total_harga-$tanda_jadi)/$jumlah;
				for($i=0;$i<$jumlah;$i++){				
					
					if($i==0){
							$tanggal = date("Y-m-d",strtotime($tanggal_input));
					}else{
						$query 		= "SELECT TOP 1 DATEADD(month,$i,CURRENT_TIMESTAMP) AS TANGGAL
										FROM RENCANA
										WHERE KODE_BLOK = '$id'";
							$obj 		= $conn->execute($query);						
							$tanggal	= $obj->fields['TANGGAL'];
					}	
					
					$query = "INSERT INTO RENCANA (KODE_BLOK,TANGGAL,KODE_BAYAR, NILAI, KETERANGAN)
									VALUES('$id',
									'$tanggal',
									'$kode_bayar',
									'$nilai',
									'$keterangan'
								)";			
					
					ex_false($conn->execute($query), $query);					
					
				}
				
				
			
				$msg = 'Data RENCANA berhasil ditambahkan.';
			}
			elseif ($act == 'Hapus') # Proses Hapus
			{
				//ex_ha('', 'D');
				
				$query = "DELETE FROM RENCANA WHERE KODE_BLOK = '$kode_blok'";
					if ($conn->Execute($query)) {
						$act[] = $id_del;
						} else {
						$error = TRUE;
					}
					
				
				$msg = ($error) ? 'Sebagian data RENCANA gagal dihapus.' : 'Data RENCANA berhasil dihapus.'; 	
			}
			
			$conn->committrans(); 
		}
		catch(Exception $e)
		{
			$msg = "Ada Kesalahan";
			$error = TRUE;
			if ($conn) { $conn->rollbacktrans(); } 
		}
		
		close($conn);
		$json = array('act' => $act, 'error'=> $error, 'msg' => $msg);
		echo json_encode($json);
		exit;
	}
	
	die_login();
	//die_app('');
	//die_mod('');
	$conn = conn($sess_db);
	die_conn($conn);
	
	if ($act == 'Ubah')
	{
		$query 		= "SELECT * FROM RENCANA WHERE KODE_BLOK = '$id'";
		$obj 		= $conn->execute($query);
		
		$kode_blok			= $obj->fields['KODE_BLOK'];
		$tanggal			= tgltgl(f_tgl($obj->fields['TANGGAL']));	
		$kode_bayar			= $obj->fields['KODE_BAYAR'];
		$nilai				= $obj->fields['NILAI'];
		$keterangan			= $obj->fields['KETERANGAN'];	
	}
	
	if ($act == 'Tambah')
	{
		$query 				= "SELECT * FROM RENCANA WHERE KODE_BLOK ='$id'";
		$obj 				= $conn->execute($query);
		$kode_blok 			= $obj->fields['KODE_BLOK'];
		$tanggal 			= tgltgl(date("d-m-Y", strtotime ($obj->fields['TANGGAL'])));
		$keterangan 		= $obj->fields['KETERANGAN'];
	}
	
	
?>