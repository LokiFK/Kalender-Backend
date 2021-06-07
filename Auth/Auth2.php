<?php

	  class Auth {

		const DURATION = '30 Minutes';		

        public static function registerUser($vorname, $nachname, $anrede, $geburtstag, $patientenid){
            $res = DB::query("insert into user(vorname,nachname,geburtstag,patientenid) values :vor,:nach,:an,:geb,:pat;",[':vor'=>$vorname, ':nach'=>$nachname, ':geb'=>$geburtstag, ':an'=>$anrede, ':pat'=>$patientenid]);
            return $res;
        }

        public static function registerAccount($userID, $username, $email, $password, $approvalNeeded) {
            if(Auth::userExists($userID)) {
                $date = date('Y-M-D');
                if ($approvalNeeded){
                    $date = null;
                }
                $res = DB::query("insert into account(userid, username, email, password, erstellungsdatum) values :i,:u,:e,:p,:d;",[':i'=>$userID,':u'=>$username,':e'=>$email,':d'=>$date,':p'=>password_hash($password, PASSWORD_DEFAULT)]);
                if($approvalNeeded){
                    $code = bin2hex(random_bytes(50));
                    DB::query("insert into notapproved(userid, code, datetime) values :u,:c,now();",[':u'=>$userID,':c'=>$code]);
                    $from = "FROM Terminplanung @noreply";
                    $betreff = "Account bestätigen";
                    $msg = $code;
                    mail($email,$betreff,$msg,$from);
                }
                return $res;
            }
        }

        public static function approveMail($userid,$code){
            $res = DB::query("select code from notapproved where userid=:u order by datetime desc limit 1;",[':u'=>$userid]);
            if(count($res)==1){
                if($res[0]['code']==$code){
                    DB::query("update account set erstellungsdatum=now() where userid=:u;", [':u'=>$userid]);
                } else {
                    return false;
                }
            }
            return false;
        }

        public static function loginWithPassword($username, $password, $ip, $isEndless) {
            $res = DB::query("select userid, password from account where username=:username;", [':username'=>$username]);
            if(count($res)==1) {
                if(password_verify($password, $res[0]['password'])) {
                    Auth::login($res[0]['userid'],$ip,$isEndless);
                    return $res[0]['userid'];
                }
            }
            return false;
        }

        private static function login($userID, $ip, $isEndless) {
            $tmp = true;
            while($tmp) {
                $token = bin2hex(random_bytes(64));
                $erg = DB::query("select count(*) as 'Anzahl' from session where userid = :userid and token = :token;", [':userid' => $userID, ':token' => $token]);
                if($erg[0]['Anzahl'] == 0) {
                    $tmp=false;
                }
            }
            $start = date('Y-M-D H:M:S');
            if($isEndless) {
                $end = null;
            } else {
                $date = new DateTime();
                $date->add(new DateInterval(Auth::DURATION));
                $end = $date->format('Y-M-D H:M:S');
            }
            DB::query("INSERT INTO session (userid, token, start, end, ip) VALUES (:userID, :token, :start, :end, :ip);", [':userID' => $userID, ':token' => $token, ':start' => $start, ':end' => $end, ':ip'=>$ip]);
            return $token;
        }

        public static function getUserid() {

            /**
             * Warum nicht einfach das:
             * $token = Auth::getToken();
             * $userID = Auth::userID();
             * Da hast du die Token-validation direkt mit drin und ganz nebenbei hast du dadurch verdopplungen im Code vermieden
             */

            $token = "";
            $userid = 0;
            if (isset($_POST['token']) && $_POST['userid']) { $token = $_POST['token']; $userid = $_POST['userid']; }
            else if (isset($_GET['token']) && $_GET['userid']) { $token = $_GET['token']; $userid = $_GET['userid']; }
            else { return false; }
            if (Auth::isValidToken($token, $userid)) {
                return $userid;
            } else {
                return false;
            }
        }

        // Wie wäre es, wenn wir einfach die Methode nehmen, um die UserID zu bekommen? schaut einfacher aus
        public static function userID()
        {
            $token = Auth::getToken();
            return DB::table('tokens')->where('token = :token', [':token' => $token])->get([], ['user_id'])[0]['user_id'];
        }

        private static function isValidToken($token, $userid) {
            $erg = DB::query("select end from session where userid = :userid and token = :token and (end is null or end<now());", [':userid' => $userid, ':token' => $token]);
            if (count($erg) == 1) {
                if ($erg[0]['end'] != null) {
                    $date = new DateTime();
                    $date->add(new DateInterval(Auth::DURATION));
                    $end = $date->format('Y-M-D H:M:S');
                    DB::query("update session set end = :end where userid = :userid and token = :token;", [':userid'=>$userid, ':token'=>$token, ':end'=>$end]);
                } 
                return true;
            }
            return false;
        }

        public static function logout() {

            /**
             * Warum nicht einfach das:
             * $token = Auth::getToken();
             * $userID = Auth::userID();
             * Da hast du die Token-validation direkt mit drin und ganz nebenbei hast du dadurch verdopplungen im Code vermieden
             */

            $token = "";
            $userid = 0;
            if (isset($_POST['token']) && $_POST['userid']) { $token = $_POST['token']; $userid = $_POST['userid']; }
            else if (isset($_GET['token']) && $_GET['userid']) { $token = $_GET['token']; $userid = $_GET['userid']; }
            else { return false; }
            if (Auth::isValidToken($token, $userid)) {
                $end = date('Y-M-D H:M:S');
                DB::query("update session set end = :end where userid = :userid and token = :token;", [':userid'=>$userid, ':token'=>$token, ':end'=>$end]);
                return true;
            }
            return false;
        }

        public static function userExists($userid) {
            $res = DB::query("select count(*) as 'Anzahl' from user where id = :userid;", [':userid'=>$userid]);
            return $res[0]['Anzahl'] == 1;
        }

        public static function isAdmin($userid){
            $res = DB::query("select count(*) as 'Anzahl' from admin where userid = :userid;", [':userid'=>$userid]);
            return $res[0]['Anzahl'] == 1;
        }

        public static function isApproved($userid){
            $res = DB::query("select erstellungsdatum from account where userid = :userid;", [':userid'=>$userid]);
            return $res[0]['erstellungsdatum'] == null; 
        }
    }

?>
