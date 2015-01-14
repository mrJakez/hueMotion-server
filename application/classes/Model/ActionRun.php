<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_ActionRun extends ORM {
    protected $_table_name = 'actionRuns';

    protected $_belongs_to = array(
        'actionGroupRun' => array('foreign_key' => 'actionGroupRun_id'),
        'action' => array('foreign_key' => 'action_id'),
    );

    /**
     * @return Model_Action
     */
    public function getAction() {
        return $this->action;
    }

    public function setAction($action) {
        $this->action = $action;
    }

    /**
     * @return Model_ActionGroupRun
     */
    public function getActionGroupRun() {
        return $this->actionGroupRun;
    }

    public function setActionGroupRun($actionGroupRun) {
        $this->actionGroupRun = $actionGroupRun;
    }
}