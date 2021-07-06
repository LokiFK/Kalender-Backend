<?php

    class Response {
        public function json(array $input)
        {
            echo json_encode($input);
        }

        public static function errorJSON($errCode, $msg)
        {
            http_response_code($errCode);
            ErrorUI::errorMsg($errCode, $msg);
        }

        public static function errorVisual($errCode, $msg)
        {
            ErrorUI::error($errCode, $msg);
        }

        public static function view(string $component, array $data = array(), array $safeData = array(), array $loopData = array(), $stuff = null)
        {
            return TemplateHTML::load($component, new ReplaceData($data, $safeData, $loopData, $stuff));
        }
    }

    class ReplaceData {
        public $data;
        public $safeData;
        public $loopData;
        public $stuff;

        public function __construct($data, $safeData, $loopData, $stuff)
        {
            $this->data = $data;
            $this->safeData = $safeData;
            $this->loopData = $loopData;
            $this->stuff = $stuff;
        }
    }
?>
