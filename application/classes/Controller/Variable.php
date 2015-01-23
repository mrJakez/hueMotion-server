<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Variable extends Controller_Rest {
    public function action_index() {
        $this->dispatchMethod();
    }


    public function action_get() {
        $variable_id = Request::$current->param('variable_id');

        if($variable_id) {

            /** @var $variable Model_Variable */
            $variable = ORM::factory('Variable', $variable_id);
            $this->setContent($variable->json());

        } else {
            $result = array();
            $variables = ORM::factory('Variable')->find_all();
            /** @var $variable Model_Variable */
            foreach($variables as $variable) {
                $result[] = $variable->json();
            }

            $this->setContent($result);
        }
    }


    public function action_put() {
        $variable_id = Request::$current->param('variable_id');

        if(!$variable_id) {
            $this->addError(huemotion::ERROR_MISSING_PARAMETER, 'missing <variable_id> parameter');
        }

        $body = json_decode(Request::$current->body());

        /** @var $variable Model_Variable */
        $variable = ORM::factory('Variable', $variable_id);
        $this->serializePayloadInObject($variable, $body);

        // return the new action
        $this->setContent($variable->json());
    }
}