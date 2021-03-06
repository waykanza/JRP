<div class="title-page">MEMO PEMBATALAN</div>

<form name="form" id="form" method="post">
<table class="t-control">
<tr>
	<td width="100">NOMOR MEMO</td><td width="10">:</td>
	<td>
		<input type="text" size="43" name="nomor_memo" id="nomor_memo" class="apply" value="">
	</td>
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
		if (code == 13) { $('#apply').trigger('click'); }
	});
	
	/* -- BUTTON -- */
	$(document).on('click', '#apply', function(e) {
		e.preventDefault();
		loadData();
		return false;
	});
	
	$(document).on('keyup', '#no_va', function(e) {
		e.preventDefault();
		loadData();
		return false;
	});
	
	$(document).on('click', '#next_page', function(e) {
		e.preventDefault();
		var total_page = parseInt($('#total_page').val()), page_num = parseInt($('.page_num').val()) + 1;
		if (page_num <= total_page) { $('.page_num').val(page_num); $('#apply').trigger('click'); }
		return false;
	});

	$(document).on('click', '#prev_page', function(e) {
		e.preventDefault();
		var page_num = parseInt($('.page_num').val()) - 1;
		if (page_num > 0) { $('.page_num').val(page_num); $('#apply').trigger('click'); }
		return false;
	});

	$(document).on('click', '#tambah', function(e) {
		e.preventDefault();
		showPopup('TambahMemo', '');
		return false;
	});
	
	$(document).on('click', 'tr.onclick td:not(.notclick)', function(e) {
		e.preventDefault();
		var id = $(this).parent().attr('id');
		var coba = jQuery('#coba').val();
		showPopup('Detail', id);
		return false;
	});
	
	$(document).on('click', '#hapus', function(e) {
		e.preventDefault();
		var checked = $(".cb_data:checked").length;
		if (checked < 1) {
			alert('Pilih data yang akan dihapus.');
		} else if (confirm('Apa anda yakin akan menghapus data ini?')) {
			hapusData();
		}
		return false;
	});
	
	loadData();
});

function loadData()
{
	if (popup) { popup.close(); }
	var data = jQuery('#form').serialize();
	jQuery('#t-detail').load(base_marketing + 'collection_tunai/transaksi/memo_pembatalan/memo_pembatalan_load.php', data);
	return false;
}

function showPopup(act, id)
{
	var url =	base_marketing + 'collection_tunai/transaksi/memo_pembatalan/memo_pembatalan_popup.php' + '?act=' + act + '&id=' + id;
	setPopup(act + ' Data Memo Pembatalan', url, 700, 500);
	return false;
}

function hapusData()
{	
	var url		= base_marketing + 'collection_tunai/transaksi/memo_pembatalan/memo_pembatalan_proses.php?act=HapusMemo',
		data	= jQuery('#form').serializeArray();
	
	jQuery.post(url, data, function(result) {
		var list_id = result.act.join(', #');
		alert(result.msg);
		loadData();
	}, 'json');
	
	return false;
}
</script>

<div id="t-detail"></div>
</form>