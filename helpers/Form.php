<?php

    class Form {
        public static function validate($formData, array $columns)
        {
            foreach ($columns as $column) {
                if (!isset($formData[$column]) || $formData[$column] == null) {
                    ErrorUI::error(400, 'Bad request');
                    exit;
                }
            }
            return $formData;
        }

        public static function validateIsset($formData, array $columns)
        {
            foreach ($columns as $column) {
                if (!isset($formData[$column])) {
                    ErrorUI::error(400, 'Bad request');
                    exit;
                }
            }
            return $formData;
        }

        public static function validateNewData($formData, array $columns)
        {
            $data = [];
            $count = 0;
            foreach ($columns as $column) {
                if (!isset($formData[$column]) || $formData[$column] == null) {
                    ErrorUI::error(400, 'Bad request');
                    exit;
                }
                $count ++; 
                $data = array_pad($data, $count, $formData[$column]);
            }
            return $data;
        }

        public static function validateDataType($formData, array $columns, bool $die=true) {
            foreach($columns as $key=>$value){
                if (is_int($key)) {
                    $key = $value;
                }
                if ($value == "canNull") {
                    if (!isset($formData[$key])) {
                        return self::errorDataType('Bad request', $die);
                    } 
                } else if (!isset($formData[$key]) || $formData[$key] == null || $formData[$key] == "") {
                    if($die){
                        ErrorUI::error(400, 'Bad request');
                        exit;
                    } else {
                        return null;
                    }
                } else if($value == "int"){
                    if (!is_numeric($formData[$key])) {
                        return self::errorDataType('Bad request', $die);
                    } 
                } else if($value == "datetime"){
                    //todo
                } else if($value == "date"){
                    //todo
                } else if($value == "time"){
                    //todo
                } else if($value == "birthday"){
                    //todo
                } else if($value == "insurance"){
                    if (! (strtolower($formData[$key]) == "gesetzlich" || strtolower($formData[$key]) == "privat")) {
                        return self::errorDataType('Bad request', $die);
                    } 
                } else if($value == "role"){
                    if (! ($formData[$key]=="Sekretär" || $formData[$key]=="Arzt" || $formData[$key]=="Arzthelfer")) {
                        return self::errorDataType('Bad request', $die);
                    } 
                } else if($value == "email"){
                    if (!strstr($formData[$key], '@')) {
                        return self::errorDataType('Die Email kann nicht richtig sein.', $die);
                    } 
                } else if($value == "newEmail"){
                    $res = DB::query("SELECT count(*) AS Anzahl FROM account WHERE email=:email", [":email"=>$formData[$key]]);
                    if ($res[0]['Anzahl']>0) {
                        return self::errorDataType('Diese Email wurde bereits registriert.', $die);
                    }
                    if (!strstr($formData[$key], '@')) {
                        return self::errorDataType('Die Email kann nicht richtig sein.', $die);
                    } 
                } else if($value == "existingEmail"){
                    $res = DB::query("SELECT count(*) AS Anzahl FROM account WHERE email=:email", [":email"=>$formData[$key]]);
                    if ($res[0]['Anzahl']==0) {
                        return self::errorDataType('Bad request', $die);
                    }
                } else if($value == "newUsername"){
                    if (Auth::usernameExists($formData[$key])) {
                        return self::errorDataType('Username schon vergeben.', $die);
                    } 
                } else if($value == "existingUsername"){
                    if (!Auth::usernameExists($formData[$key])) {
                        return self::errorDataType('Username schon vergeben.', $die);
                    } 
                } else if(substr($value, 0, 9) == "username:"){
                    if (Auth::usernameExists($formData[$key], substr($value,9))) {
                        return self::errorDataType('Username schon vergeben.', $die);
                    } 
                } else if($value == "existingRoom"){
                    $res = DB::query("SELECT count(*) AS Anzahl FROM room WHERE number=:number", [":number"=>$formData[$key]]);
                    if ($res[0]['Anzahl']==0) {
                        return self::errorDataType('Bad request', $die);
                    } 
                } else if($value == "existingRoomID"){
                    $res = DB::query("SELECT count(*) AS Anzahl FROM room WHERE id=:id", [":id"=>$formData[$key]]);
                    if ($res[0]['Anzahl']==0) {
                        return self::errorDataType('Bad request', $die);
                    } 
                } else if($value == "newRoom"){
                    $res = DB::query("SELECT count(*) AS Anzahl FROM room WHERE number=:number", [":number"=>$formData[$key]]);
                    if ($res[0]['Anzahl']>0) {
                        return self::errorDataType('Nummer schon vergeben.', $die);
                    } 
                } else if($value == "existingTreatment"){
                    $res = DB::query("SELECT count(*) AS Anzahl FROM treatment WHERE name=:name", [":name"=>$formData[$key]]);
                    if ($res[0]['Anzahl']==0) {
                        return self::errorDataType('Bad request', $die);
                    } 
                } else if($value == "existingTreatmentID"){
                    $res = DB::query("SELECT count(*) AS Anzahl FROM treatment WHERE id=:id", [":id"=>$formData[$key]]);
                    if ($res[0]['Anzahl']==0) {
                        return self::errorDataType('Bad request', $die);
                    } 
                } else if($value == "newTreatment"){
                    $res = DB::query("SELECT count(*) AS Anzahl FROM treatment WHERE name=:name", [":name"=>$formData[$key]]);
                    if ($res[0]['Anzahl']>0) {
                        return self::errorDataType('Name schon vergeben', $die);
                    } 
                } else if($value == "agb"){
                    if ($formData[$key] != 'on') {
                        return self::errorDataType('Bitte Datenschutz- und Nutzungsbedingungen akzeptieren.', $die);
                    } 
                } else if($value == "resetCode"){
                    $res = DB::query("SELECT count(*) AS Anzahl FROM passwordreset WHERE code=:code and isUsed=false", [":code"=>$formData[$key]]);
                    if ($res[0]['Anzahl']==0) {
                        return self::errorDataType('Bad request', $die);
                    } 
                }
            }
            return $formData;
        }

        private static function errorDataType($msg, $die)
        {
            if ($die) {
                ErrorUI::error(400, $msg);
                exit;
            }
            return null;
        }

        public static function require($formData, array $requires)
        {
            Form::validate($formData, $requires);

            $missingColumns = array();

            foreach ($requires as $key => $value) {
                if (!isset($formData[$key]) || (isset($formData[$key]) && empty($formData[$key]))) {
                    array_push($missingColumns, $key);
                }
            }

            return $missingColumns;
        }

        public static function create($redirect, $method, FormField ...$formFields): string
        {
            $html = "
                <form action='$redirect' method='$method'>
            ";

            foreach ($formFields as $formField) {
                $html .= $formField->getHTML();
            }

            $html .= "</form>";

            return $html;
        }
    }

?>
