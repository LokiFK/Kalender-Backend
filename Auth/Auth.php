<?php

    class Auth {

        public static function user()
        {
            $token = Auth::getToken();
            if (!isset($token)) { return; }

            $tokenWithUser = Tokens::where(['token', '=', $token], Tokens::INCLUDE_FOREIGN_DATA)->get();
            if (!isset($tokenWithUser)) { 
                UI::error(401, 'Invalid Token');
                exit;
            }

            return $tokenWithUser['user'];
        }

        public static function userID()
        {
            $token = Auth::getToken();
            return Tokens::where(['token', '=', $token])->get(['user_id']);
        }

        public static function login($userID)
        {
            $token = bin2hex(random_bytes(64));
            $timestamp = date("Y-m-d");
            Tokens::insert([$userID, $token, $timestamp]);
            return $token;
        }

        public static function logout()
        {
            $token = Auth::getToken();
            if (!isset($token)) { return; }
            
            $userID = Auth::userID();
            if (!isset($userID)) { return; }

            $tokenID = Tokens::where([
                'control' => '1 AND 2',
                1 => ['token', '=', $token],
                2 => ['user_id', '=', $userID]
            ], Tokens::IGNORE_FOREIGN_DATA)->get(['id']);

            Tokens::delete($tokenID);
        }

        private static function isValidToken($token)
        {
            $timestamp = Tokens::where(['token', '=', $token])->get(['timestamp']);
            if (!isset($timestamp)) {
                UI::error(401, 'Invalid Token');
                exit;
            }
            $lastLogin = new DateTime($timestamp[0]['timestamp']);
            $currentTime = new DateTime();
            return $lastLogin->diff($currentTime)->d < 10;
        }

        public static function getToken()
        {
            $token = isset($_POST['token']) ? $_POST['token'] : $_GET['token'];
            if (Auth::isValidToken($token)) {
                return $token;
            }
        }
    }

?>