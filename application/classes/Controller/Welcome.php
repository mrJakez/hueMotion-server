<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Welcome extends Controller {

	public function action_index() {


		/** @var $variable Model_Variable */
		$variable = ORM::factory('Variable', 1);

		$tmp = $variable->getVariableValues();
		$this->response->body("JO");






//		$request = Request::factory("http://huemotion/api/action/1");
//		$request->method(Request::PUT);
//		$request->headers('Content-Type', 'application/json');
//		$request->body(json_encode(array(
//			'duration' => 5,
//			'type' => 'motion')));
//		$response = $request->execute();
//		$this->response->body($response->body());


//		$request = Request::factory("http://huemotion/api/action/2");
//		$response = $request->execute();
//		$this->response->body($response->body());
	}
}