<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Action extends Controller_Rest {
    public function action_index() {
    $this->dispatchMethod();
}


    public function action_get() {
        $action_id = Request::$current->param('action_id');
        if(!$action_id) {
            $this->addError(huemotion::ERROR_MISSING_PARAMETER, 'missing <action_id> parameter');
        }

        /** @var $action Model_Action */
        $action = ORM::factory('Action', $action_id);
        $this->setContent($action->json());
    }


    public function action_put() {
        $action_id = Request::$current->param('action_id');

        if(!$action_id) {
            $this->addError(huemotion::ERROR_MISSING_PARAMETER, 'missing <action_id> parameter');
        }

        $body = json_decode(Request::$current->body());

        /** @var $action Model_Action */
        $action = ORM::factory('Action', $action_id);
        $this->serializePayloadInObject($action, $body);

        // return the new action
        $this->setContent($action->json());
    }
}