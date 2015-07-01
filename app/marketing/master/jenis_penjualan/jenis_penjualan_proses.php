<?php
require_once('../../../../config/config.php');

$msg = '';
$error = FALSE;

$act = (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id = (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

$kode_jenis	= (isset($_REQUEST['kode_jenis'])) ? to_number($_REQUEST['kode_jenis']) : '';
$jenis_penjualan = (isset($_REQUEST['jenis_penjualan'])) ? clean($_REQUEST['jenis_penjualan']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
		ex_login();
		ex_app('M');
		ex_mod('M07');
		$conn = conn($sess_db);
		ex_conn($conn);

		$conn->begintrans(); 
			
		if ($act == 'Tambah') # Proses Tambah
		{
			ex_ha('M07', 'I');
			
			ex_empty($kode_jenis, 'Kode penjualan harus diisi.');
			ex_empty($jenis_penjualan, 'Nama penjualan harus diisi.');
	
			$query = "SELECT COUNT(KODE_JENIS) AS TOTAL FROM JENIS_PENJUALAN WHERE KODE_JENIS = '$kode_jenis'";
			ex_found($conn->Execute($query)->fields['TOTAL'], "Kode jenis penjualan \"$kode_jenis\" telah terdaftar.");
			
			$query = "INSERT INTO JENIS_PENJUALAN (KODE_JENIS, JENIS_PENJUALAN)
			VALUES('$kode_jenis', '$jenis_penjualan')";
			ex_false($conn->Execute($query), $query);
					
			$msg = "Data Jenis penjualan berhasil ditambahkan.";
		}
		elseif ($act == 'Ubah') # Proses Ubah
		{
			ex_ha('M07', 'U');
			
			ex_empty($kode_jenis, 'Kode penjualan harus diisi.');
			ex_empty($jenis_penjualan, 'Nama penjualan harus diisi.');
			
			if ($kode_jenis != $id)
			{
				$query = "SELECT COUNT(KODE_JENIS) AS TOTAL FROM JENIS_PENJUALAN WHERE KODE_JENIS = '$kode_jenis'";
				ex_found($conn->Execute($query)->fields['TOTAL'], "Kode jenis penjualan \"$kode_jenis\" telah terdaftar.");
			}

			$query = "SELECT * FROM JENIS_PENJUALAN WHERE KODE_JENIS = '$kode_jenis' AND JENIS_PENJUALAN = '$jenis_penjualan'";
			ex_found($conn->Execute($query)->recordcount(), "Tidak ada data yang berubah.");
			
			$query = "
			UPDATE JENIS_PENJUALAN 
			SET KODE_JENIS = '$kode_jenis',
				JENIS_PENJUALAN = '$jenis_penjualan'
			WHERE
				KODE_JENIS = '$id'
			";
			ex_false($conn->Execute($query), $query);
			
			$msg = 'Data jenis penjualan berhasil diubah.';
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
die_app('M');
die_mod('M07');
$conn = conn($sess_db);
die_conn($conn);
	
if ($act == 'Tambah')
{
	$obj = $conn->Execute("SELECT MAX(KODE_JENIS) AS MAX_KODE FROM JENIS_PENJUALAN");
	$kode_jenis		= 1 + $obj->fields['MAX_KODE'];
}


if ($act == 'Ubah')
{	
	$obj = $conn->Execute("SELECT * FROM JENIS_PENJUALAN WHERE KODE_JENIS = '$id'");
	$kode_jenis	= $obj->fields['KODE_JENIS'];
	$jenis_penjualan = $obj->fields['JENIS_PENJUALAN'];
}
?>