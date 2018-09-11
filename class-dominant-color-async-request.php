<?php

class Dominant_Color_Async_Request extends WP_Async_Request {

	protected $action = 'dominant_color';

	protected function handle() {
		$id = $_POST['id'];
		update_attachment_color_dominance( $id );
	}
}
