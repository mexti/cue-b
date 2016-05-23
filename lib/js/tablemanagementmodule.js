$('#tablemanagementmodule-table').ready(function() {
	$.ajax({
		url: 'lib/ajax/tablemanagementmodule-table.php',
		type: 'POST',
		dataType: 'html',
		success: function(result) {
			$('#tablemanagementmodule-table').html(result);
		}
	});
	$('#tablemanagementmodule-table').on('click','.select-field',function() {
		$.ajax({
			url: 'lib/ajax/tablemanagementmodule-table.php',
			type: 'POST',
			data: {
				order: $(this).attr('data-order'),
				sort: $(this).attr('data-sort')
			},
			dataType: 'html',
			success: function(result) {
				$('#tablemanagementmodule-table').html(result);
			}
		});
	});
	$('#tablemanagementmodule-table').on('click','.select-page',function() {
		$.ajax({
			url: 'lib/ajax/tablemanagementmodule-table.php',
			type: 'POST',
			data: {
				page: $(this).attr('data-page')
			},
			dataType: 'html',
			success: function(result) {
				$('#tablemanagementmodule-table').html(result);
			}
		});
	});
	$('#tablemanagementmodule-table').on('click','.select-item',function() {
		var item = $.parseJSON($(this).attr('data-json'));
		$('#editForm select').each(function() {
			$(this).find('option[value='+item[$(this).attr('name')]+']').prop('selected',true);
		});
		$('#editForm input[type=hidden]').each(function() {
			$(this).val(item[$(this).attr('name')]);
		});
		$('#editForm input[type=text]').each(function() {
			$(this).val(item[$(this).attr('name')]);
		});
		$('#editForm input[type=radio]').each(function() {
			if($(this).val()==item[$(this).attr('name')]) {
				$(this).prop('checked',true);
				$(this).parent().addClass('active');
			} else {
				$(this).prop('checked',false);
				$(this).parent().removeClass('active');
			}
		});
		$('#tablemanagementmodule-edit').modal('show');
	});
});
$(document).ready(function() {
	$('#editForm ')
	$('#addForm').on('submit',function() {
		event.preventDefault();
		$.ajax({
			url: 'lib/ajax/tablemanagementmodule-add.php',
			type: 'POST',
			data: $(this).serialize(),
			dataType: 'html',
			success: function(result) {
				$('#message').append('<div class="alert alert-success fade in"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><i class="fa fa-check"></i> Successfully added this item</div>');
				$('#message .alert').delay(3000).fadeOut('slow', function() {
					$(this).alert('close');
				});
				$('#tablemanagementmodule-table').html(result);
			}
		});
		$('#tablemanagementmodule-add').modal('hide');
	});
	$('#editForm').on('submit',function() {
		event.preventDefault();
		$.ajax({
			url: 'lib/ajax/tablemanagementmodule-edit.php',
			type: 'POST',
			data: $(this).serialize(),
			dataType: 'html',
			success: function(result) {
				$('#message').append('<div class="alert alert-success fade in"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><i class="fa fa-check"></i> Successfully modified this item</div>');
				$('#message .alert').delay(3000).fadeOut('slow', function() {
					$(this).alert('close');
				});
				$('#tablemanagementmodule-table').html(result);
			}
		});
		$('#tablemanagementmodule-edit').modal('hide');
	});
	$('.select-search').bind('change keyup input',function() {
		$.ajax({
			url: 'lib/ajax/tablemanagementmodule-table.php',
			type: 'POST',
			data: {
				find: $(this).val(),
				page: '0'
			},
			dataType: 'html',
			success: function(result) {
				$('#tablemanagementmodule-table').html(result);
			}
		});
	});
	$('.select-limit').on('change',function() {
		$.ajax({
			url: 'lib/ajax/tablemanagementmodule-table.php',
			type: 'POST',
			data: {
				size: $(this).val(),
				page: '0'
			},
			dataType: 'html',
			success: function(result) {
				$('#tablemanagementmodule-table').html(result);
			}
		});
	});
});