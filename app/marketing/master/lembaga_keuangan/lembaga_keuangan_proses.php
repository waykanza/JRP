<?php
require_once('../../../../config/config.php');

$msg = '';
$error = FALSE;

$act = (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id = (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

$kode_bank	= (isset($_REQUEST['kode_bank'])) ? to_number($_REQUEST['kode_bank']) : '';
$nama_bank	= (isset($_REQUEST['nama_bank'])) ? clean($_REQUEST['nama_bank']) : '';
$alamat_bank = (isset($_REQUEST['alamat_bank'])) ? clean($_REQUEST['alamat_bank']) : '';
$npwp = (isset($_REQUEST['npwp'])) ? clean($_REQUEST['npwp']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
		ex_login();
		ex_app('A01');
		ex_mod('PM06');
		$conn = conn($sess_db);
		ex_conn($conn);

		$conn->begintrans(); 
			
		if ($act == 'Tambah') # Proses Tambah
		{
			ex_ha('PM06', 'I');
			
			ex_empty($kode_bank, 'Kode bank harus diisi.');
			ex_empty($nama_bank, 'Nama bank harus diisi.');
		
			$query = "SELECT COUNT(KODE_BANK) AS TOTAL FROM BANK WHERE KODE_BANK = '$kode_bank'";
			ex_found($conn->Execute($query)->fields['TOTAL'], "Kode lembaga keuangan \"$kode_bank\" telah terdaftar.");
			
			$query = "INSERT INTO BANK (KODE_BANK, NAMA_BANK, ALAMAT_BANK, NPWP) VALUES 
			(
				'$kode_bank',
				'$nama_bank',
				'$alamat_bank',
				'$npwp'
			)";
			ex_false($conn->Execute($query), $query);
					
			$msg = "Data Lembaga keuangan berhasil ditambahkan.";
		}
		elseif ($act == 'Ubah') # Proses Ubah
		{
			ex_ha('PM06', 'U');
			
			ex_empty($kode_bank, 'Kode bank harus diisi.');
			ex_empty($nama_bank, 'Nama bank harus diisi.');
			
			if ($kode_bank != $id)
			{
				$query = "SELECT COUNT(KODE_BANK) AS TOTAL FROM BANK WHERE KODE_BANK = '$kode_bank'";
				ex_found($conn->Execute($query)->fields['TOTAL'], "Kode bank \"$kode_bank\" telah terdaftar.");
			}
			
			$query = "SELECT * FROM BANK WHERE KODE_BANK = '$kode_bank' AND NAMA_BANK = '$nama_bank' AND ALAMAT_BANK = '$alamat_bank' AND NPWP = '$npwp'";
			ex_found($conn->Execute($query)->recordcount(), "Tidak ada data yang berubah.");
					
			$query = "
			UPDATE BANK 
			SET KODE_BANK = '$kode_bank',
				NAMA_BANK = '$nama_bank',
				ALAMAT_BANK = '$alamat_bank',
				NPWP = '$npwp'
			WHERE
				KODE_BANK = '$id'
			";
			ex_false($conn->Execute($query), $query);
			
			$msg = 'Data lembaga keuangan berhasil diubah.';
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
die_mod('PM06');
$conn = conn($sess_db);
die_conn($conn);

	
if ($act == 'Tambah')
{
	$obj = $conn->Execute("SELECT MAX(KODE_BANK) AS MAX_KODE FROM BANK");
	$kode_bank	= 1 + $obj->fields['MAX_KODE'];
}
	
if ($act == 'Ubah')
{
	$obj = $conn->Execute("SELECT * FROM BANK WHERE KODE_BANK = '$id'");
	$kode_bank = $obj->fields['KODE_BANK'];
	$nama_bank = $obj->fields['NAMA_BANK'];
	$alamat_bank = $obj->fields['ALAMAT_BANK'];
	$npwp = $obj->fields['NPWP'];
}
?>