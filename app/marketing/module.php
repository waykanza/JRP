<?php 
switch (trim(base64_decode($cmd)))
{
	# Master
	case 'A01_home' 					: die_mod('PM01'); include('marketing/home/home_setup.php');break;
	case 'A01_lokasi' 					: die_mod('PM01'); include('marketing/master/lokasi/lokasi_setup.php');break;
	case 'A01_desa' 					: die_mod('PM09'); include('marketing/master/desa/desa_setup.php');break;
	case 'A01_tipe' 					: die_mod('PM02'); include('marketing/master/tipe/tipe_setup.php');break;
	case 'A01_faktor_strategis'			: die_mod('PM03'); include('marketing/master/faktor_strategis/faktor_strategis_setup.php');break;
	case 'A01_jenis_unit' 				: die_mod('PM04'); include('marketing/master/jenis_unit/jenis_unit_setup.php');break;
	case 'A01_jenis_penjualan' 			: die_mod('PM05'); include('marketing/master/jenis_penjualan/jenis_penjualan_setup.php');break;
	case 'A01_lembaga_keuangan' 		: die_mod('PM06'); include('marketing/master/lembaga_keuangan/lembaga_keuangan_setup.php');break;
	case 'A01_harga_tanah' 				: die_mod('PM07'); include('marketing/master/harga_tanah/harga_tanah_setup.php');break;
	case 'A01_harga_bangunan' 			: die_mod('PM08'); include('marketing/master/harga_bangunan/harga_bangunan_setup.php');break;
	case 'A01_jenis_pembayaran'			: die_mod('PM10'); include('marketing/master/jenis_pembayaran/jenis_pembayaran_setup.php');break;
	case 'A01_agen'						:  include('marketing/master/agen/agen_setup.php');break;
	case 'A01_koordinator'				:  include('marketing/master/koordinator/koordinator_setup.php');break;
	
	# Operasional
	
	case 'A01_persediaan_awal'					: include('marketing/operasional/persediaan_awal/stock_awal/persediaan_awal_setup.php');break;
	case 'A01_persediaan_siap_jual'				: include('marketing/operasional/persediaan_siap_jual/persediaan_siap_jual_setup.php');break;
	case 'A01_persediaan_terjual'				: die_mod ('PO03'); include('marketing/operasional/persediaan_terjual/persediaan_terjual_setup.php');break;
	/*
	case 'A01_rencana_p_unit_p_tahun'			: die_mod('zzzz'); include('marketing/operasional/rencana_p_unit_p_tahun/rencana_p_unit_p_tahun_setup.php');break;
	case 'A01_rencana_p_tipe_p_tahun'			: die_mod('zzzz'); include('marketing/operasional/rencana_p_tipe_p_tahun/rencana_p_tipe_p_tahun_setup.php');break;
	case 'A01_rencana_p_lokasi_p_tahun'			: die_mod('zzzz'); include('marketing/operasional/rencana_p_lokasi_p_tahun/rencana_p_lokasi_p_tahun_setup.php');break;
	case 'A01_rencana_p_jenis_unit_p_tahun'		: die_mod('zzzz'); include('marketing/operasional/rencana_p_jenis_unit_p_tahun/rencana_p_jenis_unit_p_tahun_setup.php');break;
	*/
	# Transaksi
	
	//case 'A01_informasi_persediaan'	: die_mod('PT01'); include('marketing/transaksi/informasi_persediaan/informasi_persediaan_setup.php');break;
	//case 'A01_reserve_persediaan'	: die_mod('PT02'); include('marketing/transaksi/reserve_persediaan/reserve_persediaan_setup.php');break;
	case 'A01_spp'					: die_mod('PT03'); include('marketing/transaksi/spp/spp_setup.php');break;

	case 'A01_otorisasi_penjualan'	:  include('marketing/transaksi/otorisasi_penjualan/otorisasi_penjualan_setup.php');break;
	
	case 'A01_edit_stok_penjualan'	: die_mod('PT05'); include('marketing/transaksi/edit_stok_penjualan/edit_stok_penjualan_setup.php');break;
	
	case 'A01_edit_spp_penjualan'	: die_mod('PT05'); include('marketing/transaksi/edit_spp_penjualan/edit_spp_penjualan_setup.php');break;

	case 'A01_serah_terima'			: die_mod('PT12'); include('marketing/transaksi/serah_terima/serah_terima_setup.php');break;
	
	case 'A01_informasi_bangunan'	: die_mod('PT13'); include('marketing/transaksi/informasi_bangunan/informasi_bangunan_setup.php');break;
	# Laporan
	
	case 'A01_penjualan_p_unit'				: die_mod('PL01'); include('marketing/laporan/penjualan_p_unit/penjualan_p_unit_setup.php');break;
	case 'A01_penjualan_p_class'				: die_mod('PL03'); include('marketing/laporan/penjualan_p_class/penjualan_p_class_setup.php');break;
	case 'A01_penjualan_p_lokasi'				: die_mod('PL02'); include('marketing/laporan/penjualan_p_lokasi/penjualan_p_lokasi_setup.php');break;
	case 'A01_laporan_reserve'				: include('marketing/laporan/laporan_reserve/laporan_reserve_setup.php');break;
	case 'A01_laporan_persediaan_stok'				: include('marketing/laporan/laporan_persediaan_stok/laporan_persediaan_stok_setup.php');break;
	/*
	case 'A01_penjualan_p_lokasi'			: die_mod('zzzz'); include('marketing/laporan/penjualan_p_lokasi/penjualan_p_lokasi_setup.php');break;
	case 'A01_penjualan_p_class'			: die_mod('zzzz'); include('marketing/laporan/penjualan_p_class/penjualan_p_class_setup.php');break;
	case 'A01_omset_p_unit_p_tahun'			: die_mod('zzzz'); include('marketing/laporan/omset_p_unit_p_tahun/omset_p_unit_p_tahun_setup.php');break;
	case 'A01_omset_p_tipe_p_tahun'			: die_mod('zzzz'); include('marketing/laporan/omset_p_tipe_p_tahun/omset_p_tipe_p_tahun_setup.php');break;
	case 'A01_omset_p_lokasi_p_tahun'		: die_mod('zzzz'); include('marketing/laporan/omset_p_lokasi_p_tahun/omset_p_lokasi_p_tahun_setup.php');break;
	case 'A01_omset_p_jenis_unit_p_tahun'	: die_mod('zzzz'); include('marketing/laporan/omset_p_jenis_unit_p_tahun/omset_p_jenis_unit_p_tahun_setup.php');break;
	case 'A01_laporan_reserve'				: die_mod('zzzz'); include('marketing/laporan/laporan_reserve/laporan_reserve_setup.php');break;
	case 'A01_laporan_persediaan_stok'		: die_mod('zzzz'); include('marketing/laporan/laporan_persediaan_stok/laporan_persediaan_stok_setup.php');break;
	*/
	
	# Utilitas
	/*
	case 'A01_ubah_password'		: die_mod('PU01'); include('marketing/utilitas/ubah_password/ubah_password_setup.php');break;
	
	case 'A01_configurasi_sistem'	: die_mod('zzzz'); include('marketing/utilitas/configurasi_sistem/configurasi_sistem_setup.php');break;
	case 'A01_backup'				: die_mod('zzzz'); include('marketing/utilitas/backup/backup_setup.php');break;
	case 'A01_recovery'				: die_mod('zzzz'); include('marketing/utilitas/recovery/recovery_setup.php');break;
	*/
	## security_management
		case 'A01_users'			: die_mod('PU05'); include('marketing/utilitas/security_management/users/users_setup.php');break;
		case 'A01_aplications'		: die_mod('PU05'); include('marketing/utilitas/security_management/aplications/aplications_setup.php');break;
		case 'A01_modules'			: die_mod('PU05'); include('marketing/utilitas/security_management/modules/modules_setup.php');break;
		case 'A01_rights'			: die_mod('PU05'); include('marketing/utilitas/security_management/rights/rights_setup.php');break;
		
	/*
	case 'A01_nomor_customer'		: die_mod('zzzz'); include('marketing/utilitas/nomor_customer/nomor_customer_setup.php');break;
	case 'A01_kartu_pembeli'		: die_mod('zzzz'); include('marketing/utilitas/kartu_pembeli/kartu_pembeli_setup.php');break;
	case 'A01_spp_belum_distribusi'	: die_mod('zzzz'); include('marketing/utilitas/spp_belum_distribusi/spp_belum_distribusi_setup.php');break;
	case 'A01_spp_redistribusi'		: die_mod('zzzz'); include('marketing/utilitas/spp_redistribusi/spp_redistribusi_setup.php');break;
	case 'A01_ekspor_data_spp'		: die_mod('zzzz'); include('marketing/utilitas/ekspor_data_spp/ekspor_data_spp_setup.php');break;
*/

}
?>
<?php 
switch (trim(base64_decode($cmd)))
{
	# Master
	
	case 'AO1_informasi_pembeli' 		: die_mod('PI01'); die_ha('PI01', 'R'); include('marketing/collection_tunai/master/informasi_pembeli/informasi_pembeli_setup.php');break;
	case 'AO1_hari_libur' 				: die_mod('PI02'); die_ha('PI02', 'R'); include('marketing/collection_tunai/master/cs_hari_libur/cs_hari_libur_setup.php');break;
	
	# Transaksi
	## denda_keterlambatan
		case 'A01_entry' 					: die_mod('PJ01'); die_ha('PJ01', 'R'); include('marketing/collection_tunai/transaksi/denda_keterlambatan/entry/entry_setup.php');break;
		case 'A01_otoritas'					: die_mod('PJ02'); die_ha('PJ02', 'R'); include('marketing/collection_tunai/transaksi/denda_keterlambatan/otorisasi/otorisasi_setup.php');break;
	case 'A01_virtual_account'			: include('marketing/collection_tunai/transaksi/virtual_account/virtual_account_setup.php');break;
	case 'A01_ver_kwitansi_col'			: include('marketing/collection_tunai/transaksi/pembayaran/pembayaran_setup.php');break;
	case 'A01_ver_kwitansi_keu' 		: die_mod('PJ04'); die_ha('PJ04', 'R'); include('marketing/collection_tunai/transaksi/ver_kwitansi_keu/ver_kwitansi_keu_setup.php');break;
	case 'A01_pemulihan_wanprestasi'	: include('collection_tunai/transaksi/pemulihan_wanprestasi/pemulihan_wanprestasi_setup.php');break;
	case 'A01_memo_pembatalan'			: include('collection_tunai/transaksi/memo_pembatalan/memo_pembatalan_setup.php');break;
	case 'A01_download_tagihan'			: include('collection_tunai/transaksi/download_tagihan/download_tagihan_setup.php');break;
	
	#Surat
	case 'A01_pemberitahuan_jatuh_tempo'	: include('marketing/collection_tunai/surat/pemberian_jatuh_tempo/pemberian_jatuh_tempo_setup.php');break;
	case 'A01_jatuh_tempo'					: include('marketing/collection_tunai/surat/jatuh_tempo/jatuh_tempo_setup.php');break;
	case 'A01_somasi_pertama'				: include('marketing/collection_tunai/surat/somasi_satu/somasi_satu_setup.php');break;
	case 'A01_somasi_kedua'					: include('marketing/collection_tunai/surat/somasi_dua/somasi_dua_setup.php');break;
	case 'A01_somasi_ketiga'				: include('marketing/collection_tunai/surat/somasi_tiga/somasi_tiga_setup.php');break;
	case 'A01_wanprestasi'					: include('marketing/collection_tunai/surat/wanprestasi/wanprestasi_setup.php');break;
	case 'A01_administrasi_pembatalan'		: include('marketing/collection_tunai/surat/pembatalan/pembatalan_setup.php');break;
	
	#Surat SPK
	case 'A01_pemberitahuan_spk'			: include('marketing/collection_tunai/surat/spk/pemberitahuan_spk/pemberitahuan_spk_setup.php');break;
	case 'A01_perpanjangan_spk'				: include('marketing/collection_tunai/surat/spk/perpanjangan_spk/perpanjangan_spk_setup.php');break;
	case 'A01_pembatalan_spk'				: include('marketing/collection_tunai/surat/spk/pembatalan_spk/pembatalan_spk_setup.php');break;
	case 'A01_pemberitahuan_akad'			: include('marketing/collection_tunai/surat/spk/pemberitahuan_akad/pemberitahuan_akad_setup.php');break;
	case 'A01_perpanjangan_akad'			: include('marketing/collection_tunai/surat/spk/perpanjangan_akad/perpanjangan_akad_setup.php');break;
	case 'A01_pembatalan_akad'				: include('marketing/collection_tunai/surat/spk/pembatalan_akad/pembatalan_akad_setup.php');break;
	case 'A01_pemberitahuan_spk_review'		: include('marketing/collection_tunai/surat/spk/pemberitahuan_spk_review/pemberitahuan_spk_review_setup.php');break;
	case 'A01_pembatalan_spk_review'		: include('marketing/collection_tunai/surat/spk/pembatalan_spk_review/pembatalan_spk_review_setup.php');break;
	case 'A01_pemberitahuan_plafon'			: include('marketing/collection_tunai/surat/spk/pemberitahuan_plafon/pemberitahuan_plafon_setup.php');break;
	case 'A01_pembatalan_plafon'			: include('marketing/collection_tunai/surat/spk/pembatalan_plafon/pembatalan_plafon_setup.php');break;
	case 'A01_pemberitahuan_tolak_kredit'	: include('marketing/collection_tunai/surat/spk/pemberitahuan_tolak_kredit/pemberitahuan_tolak_kredit_setup.php');break;
	case 'A01_pembatalan_tolak_kredit'		: include('marketing/collection_tunai/surat/spk/pembatalan_tolak_kredit/pembatalan_tolak_kredit_setup.php');break;
	
	
	#Pelaporan
	case 'A01_proyeksi_penagihan'			: include('marketing/collection_tunai/laporan/proyeksi_penagihan/proyeksi_penagihan_setup.php');break;
	case 'A01_umur_piutang'					: include('marketing/collection_tunai/laporan/umur_piutang/umur_piutang_setup.php');break;
	case 'A01_spp_lunas_tahunan'			: include('marketing/collection_tunai/laporan/spp_lunas_tahunan/spp_lunas_tahunan_setup.php');break;
	case 'A01_pembebasan_denda'				: include('marketing/collection_tunai/laporan/pembebasan_denda/pembebasan_denda_setup.php');break;
	case 'A01_pembatalan_spp'				: include('marketing/collection_tunai/laporan/pembatalan_spp/pembatalan_spp_setup.php');break;
	case 'A01_surat_penagihan'				: include('marketing/collection_tunai/laporan/surat_penagihan/surat_penagihan_setup.php');break;
	case 'A01_daftar_memo_pembatalan'		: include('marketing/collection_tunai/laporan/daftar_memo_pembatalan/daftar_memo_pembatalan_setup.php');break;
	
	
	case 'A01_rencana_realisasi_blok'		: include('marketing/collection_tunai/laporan/rencana_realisasi_blok/rencana_realisasi_blok_setup.php');break;
	case 'A01_daftar_spp'					: include('marketing/collection_tunai/laporan/daftar_spp/daftar_spp_setup.php');break;
	
	
	/*
	# Pelaporan
	case 'C01_proyeksi_penagihan' 		: die_mod('COP01'); die_ha('COP01', 'R'); include('ppjb/laporan/daftar_ppjb/daftar_ppjb_setup.php');break;
	case 'C01_umur_piutang'				: die_mod('COP02'); die_ha('COP02', 'R'); include('ppjb/laporan/daftar_belum_ppjb/daftar_belum_ppjb_setup.php');break;
	case 'C01_spp_lunas_tahunan' 		: die_mod('COP07'); die_ha('COP07', 'R'); include('ppjb/laporan/daftar_ppjb/daftar_ppjb_setup.php');break;
	case 'C01_pembebasan_denda'			: die_mod('COP03'); die_ha('COP03', 'R'); include('ppjb/laporan/daftar_belum_ppjb/daftar_belum_ppjb_setup.php');break;
	case 'C01_pembatalan_spp'	 		: die_mod('COP04'); die_ha('COP04', 'R'); include('ppjb/laporan/daftar_ppjb/daftar_ppjb_setup.php');break;
	case 'C01_status_surat_penagihan'	: die_mod('COP05'); die_ha('COP05', 'R'); include('ppjb/laporan/daftar_belum_ppjb/daftar_belum_ppjb_setup.php');break;
	case 'C01_laporan_tanda_jadi' 		: die_mod('COP06'); die_ha('COP06', 'R'); include('ppjb/laporan/daftar_ppjb/daftar_ppjb_setup.php');break;
	case 'C01_laporan_lain_lain'		: die_mod('COP06'); die_ha('COP06', 'R'); include('ppjb/laporan/daftar_belum_ppjb/daftar_belum_ppjb_setup.php');break;
	*/
	
	# Lain-lain
	case 'A01_parameter_program'		: die_mod('CL01'); die_ha('CL01', 'R'); include('marketing/collection_tunai/lain/parameter/parameter_setup.php');break;
	case 'A01_registrasi_user'			: die_mod('COL02'); die_ha('COL02', 'R'); include('collection_tunai/lain/registrasi_user/users_setup.php');break;
	case 'A01_nomor_surat'				: die_mod('COL03'); die_ha('COL03', 'R'); include('collection_tunai/lain/nomor_surat/nomor_surat_setup.php');break;
	case 'A01_spp_tidak_valid'			: die_mod('CL04'); die_ha('CL04', 'R'); include('marketing/collection_tunai/lain/spp_tidak_valid/spp_tidak_valid_setup.php');break;
}
?>

