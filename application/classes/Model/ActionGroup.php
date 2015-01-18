<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_ActionGroup extends ORM {
    protected $_table_name = 'actionGroups';

    protected $_has_many = array(
        'actions' => array(
            'model'   => 'Action',
        ),
        'actionGroupRuns' => array(
            'model'   => 'ActionGroupRun',
        )
    );


    public function getName() {
        return $this->name;
    }

    /**
     * @return Database_Result
     */
    public function getActions() {
        return $this->actions->find_all();
    }


    /**
     * Create a new ActionGroupRun for the given actionGroup
     * @param $actionGroup Model_ActionGroup
     * @param $startTime unix timestamp
     */
    public function createActionGroupRun($startTime) {

        /** @var $actionGroupRun Model_ActionGroupRun */
        $actionGroupRun = ORM::factory('ActionGroupRun');
        $actionGroupRun->actionGroup = $this;
        $actionGroupRun->startTime = $startTime;
        $actionGroupRun->calculate();
        $actionGroupRun->save();
    }

    public function json() {
        $result = array();

        $result['id'] = $this->id;
        $result['name'] = $this->getName();
        $result['actions'] = array();

        /** @var $action Model_Action */
        foreach($this->getActions() as $action) {

            $result['actions'][] = $action->json();
        }



        return $result;
    }
}