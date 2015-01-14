<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Worker extends Controller {

    static $INITIAL_RUN_DELAY = 10;

    public function action_initialize() {
        /*
         * first we have to delete all existing actionGroupRuns... just to be sure to get rid of all the old stuff
         */
        $currentActionGroupRuns = ORM::factory('ActionGroupRun')->find_all();
        /** @var $currentActionGroupRun Model_ActionGroupRun */
        foreach($currentActionGroupRuns as $currentActionGroupRun) {
            $currentActionGroupRun->delete();
        }

        /*
         * Now we create a actionGroupRun for all configured actionGroups
         */
        $time = time();
        $actionGroups = ORM::factory('ActionGroup')->find_all();
        /** @var $actionGroup Model_ActionGroup */
        foreach($actionGroups as $actionGroup) {
            $actionGroup->createActionGroupRun($time + self::$INITIAL_RUN_DELAY);
        }

        $this->response->body('initialized');
    }

    public function action_process()
    {
        $time = time();
        Log::instance()->add(Log::NOTICE,"worker executed at: " . $time)->write();
        echo "time: $time<br>";

        $currentActionGroupRuns = ORM::factory('ActionGroupRun')->where('startTime', '<=', $time)->find_all();
        /** @var $currentActionGroupRun Model_ActionGroupRun */
        foreach($currentActionGroupRuns as $currentActionGroupRun) {
            $currentActionGroupRun->process($time);
        }

//        /** @var $actionGroup Model_ActionGroup */
//        $actionGroup = ORM::factory('ActionGroup',1);
//        $actionGroup->createActionGroupRun($time+20);

        $this->response->body('run executed');
    }
}