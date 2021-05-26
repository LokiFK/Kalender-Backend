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
    }