<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Action extends ORM {

    static $TYPE_MOTION = 'motion';
    static $TYPE_DELAY = 'delay';

    protected $_table_name = 'actions';
    protected $_belongs_to = array(
        'actionGroup' => array('foreign_key' => 'actionGroup_id')
    );

    public function isVariableDuration() {
        return Model_Variable::isIdentifier($this->getDuration());
    }

    /**
     * @param $actionGroupRun Model_ActionGroupRun
     */
    public function execute($actionGroupRun) {
        switch ($this->getType()) {
            case self::$TYPE_MOTION:

                $payload = $this->getPayload();

                $color = $payload['color'];
                $duration = $this->getDuration();

                if (Model_Variable::isIdentifier($color)) {
                    $color = $actionGroupRun->getVariableInstanceValue($color);
                }

                if (Model_Variable::isIdentifier($duration)) {
                    $duration = $actionGroupRun->getVariableInstanceValue($duration);
                }


                $config = array(
                    'xy' => hue::convertHexToXY($color),
                    'transitiontime' => $duration * 10,
                );

                Log::instance()->add(Log::NOTICE,"set lamp ".$payload['lamp']." to color ". $color)->write();
                hue::setLampConfiguration($payload['lamp'], $config);
                break;
            case self::$TYPE_DELAY:
                //do nothing..
                break;
        }
    }


    public function getWaitForActions() {
        if($this->waitForAction) {
            $waitForActions = array();
            foreach(explode(',', $this->waitForAction) as $waitForActionId) {
                $waitForActions[] = ORM::factory('action', $waitForActionId);
            }

            return  $waitForActions;
        }
        return NULL;
    }

    public function getDuration() {
        return $this->duration;
    }

    public function getType() {
        return $this->type;
    }

    public function getPayload() {
        return unserialize($this->payload);
    }


    public function json() {
        $result = array();

        $result['id'] = $this->id;
        $result['type'] = $this->getType();
        $result['duration'] = $this->getDuration();
        if ($this->getWaitForActions()) {

            $waitForActions = array();

            /** @var $action Model_Action */
            foreach($this->getWaitForActions() as $action) {
                $waitForActions[] = $action->id;
            }

            $result['waitForAction'] = $waitForActions;
        }
        $result['payload'] = $this->getPayload();

        return $result;
    }
}