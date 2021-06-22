<?php

    Auth::start();

    class Auth {
        
		const DURATION = 'PT30M';
        const GUEST = 0;
        const NOTAPPROVED = 1;
        const USER = 2;
        const ADMIN = 3;
        
        private static $status;
        private static $token;
        private static $user;
        private static $account;
        private static $admin;

        public static function getToken() {
            return self::$token;
        }
        
        public static function getStatus() {
            return self::$status;
        }
        
        public static function getUser() {
            return self::$user;
        }
        
        public static function getAccount() {
            return self::$account;
        }

        public static function getAdmin() {
            return self::$admin;
        }

        public static function getUserID() {
            if(self::$user==null) return null;
            return self::$user['id'];
        }

        public static function start() {
            self::$token = self::getGivenToken();
            if (self::$token == null && !self::isLoggedIn()) {
                self::$status = self::GUEST;
                return;
            }
            $res = DB::query("SELECT `userID`, `end` FROM `session` WHERE `token` = :token  AND (`end` IS NULL OR `end` > :end);", [':token' => self::$token, ":end" => date(DB::DATE_FORMAT)]);
            if (count($res) == 1) {
                if ($res[0]['end'] != null) {
                    $date = new DateTime();
                    $date->add(new DateInterval(self::DURATION));
                    $end = $date->format(DB::DATE_FORMAT);
                    DB::query("UPDATE `session` SET `end` = :end WHERE `token` = :token;", [':token' => self::$token, ':end' => $end]);
                } 
                self::$user = DB::query("SELECT * FROM users WHERE id = :id", [ ':id' => $res[0]['userID'] ])[0];
                self::$account = DB::query("SELECT * FROM account WHERE userID = :userID", [ ':userID' => $res[0]['userID'] ])[0];
                if (self::$account['createdAt'] != null) {
                    $res2 = DB::query("SELECT * FROM `admin` WHERE userID = :userID", [ ':userID' => $res[0]['userID'] ]);
                    if(count($res2) > 0) {
                        self::$admin = $res2[0];
                        self::$status = self::ADMIN;
                    } else {
                        self::$status = self::USER;
                    }
                } else {
                    self::$status = self::NOTAPPROVED;
                }
            } else {
                self::$status = self::GUEST;
            }
        }

        public static function registerUser(User $user)
        {
            return DB::query(
                "INSERT INTO `users` (`firstname`, `lastname`, `salutation`, `insurance`, `birthday`, `patientID`) VALUES (:firstname, :lastname, :salutation, :insurance, :birthday, :patientID)",
                [':firstname' => $user->firstname, ':lastname' => $user->lastname, ':salutation' => $user->salutation, ':insurance' => $user->insurance, ':birthday' => $user->birthday, ':patientID' => $user->patientID]
            );
        }

        public static function registerAccount(Account $account, $approvalNeeded)
        {
            if (Auth::userExists($account->userID)) {
                if ($approvalNeeded) {
                    $createdAt = null;
                } else {
                    $createdAt = date(DB::DATE_FORMAT);
                }
                DB::query(
                    "INSERT INTO account (userID, username, email, password, createdAt) VALUES (:userID, :username, :email, :password, :createdAt);",
                    [':userID' => $account->userID, ':username' => $account->username, ':email' => $account->email, ':password' => password_hash($account->password, PASSWORD_DEFAULT), ':createdAt' => $createdAt]
                );
                
                if ($approvalNeeded) {
                    return Auth::createNewCode($account->userID);
                }
            }
        }

        public static function approveAccount($code) {
            $res = DB::table("notapproved")->where("`code` = :code", [":code"=>$code])->get([], ['userID']);
            if (count($res) == 1) {
                DB::query(
                    "UPDATE `account` SET `createdAt` = :date WHERE `userID` = :userID;",
                    [':userID' => $res[0]['userID'], ":date" => date(DB::DATE_FORMAT)]
                );
                return $res[0]['userID'];
            }
            return null;
        }

        public static function createNewResetCode(string $email) {
            $res = DB::query("SELECT `userID` FROM `account` WHERE `email` = :email", [":email" => $email]);
            if (count($res) == 1) {
                $code = self::createCheckedRandomHash("passwordreset", "code");
                
                DB::query(
                    "INSERT INTO `passwordreset` (`userID`, `code`, `datetime`, `isUsed`) VALUES (:userID, :code, :date, :isUsed);",
                    [':userID' => $res[0]['userID'], ':code' => $code, ':date' => date(DB::DATE_FORMAT), ':isUsed' => false]
                );
                return $code; 
            }
        }

        public static function resetPassword($code, $password){
            $res = DB::table("passwordreset")->where("`code` = :code", [":code" => $code])->get([], ['userID']);
            if (count($res) == 1) {
                DB::query("UPDATE `passwordreset` SET `isUsed` = false WHERE `code` = :code;", [':code' => $code]);
                DB::query(
                    "UPDATE `account` SET `password` = :password WHERE `userID` = :userID;",
                    [':userID' => $res[0]['userID'], ":password" => password_hash($password, PASSWORD_DEFAULT)]
                );
                return $res[0]['userID'];
            }
            return null;
        }

        public static function login($username, $password, bool $remember)
        {
            $accounts = DB::table('account')->where('username = :username', [':username' => $username])->get();
            if (count($accounts) == 1) {
                $account = $accounts[0]; // has to be unneccessary, username has to be unique
                if (password_verify($password, $account['password'])) {
                    return Auth::specialLogin($account['userID'], $remember);
                }
            }
            return null;
        }
        public static function specialLogin($userID, $remember) {
            $token = self::createCheckedRandomHash("session", "token");
            $start = date(DB::DATE_FORMAT);
            $end = null;
            if (!$remember) {
                $date = new DateTime();
                $date->add(new DateInterval(Auth::DURATION));
                $end = $date->format(DB::DATE_FORMAT);
            }
            DB::query(
                "INSERT INTO `session` (`userid`, `token`, `start`, `end`, `ip`) VALUES (:userID, :token, :start, :end, :ip);",
                [':userID' => $userID, ':token' => $token, ':start' => $start, ':end' => $end, ":ip"=>$_SERVER["REMOTE_ADDR"]]
            );
            return $token;
        }

        public static function logout()
        {
            if (self::$status > 0 && (isset($_COOKIE['token']))) {
                DB::query('UPDATE `session` SET `end` = :end WHERE `token` = :token', [':token' => self::$token, ':end' => date(DB::DATE_FORMAT)]);
                unset($_COOKIE['token']); 
                setcookie('token', null, -1, '/');
            }
        }

        public static function userExists($id): bool
        {
            $res = DB::table("users")->where("`id` = :id;", [':id' => $id])->get([], ['id']);
            return count($res) > 0;
        }

        private static function isValidToken($token): bool
        {
            $erg = DB::table("session")->where("`token` = :token AND (`end` IS NULL OR `end` > :end);", [':token' => $token, ":end" => date(DB::DATE_FORMAT)])->get([], ['end']);
            if (count($erg) == 1) {
                if ($erg[0]['end'] != null) {
                    $date = new DateTime();
                    $date->add(new DateInterval(Auth::DURATION));
                    $end = $date->format(DB::DATE_FORMAT);
                    DB::query("UPDATE `session` SET `end` = :end WHERE `token` = :token;", [':token' => $token, ':end' => $end]);
                } 
                return true;
            }
            return false;
        }

        public static function getCheckedToken() {
            $token = Auth::getToken();
            if ($token == null) {
                ErrorUI::error(401, 'Invalid Token');
                exit;
            }
            return $token;
        }
        
        public static function getTokenWithUnapprovedUsers() {
            $token = Auth::getGivenToken();
            if (Auth::isValidToken($token)) {
                return $token;
            }
        }
        
        public static function getGivenToken(): string
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
            return self::getTokenWithUnapprovedUsers() != null;
        }

        public static function getUsername(): string
        {
            /*$token = Auth::getCheckedToken();
            $username = DB::query("SELECT `username` FROM `account`, `session` WHERE `account`.`userID` = `session`.`userID` AND `token` = :token;", [':token' => $token]);
            if (count($username) > 0) {
                return $username[0]['username'];
            }
            return "Default";*/

            if (self::$status > 1) {
                return self::$account['username'];
            }
            return "Default";
        }

        public static function userIDExists($userID): bool
        {
            $res = DB::query("SELECT count(*) AS 'Anzahl' FROM users WHERE id = :userid;", [':userid' => $userID]);
            return $res[0]['Anzahl'] > 0;
        }

        public static function createRandomHash(): string
        {
            try {
                return bin2hex(random_bytes(25));
            } catch (Exception $e) {
                ErrorUI::error(605, $e);
            }
        }

        public static function createCheckedRandomHash(string $table, string $columnName)
        {
            $code = self::createRandomHash();
            while (count(DB::table($table)->where($columnName . ' = :code', [':code' => $code])->get()) > 0) {
                $code = self::createRandomHash();
            }
            return $code;
        }
        
        public static function createNewCode($userID)
        {
            $code = self::createCheckedRandomHash("notapproved", "code");

            DB::query(
                "INSERT INTO `notapproved` (`userID`, `code`, `datetime`) VALUES (:userID, :code, :date);",
                [':userID' => $userID, ':code' => $code, ':date' => date(DB::DATE_FORMAT)]
            );
            return $code;
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