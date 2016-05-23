$(document).ready(function() {
	$('#tablegroupmanagementmodule-table').on('click','[data-toggle=link]',function() {
		$(location).attr('href',$(this).attr('data-target'));
	});
});