<?php
	require_once('../../../../config/config.php');
	
	$msg 	= '';
	$error	= FALSE;
	
	$act	= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
	$id		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';
	
	$jumlah					= '';
	$tanggal				= '';
	$kode_bayar				= '';
	$keterangan				= '';
	
	$total_harga			= (isset($_REQUEST['total_harga'])) ? to_number($_REQUEST['total_harga']) : '';
	$tanda_jadi				= (isset($_REQUEST['tanda_jadi'])) ? to_number($_REQUEST['tanda_jadi']) : '';
	$jumlah					= (isset($_REQUEST['jumlah'])) ? clean($_REQUEST['jumlah']) : '';	
	$tanggal_input			= (isset($_REQUEST['tgl_spp'])) ? clean($_REQUEST['tgl_spp']) : '';
	$kode_bayar				= (isset($_REQUEST['kode_bayar'])) ? clean($_REQUEST['kode_bayar']) : '';
	$keterangan				= (isset($_REQUEST['keterangan'])) ? clean($_REQUEST['keterangan']) : '';
	$pola_bayar				= (isset($_REQUEST['pola_bayar'])) ? clean($_REQUEST['pola_bayar']) : '';
	$status_kompensasi		= (isset($_REQUEST['status_kompensasi'])) ? clean($_REQUEST['status_kompensasi']) : '';
	$uang_muka				= (isset($_REQUEST['uang_muka'])) ? clean($_REQUEST['uang_muka']) : '';
	
	
	
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
				$pecah_tgl  = explode("-",$tgl_input);
				$tgl		= $pecah_tgl[0];
				$bln		= $pecah_tgl[1];
				$thn		= $pecah_tgl[2];
				
				if($tgl <= 28)
				{
					$tempo = 1;
				}
				else
				{
					$tempo = 2;
				}
				
				$next_bln	= $bln + $tempo;  
				$next_thn	= $thn;
				if($next_bln > 12)
				{
					$next_bln = $nexy_bln % 12;
					$next_thn = $next_thn + 1;
					
				}
				
				$tanggal_input = '25-07-2015';
				
				if ($status_kompensasi == 2){
					$nilai	= ($total_harga/$pola_bayar);
				}
				else if ($status_kompensasi == 1){
					$nilai	= ((($total_harga*$uang_muka)/100)/$pola_bayar);
				}
				
				$nilai_a = $nilai;
				
				for($i=0;$i<$pola_bayar;$i++){				
					
					if($i==0){
							$tanggal = date("Y-m-d",strtotime($tanggal_input));
							$nilai = $nilai - $tanda_jadi;
							if($nilai < 0)
							{	$sisa = $nilai * -1;
								$nilai = 0;
							}
					}
					else{
						$nilai = $nilai_a;
						if($i == 1){
							$nilai = $nilai - $sisa;
						}
						
						$query 		= "SELECT TOP 1 DATEADD(month,1,TANGGAL) AS TANGGAL
										FROM RENCANA
										WHERE KODE_BLOK = '$id'
										ORDER BY TANGGAL DESC";
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
				
				
			
				//$msg = 'Data RENCANA berhasil ditambahkan.';
				$msg = $pola_bayar.','.$sisa;
			}
			elseif ($act == 'Hapus') # Proses Hapus
			{
				//ex_ha('', 'D');
				
				$query = "DELETE FROM RENCANA WHERE KODE_BLOK = '$id'";
				
				ex_false($conn->execute($query), $query);
				
				$msg ='Data RENCANA berhasil dihapus.'; 	
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