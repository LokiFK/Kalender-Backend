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