<?php

class Owm_Migration_Controller {
	private $actions = array();

	public static function instance() {
		static $self = false;
		if ( ! $self ) {
			$self = new Owm_Migration_Controller();
			array_push( $self->actions, Owm_Migration_Action_Wp_Info::get_action_info() );
			array_push( $self->actions, Owm_Backup_Action_Info::get_action_info() );
			array_push( $self->actions, Owm_Backup_Action_Backup::get_action_info() );
			array_push( $self->actions, Owm_Backup_Action_Remove::get_action_info() );
			array_push( $self->actions, Owm_Backup_Action_Log::get_action_info() );
			array_push( $self->actions, Owm_Restore_Action_Restore::get_action_info() );
			array_push( $self->actions, Owm_Restore_Action_Remove::get_action_info() );
			array_push( $self->actions, Owm_Restore_Action_Info::get_action_info() );
			array_push( $self->actions, Owm_Restore_Action_Log::get_action_info() );
			array_push( $self->actions, Owm_Ssl_Action_Create_Challenge_File::get_action_info() );
            array_push( $self->actions, Owm_Ssl_Action_Delete_Challenge_File::get_action_info() );
		}
		add_action( 'wp_ajax_owm_migration', array( $self, 'execute' ) );

		# add job action
		$backup_job = new Owm_Backup_Job();
		$backup_job->add_action();
		$restore_job = new Owm_Restore_Job();
		$restore_job->add_action();

		return $self;
	}

	public function execute() {
		if ( ! current_user_can( 'administrator' ) ) {
			return;
		}
		if ( ! isset( $_GET[ 'owm_migration_actions' ] ) ) {
			return;
		}
		$execute_actions = $_GET[ 'owm_migration_actions' ];
		if ( empty( $execute_actions ) ) {
			Owm_Migration_Response::create_error_response( 'unauthorized', 401 );
		}

		if ( ! is_admin() ) {
			Owm_Migration_Response::create_error_response( 'unauthorized', 401 );
		}
		foreach ( $this->actions as $action ) {
			if ( $action[ 'action_key' ] === $execute_actions ) {
				ini_set( 'memory_limit', '1024M' );
				@set_time_limit( 0 );
				$action_cls = new $action[ 'class_name' ];
				$action_response = $action_cls->do_action();
				Owm_Migration_Response::create_response( $action_response );
			}
		}
	}
}
