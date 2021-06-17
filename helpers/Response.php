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

        public static function view(string $component, array $data = array(), array $safeData = array(), array $loopData = array())
        {
            return TemplateHTML::load($component, new ReplaceData($data, $safeData, $loopData));
        }
    }

    class ReplaceData {
        public array $data;
        public array $safeData;
        public array $loopData;

        public function __construct($data, $safeData, $loopData)
        {
            $this->data = $data;
            $this->safeData = $safeData;
            $this->loopData = $loopData;
        }
    }
?>