<?php 
switch (trim(base64_decode($cmd)))
{
	# Master
	case 'A01_kelurahan' 				: die_mod('JB01'); die_ha('JB01', 'R'); include('marketing/ppjb/master/kelurahan/kelurahan_setup.php');break;
	case 'A01_kecamatan' 				: die_mod('JB02'); die_ha('JB02', 'R'); include('marketing/ppjb/master/kecamatan/kecamatan_setup.php');break;
	case 'A01_jenis_ppjb' 				: die_mod('JB03'); die_ha('JB03', 'R'); include('marketing/ppjb/master/jppjb/jppjb_setup.php');break;
	case 'A01_jenis_add_ppjb'			: die_mod('JB04'); die_ha('JB04', 'R'); include('marketing/ppjb/master/jappjb/jappjb_setup.php');break;
	case 'A01_tipe_bangunan'			: die_mod('JB05'); die_ha('JB05', 'R'); include('marketing/ppjb/master/tipe_bangunan/tipe_bangunan_setup.php');break;
	
	# Transaksi
	case 'A01_ppjb' 					: die_mod('JB06'); die_ha('JB06', 'R'); include('marketing/ppjb/transaksi/ppjb/ppjb_setup.php');break;
	case 'A01_verifikasi_ppjb'			: die_mod('JB07'); die_ha('JB07', 'R'); include('marketing/ppjb/transaksi/verifikasi/verifikasi_setup.php');break;
	case 'A01_pembatalan_ppjb'			: die_mod('JB08'); die_ha('JB08', 'R'); include('marketing/ppjb/transaksi/pembatalan/pembatalan_setup.php');break;
	case 'A01_pengalihan_hak'			: die_mod('JB09'); die_ha('JB09', 'R'); include('marketing/ppjb/transaksi/pengalihan_hak/pengalihan_hak_setup.php');break;	
	
	# Laporan
	case 'A01_daftar_ppjb' 				: die_mod('JB10'); die_ha('JB10', 'R'); include('marketing/ppjb/laporan/daftar_ppjb/daftar_ppjb_setup.php');break;
	case 'A01_daftar_spp_belum_ppjb'	: die_mod('JB11'); die_ha('JB11', 'R'); include('marketing/ppjb/laporan/daftar_belum_ppjb/daftar_belum_ppjb_setup.php');break;
	
	# Lain-lain
	case 'A01_parameter_program'		: die_mod('JB12'); die_ha('JB12', 'R'); include('marketing/ppjb/lain/parameter/parameter_setup.php');break;
	//case 'C03_registrasi_user'		: die_mod('JBLL2'); die_ha('JBLL2', 'R'); include('ppjb/lain/xxx/xxx_setup.php');break;
	//case 'C03_ubah_sandi' 			: include('ppjb/lain/xxx/xxx_setup.php');break;
}
?>

