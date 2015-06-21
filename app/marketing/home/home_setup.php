<script type="text/javascript">
jQuery(function($) {
	
	/* -- BUTTON -- */
	$(document).on('click', '#detail_distribusi', function(e) {
		e.preventDefault();
		return false;
	});
	
	$(document).on('click', '#detail_ppjb', function(e) {
		if (popup) { popup.close(); }
		jQuery('#t-detail').load(base_marketing + 'ppjb/laporan/daftar_belum_ppjb/daftar_belum_ppjb_setup.php', data);	
		var data = jQuery('#form').serialize();
		return false;
	});
	
	loadData();
});
function loadData()
{
	if (popup) { popup.close(); }
	jQuery('#t-detail').load(base_marketing + 'home/home_load.php', data);	
	var data = jQuery('#form').serialize();
	return false;
}

</script>
<div id="t-detail"></div>
