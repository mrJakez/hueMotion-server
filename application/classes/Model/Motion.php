<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Motion extends ORM {


    /**
     * example of documenting a method, and using optional description with @return
     * @param bool|string $lamp sometimes a boolean, sometimes a string (or, could have just used "mixed")
     */
    public function setLamp($lamp) {
        $this->lamp = $lamp;
    }


    /**
     * @return Model_Motion testreturn
     */
    public function getLamp() {
        return $this->lamp;
    }
}