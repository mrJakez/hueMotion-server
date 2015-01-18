<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_VariableValue extends ORM {
    protected $_table_name = 'variableValues';
    protected $_belongs_to = array(
        'variable' => array('foreign_key' => 'variable_id')
    );


    public function getValue() {
        return $this->value;
    }

    public function json() {
        $result = array();

        $result['id'] = $this->id;
        $result['value'] = $this->getValue();

        return $result;
    }
}