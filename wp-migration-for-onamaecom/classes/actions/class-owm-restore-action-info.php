<?php

class Owm_Restore_Action_Info extends Owm_Migration_Action {
	public static $action_key = 'restore_info';

	public function do_action() {
		$info = new Owm_Restore_Info();
		return array(
			'status' => $info->get_status(),
			'restore_dir_name' => $info->get_restore_dir_name(),
			'start_datetime' => $info->get_start_datetime(),
			'finish_datetime' => $info->get_finish_datetime()
		);
	}
}
