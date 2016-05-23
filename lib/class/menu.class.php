<?php
/**************************************************************************************************************
 Class Menu
			The Menu class extends the Object class. This class holds the menus that can be displayed on the
			website. All menus have several menu options.
 **************************************************************************************************************/
defined('Qb_Started') || die("No use starting a class without an include");

class Menu extends Object {
	protected $table = "Menus";
	
	private function getOptions($id) {
		global $_db;					// TODO: Is this the correct method to retrieve stuff from the database?
		$_db->select("`*`")->from("`menuoptions`")->where("`menu`='{$this->id}'")->order("`order` ASC");
		$menuoptions = $_db->load();
		foreach($menuoptions as $ix=>$menuoption) $menuoptions[$ix] = (object)$menuoption;
		return $menuoptions;
	}
	
	private function rewrite($option) {
		global $_mainmenu,$_language,$_db;
		$url = "/";
		if(isset($_language) && $_language->getId()!='1') {			// Not the default language
			$url .= $_language->getName()."/";
		}
		return $url.$option->name;									// TODO: For the moment, only one deep; also eliminate the Home item
	}
	
	private function hasAccess($roles) {
		global $_user;
		if($roles==null) return true;								// Null value means it is public
		$denied = empty($roles) || empty($_user) || !$_user->hasAccess($roles);
		return !$denied;
	}
	
	public function printer($html="") {
		$this->display($html);
	}
	
	public function screen($html="") {
		$this->display($html);
	}
	
	public function display($html="") {
		if(!isset($this->options)) $this->options = $this->getOptions($this->id);
		if(count($this->options)>2) {
?>
<a href="#" class="navbar-toggler hidden-sm-up<?php if(!empty($this->properties->class)) echo " {$this->properties->class}"; ?>" data-toggle="collapse" data-target="#header-nav" aria-controls="header-nav" title="Toggle menu"><i class="fa fa-bars fa-lg"></i></a>
<div class="clearfix hidden-sm-up"></div>
<nav class="collapse navbar-toggleable-xs<?php if(!empty($this->properties->class)) echo " {$this->properties->class}"; ?>" id="header-nav" role="navigation">
<?php
		} else {
?>
<nav <?php if(!empty($this->properties->class)) echo "class=\"{$this->properties->class}\" "; ?>id="header-nav" role="navigation">
<?php
		}
?>
	<ul class="nav navbar-nav">
<?php
		foreach($this->options as $option) {
			if($this->hasAccess($option->roles)) {
?>
		<li>
			<a href="<?php echo $this->rewrite($option); ?>" title="<?php echo $option->description; ?>">
				<?php if(!empty($option->icon)) { ?><i class="fa fa-<?php echo $option->icon; ?> fa-lg"></i><span class="hidden-md-down"> <?php } echo $option->title; ?></span>
			</a>
		</li>
<?php
			}
		}
?>
	</ul>
</nav>
<?php
	}
}
?>