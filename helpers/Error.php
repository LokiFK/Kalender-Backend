<?php

    class ErrorUI {
        public static function errorCode($errorCode)
        {
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