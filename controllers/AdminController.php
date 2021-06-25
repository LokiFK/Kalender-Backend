<?php

    $day = "";

    class AdminController {
        
        public function __construct() {
            Middleware::statusBiggerOrEqualTo(3);
        }

        public function landingPage(Request $req, Response $res) {
            echo $res->view('admin/landingPage');
        }

        public function rooms(Request $req, Response $res) {
            $data = DB::query("SELECT * from room;");
            echo $res->view('admin/rooms/rooms', array(), array(), ['rooms'=>$data ]);
        }

        public function roomChange(Request $req, Response $res) {
            $data = Form::validateDataType($req->getBody(), ['type']);
            if($data['type']=="delete"){
                $data = Form::validateDataType($req->getBody(), ['number'=>"existingRoom"]);
                DB::query("DELETE FROM room WHERE number = :number", [":number"=>$data['number']]);
                Path::redirect(Path::ROOT."admin/rooms");
            } else if($data['type']=="changeRequest"){
                $data = Form::validateDataType($req->getBody(), ['number'=>"existingRoom"]);
                echo $res->view("admin/rooms/changeRoom", ['number'=>$data['number']]);
            } else if($data['type']=="change"){
                $data = Form::validateDataType($req->getBody(), ['number'=>"existingRoom", 'newNumber'=>"newRoom"]);
                DB::query("UPDATE room SET number=:newNumber WHERE number = :number", [":number"=>$data['number'], ":newNumber"=>$data['newNumber']]);
                Path::redirect(Path::ROOT."admin/rooms");
            } else if($data['type']=="newRequest"){
                echo $res->view("admin/rooms/newRoom");
            } else if($data['type']=="new"){
                $data = Form::validateDataType($req->getBody(), ['number'=>"newRoom"]);
                DB::query("INSERT INTO room(number) VALUES (:number)", [":number"=>$data['number']]);
                Path::redirect(Path::ROOT."admin/rooms");
            }
        }

        public function treatments(Request $req, Response $res) {
            $data = DB::query("SELECT * from treatment;");
            echo $res->view('admin/treatments/treatments',  [], [], ['treatments'=>$data]);
        }
        
        public function treatmentChange(Request $req, Response $res) {
            $data = Form::validateDataType($req->getBody(), ['type']);
            if($data['type']=="delete"){
                $data = Form::validateDataType($req->getBody(), ['name'=>"existingTreatment"]);
                DB::query("DELETE FROM treatment WHERE name = :name", [":name"=>$data['name']]);
                Path::redirect(Path::ROOT."admin/treatments");
            } else if($data['type']=="changeRequest"){
                $data = Form::validateDataTYpe($req->getBody(), ['name'=>"existingTreatment"]);
                $dbData = DB::query("SELECT * FROM treatment WHERE name=:name", [':name'=>$data['name']])[0];
                echo $res->view("admin/treatments/changeTreatment", $dbData);
            } else if($data['type']=="change"){
                $data = Form::validateDataTYpe($req->getBody(), ['name'=>"existingTreatment", 'newName'=>"newTreatment", 'duration'=>"int", 'nrDoctors'=>"int", 'nrNurses'=>"int"]);
                DB::query("UPDATE treatment SET name=:newName, duration=:duration, nrDoctors=:nrDoctors, nrNurses=:nrNurses WHERE name = :name", [":name"=>$data['name'], ":newName"=>$data['newName'], ':duration'=>$data['duration'], ':nrDoctors'=>$data['nrDoctors'], 'nrNurses'=>$data['nrNurses']]);
                Path::redirect(Path::ROOT."admin/treatments");
            } else if($data['type']=="newRequest"){
                echo $res->view("admin/treatments/newTreatment");
            } else if($data['type']=="new"){
                $data = Form::validateDataTYpe($req->getBody(), ['name'=>"newTreatment", 'duration'=>"int", 'nrDoctors'=>"int", 'nrNurses'=>"int"]);
                DB::query("INSERT INTO treatment(name, duration, nrDoctors, nrNurses) VALUES (:name, :duration, :nrDoctors, :nrNurses)", [":name"=>$data['name'], ':duration'=>$data['duration'], ':nrDoctors'=>$data['nrDoctors'], 'nrNurses'=>$data['nrNurses']]);
                Path::redirect(Path::ROOT."admin/treatments");
            }
        }

        public function newAppointment(Request $req, Response $res){
            if ($req->getMethod() == "GET") {
                $treatments = DB::query("SELECT * FROM treatment ORDER BY name");
                $rooms = DB::query("SELECT * FROM room ORDER BY number");
                echo $res->view("admin/newAppointment", [], [], ["treatments"=>$treatments, "rooms"=>$rooms]);
            } else if ($req->getMethod() == "POST") {
                $data = Form::validate($req->getBody(), ["day", 'start', 'end', 'room', 'treatment']);
                DB::query("INSERT INTO appointment(treatmentID, roomID, start, end, day) VALUES (:treatment, :room, :start, :end, :day)", [":treatment"=>$data["treatment"], ":room"=>$data["room"], ":start"=>$data["start"], ":end"=>$data["end"], ":day"=>$data["day"]]);
                Path::redirect(Path::ROOT."admin/appointment/new");
            } 
        }

        public function pending(Request $req, Response $res)
        {
            if ($req->getMethod() == "GET") {
                $appointments = DB::query("SELECT a.id, a.day, a.start, a.end, b.firstname, b.lastname, b.insurance FROM `appointment` a, `users` b WHERE `status` = 'warten' AND a.userID = b.id");
                echo $res->view('admin/pending', [], [], ['appointments' => $appointments]);
            } else {
                $data = Form::validateDataType($req->getBody(), ['id'=>"int", 'action']);

                // statusse = bestätigt, abgelehnt, warten, wahrgenommen

                if ($data['action'] == 'approve') {
                    DB::query('UPDATE `appointment` SET `status` = :status WHERE `id` = :id', [':status' => 'bestätigt', ':id' => $data['id']]);
                } else if ($data['action'] == 'decline') {
                    DB::query('UPDATE `appointment` SET `status` = :status WHERE `id` = :id', [':status' => 'abgelehnt', ':id' => $data['id']]);
                }

                Path::redirect(Path::ROOT . 'admin/pending');
            }
        }
        public function search(Request $req, Response $res){                        //für Echtzeit suche vllt spätewr ajax Lösung
            $search = "";
            $patients = [];
            if(isset($req->getBody()['search']) && $req->getBody()['search']!=null){
                $search = $req->getBody()['search'];
                $patients = DB::query("SELECT * FROM users WHERE lastname LIKE :name", [":name"=>$search . "%"]);  
            } else {
                $patients = DB::query("SELECT * FROM users"); 
            }
            echo $res->view('admin/search/search', ["search"=>$search],[], ["patients"=>$patients]);
        }
        public function user(Request $req, Response $res){
            if ($req->getMethod() == "GET") {
                $userID = Form::validate($req->getBody(),["id"])["id"];
                $userInfo = new UserInfo($userID);
                if($userInfo->user != null){
                    if($userInfo->account ==null){
                        $account = $res->view("admin/search/noAccount");
                    } else {
                        $account = $res->view("admin/search/account", ["account"=>$userInfo->account], [], [], $userInfo);
                    }
                    if($userInfo->admin ==null){
                        $admin = $res->view("admin/search/noAdmin");
                    } else {
                        $admin = $res->view("admin/search/admin", ["admin"=>$userInfo->admin], [], [], $userInfo);
                    }
                    echo $res->view("admin/search/user",["user"=>$userInfo->user, "account"=>$account, "admin"=>$admin, "userID"=>$userID], [], [], $userInfo);
                } else {
                    ErrorUI::error(400, 'Bad request');
                    exit;
                }
            } else {

            }
        }
        public function userChange(Request $req, Response $res){
            $data = $req->getBody();
            Form::validateDataType($data,["id", "type"]);
            $userID = $data["id"];
            if($data["type"] == "dataChange"){
                if ($req->getMethod() == "GET") {
                    $userInfo = new UserInfo($userID);
                    if($userInfo->user != null){
                        if($userInfo->account ==null){
                            $account = "";
                        } else {
                            $account = $res->view("admin/search/accountChange", ["account"=>$userInfo->account]);
                        }
                        if($userInfo->admin ==null){
                            $admin = "";
                        } else {
                            $admin = $res->view("admin/search/adminChange", ["admin"=>$userInfo->admin]);
                        }
                        echo $res->view("admin/search/userChange",["user"=>$userInfo->user, "account"=>$account, "admin"=>$admin, "userID"=>$userID]);
                    } else {
                        ErrorUI::error(400, 'Bad request');
                        exit;
                    }
                } else {
                    $userInfo = new UserInfo($userID);
                    if($userInfo->user != null){
                        Form::validateDataType($data, ['salutation','firstname','lastname','birthday','insurance']);
                            if(Form::validateDataType($data, ["patientID"], false)==null){
                                $data['patientID']=null;
                            }
                        DB::query("UPDATE users SET salutation=:salutation, firstname=:firstname, lastname=:lastname, birthday=:birthday, insurance=:insurance, patientID=:patientID WHERE id=:id", [":id"=>$userID, ":salutation"=>$data['salutation'], ":firstname"=>$data['firstname'], ":lastname"=>$data["lastname"], ":birthday"=>$data["birthday"], ":insurance"=>$data["insurance"], "patientID"=>$data["patientID"]]);
                        if($userInfo->account !=null){
                            Form::validateDataType($data, ['username'=>"username:".$userID,'email']);
                            DB::query("UPDATE account SET username=:username, email=:email WHERE userID=:userID", [":userID"=>$userID, ":username"=>$data['username'], ":email"=>$data["email"]]);
                            if(Form::validateDataType($data, ["password"], false)!=null){
                                Auth::specialResetPassword($userID, $data['password']);
                            }
                        }
                        if($userInfo->admin !=null){
                            Form::validateDataType($data, ['role']);
                            DB::query("UPDATE admin SET role=:role WHERE userID=:userID", [":userID"=>$userID, ":role"=>$data['role']]);
                        }
                        Path::redirect(Path::ROOT . 'admin/search/user?id='.$userID);
                    } else {
                        ErrorUI::error(400, 'Bad request');
                        exit;
                    }
                }
            } else if($data["type"] == "createAccount"){
                if ($req->getMethod() == "GET") {
                    echo $res->view("admin/search/accountCreate", ["userID"=>$userID]);
                } else {
                    Form::validateDataType($data, ["email"=>"newEmail","username","password"]);
                    Auth::registerAccount(new Account($userID, $data['username'], $data['email'], $data['password'], false), false);
                    Path::redirect(Path::ROOT . 'admin/search/user?id='.$userID);
                }
            } else if($data["type"] == "createAdmin"){
                if ($req->getMethod() == "GET") {
                    echo $res->view("admin/search/adminCreate", ["userID"=>$userID]);
                } else {
                    Form::validateDataType($data, ["role"=>"role"]);
                    Auth::registerAdmin(new Admin($userID, $data['role']));
                    Path::redirect(Path::ROOT . 'admin/search/user?id='.$userID);
                }
            } else if($data["type"] == "approveMail"){
                Auth::specialApproveAccount($userID);
                Path::redirect(Path::ROOT . 'admin/search/user?id='.$userID);
            } else if($data["type"] == "deleteAdmin"){
                DB::query("DELETE FROM admin WHERE userID=:userID", [":userID"=>$userID]);
                Path::redirect(Path::ROOT . 'admin/search/user?id='.$userID);
            } else if($data["type"] == "deleteAccount"){
                DB::query("DELETE FROM account WHERE userID=:userID", [":userID"=>$userID]);
                Path::redirect(Path::ROOT . 'admin/search/user?id='.$userID);
            } else if($data["type"] == "deleteUser"){
                echo "todo";                                                                        //todo
            } else {
                ErrorUI::error(400, 'Bad request');
                exit;
            }
        }
        public function createUser(Request $req, Response $res){
            if ($req->getMethod() == "GET") {
                echo $res->view("admin/search/userCreate");
            } else {
                $data = $req->getBody();
                Form::validateDataType($data, ['salutation','firstname','lastname','birthday','insurance']);
                    if(Form::validateDataType($data, ["patientID"], false)==null){
                         $data['patientID']=null;
                    }
                $userID = Auth::registerUser(new User($data['firstname'], $data['lastname'], $data['salutation'], $data['birthday'], $data['insurance'], $data['patientID']));
                Path::redirect(Path::ROOT . 'admin/search/user?id='.$userID);
            }
        }

        public function personalAppointments(Request $req, Response $res){
            Middleware::statusBiggerOrEqualTo(4);

            $appointments = DB::query("SELECT a.day,a.start,a.end,number,name,lastname,status FROM appointment a, appointment_admin b, room r, users u, treatment t WHERE a.id = b.appointmentID and a.roomID=r.id and a.userID=u.id and a.treatmentID=t.id AND adminID=:userID AND start>:datetime ORDER BY start;", [":userID"=>Auth::getUser()['id'], ":datetime"=>date(DB::DATE_FORMAT)]);
            $json = json_encode($appointments);
            echo $res->vieW("admin/personalAppointments", ["appointments"=>$json]);
        }
        public static function generalPlaning(Request $req, Response $res) {
            if ($req->getMethod()=="GET") {
                $treatment = DB::query("SELECT DISTINCT * FROM treatment");
                echo $res->view("admin/generalPlaning", array(), array(), ['treatment'=>$treatment]);
            } else if ($req->getMethod()=="POST") {
                $day = $req->getBody()['weekday'];
                $start = strtotime($req->getBody()['startTime']);
                $end = strtotime($req->getBody()['endTime']);
                $treatment = $req->getBody()['treatment'];
                $results = DB::query("SELECT * FROM appointment_typical JOIN treatment t on t.id = appointment_typical.treatment AND t.name = :treatment;", [':treatment' => $treatment]);
                foreach ($results as $result) {
                    $startTime = strtotime($result["startTime"]);
                    $endTime = strtotime($result["endTime"]);
                    if ($result["day"] = $day) {
//                        Zeitfenster muss innerhalb eines Tages liegen
                        if ($startTime <= strtotime("24:00") && $startTime >= strtotime("00:00")) {
                            if ($startTime > $endTime) {
                                $temp = $startTime;
                                $startTime = $endTime;
                                $endTime = $temp;
                            } else if (!(($start<=$startTime && $end<=$startTime) || ($start>=$endTime && $end>=$endTime))) {
                                echo $res->view('admin/merge', ['day'=>$day, 'start'=>date('H:i', $start), 'end'=>date('H:i', $end), 'treatment'=>$treatment, 'result'=>$result]);
                            }
                            $start=date('H:i:s', $start);
                            $startTime=date('H:i:s', $startTime);
                            $end=date('H:i:s', $end);
                            $endTime=date('H:i:s', $endTime);
                            DB::query("UPDATE appointment_typical SET startTime = :start WHERE startTime = :startTime AND day = :day AND treatment = :treatment", [':start'=>$start, ':startTime'=>$startTime, ':day'=>$day, ':treatment'=>$treatment]);
                            DB::query("UPDATE appointment_typical SET endTime = :end WHERE endTIme = :endTime AND day = :day AND treatment = :treatment", [':end'=>$end, ':endTime'=>$endTime, ':day'=>$day, ':treatment'=>$treatment]);
                            Path::redirect(Path::ROOT . 'admin/generalPlaning');
                        }
                    }
                }
            }
        }

        public static function merge(Request $req, Response $res) {
            if($req->getMethod()=="GET") {
                echo $res->view('/admin/merge');
            } else {
                $startTime = strtotime($req->getBody()['rstart']);
                $endTime = strtotime($req->getBody()['rend']);
                $start = strtotime($req->getBody()['start']);
                $end = strtotime($req->getBody()['end']);
                $day = $req->getBody()['day'];
                $treatment = $req->getBody()['treatment'];
                $treatmentId = DB::query("SELECT * FROM treatment WHERE name=:treatment", [':treatment'=>$treatment])[0]['id'];
                if (isset($_POST['merge'])) {
                    if ($startTime > $start) {
                        $start=date('H:i:s', $start);
                        $startTime=date('H:i:s', $startTime);
                        DB::query("UPDATE appointment_typical SET startTime = :start WHERE startTime = :startTime AND day = :day AND treatment = :treatment", [':start'=>$start, ':startTime'=>$startTime, ':day'=>$day, ':treatment'=>$treatmentId]);
                    }
                    if ($endTime < $end) {
                        $end=date('H:i:s', $end);
                        $endTime=date('H:i:s', $endTime);
                        DB::query("UPDATE appointment_typical SET endTime = :end WHERE endTIme = :endTime AND day = :day AND treatment = :treatment", [':end'=>$end, ':endTime'=>$endTime, ':day'=>$day, ':treatment'=>$treatmentId]);
                    }
                } else if(isset($_POST['overwrite'])) {
                    $start=date('H:i:s', $start);
                    $startTime=date('H:i:s', $startTime);
                    DB::query("UPDATE appointment_typical SET startTime = :start WHERE startTime = :startTime AND day = :day AND treatment = :treatment", [':start'=>$start, ':startTime'=>$startTime, ':day'=>$day, ':treatment'=>$treatmentId]);
                    $end=date('H:i:s', $end);
                    $endTime=date('H:i:s', $endTime);
                    DB::query("UPDATE appointment_typical SET endTime = :end WHERE endTIme = :endTime AND day = :day AND treatment = :treatment", [':end'=>$end, ':endTime'=>$endTime, ':day'=>$day, ':treatment'=>$treatmentId]);
                }
                Path::redirect(Path::ROOT . 'admin/generalPlaning');
            }
        }
        public static function overview(Request $req, Response $res){
            echo "todo";
        }
        public static function workhours(Request $req, Response $res){
            Middleware::statusBiggerOrEqualTo(4);

            $workhours = DB::query("SELECT * FROM workhours WHERE patientID=:userID ORDER BY day, start", [":userID"=>Auth::getUser()['id']]);
            $blocks = DB::query("SELECT * FROM workhoursblock WHERE patientID=:userID ORDER BY day, start", [":userID"=>Auth::getUser()['id']]);

            echo $res->view("admin/workhours/workhours", [],[],["workhours"=>$workhours, "blocks"=>$blocks]);
        }    
        public static function workhoursAdd(Request $req, Response $res){
            Middleware::statusBiggerOrEqualTo(4);
            $data = $req->getBody();
            Form::validateDataType($data, ['type']);
            if ($req->getMethod() == "GET") {
                if($data['type']=="workhours"){
                    echo $res->view("/admin/workhours/workhoursAdd");
                } else if($data['type']=="block"){
                    echo $res->view("/admin/workhours/workhoursAddBlock");
                }
            } else {
                if($data['type']=="workhours"){
                    Form::validateDataType($data, ['day'=>"weekday", "start"=>"time", "end"=>"time"]);
                    DB::query("INSERT INTO workhours(patientID, day, start, end) VALUES (:userID, :day, :start, :end)", [":day"=>$data['day'], ":start"=>$data['start'], ":end"=>$data["end"], ":userID"=>Auth::getUser()['id']]);
                } else if($data['type']=="block"){
                    Form::validateDataType($data, ['day'=>"date", "start"=>"time", "end"=>"time"]);
                    DB::query("INSERT INTO workhoursblock(patientID, day, start, end) VALUES (:userID, :day, :start, :end)", [":day"=>$data['day'], ":start"=>$data['start'], ":end"=>$data["end"], ":userID"=>Auth::getUser()['id']]);
                }
                Path::redirect(Path::ROOT . 'admin/workhours/workhours');
            }
        }  
        public static function workhoursDelete(Request $req, Response $res){
            Middleware::statusBiggerOrEqualTo(4);
            $data = $req->getBody();
            Form::validateDataType($data, ['type', 'id']);
            if($data['type']=="workhours"){
                DB::query("DELETE FROM workhours WHERE id=:id AND patientID=:userID", [":id"=>$data['id'], ":userID"=>Auth::getUser()['id']]);
            } else if($data['type']=="block"){
                DB::query("DELETE FROM workhoursblock WHERE id=:id AND patientID=:userID", [":id"=>$data['id'], ":userID"=>Auth::getUser()['id']]);
            }
            Path::redirect(Path::ROOT . 'admin/workhours/workhours');
        }    
    }

?>
