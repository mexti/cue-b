<?php
/**************************************************************************************************************
 Class Article
			The Article class extends the Object class. This class contains the articles in the website,
			i.e. the main content.
 **************************************************************************************************************/
defined('Qb_Started') || die("No use starting a class without an include");

class Article extends Object {
	protected $table = "Articles";
	
	private function hasAccess($roles) {
		global $_user;
		if($roles==null) return true;								// Null value means it is public
		$denied = empty($_user) || !$_user->hasAccess($roles);
		return !$denied;
	}
	
	public function printer($html="") {
		$this->display($html);
	}
	
	public function screen($html="") {
		$this->display($html);
	}
	
	public function display($html="") {
		global $_cue_b;
		if(!$this->hasAccess($this->properties->roles)) {
			ob_end_clean();
			$_SESSION['logonTarget'] = $_SERVER['REQUEST_URI'];
			exit(header("Location: ".$_SERVER['REQUEST_URI'].(preg_match("`\?`") ? "&" : "?")."logon"));
		}
?>
<article role="article" cue-b:scope="Article">
	<div class="col-12">
		<h1 <?php echo $_cue_b->prop; ?>="headline"><?php echo $this->properties->title; ?></h1>
		<div <?php echo $_cue_b->prop; ?>="articleBody">
			<div class="lead-content"><?php echo $this->properties->leadcontent; ?></div>
			<div><?php echo $this->properties->content; ?></div>
		</div>
	</div>
</article>
<?php
	}
}
?>