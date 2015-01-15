<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Action extends ORM {

    static $TYPE_MOTION = 'motion';
    static $TYPE_DELAY = 'delay';

    protected $_table_name = 'actions';
    protected $_belongs_to = array(
        'actionGroup' => array('foreign_key' => 'actionGroup_id')
    );

    public function isRandomDuration() {
        if (strpos($this->getDuration(), ':') !== FALSE) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function execute() {
        switch ($this->getType()) {
            case self::$TYPE_MOTION:

                $payload = $this->getPayload();
                $config = array(
                    'xy' => hue::convertHexToXY($payload['color']),
                    'transitiontime' => $this->getDuration() * 10,
                );

                hue::setLampConfiguration($config['lamp'], $config);
                break;
            case self::$TYPE_DELAY:
                //do nothing..
                break;
        }
    }

    /**
     * @return Model_Action
     */
    public function getWaitForAction() {
        if($this->waitForAction) {
            return  ORM::factory('action', $this->waitForAction);
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

}