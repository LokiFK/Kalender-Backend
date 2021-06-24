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

        public static function validateDataType($formData, array $columns){
            foreach($columns as $key=>$value){
                if(is_int($key)){       //falls key gleich datentyp
                    $key = $value;
                }
                if($value == "canNull"){
                    if (!isset($formData[$key])) {
                        ErrorUI::error(400, 'Bad request');
                        exit;
                    } 
                } else if ($formData[$key] == null || $formData[$key] == "") {
                    ErrorUI::error(400, 'Bad request');
                    exit;
                } else if($value == "int"){
                    if (!is_numeric($formData[$key])) {
                        ErrorUI::error(400, 'Bad request');
                        exit;
                    } 
                } else if($value == "birhday"){
                    //todo
                } else if($value == "incurance"){
                    if (! ($formData[$key]=="gesetzlich" || $formData[$key]=="privat")) {
                        ErrorUI::error(400, 'Bad request');
                        exit;
                    } 
                } else if($value == "role"){
                    if (! ($formData[$key]=="Sekretär" || $formData[$key]=="Arzt" || $formData[$key]=="Arzthelfer")) {
                        ErrorUI::error(400, 'Bad request');
                        exit;
                    } 
                } else if($value == "email"){
                    if (!strstr($formData[$key], '@')) {
                        ErrorUI::error(400, 'Bad request');
                        exit;
                    } 
                } else if($value == "username"){
                    if (Auth::usernameExists($formData[$key])) {
                        ErrorUI::error(400, 'Username schon vergeben.');
                        exit;
                    } 
                } else if(substr($value, 0, 9) == "username:"){
                    if (Auth::usernameExists($formData[$key], substr($value,9))) {
                        ErrorUI::error(400, 'Username schon vergeben.');
                        exit;
                    } 
                }
            }
            return $formData;
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
    }

?>