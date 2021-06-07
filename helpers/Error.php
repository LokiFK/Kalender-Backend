<?php

    class ErrorUI {
        public static function errorCode($errorCode)
        {
            echo Response::view("general/error404");
            http_response_code($errorCode);
        }

        public static function error($msg)
        {
            echo json_encode(
                array(
                    'message' => $msg
                )
            );
        }
    }