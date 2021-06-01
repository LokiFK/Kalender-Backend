<?php

    class UI {

        const TABLE_STYLE_DEFAULT = 0;

        public static function table(array $data, array $headers, int $style)
        {
            $styleClasses = UI::getStyle($style);
            $table = "<table class=$styleClasses[table]><thead><tr class=$styleClasses[row]>";
            foreach ($headers as $head) {
                $table .= "<th class=" . $styleClasses['item-head'] . ">" . $head . "</th>";
            }
            $table .= "</tr></thead><tbody>";

            foreach ($data as $row) {
                $table .= "<tr class=$styleClasses[row]>";
                foreach ($headers as $header) {
                    $insert = isset($row[$header]) ? $row[$header] : '';
                    $table .= "<td class=$styleClasses[item]>" . $insert . "</td>";
                }
                $table .= "</tr>";
            }
            return $table;
        }

        public static function getStyle(int $styleIndex)
        {
            switch ($styleIndex) {
                case UI::TABLE_STYLE_DEFAULT:
                    return array(
                        'table' => 'table-default',
                        'row' => 'row-default',
                        'item' => 'item-default',
                        'item-head' => 'item-head-default'
                    );
                
                default:
                    break;
            }
        }
    }