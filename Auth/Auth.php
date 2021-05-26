<?php

    class Auth {

        public static function user()
        {
            $token = Auth::getToken();
            if (!isset($token)) { return; }

            $tokenWithUser = DB::table('tokens')->where('token = :token', [':token' => $token])->get();
            if (!isset($tokenWithUser)) { 
                UI::error(401, 'Invalid Token');
                exit;
            }

            return $tokenWithUser['user'];
        }

        public static function userID()
        {
            $token = Auth::getToken();
            return DB::table('tokens')->where('token = :token', [':token' => $token])->get([], ['user_id'])[0]['user_id'];
        }

        public static function login($userID)
        {
            $token = bin2hex(random_bytes(64));
            $timestamp = date("Y-m-d");

            DB::query("INSERT INTO tokens (user_id, token, timestamp) VALUES (:userID, :token, :timestamp);", [':userID' => $userID, ':token' => $token, ':timestamp' => $timestamp]);
            return $token;
        }

        public static function logout()
        {
            $token = Auth::getToken();
            if (!isset($token)) { return; }
            
            $userID = Auth::userID();
            if (!isset($userID)) { return; }

            $tokenID = DB::table('tokens')->where('token = :token AND user_id = :user_id', [':token' => $token, ':user_id' => $userID])->get([], ['id'])[0]['id'];
            DB::query('DELETE FROM tokens WHERE id = :id', [':id' => $tokenID]);
        }

        private static function isValidToken($token)
        {
            $timestamp = DB::table('tokens')->where('token = :token', [':token' => $token])->get(['timestamp'])[0]['timestamp'];
            if (!isset($timestamp)) {
                UI::error(401, 'Invalid Token');
                exit;
            }
            $lastLogin = new DateTime($timestamp);
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