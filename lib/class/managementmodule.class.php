<?php
/**************************************************************************************************************
 Class ManagementModule
			The Module class is the parent class of all module classes, and itself is a special component
			class. It contains methods to process content during generation. This allows inserting
			additional content to the page displayed.
 **************************************************************************************************************/
defined('Qb_Started') || die("No use starting a class without an include");

class ManagementModule extends Module {
	protected $module = "Management";
	
	public function printer($html="") {
		$this->display($html);
	}
	
	public function screen($html="") {
		$this->display($html);
	}
	
	private function hasAccess($roles) {
		global $_user;
		if($roles==null) return true;								// Null value means it is public
		$denied = empty($_user) || !$_user->hasAccess($roles);
		return !$denied;
	}
	
	public function display($html="") {
		global $_db;
		if(isset($_GET['table'])) {
			$table = new Table($_GET['table']);
			$table->display();
			return;
		}
		$_db->select("`id`,`name`,`title`,`description`,`icon`,`roles`")->from("`tablegroups`")->where("1")->order("`title`");
		$tablegroups = $_db->load();
?>
<ul class="nav nav-tabs" role="tablist">
<?php
		$first = true;
		foreach($tablegroups as &$tablegroup) {
			$tablegroup = (object)$tablegroup;
			if($this->hasAccess($tablegroup->roles)) {
				$_db->select("COUNT(`id`) AS `count`")->from("`tables`")->where("`tablegroup`='{$tablegroup->id}'");
				$total = $_db->loadObject();
				if($total->count) {
?>
	<li class="nav-item"><a class="nav-link<?php if($first) echo " active"; ?>" href="#<?php echo $tablegroup->name; ?>" data-toggle="tab"><i class="fa fa-<?php echo $tablegroup->icon; ?> fa-lg"></i><span class="hidden-md-down"> <?php echo $tablegroup->title; ?></span></a></li>
<?php
				}
				$first = false;
			}
		}
?>
</ul>
<div class="tab-content p-x-2 p-y-1">
<?php
		$first = true;
		foreach($tablegroups as &$tablegroup) {
			$tablegroup = (object)$tablegroup;
			if($this->hasAccess($tablegroup->roles)) {
?>
	<div class="tab-pane<?php if($first) echo " active"; ?>" id="<?php echo $tablegroup->name; ?>" role="tabpanel">
		<h3><?php echo $tablegroup->title; ?></h3>
		<p><?php echo $tablegroup->description; ?></p>
		<div class="card-columns">
<?php
				$_db->select("`id`,`name`,`title`,`description`,`plural`,`singular`")->from("`tables`")->where("`tablegroup`='{$tablegroup->id}'")->order("`title`");
				$tables = $_db->load();
				foreach($tables as &$table) {
					$table = (object)$table;
					$_db->select("COUNT(`id`) AS `count`")->from("`{$table->name}`");
					$total = $_db->loadObject();
					if($total->count) {
?>
			<a href="/?component=Table&id=<?php echo $table->id; ?>" title="<?php echo $table->title; ?>">
				<div class="card">
					<div class="card-header"><h6 class="card-title"><?php echo $table->title; ?></h6></div>
					<div class="card-block"><p><?php echo $table->description; ?></p></div>
					<div class="card-footer"><small><?php echo $total->count." ".($total->count==1 ? $table->singular : $table->plural); ?></small></div>
				</div>
			</a>
<?php
					}
				}
?>
		</div>
	</div>
<?php
				$first = false;
			}
		}
?>
</div>
<?php
	}
}
?>