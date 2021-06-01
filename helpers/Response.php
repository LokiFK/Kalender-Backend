<?php

    class Response {
        public function send($input)
        {
            echo json_encode($input);
        }

        public function errorCode($errorCode)
        {
            http_response_code($errorCode);
        }

        public function error($msg)
        {
            echo json_encode(
                array(
                    'message' => $msg
                )
            );
        }

        public static function view(string $path, array $data)
        {
            if (is_file($path)) {
                $content = file_get_contents($path);
                foreach ($data as $key => $value) {
                    $content = str_replace('{{ ' . $key . ' }}', $value, $content);
                }
                return $content;
            }
            $res = new Response();
            $res->error('Error setting up HTML');
            $res->errorCode(500);
        }
    }