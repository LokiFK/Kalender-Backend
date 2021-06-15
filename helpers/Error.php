<?php

    class ErrorUI {
        public static function errorMsg($errCode, $msg)
        {
            echo json_encode(
                array(
                    'errorCode' => $errCode,
                    'message' => $msg
                )
            );
        }

        public static function error($errCode, $msg)
        {
            http_response_code($errCode);
            if(strpos($msg, "/api")) {
                ErrorUI::errorMsg($errCode, $msg);
            } else {
                echo Response::view("general/error", ["errorCode" => $errCode, "msg" => $msg]);
            }
            exit();
        }

        public static function generalError() {
            try {
                self::error(random_int(-700, -600), "Unknown Error");
            } catch (Exception $e) {
                self::error(601, $e);
            }
        }

        public static function errorFiveHundred($errCode)
        {
            ErrorUI::error(500, 'Error: ' . $errCode->getMessage());
        }
    }