<?php
/**************************************************************************************************************
 Class LogonModule
			The LogonModule class extends the Module class. This class allows a user to log on through a
			logon window.
 **************************************************************************************************************/
defined('Qb_Started') || die("No use starting a class without an include");

class LogonModule extends Module {
	protected $module = "Logon";
	
	public function printer($html="") {
		$this->display($html);
	}
	
	public function screen($html="") {
		$this->display($html);
	}
	
	public function display($html="") {
		global $_template;
		$_template->addScript("logonmodule.js");
?>
<div class="row">
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xs-offset-3 col-sm-offset-3 col-md-offset-3 col-lg-offset-3">
		<form id="logonForm" role="form" method="post" action="<?php echo htmlentities(isset($_SESSION['logonTarget']) ? $_SESSION['logonTarget'] : $_SERVER['REQUEST_URI']); ?>">
			<div class="form-group row">
				<div class="input-group col-md-6 col-lg-6 col-lg-offset-3">
					<label for="username" class="input-group-addon"><i class="fa fa-user fa-fw"></i></label>
					<input id="username" name="username" class="form-control" type="text" maxlength="100" placeholder="Account name" value="" />
				</div>
			</div>
			<div class="form-group row">
				<div class="input-group col-md-6 col-lg-6 col-lg-offset-3">
					<label for="password" class="input-group-addon"><i class="fa fa-key fa-fw"></i></label>
					<input id="password" name="password" class="form-control" type="password" data-minlength="6" maxlength="100" placeholder="Password" value="" />
				</div>
			</div>
			<div class="row text-right">
				<button type="submit" class="btn btn-primary">Confirm</button>
			</div>
		</form>
	</div>
</div>
<?php
	}
}
?>