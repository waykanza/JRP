<form name="form" id="form" method="post">
<table class="t-control2">
    <tr>
      <td style="font-size:20px;text-align:center;font-weight:bold" >Daftar Tagihan Bulan <?php echo substr(fm_date(date("Y-m-d")),3);  ?></td>
    </tr>
</table>	
<table class="t-control">
	<tr>
      <td>Tanggal</td><td>:</td>
	  <td><?php echo fm_date(date("Y-m-d"));  ?></td>
    </tr>
	<tr>
	<td>Status Distribusi</td><td>:</td>
		<td>
			<input type="radio" name="status_distribusi" id="sbb" class="status" value="1" checked="true"> <label for="sbb">Sudah</label>
			<input type="radio" name="status_distribusi" id="sbs" class="status" value="0"> <label for="sbs">Belum</label>
		</td>
	</tr>
    <tr>
		<td width="100">Jumlah Baris</td><td width="10">:</td>
		<td>
			<input type="text" name="per_page" size="3" id="per_page" class=" apply text-center" value="20">
			<input type="button" name="apply" id="apply" value=" Apply ">
			<input type="hidden" name="act" id="act" value="Surat">
		</td>
	</tr>
	<tr>
		<td>Total Data</td><td>:</td>
		<td id="total-data"></td>
	</tr>
	<tr>
	<td width="100">Download Tagihan</td><td width="10">:</td>
		<td>
			<select name="pilih" id="pilih" class="wauto">
				<option value="bca"> Bank BCA </option>
				<option value="mandiri"> Bank Mandiri </option>
			</select>
			<input type="button" id="download" value=" Download ">		
		</td>
	</tr>
	
</table>

<script type="text/javascript">
jQuery(function($) {
	/* -- FILTER -- */
	var distribusi = 1;
	var bank = 'bca';
	
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
	
	$(document).on('click', '#pilih', function(e) {
		e.preventDefault();
		if($(this).val() == 'bca'){
			bank = 'bca';
		} else {
			bank = 'mandiri';
		}
		return false;
	});
	

	$(document).on('click', '#download', function(e) {
		e.preventDefault();
		if(distribusi == 0){
			alert('Maaf datfar tagihan ini belum didistribusi');
		}
		else{
			if(bank == 'bca'){
			location.href = base_marketing + 'collection_tunai/transaksi/download_tagihan/download_tagihan_bca_excel.php?' + $('#form').serializeArray();
			}
			else{
			location.href = base_marketing + 'collection_tunai/transaksi/download_tagihan/download_tagihan_mandiri_excel.php?' + $('#form').serializeArray();
			}
		}
		
		return false;
	});
	
	$('input:radio[name="status_distribusi"]').change(function(e){
		e.preventDefault();
		if($(this).val() == 0){
			distribusi = 0;
		}
		else if($(this).val() == 1){
			distribusi = 1;
		}
		loadData();
		return false;
	});
	
	loadData();
});

function loadData()
{
	if (popup) { popup.close(); }
	var data = jQuery('#form').serialize();
	jQuery('#t-detail').load(base_marketing + 'collection_tunai/transaksi/download_tagihan/download_tagihan_load.php', data);
	return false;
}


</script>

<div id="t-detail"></div>
</form>