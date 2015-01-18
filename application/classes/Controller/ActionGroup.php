<?php defined('SYSPATH') or die('No direct script access.');

class Controller_ActionGroup extends Controller_Rest {
    public function action_index() {
        $this->dispatchMethod();
    }


    public function action_get() {

        $result = array();
        $actionGroups = ORM::factory('ActionGroup')->find_all();
        /** @var $actionGroup Model_ActionGroup */
        foreach($actionGroups as $actionGroup) {
            $result[] = $actionGroup->json();
        }

        $this->setContent($result);
    }
}