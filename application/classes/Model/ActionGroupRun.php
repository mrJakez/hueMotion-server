<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_ActionGroupRun extends ORM {

    protected $_table_name = 'actionGroupRuns';
    protected $_belongs_to = array('actionGroup' => array());

    protected $_has_many = array(
        'actionRuns' => array(
            'model'   => 'ActionRun',
        )
    );


    public function getDuration() {
        return $this->duration;
    }

    /**
     * @param $action Model_Action
     */
    public function getRealDuration($action) {
        if($action->isRandomDuration()) {
            return $this->getRandomInstances()[$action->id];
        } else {
          return $action->getDuration();
        }
    }

    public function getStartTime() {
        return $this->startTime;
    }

    public function setRandomInstances($randomInstances) {
        $this->randomInstances = serialize($randomInstances);
    }

    public function getRandomInstances() {
        return unserialize($this->randomInstances);
    }

    public function process($time) {
        $currentRelativeTime = $time - $this->getStartTime();
        $relativeTimeSum = 0;

        /** @var $action Model_Action */
        foreach($this->getActionGroup()->getActions() as $action) {

            if($this->hasExecutedAction($action)) {
                $relativeTimeSum += $this->getRealDuration($action);
                continue;
            }

            if ($action->getWaitForAction()) {
                if(!$this->hasExecutedAction($action->getWaitForAction())) {
                    continue;
                }
            }


            if($currentRelativeTime >= $relativeTimeSum) {
                Log::instance()->add(Log::NOTICE,"--------------> process action with ID: " . $action->id . " at relativeTime: $currentRelativeTime")->write();
                $this->execute($action);
                break;
            }
        }


        // check if all actions are executed. if yes: remove actionGroupRun and recreate a new one
        if($currentRelativeTime >= $this->getDuration()) {
            Log::instance()->add(Log::NOTICE,"--------------> RECREATED ACTION GROUP RUN with ID: " . $this->id)->write();

            $this->getActionGroup()->createActionGroupRun($time);
            $this->delete();
        }
    }

    /**
     * @param $action Model_Action
     */
    protected function execute($action) {

        //execute HUE command
        $action->execute();

        //store in actionRuns to identify already executed actions
        /** @var $actionRun Model_ActionRun */
        $actionRun = ORM::factory('ActionRun');
        $actionRun->setAction($action);
        $actionRun->setActionGroupRun($this);
        $actionRun->save();
    }

    protected function hasExecutedAction($action) {

        $count = ORM::factory('ActionRun')->where('action_id', '=', $action->id)->and_where('actionGroupRun_id', '=', $this->id)->find_all()->count();
        if($count == 1) {
            return TRUE;
        }

        return FALSE;
    }

    //TODO: remove method?!
    public function hasExecutedAllActions() {
        $actionsCount = $this->getActionGroup()->getActions()->count();
        $actionRunsCount = ORM::factory('ActionRun')->where('actionGroupRun_id', '=', $this->id)->find_all()->count();

        if ($actionsCount == $actionRunsCount) {
            return TRUE;
        }

        return FALSE;
    }


    /**
     * @return Model_ActionGroup
     */
    public function getActionGroup() {
        return $this->actionGroup;
    }

    public function calculate(){
        //calculate the randoms
        $this->setRandomInstances($this->calculateRandomInstances());

        //set the duration
        $this->duration = $this->calculateDuration();
    }


    protected function calculateRandomInstances() {
        $payload = array();
        /** @var $action Model_Action */
        foreach($this->getActionGroup()->getActions() as $action) {

            if (!$action->isRandomDuration()) {
                continue;
            }

            $randomDuration = explode(':', $action->getDuration());
            $payload[$action->id] = rand($randomDuration[0], $randomDuration[1]);
        }

        return $payload;
    }


    protected function calculateDuration() {
        $duration = 0;
        /** @var $action Model_Action */
        foreach($this->getActionGroup()->getActions() as $action) {
            if (!$action->isRandomDuration()) {
                $duration += $action->getDuration();
            } else {
                $duration += $this->getRandomInstances()[$action->id];
            }
        }
        return $duration;
    }


    /**
     * Deletes a single record while ignoring relationships.
     *
     * @chainable
     * @throws Kohana_Exception
     * @return ORM
     */
    public function delete() {
        //delete all attached actionRuns
        DB::delete('actionRuns')->where('actionGroupRun_id', '=', $this->id)->execute();

        parent::delete();
    }

}