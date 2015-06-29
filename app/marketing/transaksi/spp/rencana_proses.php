<?php
	require_once('../../../../config/config.php');
		
	$id				= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';
	$act			= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
	
	$tanda_jadi				= (isset($_REQUEST['tanda_jadi'])) ? to_number($_REQUEST['tanda_jadi']) : '';
	$jumlah					= (isset($_REQUEST['jumlah'])) ? clean($_REQUEST['jumlah']) : '';	
	$tanggal_input			= (isset($_REQUEST['tgl_spp'])) ? clean($_REQUEST['tgl_spp']) : '';
	$kode_bayar				= (isset($_REQUEST['kode_bayar'])) ? clean($_REQUEST['kode_bayar']) : '';
	$keterangan				= (isset($_REQUEST['keterangan'])) ? clean($_REQUEST['keterangan']) : '';
	$pola_bayar				= (isset($_REQUEST['pola_bayar'])) ? clean($_REQUEST['pola_bayar']) : '';
	$status_kompensasi		= (isset($_REQUEST['status_kompensasi'])) ? clean($_REQUEST['status_kompensasi']) : '';
	$uang_muka				= (isset($_REQUEST['uang_muka'])) ? clean($_REQUEST['uang_muka']) : '';
	$total					= (isset($_REQUEST['total'])) ? clean($_REQUEST['total']) : '';
	$kbank					= (isset($_REQUEST['kbank'])) ? clean($_REQUEST['kbank']) : '';
	
	
	
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
		die_login();
		// die_app('');
		// die_mod('');
		$conn = conn($sess_db);
		die_conn($conn);
		$conn->begintrans();
		$error = false;
		
		if ($act == 'Apply') # Proses Ubah
			{
			
				ex_empty($pola_bayar, 'Pola Bayar harus diisi.');
				
				
				$query = "DELETE FROM RENCANA WHERE KODE_BLOK = '$id'";
				
				ex_false($conn->execute($query), $query);
			
				$obj = $conn->execute("		
										SELECT * FROM POLA_BAYAR
										WHERE KODE_POLA_BAYAR = $pola_bayar
 									 ");
				
				$kode_jenis = $obj->fields['KODE_JENIS'];
				$nilai1	 	= $obj->fields['NILAI1'];
				$kali1	 	= $obj->fields['KALI1'];
				$nilai2		= $obj->fields['NILAI2'];
				$kali2 		= $obj->fields['KALI2'];
				$nilai3 	= $obj->fields['NILAI3'];
				$kali3 		= $obj->fields['KALI3'];
				$nilai4		= $obj->fields['NILAI4'];
				$kali4 		= $obj->fields['KALI4'];
				$nilai5 	= $obj->fields['NILAI5'];
				$kali5 		= $obj->fields['KALI5'];
				$nilai_jenis = $obj->fields['NILAI_JENIS'];
			
				
				$b = $tanggal_input;
				
				$pecah_tgl  = explode("-",$tanggal_input);
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
				
				$tanggal_input = '25-'.$next_bln.'-'.$next_thn;
								
				$nilai	= $total;
				$total_harga_awal = $total;
				
				$nilai_bagi = array();
				for($i=0;$i<$kali1;$i++){
					$nilai_bagi[] = ($nilai * $nilai1)/100;
				}
				for($i=0;$i<$kali2;$i++){
					$nilai_bagi[] = ($nilai * $nilai2)/100;
				}
				for($i=0;$i<$kali3;$i++){
					$nilai_bagi[] = ($nilai * $nilai3)/100;
				}
				for($i=0;$i<$kali4;$i++){
					$nilai_bagi[] = ($nilai * $nilai4)/100;
				}
				for($i=0;$i<$kali5;$i++){
					$nilai_bagi[] = ($nilai * $nilai5)/100;
				}
				
				
				$kali = $kali1+$kali2+$kali3+$kali4+$kali5;
				$nilai_fix=0;
				for($i=0;$i<$kali;$i++){				
					
					if($i==0){
							$tanggal = date("Y-m-d",strtotime($tanggal_input));
							$nilai_fix = $nilai_bagi[$i] - $tanda_jadi;
							if($nilai_fix < 0)
							{	$sisa = $nilai_fix * -1;
								$nilai_fix = 0;
							}
					}else 
					if ($i == $kali-1){
						$obj = $conn->execute("		
										SELECT SUM(NILAI) AS JUMLAH FROM RENCANA
										WHERE KODE_BLOK = '$id'
 									 ");
						$jumlah	 	= $obj->fields['JUMLAH'];
						
						$query 		= "SELECT TOP 1 DATEADD(month,1,TANGGAL) AS TANGGAL
										FROM RENCANA
										WHERE KODE_BLOK = '$id'
										ORDER BY TANGGAL DESC";
						$obj 		= $conn->execute($query);						
						$tanggal	= $obj->fields['TANGGAL'];
						if ($kode_jenis == 2){
							$nilai_fix = $total_harga_awal - ($jumlah + $tanda_jadi);
						}
						else if ($kode_jenis == 1){
							$nilai_fix = $nilai_bagi[$i];
						}
						
					}
					else{
						$nilai_fix = $nilai_bagi[$i];
						
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
									'$nilai_fix',
									'$keterangan'
								)";			
					
					ex_false($conn->execute($query), $query);					
					
				}
				
				$obj = $conn->execute("		
										SELECT SUM(NILAI) AS JUMLAH FROM RENCANA
										WHERE KODE_BLOK = '$id'
 									 ");
				$jumlah	 	= $obj->fields['JUMLAH'];
				$jumlah_kpr = $total_harga_awal - ($jumlah + $tanda_jadi);
				
				$query 		= "SELECT TOP 1 DATEADD(month,1,TANGGAL) AS TANGGAL
										FROM RENCANA
										WHERE KODE_BLOK = '$id'
										ORDER BY TANGGAL DESC";
				$obj 		= $conn->execute($query);
				$tgl_akad	= $obj->fields['TANGGAL'];
				
				
				$query = "UPDATE SPP SET  
							KODE_BANK			= '$kbank',
							STATUS_KOMPENSASI	= '$kode_jenis',
							JUMLAH_KPR			= '$jumlah_kpr',
							TANGGAL_AKAD		= '$tgl_akad'
						  WHERE KODE_BLOK		= '$id'
						  ";
				
				ex_false($conn->execute($query), $query);

				$msg = 'Rencana Pembayaran Berhasil Disimpan';
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
// die_app('');
// die_mod('');
$conn = conn($sess_db);
die_conn($conn);

if ($act == 'Rencana')
{
	$query 		= "SELECT *,s.NPWP AS CS_NPWP FROM SPP S
			   LEFT JOIN BANK B ON S.KODE_BANK = B.KODE_BANK
			WHERE S.KODE_BLOK = '$id'";
	$obj 		= $conn->execute($query);
	
	$tgl_spp			= tgltgl(f_tgl($obj->fields['TANGGAL_SPP']));	
	$no_customer		= $obj->fields['NOMOR_CUSTOMER'];
	$no_spp				= $obj->fields['NOMOR_SPP'];
	$nama				= $obj->fields['NAMA_PEMBELI'];
	$alamat_rumah		= $obj->fields['ALAMAT_RUMAH'];
	$alamat_surat		= $obj->fields['ALAMAT_SURAT'];	
	$alamat_npwp		= $obj->fields['ALAMAT_NPWP'];
	$email				= $obj->fields['ALAMAT_EMAIL'];
	$tlp_rumah			= $obj->fields['TELP_RUMAH'];
	$tlp_kantor			= $obj->fields['TELP_KANTOR'];
	$tlp_lain			= $obj->fields['TELP_LAIN'];
	$identitas			= $obj->fields['IDENTITAS'];
	$no_identitas		= $obj->fields['NO_IDENTITAS'];
	$npwp				= $obj->fields['CS_NPWP'];
	$jenis_npwp			= $obj->fields['JENIS_NPWP'];
	$kbank				= $obj->fields['KODE_BANK'];
	$nospk				= $obj->fields['NOMOR_SPK_BANK'];
	$plafonkpr			= $obj->fields['PLAFON_KPR_DISETUJUI'];
	$retensi			= $obj->fields['NILAI_RETENSI'];
	$jumlah_kpr			= $obj->fields['JUMLAH_KPR'];
	$agen				= $obj->fields['KODE_AGEN'];
	$koordinator		= $obj->fields['KODE_KOORDINATOR'];	
	$tgl_akad			= tgltgl(f_tgl($obj->fields['TANGGAL_AKAD'])); 
	$tgl_akad			= tgltgl(f_tgl($obj->fields['TANGGAL_REALISASI_AKAD_KREDIT']));
	$tgl_spk			= tgltgl(f_tgl($obj->fields['TANGGAL_SPK_BANK']));
	$tgl_cair_kpr		= tgltgl(f_tgl($obj->fields['TANGGAL_CAIR_KPR'])); 
	$tgl_retensi		= tgltgl(f_tgl($obj->fields['TANGGAL_RETENSI'])); 
	$status_kompensasi	= $obj->fields['STATUS_KOMPENSASI'];
	$tanda_jadi			= $obj->fields['TANDA_JADI'];
	$status_spp			= $obj->fields['STATUS_SPP'];
	$tgl_proses			= tgltgl(f_tgl($obj->fields['TANGGAL_PROSES']));
	$tgl_tanda_jadi		= tgltgl(f_tgl($obj->fields['TANGGAL_TANDA_JADI']));
	$redistribusi		= $obj->fields['SPP_REDISTRIBUSI'];
	$tgl_redistribusi	= tgltgl(f_tgl($obj->fields['SPP_REDISTRIBUSI_TANGGAL']));
	$keterangan			= $obj->fields['KETERANGAN'];	
	$status_otorisasi	= $obj->fields['OTORISASI'];	
	
	
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
		KODE_BLOK = '$id'");
	$r_kode_desa			= $obj->fields['KODE_DESA'];
	$r_kode_lokasi			= $obj->fields['KODE_LOKASI'];
	$r_kode_unit			= $obj->fields['KODE_UNIT'];
	$r_kode_sk_tanah		= $obj->fields['KODE_SK_TANAH'];
	$r_kode_faktor			= $obj->fields['KODE_FAKTOR'];
	$r_kode_tipe			= $obj->fields['KODE_TIPE'];
	$r_kode_sk_bangunan		= $obj->fields['KODE_SK_BANGUNAN'];
	$r_kode_penjualan		= $obj->fields['KODE_PENJUALAN'];
	
	$r_nama_desa			= $obj->fields['NAMA_DESA'];
	$r_lokasi				= $obj->fields['LOKASI'];
	$r_jenis_unit			= $obj->fields['JENIS_UNIT'];
	$r_harga_tanah_sk		= $obj->fields['HARGA_TANAH_SK'];
	$r_faktor_strategis		= $obj->fields['FAKTOR_STRATEGIS'];
	$r_tipe_bangunan		= $obj->fields['TIPE_BANGUNAN'];
	$r_harga_bangunan_sk	= $obj->fields['HARGA_BANGUNAN_SK'];
	$r_jenis_penjualan		= $obj->fields['JENIS_PENJUALAN'];
	
	$r_tgl_bangunan			= tgltgl(f_tgl($obj->fields['TGL_BANGUNAN']));
	$r_tgl_selesai			= tgltgl(f_tgl($obj->fields['TGL_SELESAI']));
	$r_progress				= $obj->fields['PROGRESS'];
	$r_class				= $obj->fields['CLASS'];
	$r_status_gambar_siteplan	= $obj->fields['STATUS_GAMBAR_SITEPLAN'];
	$r_status_gambar_lapangan	= $obj->fields['STATUS_GAMBAR_LAPANGAN'];
	$r_status_gambar_gs		= $obj->fields['STATUS_GAMBAR_GS'];
	$r_program				= $obj->fields['PROGRAM'];
	
	$r_luas_tanah			= $obj->fields['LUAS_TANAH'];
	$r_base_harga_tanah		= $obj->fields['BASE_HARGA_TANAH'];
	$r_nilai_tambah			= $obj->fields['NILAI_TAMBAH'];
	$r_nilai_kurang			= $obj->fields['NILAI_KURANG'];
	$r_fs_harga_tanah		= $obj->fields['FS_HARGA_TANAH'];
	$r_disc_tanah			= $obj->fields['DISC_TANAH'];
	$r_disc_harga_tanah		= $obj->fields['DISC_HARGA_TANAH'];
	$r_ppn_tanah			= $obj->fields['PPN_TANAH'];
	$r_ppn_harga_tanah		= $obj->fields['PPN_HARGA_TANAH'];
	$r_harga_tanah			= $r_base_harga_tanah + $r_fs_harga_tanah - $r_disc_harga_tanah + $r_ppn_harga_tanah;
	
	$r_luas_bangunan		= $obj->fields['LUAS_BANGUNAN'];
	$r_base_harga_bangunan	= $obj->fields['BASE_HARGA_BANGUNAN'];
	$r_fs_harga_bangunan	= 0;
	$r_disc_bangunan		= $obj->fields['DISC_BANGUNAN'];
	$r_disc_harga_bangunan	= $obj->fields['DISC_HARGA_BANGUNAN'];
	$r_ppn_bangunan			= $obj->fields['PPN_BANGUNAN'];
	$r_ppn_harga_bangunan	= $obj->fields['PPN_HARGA_BANGUNAN'];
	$r_harga_bangunan		= $r_base_harga_bangunan + $r_fs_harga_bangunan - $r_disc_harga_bangunan + $r_ppn_harga_bangunan;
	
	$r_progres				= $obj->fields['PROGRESS'];
	$r_base_total_harga		= $r_base_harga_tanah + $r_base_harga_bangunan + $r_fs_harga_tanah;
	$r_base_nilai_potongan	= $r_disc_harga_bangunan + $r_disc_harga_tanah;
	$r_base_potongan		= ($r_base_nilai_potongan / $r_base_total_harga)*100;
	$r_harga_net			= $r_base_total_harga - $r_base_nilai_potongan;
	$r_base_nilai_ppn		= $r_ppn_harga_tanah + $r_ppn_harga_bangunan;
	$r_base_ppn				= ($r_base_nilai_ppn / $r_harga_net)*100;
	$r_harga_setelah_ppn	= $r_harga_net + $r_base_nilai_ppn;
	$r_base_sisa_1			= $r_harga_setelah_ppn - $jumlah_kpr;
	$r_base_sisa_2			= $r_base_sisa_1 - $tanda_jadi;
}
	
	
?>