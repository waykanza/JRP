<?php
require_once('../../../../../config/config.php');

$msg 	= '';
$error	= FALSE;

$act				= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id					= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';
$status_otorisasi	= (isset($_REQUEST['status_otorisasi'])) ? clean($_REQUEST['status_otorisasi']) : '';

$kode_blok		= (isset($_REQUEST['kode_blok'])) ? clean($_REQUEST['kode_blok']) : '';
$nomor_memo		= (isset($_REQUEST['nomor_memo'])) ? clean($_REQUEST['nomor_memo']) : '';
$tanggal		= f_tgl (date("Y-m-d"));



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
		
		if ($act == 'Tambah') # Proses Tambah
		{
			ex_empty($kode_blok, 'Kode blok harus diisi.');
			
			$obj = $conn->Execute("
			SELECT  
				s.*,
				f.NILAI_TAMBAH, 
				f.NILAI_KURANG, 
				
				(s.LUAS_TANAH * ht.HARGA_TANAH) AS BASE_HARGA_TANAH, 
				(
					((s.LUAS_TANAH * ht.HARGA_TANAH) * f.NILAI_TAMBAH / 100) - 
					((s.LUAS_TANAH * ht.HARGA_TANAH) * f.NILAI_KURANG / 100)
				) AS FS_HARGA_TANAH, 
				
				(
					(
						(s.LUAS_TANAH * ht.HARGA_TANAH) + 
						((s.LUAS_TANAH * ht.HARGA_TANAH) * f.NILAI_TAMBAH / 100) - 
						((s.LUAS_TANAH * ht.HARGA_TANAH) * f.NILAI_KURANG / 100)
					)
					* s.DISC_TANAH / 100
				) AS DISC_HARGA_TANAH, 
				
				(
					(
						((s.LUAS_TANAH * ht.HARGA_TANAH) + 
						((s.LUAS_TANAH * ht.HARGA_TANAH) * f.NILAI_TAMBAH / 100) - 
						((s.LUAS_TANAH * ht.HARGA_TANAH) * f.NILAI_KURANG / 100))
						-
						(
							((s.LUAS_TANAH * ht.HARGA_TANAH) + 
							((s.LUAS_TANAH * ht.HARGA_TANAH) * f.NILAI_TAMBAH / 100) - 
							((s.LUAS_TANAH * ht.HARGA_TANAH) * f.NILAI_KURANG / 100))
							* s.DISC_TANAH / 100
						)
					) * s.PPN_TANAH / 100
				) AS PPN_HARGA_TANAH, 
				
				
				(s.LUAS_BANGUNAN * hb.HARGA_BANGUNAN) AS BASE_HARGA_BANGUNAN, 
				((s.LUAS_BANGUNAN * hb.HARGA_BANGUNAN) * s.DISC_BANGUNAN / 100) AS DISC_HARGA_BANGUNAN, 
				(
					(
						(s.LUAS_BANGUNAN * hb.HARGA_BANGUNAN) -
						((s.LUAS_BANGUNAN * hb.HARGA_BANGUNAN) * s.DISC_BANGUNAN / 100)
					) * s.PPN_BANGUNAN / 100
				) AS PPN_HARGA_BANGUNAN, 
				
				d.NAMA_DESA,
				l.LOKASI,
				ju.JENIS_UNIT,
				ht.HARGA_TANAH AS HARGA_TANAH_SK,
				f.FAKTOR_STRATEGIS,
				t.TIPE_BANGUNAN,
				hb.HARGA_BANGUNAN AS HARGA_BANGUNAN_SK,
				p.JENIS_PENJUALAN
			FROM 
				STOK s
				
				LEFT JOIN HARGA_BANGUNAN hb ON s.KODE_SK_BANGUNAN = hb.KODE_SK
				LEFT JOIN HARGA_TANAH ht ON s.KODE_SK_TANAH = ht.KODE_SK
				
				LEFT JOIN DESA d ON s.KODE_DESA = d.KODE_DESA
				LEFT JOIN LOKASI l ON s.KODE_LOKASI = l.KODE_LOKASI
				LEFT JOIN JENIS_UNIT ju ON s.KODE_UNIT = ju.KODE_UNIT
				LEFT JOIN FAKTOR f ON s.KODE_FAKTOR = f.KODE_FAKTOR
				LEFT JOIN TIPE t ON s.KODE_TIPE = t.KODE_TIPE
				LEFT JOIN JENIS_PENJUALAN p ON s.KODE_PENJUALAN = p.KODE_JENIS
			WHERE
				KODE_BLOK = '$kode_blok'");
						
			$r_base_harga_tanah		= $obj->fields['BASE_HARGA_TANAH'];
			$r_fs_harga_tanah		= $obj->fields['FS_HARGA_TANAH'];
			$r_disc_harga_tanah		= $obj->fields['DISC_HARGA_TANAH'];
			$r_ppn_harga_tanah		= $obj->fields['PPN_HARGA_TANAH'];
			$r_harga_tanah			= $r_base_harga_tanah + $r_fs_harga_tanah - $r_disc_harga_tanah + $r_ppn_harga_tanah;
			
			$r_base_harga_bangunan	= $obj->fields['BASE_HARGA_BANGUNAN'];
			$r_fs_harga_bangunan	= 0;
			$r_disc_harga_bangunan	= $obj->fields['DISC_HARGA_BANGUNAN'];
			$r_ppn_harga_bangunan	= $obj->fields['PPN_HARGA_BANGUNAN'];
			$r_harga_bangunan		= $r_base_harga_bangunan + $r_fs_harga_bangunan - $r_disc_harga_bangunan + $r_ppn_harga_bangunan;
			
			$jumlah_harga			= $r_harga_tanah + $r_harga_bangunan;
			
			
			$query = "
				SELECT * FROM SPP WHERE KODE_BLOK = '$kode_blok'
			";
			$obj = $conn->execute($query);
			$tanggal_spp		= $obj->fields['TANGGAL_SPP'];
			$nama_pembeli		= $obj->fields['NAMA_PEMBELI'];

			$query = "
			INSERT INTO CS_MEMO_PEMBATALAN (
				KODE_BLOK, TANGGAL_SPP, NAMA_PEMBELI, TANGGAL_MEMO, NILAI_TRANSAKSI, TOTAL_PEMBAYARAN, TOTAL_PENGEMBALIAN, NOMOR_MEMO
			)
			VALUES(
				'$kode_blok', '$tanggal_spp', '$nama_pembeli', CONVERT(DATETIME,'$tanggal',105), $jumlah_harga, '0.00', '0.00', $nomor_memo
			)
			";
		
			ex_false($conn->execute($query), $query);
			
			$msg = 'Data memo pembatalan telah ditambah.';
		}
		else if($act == 'Hapus') #Proses Hapus
		{			
			//ex_ha('', 'D');
		
			$act = array();
			$cb_data = $_REQUEST['cb_data'];
			ex_empty($cb_data, 'Pilih data yang akan dihapus.');
			
			foreach ($cb_data as $id_del)
			{		
				$query = "DELETE FROM CS_MEMO_PEMBATALAN WHERE KODE_BLOK = '$id_del'";
				if ($conn->Execute($query)) {
					$act[] = $id_del;
				} else {
					$error = TRUE;
				}
					
			}
			
			$msg = ($error) ? 'Sebagian data gagal dihapus.' : 'Data memo pembatalan berhasil dihapus.';
		}
		
		else if($act == 'HapusMemo') #Proses Hapus
		{			
			//ex_ha('', 'D');
		
			$act = array();
			$cb_data = $_REQUEST['cb_data'];
			ex_empty($cb_data, 'Pilih data yang akan dihapus.');
			
			foreach ($cb_data as $id_del)
			{		
				$query = "DELETE FROM CS_MEMO_PEMBATALAN WHERE NOMOR_MEMO = '$id_del'";
				if ($conn->Execute($query)) {
					$act[] = $id_del;
				} else {
					$error = TRUE;
				}
					
			}
			
			$msg = ($error) ? 'Sebagian data gagal dihapus.' : 'Data memo pembatalan berhasil dihapus.';
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
//die_app('');
//die_mod('');
$conn = conn($sess_db);
die_conn($conn);
	
if ($act == 'Detail')
{
	$query = "
		SELECT NOMOR_MEMO, MAX(TANGGAL_MEMO) AS TGL_MEMO FROM
		CS_MEMO_PEMBATALAN
		WHERE NOMOR_MEMO = '$id'
		GROUP BY NOMOR_MEMO
	";
	$obj = $conn->execute($query);
	$nomor_memo			= $obj->fields['NOMOR_MEMO'];
	$tanggal_memo		= kontgl(tgltgl(date("d M Y", strtotime($obj->fields['TGL_MEMO']))));	
}

if ($act == 'Ubah')
{
	
	$query = "
		SELECT * FROM CS_MEMO_PEMBATALAN WHERE NOMOR_MEMO = '$id'
	";
	$obj = $conn->execute($query);
	$nomor_memo			= $obj->fields['NOMOR_MEMO'];
	$tanggal_memo		= $obj->fields['TANGGAL_MEMO'];
}

if ($act == 'TambahMemo')
{
	$query = "
		SELECT MAX(NOMOR_MEMO) AS NO_MEMO FROM CS_MEMO_PEMBATALAN
	";
	$obj = $conn->execute($query);
	
	$kode_blok 		= '';
	$nomor_memo 	= 1 + $obj->fields['NO_MEMO'];
	$tanggal_memo 	= kontgl(tgltgl(date("d M Y")));
	$id 			= $nomor_memo;
}

if ($act == 'Tambah')
{
	$query = "
		SELECT MAX(NOMOR_MEMO) AS NO_MEMO FROM CS_MEMO_PEMBATALAN
	";
	$obj = $conn->execute($query);
	
	$kode_blok 		= '';
	$tanggal_memo 	= kontgl(tgltgl(date("d M Y")));
	$id 			= $nomor_memo;
}

?>