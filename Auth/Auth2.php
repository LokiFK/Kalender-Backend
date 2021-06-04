<?php

	class Auth{

		const DURATION = '30 Minutes';		

  public static function login($username, $password, $isEndless){
    $res = DB::query("select userid, password from account where username=:username", [':username'=>$username]);
    if(count($res)==1){
      if(verify($res[0]['password],$password)){  //hash überprüfen???
        login($res[0]['userid'],$IsEndless);
        return $res[0]['userid'];
      }
    }
    return false;
  }

		private static function login($userID, $isEndless){
			$tmp = true;
			while(tmp){
				$token = bin2hex(random_bytes(64));
				$erg = DB::query("select count(*) as 'Anzahl' from session where userid = :userid and token = :token;", [':userid' => $userid, ':token' => $token);
				if($erg[0]['Anzahl']==0){
					$tmp=false;
				}
			}
			$start = date('Y-M-D H:M:S')
			if($isEndless){
				$end = null;
			} else {
				$date = new DateTime();
				$date->add(new DateInterval(self::duration));
				$end = $date->format('Y-M-D H:M:S')
			}
			DB::query("INSERT INTO session (userid, token, start, end) VALUES (:userID, :token, :start, :end);", [':userID' => $userID, ':token' => $token, ':start' => $start, ':end' => $end]);
            return $token;
		}

		public static function getUserid(){
            $token = "";
			$userid = 0;
            if (isset($_POST['token']) && $_POST['userid'])){ $token = $_POST['token']; $userid = $_POST['userid'];}
            else if (isset($_GET['token']) && $_GET['userid'])){ $token = $_GET['token']; $userid = $_GET['userid'];}
            else { return false; }
            if (Auth::isValidToken($token, $userid)) {
                return $userid;
            } else {
				return false;
			}
        }
		private static function isValidToken($token, $userid){
            $erg = DB::query("select end from session where userid = :userid and token = :token and (end is null or end<now());", [':userid' => $userid, ':token' => $token);
            if(count($erg)==1){
				if($erg[0]['end']!=null){
					$date = new DateTime();
					$date->add(new DateInterval(self::duration));
					$end = $date->format('Y-M-D H:M:S')
					DB::query("update session set end = :end where userid = :userid and token = :token;", [':userid'=>$userid, ':token'=>$token, ':end'=>$end]);
				} 
				return true;
			}
			return false;
        }

		public static function logout(){
			$token = "";
			$userid = 0;
            if (isset($_POST['token']) && $_POST['userid'])){ $token = $_POST['token']; $userid = $_POST['userid'];}
            else if (isset($_GET['token']) && $_GET['userid'])){ $token = $_GET['token']; $userid = $_GET['userid'];}
            else { return false; }
			if (Auth::isValidToken($token, $userid)) {
				$end = date('Y-M-D H:M:S')
				DB::query("update session set end = :end where userid = :userid and token = :token;", [':userid'=>$userid, ':token'=>$token, ':end'=>$end]);
				return true;
			}
			return false;
		}

public static function isAdmin($userid){
  $res = DB::query("select count(*) as 'Anzahl' from admin where userid = :userid", [':userid'=>$userid]);
  If($res[0]['Anzahl']==1){
    return true;
  }
  return false;
}
	}

?>
