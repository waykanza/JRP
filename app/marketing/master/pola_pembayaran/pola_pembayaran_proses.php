<?php
require_once('../../../../config/config.php');

$msg = '';
$error = FALSE;

$act = (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id = (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

$kode_pola_bayar 	= (isset($_REQUEST['kode_pola_bayar'])) ? to_number($_REQUEST['kode_pola_bayar']) : '';
$kode_jenis 		= (isset($_REQUEST['kode_jenis'])) ? clean($_REQUEST['kode_jenis']) : '';
$nama_pola_bayar 	= (isset($_REQUEST['nama_pola_bayar'])) ? clean($_REQUEST['nama_pola_bayar']) : '';
$rumus_pola_bayar 	= (isset($_REQUEST['rumus_pola_bayar'])) ? clean($_REQUEST['rumus_pola_bayar']) : '';


if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
		ex_login();
		ex_app('A01');
		ex_mod('PM10');
		$conn = conn($sess_db);
		ex_conn($conn);

		$conn->begintrans(); 
			
		if ($act == 'Tambah') # Proses Tambah
		{
			ex_ha('PM10', 'I');
			
			ex_empty($nama_pola_bayar, 'Nama pola pembayaran harus diisi.');
			ex_empty($rumus_pola_bayar, 'Rumus pola pembayaran harus diisi.');
		
			$query = "INSERT INTO POLA_BAYAR (KODE_POLA_BAYAR, KODE_JENIS, NAMA_POLA_BAYAR, RUMUS_POLA_BAYAR)
			VALUES($kode_pola_bayar, '$kode_jenis', '$nama_pola_bayar', $rumus_pola_bayar)";
			ex_false($conn->Execute($query), $query);
					
			$msg = "Data pola pembayaran berhasil ditambahkan.";
		}
		elseif ($act == 'Ubah') # Proses Ubah
		{
			ex_ha('PM10', 'U');
			
			ex_empty($nama_pola_bayar, 'Nama pola pembayaran harus diisi.');
			ex_empty($rumus_pola_bayar, 'Rumus pola pembayaran harus diisi.');
			
			$query = "
			UPDATE POLA_BAYAR
			SET KODE_JENIS = '$kode_jenis',
				NAMA_POLA_BAYAR = '$nama_pola_bayar',
				RUMUS_POLA_BAYAR = $rumus_pola_bayar
			WHERE
				KODE_POLA_BAYAR = $id
			";
			ex_false($conn->Execute($query), $query);
					
			$msg = 'Data jenis pembayaran berhasil diubah.';
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
die_mod('PM10');
$conn = conn($sess_db);
die_conn($conn);
	
if ($act == 'Tambah')
{
	$obj = $conn->Execute("SELECT MAX(KODE_POLA_BAYAR) AS MAX_KODE FROM POLA_BAYAR");
	$kode_pola_bayar	= 1 + $obj->fields['MAX_KODE'];
	$kode_jenis			= 1;
	$nama_pola_bayar	= '';
	$rumus_pola_bayar	= '';
}

if ($act == 'Ubah')
{
	$obj = $conn->Execute("SELECT * FROM POLA_BAYAR WHERE KODE_POLA_BAYAR = '$id'");
	$kode_pola_bayar	= $obj->fields['KODE_POLA_BAYAR'];
	$kode_jenis			= $obj->fields['KODE_JENIS'];
	$nama_pola_bayar	= $obj->fields['NAMA_POLA_BAYAR'];
	$rumus_pola_bayar	= $obj->fields['RUMUS_POLA_BAYAR'];
}
?>