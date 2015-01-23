<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Configuration extends Controller_Rest {


    public function action_index() {
        $this->dispatchMethod();
    }


    public function action_get() {
        $result = array();

        $actionGroups = Request::factory('ActionGroup')->execute()->body();
        $result['actionGroups'] = json_decode($actionGroups, TRUE);

        $variables = Request::factory('Variable')->execute()->body();
        $result['variables'] = json_decode($variables, TRUE);

        $this->setContent($result);
    }
}