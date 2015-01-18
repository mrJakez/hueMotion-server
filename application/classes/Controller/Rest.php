<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Rest extends Controller_Template
{
    private $content = NULL;
    public $template = 'templates/rest';

    public function dispatchMethod() {
        switch (Request::$current->method()) {
            case 'GET':
                $this->action_get();
                break;
            case 'PUT':
                $this->action_put();
                break;
        }
    }

    public function after() {
        $this->response->headers('Content-Type', 'application/json');
        $this->template->content = $this->content;

        parent::after();
    }

    public function setContent($content) {
        $this->content = $content;
    }

    /**
     * @param mixed $errorStack
     */
    static function addError($errorCode, $description = NULL)
    {
        if (!isset($GLOBALS['hueMotionErrorStack'])) {
            $GLOBALS['hueMotionErrorStack'] = array();
        }

        $GLOBALS['hueMotionErrorStack'][] = array(
            'code' => $errorCode,
            'description' => $description,
        );
    }


    public function action_put() {}
    public function action_get() {}


    public function serializePayloadInObject($object, $payload) {
        foreach($payload as $key => $value) {
            if($object->__get($key)) {
                $object->$key = $value;
            }
        }

        $object->save();
    }
}