<?php 
switch (trim(base64_decode($cmd)))
{

	# Transaksi
	case 'A01_kuitansi' 				: include('marketing/kredit/transaksi/kuitansi/kuitansi_setup.php');break;	
	case 'A01_kuitansi_lain'			: include('marketing/kredit/transaksi/kuitansi_lain/kuitansi_lain_setup.php');break;	
	case 'A01_tanda_terima'				: include('marketing/kredit/transaksi/tanda_terima/tanda_terima_setup.php');break;
	
	# Pelaporan
	case 'A01_laporan_kuitansi'			: include('marketing/kredit/pelaporan/laporan_kuitansi/laporan_kuitansi_setup.php');break;
	case 'A01_laporan_kuitansi_lain'	: include('marketing/kredit/pelaporan/laporan_kuitansi_lain/laporan_kuitansi_lain_setup.php');break;
	case 'A01_progres_penerimaan'		: include('marketing/kredit/pelaporan/progres_penerimaan/progres_penerimaan_setup.php');break;
	case 'A01_kartu_kuning'				: include('marketing/kredit/pelaporan/kartu_kuning/kartu_kuning_setup.php');break;
	case 'A01_faktur_pajak'				: include('marketing/kredit/pelaporan/laporan_faktur_pajak/laporan_faktur_pajak_setup.php');break;
	
	# Utilitas
	case 'A01_parameter'				: include('marketing/kredit/utilitas/parameter/parameter_setup.php');break;
	case 'A01_denda'					: include('marketing/kredit/utilitas/denda/denda_setup.php');break;
	case 'A01_kartu_pembeli'			: include('marketing/kredit/utilitas/kartu_pembeli/kartu_pembeli_setup.php');break;
	case 'A01_penomoran_fp'				: include('marketing/kredit/utilitas/penomoran_fp/penomoran_fp_setup.php');break;

}

switch ($_SESSION['HOME'])
{
	case 'home' 					: die_mod('PM01'); include('marketing/home/home_setup.php');$_SESSION['HOME'] = '-';
	
}

?>