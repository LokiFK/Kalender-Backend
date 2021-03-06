<?php

    class AuthController {

        public function createUser(Request $req, Response $res)
        {
            if ($req->getMethod() == "GET") {
                echo $res->view('auth/createUser');
            } else if ($req->getMethod() == "POST") {
                $validatedData = Form::validateDataType($req->getBody(), ['firstname', 'lastname', 'salutation', 'insurance', 'birthday', 'username'=>'newUsername', 'email'=>"newEmail", 'password', 'agb']);
                $id = Auth::registerUser(
                    new User(
                        $validatedData['firstname'],
                        $validatedData['lastname'],
                        $validatedData['salutation'],
                        $validatedData['birthday'],
                        $validatedData['insurance'],
                        ""
                    )
                );
                $code = Auth::registerAccount(
                    new Account(
                        $id,
                        $validatedData['username'],
                        $validatedData['email'],
                        $validatedData['password'],
                        false
                    ), true
                );

                $token = Auth::specialLogin($id, false);
                if($token!=null){
                    setcookie('token', $token, time() + 60 * 60 * 24 * 30, '/');
                } else {
                    echo "Fehler";      //todo
                }

                $link =  $_SERVER['HTTP_HOST'].'/auth/account/approve?code='.$code;
                echo "mail: <a href=\"$link\">".$link."</a><br>";
                echo "<a href=../../../auth/account/notApproved>automatische Weiterleitung</a>";
            }
        }

        public function approve(Request $req, Response $res) {
            echo "fjdso";
            $data = Form::validate($req->getBody(), ['code']);
            $id = Auth::approveAccount($data['code']);
            if ($id != null) {
                $token = Auth::specialLogin($id, false);
                setcookie('token', $token, time() + 60 * 60 * 24 * 30, '/');
                Auth::start();
                echo $res->view("auth/approved");                               //todo
            } else {
                ErrorUI::errorMsg(500, 'bad request');
                exit();
            }
        }

        public function notApproved(Request $req, Response $res) {
            Middleware::statusEqualTo(1);

            if ($req->getMethod() == "GET") {
                echo $res->view("auth/notApproved", ["email"=>Auth::getAccount()['email']]);
            } else if ($req->getMethod() == "POST") {
                if ( Form::validateDataType($req->getBody(), ['email'=>"newEmail"], false) != null ) {
                    DB::query("UPDATE account SET email = :email WHERE userID = :userID", [ ':email'=>$req->getBody()['email'], ':userID'=>Auth::getUser()['id'] ]);
                }
                $code = Auth::createNewCode(Auth::getUser()['id']);

                $link =  $_SERVER['HTTP_HOST'].'/auth/account/approve?code='.$code;
                echo "mail: <a href=\"$link\">".$link."</a><br>";
                echo "<a href=../../../auth/account/notApproved>automatische R??ckleitung</a>";
                
                /*$from = "FROM Terminplanung @noreply";
                $subject = "Account best??tigen";
                $msg = "BlaBlaBla Hier ihr anmelde Link: '.$link;
                mail($account->email, $subject, $msg, $from);
                
                Path::redirect('../../../auth/account/emailApproved');
                */
            }
        }

        public function login(Request $req, Response $res)
        {
            if ($req->getMethod() == "GET") {
                //echo "1";
                echo $res->view('auth/login');
            } else if ($req->getMethod() == "POST") {
                //echo "2";
                $validatedData = Form::validateDataType($req->getBody(), ['username'=>"", 'password']);
                $remember = isset($req->getBody()['remember']) && $req->getBody()['remember'] == 'on';
                //echo "3";
                $token = Auth::login($validatedData['username'], $validatedData['password'], $remember);
                //echo "4";
                if ($token != null) {
                    setcookie('token', $token, time() + 60 * 60 * 24 * 30, '/');

                    $res = DB::query("select count(*) as Anzahl from session, account where session.userID = account.userID and token = :token and createdAt is not null", [":token"=>$token]);
                    if($res[0]['Anzahl']==1){   //approved
                        Path::redirect(Path::ROOT);
                    } else {
                        Path::redirect(Path::ROOT . "auth/account/notApproved");
                    }
                } else {
                    ErrorUI::error(401, "Falscher Nutzername oder Passwort");
                }
            }
        }

        public function logout(Request $req, Response $res){
            if (Auth::logout() != true) {

            }
            Path::redirect(Path::ROOT);
        }

        public function resetLink(Request $req, Response $res){
            if ($req->getMethod() == "GET") {
                echo $res->view('auth/resetLink');
            } else if ($req->getMethod() == "POST") {
                $validatedData = Form::validateDataType($req->getBody(), ['email']);
                $code = Auth::createNewResetCode($validatedData['email']);
                    
                if($code!=null){
                    $link =  $_SERVER['HTTP_HOST'].'/auth/account/resetPassword?code='.$code;
                    echo "mail: <a href=\"$link\">".$link."</a><br>";
                    echo "<a href=>automatische R??ckleitung</a>";
                        
                    /*$from = "FROM Terminplanung @noreply";
                    $subject = "Account best??tigen";
                    $msg = "BlaBlaBla Hier ihr anmelde Link: '.$link;
                    mail($account->email, $subject, $msg, $from);
                        
                    Path::redirect('');
                    */
                } else {
                    Path::redirect('/auth/account/resetLink');
                }
            }
        }
        public function resetPassword(Request $req, Response $res){
            if ($req->getMethod() == "GET") {
                $data = Form::validate($req->getBody(), ['code']);
                echo $res->view('auth/resetPassword', ["code"=>$data['code']]);
            } else if ($req->getMethod() == "POST") {
                $data = Form::validateDataType($req->getBody(), ['password', 'code']);
                $userID = Auth::resetPassword($data['code'], $data['password']);
                if($userID!=null){
                    $token = Auth::specialLogin($userID, false);
                    setcookie('token', $token, time() + 60 * 60 * 24 * 30, '/');
                    Path::redirect(Path::ROOT);
                } else {
                    ErrorUI::errorMsg(500, 'bad request');
                    exit(); 
                }
            } 
        }

        public function resetUserdata(Request $req, Response $res) {
            if ($req->getMethod() == "GET") {
                echo $res->view('auth/resetUserdata');
            } else {
                $userID = Auth::getUserID();
                $data = Form::validateDataType($req->getBody(), ['email', 'password']);
                if (count($data)>0) {
                    $accounts = DB::table('account')->where('email = :email', [':email' => $data['email']])->get();
                    if (count($accounts) == 1) {
                        $account = $accounts[0];
                        if (password_verify($data['password'], $account['password'])) {
                            $token = Auth::specialLogin($userID, false);
                            setcookie('token', $token, time() + 60 * 60 * 24 * 30, '/');
                            Path::redirect(Path::ROOT . 'auth/account/dataReset');
                        } else {
                            $res->errorVisual(401, "Bitte das Passwort ??berpr??fen");
                        }
                    } else {
                        ErrorUI::error(401, "Bitte Angaben ??berpr??fen");
                    }
                } else {
                    ErrorUI::popRedirect("Bitte Angaben ??berpr??fen", Path::ROOT . 'auth/account/resetUserdata');
                }
            
            }
        }

        public function dataReset(Request $req, Response $res) {
            $userId = Auth::getUserID();
            $user = DB::table('users')->where("id = :id",[':id'=>$userId])->get();
            $account = DB::table('account')->where("userID = :id", [':id'=>$userId])->get();
            if(count($user)>0 && count($account)>0) {
                $user = $user[0];
                $account = $account[0];
            } else {
                $res->errorVisual(500, "Nutzer nicht gefunden");
            }
            if ($req->getMethod() == "GET") {
                $birthday = date('Y-m-d', strtotime($user['birthday']));
                $view = $res->view('auth/dataReset', ['firstname' => $user['firstname'], 'lastname' => $user['lastname'], 'salutation' => $user['salutation'], 'insurance' => $user['insurance'], 'birthday' => $birthday, 'email' => $account['email']]);
                echo $view;
            } else {
                $birthday = $req->getBody()['birthday'];
                if ($birthday == null) {
                    ErrorUI::popRedirect("Bitte alles ausf??llen", Path::ROOT . 'auth/account/dataReset');
                    $view = $res->view('auth/dataReset', ['firstname' => $user['firstname'], 'lastname' => $user['lastname'], 'salutation' => $user['salutation'], 'insurance' => $user['insurance'], 'birthday' => $birthday, 'email' => $account['email']]);
                    echo $view;
                }
                $data = Form::validateNewData($req->getBody(), ['firstName', 'lastName', 'insurance', 'birthday', 'salutation', 'email']);
                $changeData = array('firstname', 'lastname', 'insurance', 'birthday', 'salutation');
                for ($i = 0; $i < count($changeData); $i++) {
                    if($data[$i] != Auth::getUser()[$changeData[$i]]){
                      DB::query("UPDATE users SET $changeData[$i]=:data WHERE id=:userId", [':data'=>$data[$i], ':userId'=>$userId]);
                    }
                }
                if($req->getBody()['email'] != Auth::getAccount()['email']){
                    //echo "1";
                    DB::query("UPDATE account SET `email`=:data, `createdAt`=null WHERE userID=:userId", [':data'=>$req->getBody()['email'], ':userId'=>$userId]);
                    //echo "hi";
                    $code = Auth::createNewCode($userId);
                    $link =  $_SERVER['HTTP_HOST'].'/auth/account/approve?code='.$code;
                    echo "mail: <a href=\"$link\">".$link."</a><br>";
                    echo "<a href=../../../auth/account/notApproved>automatische Weiterleitung</a>";
                        
                    /*$from = "FROM Terminplanung @noreply";
                    $subject = "Account best??tigen";
                    $msg = "BlaBlaBla Hier ihr anmelde Link: '.$link;
                    mail($account->email, $subject, $msg, $from);
                        
                    Path::redirect('../../../auth/account/emailApproved');
                    */
                } else {
                  Path::redirect(Path::ROOT . 'user/profile');
                }
            }
        }

        public function permissions(Request $req, Response $res)
        {
            Middleware::auth();

            $userID = Auth::getUser()['id'];
            
            $adminRelation = DB::table('admin')->where('userID = :userID', [':userID' => $userID])->get();
            return count($adminRelation) > 0
                ? $res->json(array('role' => 'admin'))
                : $res->json(array('role' => 'account'));
        }
    }

?>
