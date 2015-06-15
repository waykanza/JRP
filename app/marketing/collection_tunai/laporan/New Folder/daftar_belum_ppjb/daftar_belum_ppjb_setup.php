<div class="title-page">LAPORAN DAFTAR SPP BELUM PPJB</div>

<form name="form" id="form" method="post">
<table class="t-control wauto">
<tr>
	<td width="100">Jumlah Baris</td><td>:</td>
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
			alert('Masukkan periode laporan!');
			jQuery('#periode_awal').focus();
			return false;
		}
		else if (jQuery('#periode_akhir').val() == '') {
			alert('Masukkan periode laporan!');
			jQuery('#periode_akhir').focus();
			return false;
		}
		loadData();
		return false;
	});
	
	$(document).on('click', '#excel', function(e) {
		e.preventDefault();
		location.href = base_ppjb_laporan + 'daftar_belum_ppjb/daftar_belum_ppjb_xls.php?' + $('#form').serialize();
		return false;
	});
	
	$(document).on('click', '#print', function(e) {
		e.preventDefault();
		var url = base_ppjb_laporan + 'daftar_belum_ppjb/daftar_belum_ppjb_print.php?' + $('#form').serialize();
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
	jQuery('#t-detail').load(base_ppjb_laporan + 'daftar_belum_ppjb/daftar_belum_ppjb_load.php', data);	
	return false;
}
</script>

<div id="t-detail"></div>
</form>