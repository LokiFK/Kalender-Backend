<?php

    Auth::start();

    class Auth {
        
        private static $status;
        private static $token;
        private static $user;
        private static $account;
        private static $admin;

		const DURATION = 'PT30M';

        public static function getToken(){
            return self::$token;
        }
        public static function getStatus(){
            return self::$status;
        }
        public static function getUser(){
            return self::$user;
        }
        public static function getAccount(){
            return self::$account;
        }
        public static function getAdmin(){
            return self::$admin;
        }

        public static function start(){
            self::$token = Auth::getGivenToken();
            if(self::$token == null){
                self::$status=0;
                return;
            }
            $res = DB::query("SELECT `userID`, `end` FROM `session` WHERE `token` = :token  AND (`end` IS NULL OR `end` > :end);", [':token' => self::$token, ":end" => date('Y/m/d h:i:sa')] );
            if (count($res) == 1) {
                if ($res[0]['end'] != null) {
                    $date = new DateTime();
                    $date->add(new DateInterval(Auth::DURATION));
                    $end = $date->format('Y/m/d h:i:sa');
                    DB::query("UPDATE `session` SET `end` = :end WHERE `token` = :token;", [':token' => self::$token, ':end' => $end]);
                } 
                self::$user = DB::query("SELECT * FROM users WHERE id = :id", [ ':id' => $res[0]['userID'] ])[0];
                self::$account = DB::query("SELECT * FROM account WHERE userID = :userID", [ ':userID' => $res[0]['userID'] ])[0];
                if(self::$account['createdAt']!=null){
                    $res2 = DB::query("SELECT * FROM `admin` WHERE userID = :userID", [ ':userID' => $res[0]['userID'] ]);
                    if(count($res2)==1){
                        self::$admin = $res2[0];
                        self::$status = 3;
                    } else {
                        self::$status = 2;
                    }
                } else {
                    self::$status = 1;
                }
            } else {
                self::$status = 0;
            }
        }

        public static function user()
        {
            return self::$user;
            /*$token = Auth::getCheckedToken();
            if (!isset($token)) { return; }

            $tokenWithUser = DB::table('session')->where('token = :token', [':token' => $token])->get([new ForeignDataKey('userID', 'users', 'id')]);
            if (!isset($tokenWithUser)) {
                ErrorUI::error(401, 'Invalid Token');
                exit;
            }

            return $tokenWithUser[0]['user'];*/
        }

        public static function userID()
        {
            return self::$user['id'];
            /*$token = Auth::getCheckedToken();
            return DB::table('session')->where('token = :token', [':token' => $token])->get([], ['userID'])[0]['userID'];
            */
        }

        public static function registerUser(User $user)
        {
            return DB::query("INSERT INTO users (firstname, lastname, salutation, insurance, birthday, patientID) VALUES (:firstname, :lastname, :salutation, :insurance, :birthday, :patientID)", [':firstname' => $user->firstname, ':lastname' => $user->lastname, ':salutation' => $user->salutation, ':insurance' => $user->insurance, ':birthday' => $user->birthday, ':patientID' => $user->patientID]);
            /*$userID = DB::table('users')->where('firstname = :firstname', [':firstname' => $user->firstname])->get([], ['id'])[0]['id'];    //nich bsonders sicher
            
            return $userID;*/
        }

        public static function registerAccount(Account $account, $approvalNeeded)
        {
            if (Auth::userExists($account->userID)) {
                if($approvalNeeded){                    //unterscheidet approved User von nicht approved User
                    $created = null;
                } else {
                    $created = date('Y-M-D');
                }
                DB::query("INSERT INTO account (userID, username, email, password, createdAt) VALUES (:userID, :username, :email, :password, :createdAt);",[':userID' => $account->userID, ':username' => $account->username, ':email' => $account->email, ':password' => password_hash($account->password, PASSWORD_DEFAULT), ':createdAt' => $created]);
                
                if($approvalNeeded){
                    return Auth::createNewCode($account->userID);
                }
            }
        }
        public static function createNewCode($userID)
        {
            try {
                $code = bin2hex(random_bytes(25));
            } catch (Exception $e) {
                ErrorUI::error(605, $e);
            }
            while (count(DB::table('notapproved')->where('code = :code', [':code' => $code])->get()) > 0) {
                try {
                    $code = bin2hex(random_bytes(25));
                } catch (Exception $e) {
                    ErrorUI::error(605, $e);
                }
            }
            DB::query("INSERT INTO notapproved (userID, code, datetime) VALUES (:userID, :code, :date);", [':userID' => $userID, ':code' => $code, ':date' => date('Y/m/d h:i:sa')]);
            return $code;
        }

        public static function approveAccount($code) {
            $res = DB::query("select userID from notapproved where code = :code", [":code"=>$code]);
            if(count($res)==1){
                DB::query("UPDATE account SET createdAt = :date WHERE userID = :userID;", [':userID' => $res[0]['userID'], ":date"=>date('Y-M-D') ]);
                return $res[0]['userID'];
            }
            return null;
        }
        public static function createNewResetCode($email){
            $res = DB::query("SELECT userID from account WHERE email = :email", [":email"=>$email]);
            if(count($res)==1){
                try {
                    $code = bin2hex(random_bytes(25));
                } catch (Exception $e) {
                    ErrorUI::error(605, $e);
                }
                while (count(DB::table('passwordreset')->where('code = :code', [':code' => $code])->get()) > 0) {
                    try {
                        $code = bin2hex(random_bytes(25));
                    } catch (Exception $e) {
                        ErrorUI::error(605, $e);
                    }
                }
                DB::query("INSERT INTO passwordreset (userID, code, datetime, isUsed) VALUES (:userID, :code, :date, :isUsed);", [':userID' => $res[0]['userID'], ':code' => $code, ':date' => date('Y/m/d h:i:sa'), ':isUsed'=>false]);
                return $code; 
            }
        }
        public static function resetPassword($code, $password){
            $res = DB::query("select userID from passwordreset where code = :code", [":code"=>$code]);
            if(count($res)==1){
                DB::query("UPDATE passwordreset SET isUsed = false WHERE code = :code;", [ ':code'=>$code ]);
                DB::query("UPDATE account SET password = :password WHERE userID = :userID;", [':userID' => $res[0]['userID'], ":password"=>password_hash($password, PASSWORD_DEFAULT) ]);
                return $res[0]['userID'];
            }
            return null;
        }

        public static function login($username, $password, bool $remember)
        {
            $accounts = DB::table('account')->where('username = :username', [':username' => $username])->get();
            if(count($accounts)==1){
                $account = $accounts[0];
                if (password_verify($password, $account['password'])) {
                    return Auth::specialLogin($account['userID'], $remember);
                }
            }
            return null;    
        }
        public static function specialLogin($userID, $remember){
            $token = Auth::createNewToken();
            $start = date('Y/m/d h:i:sa');
            $end = null;
            if (!$remember) {
                $date = new DateTime();
                $date->add(new DateInterval(Auth::DURATION));
                $end = $date->format('Y/m/d h:i:sa');
            }
            DB::query(
                "INSERT INTO `session` (`userid`, `token`, `start`, `end`) VALUES (:userID, :token, :start, :end);",
                [':userID' => $userID, ':token' => $token, ':start' => $start, ':end' => $end]
            );
            return $token;
        }

        public static function logout()
        {
            //$token = Auth::getToken();
            if(self::$status>0){
                DB::query('UPDATE `session` SET `end` = :end WHERE `token` = :token', [':token' => self::$token, ':end' => date('Y/m/d h:i:sa')]);
            }
        }

        public static function userExists($id): bool
        {
            $res = DB::query("SELECT `id` FROM `users` WHERE id = :id;", [':id' => $id]);
            return count($res) > 0;
        }

        private static function isValidToken($token): bool
        {
            $erg = DB::query("SELECT `end` FROM `session` WHERE `token` = :token AND (`end` IS NULL OR `end` > :end);", [':token' => $token, ":end" => date('Y/m/d h:i:sa')]);
            if (count($erg) == 1) {
                if ($erg[0]['end'] != null) {
                    $date = new DateTime();
                    $date->add(new DateInterval(Auth::DURATION));
                    $end = $date->format('Y/m/d h:i:sa');
                    DB::query("UPDATE `session` SET `end` = :end WHERE `token` = :token;", [':token' => $token, ':end' => $end]);
                } 
                return true;
            }
            return false;
        }

        public static function createNewToken()
        {
            try {
                $token = bin2hex(random_bytes(25));
            } catch (Exception $e) {
                ErrorUI::error(605, $e);
            }
            while (count(DB::table('session')->where('token = :token', [':token' => $token])->get()) > 0) {
                try {
                    $token = bin2hex(random_bytes(25));
                } catch (Exception $e) {
                    ErrorUI::error(605, $e);
                }
            }
            return $token;
        }

        public static function getCheckedToken() {
            $token = Auth::getToken();
            if ($token == null) {
                ErrorUI::error(401, 'Invalid Token');
                exit;
            }
            return $token;
        }
        /*public static function getToken() {
            $token = Auth::getTokenWithUnapprovedUsers();
            $res = DB::query("select count(*) as Anzahl from session, account where session.userID = account.userID and createdAt is not null and token = :token", [":token"=>$token]);
            if ($res[0]['Anzahl'] == 1) {
                return $token;
            }
        }*/
        public static function getTokenWithUnapprovedUsers() {
            $token = Auth::getGivenToken();
            if (Auth::isValidToken($token)) {
                return $token;
            }
        }
        public static function getGivenToken()
        {
            $token = "";
            if (isset($_POST['token'])) $token = $_POST['token'];
            else if (isset($_GET['token'])) $token = $_GET['token'];
            else if (isset($_COOKIE['token'])) $token = $_COOKIE['token'];
            else { return false; }
            return $token;
        }

        public static function isLoggedIn(): bool
        {
            $token = Auth::getToken();
            //echo "token: ".$token;
            return $token!=null;
        }

        public static function getUsername(): string
        {
            //return "testusername";
            $token = Auth::getCheckedToken();
            $username = DB::query("SELECT username FROM `account`, `session` WHERE account.userID = `session`.userID AND token = :token;", [':token' => $token]);
            if (count($username) > 0) {
                return $username[0]['username'];
            }
            return "Default";
        }

        public static function userIDExists($userID): bool
        {
            $res = DB::query("SELECT count(*) AS 'Anzahl' FROM users WHERE id = :userid;", [':userid' => $userID]);
            return $res[0]['Anzahl'] > 0;
        }
    }


    class User {
        public string $firstname = "";
        public string $lastname = "";
        public string $salutation = "";
        public string $birthday = "";
        public string $insurance = "";
        public string $patientID = "";

        public function __construct($firstname, $lastname, $salutation, $birthday, $insurance, $patientID)
        {
            $this->firstname = $firstname;
            $this->lastname = $lastname;
            $this->salutation = $salutation;
            $this->birthday = $birthday;
            $this->insurance = $insurance;
            $this->patientID = $patientID;
        }
    }

    class Account {
        public string $userID = "";
        public string $username = "";
        public string $email = "";
        public string $password = "";
        public string $approvalNeeded = "";

        public function __construct($userID, $username, $email, $password, $approvalNeeded)
        {
            $this->userID = $userID;
            $this->username = $username;
            $this->email = $email;
            $this->password = $password;
            $this->approvalNeeded = $approvalNeeded;
        }
    }

?>