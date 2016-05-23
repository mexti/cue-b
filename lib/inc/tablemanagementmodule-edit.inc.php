<?php
/**************************************************************************************************************
 Modal TableManagementModule-Edit
			The edit modal for the Table Management module allows editing tables.
 **************************************************************************************************************/
defined('Qb_Started') || die("No use starting a class without an include");

if(!function_exists('loadSelectionQuery')) {
	function loadSelectionQuery($db,$table) {
		$db->select("`name`")->from("`tables`")->where("`id`='{$table}'");
		$targettable = $db->loadObject();
		$db->select("`id`,`title`")->from("`{$targettable->name}`")->order("`title`");
		return $db->load();
	}
}

function loadEditModal($table) {
	global $_config;
	$db = new Database($_config->database);
	$db->connect();
	$db->select("*")->from("`tablefields`")->where("`table`='{$table->id}'");
	$fields = $db->load();
?>
<div class="modal fade" id="tablemanagementmodule-edit" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id="editForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Modify <?php echo $table->singular; ?></h4>
				</div>
				<div class="modal-body">
<?php
foreach($fields as $field) {
	switch($field['fieldtype']) {
		case '1': // Key
?>
					<input type="hidden" name="<?php echo $field['name']; ?>" />
<?php
			break;
		case '2': // Select
		case '6': // Select optional
		case '7': // Select multiple
?>
					<div class="form-group">
						<label for="field-<?php echo $field['name']; ?>"><?php echo $field['title']; ?></label>
						<select class="form-control" name="<?php echo $field['name']; ?>" id="field-<?php echo $field['name']; ?>" placeholder="<?php echo $field['title']; ?>">
<?php
			if($field['fieldtype']=='6') {
?>
							<option value="null" selected>-- None --</option>
<?php
			}
			$options = loadSelectionQuery($db,$field['targettable']);
			foreach($options as $option) {
?>
							<option value="<?php echo $option['id']; ?>"><?php echo $option['title']; ?></option>
<?php
			}
?>
						</select>
					</div>
<?php
			break;
		case '4': // Toggle
?>
					<div class="form-group">
						<label><?php echo $field['title']; ?></label><br />
						<div class="btn-group" role="group" data-toggle="buttons">
							<label id="field-<?php echo $field['name']; ?>-1" class="btn btn-success-outline"><input type="radio" name="<?php echo $field['name']; ?>" value="1" autocomplete="off" />Yes</label>
							<label id="field-<?php echo $field['name']; ?>-0" class="btn btn-danger-outline"><input type="radio" name="<?php echo $field['name']; ?>" value="0" autocomplete="off" />No</label>
						</div>
					</div>
<?php
			break;
		case '3': // Text input
		default:
?>
					<div class="form-group">
						<label for="field-<?php echo $field['name']; ?>"><?php echo $field['title']; ?></label>
						<input type="text" class="form-control" name="<?php echo $field['name']; ?>" id="field-<?php echo $field['name']; ?>" placeholder="<?php echo $field['title']; ?>" />
					</div>
<?php
	}
}
?>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-success" id="confirm-change">Confirm</button>
				</div>
			</form>
		</div>
	</div>
</div>
<?php
}

loadEditModal($this->target);
?>
