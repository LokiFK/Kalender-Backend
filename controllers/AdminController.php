<?php

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
            $data = Form::validate($req->getBody(), ['type']);
            if($data['type']=="delete"){
                $data = Form::validate($req->getBody(), ['number']);
                DB::query("DELETE FROM room WHERE number = :number", [":number"=>$data['number']]);
                Path::redirect(Path::ROOT."admin/rooms");
            } else if($data['type']=="changeRequest"){
                $data = Form::validate($req->getBody(), ['number']);
                echo $res->view("admin/rooms/changeRoom", ['number'=>$data['number']]);
            } else if($data['type']=="change"){
                $data = Form::validate($req->getBody(), ['number', 'newNumber']);
                DB::query("UPDATE room SET number=:newNumber WHERE number = :number", [":number"=>$data['number'], ":newNumber"=>$data['newNumber']]);
                Path::redirect(Path::ROOT."admin/rooms");
            } else if($data['type']=="newRequest"){
                echo $res->view("admin/rooms/newRoom");
            } else if($data['type']=="new"){
                $data = Form::validate($req->getBody(), ['number']);
                DB::query("INSERT INTO room(number) VALUES (:number)", [":number"=>$data['number']]);
                Path::redirect(Path::ROOT."admin/rooms");
            }
        }    
        public function treatments(Request $req, Response $res) {
            $data = DB::query("SELECT * from treatment;");
            echo $res->view('admin/treatments/treatments',  [], [], ['treatments'=>$data]);
        }
        public function treatmentChange(Request $req, Response $res) {
            $data = Form::validate($req->getBody(), ['type']);
            if($data['type']=="delete"){
                $data = Form::validate($req->getBody(), ['name']);
                DB::query("DELETE FROM treatment WHERE name = :name", [":name"=>$data['name']]);
                Path::redirect(Path::ROOT."admin/treatments");
            } else if($data['type']=="changeRequest"){
                $data = Form::validate($req->getBody(), ['name']);
                $dbData = DB::query("SELECT * FROM treatment WHERE name=:name", [':name'=>$data['name']])[0];
                echo $res->view("admin/treatments/changeTreatment", $dbData);
            } else if($data['type']=="change"){
                $data = Form::validate($req->getBody(), ['name', 'newName', 'duration', 'nrDoctors', 'nrNurses']);
                DB::query("UPDATE treatment SET name=:newName, duration=:duration, nrDoctors=:nrDoctors, nrNurses=:nrNurses WHERE name = :name", [":name"=>$data['name'], ":newName"=>$data['newName'], ':duration'=>$data['duration'], ':nrDoctors'=>$data['nrDoctors'], 'nrNurses'=>$data['nrNurses']]);
                Path::redirect(Path::ROOT."admin/treatments");
            } else if($data['type']=="newRequest"){
                echo $res->view("admin/treatments/newTreatment");
            } else if($data['type']=="new"){
                $data = Form::validate($req->getBody(), ['name', 'duration', 'nrDoctors', 'nrNurses']);
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
                $data = Form::validate($req->getBody(), ['start', 'end', 'room', 'treatment']);
                DB::query("INSERT INTO appointment(treatmentID, roomID, start, end) VALUES (:treatment, :room, :start, :end)", [":treatment"=>$data["treatment"], ":room"=>$data["room"], ":start"=>$data["start"], ":end"=>$data["end"]]);
                Path::redirect(Path::ROOT."admin/appointment/new");
            } 
        }

        public function pending(Request $req, Response $res)
        {
            if ($req->getMethod() == "GET") {
                $appointments = DB::query("SELECT a.id, a.start, a.end, b.firstname, b.lastname, b.insurance FROM `appointment` a, `users` b WHERE `status` = 'warten' AND a.userID = b.id");
                echo $res->view('admin/pending', [], [], ['appointments' => $appointments]);
            } else {
                $data = Form::validate($req->getBody(), ['id', 'action']);

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
            if(isset($req->getBody()['search'])){
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
            Form::validate($data,["id", "type"]);
            if($data["type"] == "dataChange"){
                if ($req->getMethod() == "GET") {
                    $userID = $data["id"];
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
                    $userID = $data["id"];
                    $userInfo = new UserInfo($userID);
                    if($userInfo->user != null){
                        Form::validate($data, ['salutation','firstname','lastname','birthday','insurance','patientID']);
                        if($userInfo->account !=null){
                            Form::validate($data, ['username','email']);
                        }
                        if($userInfo->admin !=null){
                            Form::validate($data, ['role']);
                        }
                        Path::redirect(Path::ROOT . 'admin/search/user?id='.$userID);
                    } else {
                        ErrorUI::error(400, 'Bad request');
                        exit;
                    }
                }
            } else if($data["type"] == "createAccount"){

            } else if($data["type"] == "createAdmin"){

            } else if($data["type"] == "approveMail"){

            }
        }
    }

?>
