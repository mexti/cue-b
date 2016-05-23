<?php
// Initialisation
if(isset($_GET['debug'])) {
	ini_set('display_errors',1);
	error_reporting(E_ALL);
}

// Load configuration
require_once($_SERVER['DOCUMENT_ROOT']."/.config.php");

// Start session
!isset($_config->sessionName) || session_name($_config->sessionName);
session_start();

// Autoload class
if(!function_exists('__autoload')) {
	function __autoload($class) {
		require_once("../class/{$class}.class.php");
		if(!class_exists($class))
			trigger_error("Could not load class {$class}",E_USER_ERROR);
	}
}

// Load session data
if($_SESSION['table']) {
	$table = $_SESSION['table'];
	isset($table->preferences) || $table->preferences = array('page'=>'0','size'=>'20','order'=>'title','sort'=>'ASC','find'=>'');
	!isset($_POST['page']) || $table->preferences['page'] = $_POST['page'];
	!isset($_POST['size']) || $table->preferences['size'] = $_POST['size'];
	!isset($_POST['order']) || $table->preferences['order'] = $_POST['order'];
	!isset($_POST['sort']) || $table->preferences['sort'] = $_POST['sort'];
	!isset($_POST['find']) || $table->preferences['find'] = $_POST['find'];
} else die("YOU ARE DEAD");														// REMINDER: Perhaps there is a nicer way to do this?

// Start the application
define('Qb_Started',true);

$db = new Database($_config->database);
$db->connect();
$fieldset = array();
$db = new Database($_config->database);
$db->connect();
$db->select("*")->from("`tablefields`")->where("`table`='{$table->id}' AND (`shownarrow` OR `showwide`) ORDER BY `order`");
$fields = $db->load();
?>
<table class="table table-hover table-condensed">
	<thead class="table-inverse">
		<tr>
<?php
$fieldset = "`t0`.`id`";
$joinset = $searchset = "";
$t = 0;
foreach($fields as $field) {
	if($field['fieldtype']==2) {
		$t++;
		$db->select("`name`")->from("`tables`")->where("`id`='{$field['targettable']}'");
		$targettable = $db->loadObject();
		$fieldset .= (empty($fieldset) ? "" : ",")."`t{$t}`.`title` AS `{$field['name']}`";
		$joinset .= (empty($joinset) ? "" : " INNER JOIN ")."`{$targettable->name}` AS `t{$t}` ON `t0`.`{$field['name']}`=`t{$t}`.`id`";
		$searchset .= (empty($searchset) ? "" : " OR ")."`{$field['name']}` LIKE '%{$table->preferences['find']}%'";
	} else {
		$fieldset .= (empty($fieldset) ? "" : ",")."`t0`.`{$field['name']}` AS `{$field['name']}`";
		$searchset .= (empty($searchset) ? "" : " OR ")."`{$field['name']}` LIKE '%{$table->preferences['find']}%'";
	}
?>
			<th class="select-field" data-toggle="form-submit" data-target="tableSelect" data-order="<?php echo $field['name']; ?>" data-sort="<?php echo ($table->preferences['sort']=='DESC' || $table->preferences['order']!=$field['name'] ? "ASC" : "DESC"); ?>">
				<span class="pull-left"><?php echo $field['title']; ?></span>
				<span class="pull-right"><?php if($table->preferences['order']==$field['name']) { if($table->preferences['sort']=='DESC') echo '<i class="fa fa-caret-up"></i>'; else echo '<i class="fa fa-caret-down"></i>'; } else { echo '<i class="fa fa-sort"></i>'; } ?></span>
			</th>
<?php
}
$db->select("COUNT(`id`) AS count")->from("`{$table->name}`")->where(empty($table->preferences['find']) ? "'1'" : $searchset);
$totals = $db->loadObject();
$db->select($fieldset)->from("`{$table->name}` AS `t0`")->having(empty($table->preferences['find']) ? "'1'" : $searchset);
empty($joinset) || $db->innerjoin($joinset);
$db->limit($table->preferences['size'])->offset($table->preferences['size']*$table->preferences['page']);
$items = $db->load();
?>
		</tr>
	</thead>
	<tbody>
<?php
foreach($items as $item) {
	$db->select("*")->from("`{$table->name}`")->where("`id`='{$item['id']}'");
	$values = $db->loadObject();
?>
		<tr class="select-item" data-json="<?php echo htmlspecialchars(json_encode($values)); ?>" data-target="tablemanagementmodule-edit">
<?php
	foreach($item as $column=>$value) {
		if($column!="id") {
?>
			<td><?php echo $value; ?></td>
<?php
		}
	}
?>
		</tr>
<?php
}
$db->disconnect();
?>
		</tr>
	</tbody>
	<tfoot class="tfoot-inverse">
		<tr>
			<td colspan="99">
<?php
if(sizeof($items)>0) {
?>
				<span class="pull-left">Shown <?php echo $table->preferences['page']*$table->preferences['size']+1; ?> to <?php echo $table->preferences['page']*$table->preferences['size']+sizeof($items); ?> of <?php echo $totals->count; ?></span>
<?php
} else {
?>
				<span class="pull-left">No items shown</span>
<?php
}
if(sizeof($items)<$totals->count) {
	$pages = ceil($totals->count/$table->preferences['size']);
	$page = $table->preferences['page'];
	$start = max(0,min($pages-5,$page-2));
	$end = min($pages,max($page+3,5));
?>
				<span class="pull-right">
					<div class="btn-group btn-group-xs">
<?php
	if($pages>5) {
?>
						<button class="select-page btn btn-default" data-page="0"><i class="fa fa-step-backward"></i></button>
						<button class="select-page btn btn-default" data-page="<?php echo max($page-1,0); ?>"><i class="fa fa-caret-left"></i></button>
<?php
	}
	for($count=$start;$count<$end;$count++) {
?>
						<button class="select-page btn btn-<?php echo ($page==$count ? "primary" : "default"); ?>" data-page="<?php echo $count; ?>"><?php echo $count+1; ?></button>
<?php
	}
	if($pages>5) {
?>
						<button class="select-page btn btn-default" data-page="<?php echo min($page+1,$pages-1); ?>"><i class="fa fa-caret-right"></i></button>
						<button class="select-page btn btn-default" data-page="<?php echo $pages-1; ?>"><i class="fa fa-step-forward"></i></button>
<?php
	}
?>
					</div>
				</span>
<?php
}
?>
			</td>
		</tr>
	</tfoot>
</table>