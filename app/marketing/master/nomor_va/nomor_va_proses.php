<?php
require_once('../../../../config/config.php');

$msg = '';
$error = FALSE;

$act = (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id = (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

$kode_blok 	= (isset($_REQUEST['kode_blok'])) ? clean($_REQUEST['kode_blok']) : '';
$nomor_va	= (isset($_REQUEST['nomor_va'])) ? clean($_REQUEST['nomor_va']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
		ex_login();
		ex_app('A01');
		ex_mod('PM09');
		$conn = conn($sess_db);
		ex_conn($conn);

		$conn->begintrans(); 
			
		if ($act == 'Tambah') # Proses Tambah
		{
			ex_ha('PM09', 'I');
					
			$query = "SELECT * FROM STOK";
			$obj = $conn->execute($query);
			
			$no = 1;
			while( ! $obj->EOF)
			{
				$id = $obj->fields['KODE_BLOK'];
				$va = sprintf("%06d", $no);
				
				$query2 = "
				UPDATE STOK SET
					NO_VA = '$va'
				WHERE
					KODE_BLOK = '$id'
				";
				ex_false($conn->Execute($query2), $query2);
				
				$obj->movenext();
				$no++;
			}
			$msg = 'Nomor VA berhasil otomatis generate';
		}
		elseif ($act == 'Ubah') # Proses Ubah
		{
			ex_ha('PM09', 'U');
					
			$obj = $conn->Execute("SELECT COUNT(KODE_BLOK) AS TOTAL FROM STOK WHERE NO_VA = '$nomor_va'");
			$total	= $obj->fields['TOTAL'];
			if($total > 0)
			{
				$msg = "Nomor VA \"$nomor_va\" telah terdaftar.";
			}
			else{
				$query = "SELECT * FROM STOK WHERE KODE_BLOK = '$id' AND NO_VA = '$nomor_va'";
				ex_found($conn->Execute($query)->recordcount(), "Tidak ada data yang berubah.");
						
				$query = "
				UPDATE STOK SET
					NO_VA = '$nomor_va'
				WHERE
					KODE_BLOK = '$id'
				";
				ex_false($conn->Execute($query), $query);
						
				$msg = 'Data Virtual Account berhasil diubah.';
			
			}
		}
		
		$conn->committrans(); 
	}
	catch(Exception $e)
	{
		$msg = $e->getmessage();
		$error = TRUE;
		if ($conn) { $conn->rollbacktrans(); } 
	}

	close($conn);
	$json = array('act' => $act, 'error'=> $error, 'msg' => $msg);
	echo json_encode($json);
	exit;
}

die_login();
die_app('A01');
die_mod('PM09');
$conn = conn($sess_db);
die_conn($conn);
	
if ($act == 'Ubah')
{
	$obj = $conn->Execute("SELECT KODE_BLOK, ISNULL(NO_VA, '') AS VA FROM STOK WHERE KODE_BLOK = '$id'");
	$kode_blok	= $obj->fields['KODE_BLOK'];
	$nomor_va	= $obj->fields['VA'];
}
?>