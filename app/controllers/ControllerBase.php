<?php

class ControllerBase extends Phalcon\Mvc\Controller
{
    protected function _getTransPath()
    {
        $translationPath = './app/messages/';
        $language = $this->session->get("language");
        if (!$language) {
            $this->session->set("language", "en");
        }
        if ($language === 'ja' || $language === 'en') {
            return $translationPath.$language;
        } else {
            return $translationPath.'en';
        }
    }   

      /**
       * Loads a translation for the active controller
       */
    public function loadCustomTrans($transFile)
    {
        $translationPath = $this->_getTransPath();
        require $translationPath.'/'.$transFile.'.php';

        //Return a translation object
        $controllerTranslate = new Phalcon\Translate\Adapter\NativeArray(array(
            "content" => $messages
        ));

        //Set $t as controller's translation object
        $this->view->setVar("t", $controllerTranslate);
    }

    public function initialize()
    {
        Phalcon\Tag::prependTitle('PHP: ');
    }
}
