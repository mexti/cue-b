<?php
/**************************************************************************************************************
 Class SocialModule
			The SocialModule class extends the Module class. This class shows the social icons of the web
			site.
 **************************************************************************************************************/
defined('Qb_Started') || die("No use starting a class without an include");

class SocialModule extends Module {
	protected $module = "Social";
	
	public function printer($html="") {
		$this->display($html);
	}
	
	public function screen($html="") {
		$this->display($html);
	}
	
	public function display($html="") {
		global $_template,$_db;
		$_db->select("`id`,`name`,`title`,`url`,`icon`")->from("`sociallinks`")->where("`enabled`");
		$sociallinks = $_db->load();
		foreach($sociallinks as &$link) {
			$link = (object)$link;
?>
<li>
	<a href="<?php echo $link->url; ?>" target="_blank" title="<?php echo $link->title; ?>"><i class="fa fa-<?php echo $link->icon; ?> fa-lg fa-fw"></i></a>
</li>
<?php
		}
	}
}
?>