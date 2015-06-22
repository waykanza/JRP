<div class="title-page">RENCANA REALISASI SELURUH BLOK</div>

<form name="form" id="form" method="post">
<table class="t-control wauto">
<tr>
<td width="100">Kriteria</td><td width="10">:</td>
	<td>
		<select name="field1" id="field1" class="wauto">
			<option value="all"> Keseluruhan </option>
			<option value="periode"> Periode SPP </option>
		</select>
	</td>
</tr>
<tr>	
	<td width="100">Periode SPP</td><td width="10">:</td>
	<td><input type="text" name="periode_awal" id="periode_awal" class="apply dd-mm-yyyy" size="15" value=""> s/d
	<input type="text" name="periode_akhir" id="periode_akhir" class="apply dd-mm-yyyy" size="15" value=""></td>
</tr>
<tr>
	<td>Jumlah Baris</td><td>:</td>
	<td>
		<input type="text" name="per_page" size="3" id="per_page" class=" apply text-center" value="20">
		<input type="button" name="apply" id="apply" value=" Apply ">
	</td>
</tr>
<tr>
	<td>Total Data</td><td>:</td>
	<td id="total-data"></td>
</tr>
</table>

<script type="text/javascript">
jQuery(function($) {
	/* -- FILTER -- */
	
	$(document).on('keypress', '.apply', function(e) {
		var code = (e.keyCode ? e.keyCode : e.which);
		if (code == 13) { $('#apply').trigger('click'); return false; }
	});
	
	/* -- BUTTON -- */
	$(document).on('click', '#apply', function(e) {
		e.preventDefault();
		if (jQuery('#periode_awal').val() == '') {
			alert('Masukkan periode surat !');
			jQuery('#periode_awal').focus();
		}
		else if (jQuery('#periode_akhir').val() == '') {
			alert('Masukkan periode surat !');
			jQuery('#periode_akhir').focus();
		}
		else {
			loadData();
		}	
		return false;
	});
	
		
	$(document).on('click', '#excel', function(e) {
		e.preventDefault();
		location.href = base_marketing + 'kredit/pelaporan/laporan_kuitansi_lain/print_report_kuitansi.php?' + $('#form').serialize();
		return false;
	});
	
	$(document).on('click', '#print', function(e) {
		e.preventDefault();
		var url = base_marketing + 'kredit/pelaporan/laporan_kuitansi_lain/laporan_kuitansi_lain_print.php?' + $('#form').serialize();
		open_print(url)
		return false;
	});
	
	$(document).on('click', '#next_page', function(e) {
		e.preventDefault();
		var total_page = parseInt($('#total_page').val()),
			page_num = parseInt($('.page_num').val()) + 1;
		if (page_num <= total_page)
		{
			$('.page_num').val(page_num);
			$('#apply').trigger('click');
		}
	});
	
	$(document).on('click', '#prev_page', function(e) {
		e.preventDefault();
		var page_num = parseInt($('.page_num').val()) - 1;
		if (page_num > 0)
		{
			$('.page_num').val(page_num);
			$('#apply').trigger('click');
		}
	});
	
	
});

function loadData()
{
	if (popup) { popup.close(); }
	var data = jQuery('#form').serialize();
	jQuery('#t-detail').load(base_marketing + 'collection_tunai/laporan/rencana_realisasi_blok/rencana_realisasi_blok_load.php', data);	
	return false;
}

</script>

<div id="t-detail"></div>
</form>