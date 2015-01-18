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
        if($action->isVariableDuration()) {
            return $this->getVariableInstanceValue($action->getDuration());
        } else {
          return $action->getDuration();
        }
    }

    public function getStartTime() {
        return $this->startTime;
    }

    public function setVariableInstances($randomInstances) {
        $this->variableInstances = serialize($randomInstances);
    }

    public function getVariableInstances() {
        return unserialize($this->variableInstances);
    }

    public function getVariableInstanceValue($identifier) {
        $identifier = Model_Variable::canonicalIdentifier($identifier);
        $instances = $this->getVariableInstances();
        return $instances[$identifier];
    }


    public function process($time) {
        $currentRelativeTime = $time - $this->getStartTime();
        $relativeTimeSum = 0;

        /** @var $action Model_Action */
        foreach($this->getActionGroup()->getActions() as $action) {

            // check if the currentAction was already executed. If yes: skip
            if($this->hasExecutedAction($action)) {
                $relativeTimeSum += $this->getRealDuration($action);
                continue;
            }

            /*
             * check waiting dependencies:
             * That means that we want to check if waitForActions are set. If yes we check if this actions has already been executed.
             * Also we check if the $currentRelativeTime is greater/equal the startTime+duration of this waitForAction action.
             */
            if ($action->getWaitForActions()) {


                $waitForActionComplete = TRUE;

                /** @var $action Model_Action */
                foreach($action->getWaitForActions() as $waitForAction) {

                    // execution just means that the command was send.
                    if(!$this->hasExecutedAction($waitForAction)) {
                        $waitForActionComplete = FALSE;
                        break;
                    }

                    // check if we have reached the correct time
                    /** @var $actionRunFromWaitForAction Model_ActionRun */
                    $actionRunFromWaitForAction = ORM::factory('ActionRun')->where('action_id', '=', $waitForAction)->and_where('actionGroupRun_id', '=', $this->id)->find();
                    if($currentRelativeTime <= $actionRunFromWaitForAction->getTotalTime()) {
                        $waitForActionComplete = FALSE;
                        break;
                    }
                }

                if ($waitForActionComplete == FALSE) {
                    continue;
                }
            }

            Log::instance()->add(Log::NOTICE,"--------------> process action with ID: " . $action->id . " at relativeTime: $currentRelativeTime")->write();
            $this->execute($action);
        }

        // check if all actions are executed. if yes: remove actionGroupRun and recreate a new one
        if($this->hasExecutedAllActions() && $this->getDuration() <= $currentRelativeTime) {
            Log::instance()->add(Log::NOTICE,"--------------> RECREATED ACTION GROUP RUN with ID: " . $this->id)->write();
            $this->getActionGroup()->createActionGroupRun($time);
            $this->delete();
        }
    }


    /**
     * @param $action Model_Action
     */
    protected function execute($action) {

        // execute HUE command
        $action->execute($this);

        // calculate totalTime (totalTime = oldTotalTime + duration)
        $totalTime = $this->getRealDuration($action);
        if ($action->getWaitForActions()) {
            // get the totalTime from the LONGEST action before and add it to the new totalTime
            $longestParentActionDuration = 0;
            /** @var $action Model_Action */
            foreach($action->getWaitForActions() as $waitForAction) {
                /** @var $actionRunFromWaitForAction Model_ActionRun */
                $actionRunFromWaitForAction = ORM::factory('ActionRun')->where('action_id', '=', $waitForAction->id)->and_where('actionGroupRun_id', '=', $this->id)->find();

                if($actionRunFromWaitForAction->getTotalTime() > $longestParentActionDuration) {
                    $longestParentActionDuration = $actionRunFromWaitForAction->getTotalTime();
                }
            }

            $totalTime += $longestParentActionDuration;
        }

        //store in actionRuns to identify already executed actions
        /** @var $actionRun Model_ActionRun */
        $actionRun = ORM::factory('ActionRun');
        $actionRun->setAction($action);
        $actionRun->setActionGroupRun($this);
        $actionRun->setTotalTime($totalTime);
        $actionRun->save();
    }


    protected function hasExecutedAction($action) {

        $count = ORM::factory('ActionRun')->where('action_id', '=', $action->id)->and_where('actionGroupRun_id', '=', $this->id)->find_all()->count();
        if($count == 1) {
            return TRUE;
        }

        return FALSE;
    }


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
        $this->setVariableInstances($this->calculateVariableInstances());

        //set the duration
        $this->duration = $this->calculateDuration();
    }


    protected function calculateVariableInstances() {
        $payload = array();
        /** @var $action Model_Action */
        foreach($this->getActionGroup()->getActions() as $action) {

            // calculate variable duration
            if ($action->isVariableDuration()) {
                $variableName = $action->getDuration();

                $variable = Model_Variable::findWithIdentifier($variableName);
                $payload[$variable->getIdentifier()] = $variable->calculateValue();
            }

            // calculate variable color
            if ($action->getType() == Model_Action::$TYPE_MOTION) {
                $actionPayload = $action->getPayload();

                if (Model_Variable::isIdentifier($actionPayload['color'])) {
                    $variableName = $actionPayload['color'];

                    $variable = Model_Variable::findWithIdentifier($variableName);
                    $payload[$variable->getIdentifier()] = $variable->calculateValue();
                }
            }
        }

        return $payload;
    }


    protected function calculateDuration() {

        $durations = array();
        $actions = $this->getActionGroup()->getActions();

        //  get start actions (waitForAction=0)
        /** @var $action Model_Action */
        foreach($actions as $action) {
            if ($action->getWaitForActions() == NULL) {
                $durations[$action->id] = $this->getRealDuration($action);
            }
        }

        // calculate all other
        for($i=0; $i < $actions->count(); $i++) {

            /** @var $action Model_Action */
            foreach($actions as $action) {
                // check if we calculated this action already
                if (array_key_exists($action->id, $durations)) {
                    continue;
                }

                // check if all dependencies are calculated already and calculate the durationBefore
                $durationBefore = 0;
                /** @var $waitForAction Model_Action */
                foreach($action->getWaitForActions() as $waitForAction) {
                    if(!array_key_exists($waitForAction->id, $durations)) {
                        continue;
                    }

                    if ($durations[$waitForAction->id] > $durationBefore) {
                      $durationBefore = $durations[$waitForAction->id];
                    }
                }

                // set the new duration
                $durations[$action->id] = $durationBefore + $this->getRealDuration($action);
            }
        }

        $maxDuration = 0;
        foreach($durations as $duration) {
            if ($duration > $maxDuration) {
                $maxDuration = $duration;
            }
        }

        return $maxDuration;
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