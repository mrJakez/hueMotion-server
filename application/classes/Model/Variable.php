<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Variable extends ORM {

    protected $_table_name = 'variables';
    protected $_has_many = array(
        'variableValues' => array(
            'model'   => 'VariableValue',
        ),
    );

    /**
     * @param $identifier
     * @return Model_Variable
     * @throws Kohana_Exception
     */
    static function findWithIdentifier($identifier) {

        $identifier = self::canonicalIdentifier($identifier);

        /** @var $variableValue Model_Variable */
        $variable = ORM::factory('variable')->where('identifier', '=', $identifier)->find();
        return $variable;
    }


    static function canonicalIdentifier($identifier) {
        $identifier = str_replace('<', '', $identifier);
        $identifier = str_replace('>', '', $identifier);
        return $identifier;
    }

    /**
     * @param $string
     * @return bool
     */
    static function isIdentifier($string) {
        if (strpos($string, '<') !== FALSE) {
            return TRUE;
        }

        return FALSE;

    }



    /**
     * @return Database_Result
     */
    public function getVariableValues() {
        return $this->variableValues->find_all();
    }

    public function getIdentifier() {
        return $this->identifier;
    }

    public function getType() {
        return $this->type;
    }

    public function getRandomOrSpecific() {
        return $this->randomOrSpecific;
    }


    public function calculateValue() {
        $values = $this->getVariableValues();

        switch($this->getType()) {
            case 'color' :
                    $position = rand(1, $values->count());
                    return $values[$position - 1]->getValue();
                break;
            case 'duration' :

                if ($values->count() == 1) {
                    return $values[0]->getValue();
                } else if ($values->count() == 2) {
                    return rand($values[0]->getValue(), $values[1]->getValue());
                } else {
                    //error!
                    return 0;
                }

                break;
        }
    }



    public function json() {
        $result = array();

        $result['id'] = $this->id;
        $result['type'] = $this->getType();
        $result['identifier'] = $this->getIdentifier();
        $result['randomOrSpecific'] = $this->getRandomOrSpecific();

        /** @var $variableValue Model_VariableValue */
        foreach($this->getVariableValues() as $variableValue) {

            $result['variableValues'][] = $variableValue->json();
        }

        return $result;
    }
}