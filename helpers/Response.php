<?php

    class Response {
        public function json(array $input)
        {
            echo json_encode($input);
        }

        public function errorCode(int $errorCode)
        {
            http_response_code($errorCode);
        }

        public function error(string $msg)
        {
            echo json_encode(
                array(
                    'message' => $msg
                )
            );
        }

        public static function view(string $component, array $data = array())
        {
            $pathHTML = "./public/$component.html";
            $pathCSS = "./public/css/$component.css";
            $pathDefaultCSS = "./public/css/default-styles.css";
            if (is_file($pathHTML)) {
                $content = file_get_contents($pathHTML);
                foreach ($data as $key => $value) {
                    $content = str_replace('{{ ' . $key . ' }}', $value, $content);
                }
                if (is_file($pathCSS)) {
                    $styles = '<style>' . file_get_contents($pathCSS) . file_get_contents($pathDefaultCSS) . '</style>';
                    $content = str_replace('{% styles %}', $styles, $content);
                } else {
                    $content = str_replace('{% styles %}', '', $content);
                }
                return $content;
            }
            $res = new Response();
            $res->error('Error setting up HTML');
            $res->errorCode(500);
        }
    }