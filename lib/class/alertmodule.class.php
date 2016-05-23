<?php
/**************************************************************************************************************
 Class AlertModule
			The AlertModule class extends the Module class. This class is used to display the alerts that
			are issued.
 **************************************************************************************************************/
defined('Qb_Started') || die("No use starting a class without an include");

class AlertModule extends Module {
	protected $module = "Alert";
	
	// Add alert to alert queue
	static public function addMessage($message,$type='success',$fade=true) {
		isset($_SESSION['alerts']) || $_SESSION['alerts'] = array();
		$_SESSION['alerts'][] = (object)['type'=>$type,'message'=>$message,'fade'=>$fade];
		return null;
	}
	
	public function printer($html="") {
		$this->display($html);
	}
	
	public function screen($html="") {
		$this->display($html);
	}
	
	public function display($html="") {
		global $_template;
		if(isset($_SESSION['alerts'])) {
			$_template->addScript("cue-b-alerts.js");
			foreach($_SESSION['alerts'] as $alert) {
				switch($alert->type) {
					case 'success':
						$icon = "check";
						break;
					case 'info':
						$icon = "question";
						break;
					case 'warning':
						$icon = "exclamation";
						break;
					case 'error':
					default:
						$alert->type = "danger";
						$icon = "times";
				}
?>
<div class="alert alert-<?php echo $alert->type; ?> alert-dismissible fade in<?php if($alert->fade) echo " alert-fade"; ?>">
	<button type="button" class="close" data-dismiss="alert" aria-label="close"><span aria-hidden="true">&times;</span></button>
	<span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-<?php echo $icon; ?> fa-stack-1x fa-inverse"></i></span> <?php echo $alert->message; ?>
</div>
<?php
			}
			unset($_SESSION['alerts']);
		}
	}
}
?>