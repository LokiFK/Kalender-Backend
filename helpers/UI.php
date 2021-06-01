<?php

    class UI {

        const TABLE_STYLE_DEFAULT = 0;

        public static function table(array $data, array $headers, int $styleIndex)
        {
            $styleClasses = UI::getStyle($styleIndex);
            $table = "<table class=$styleClasses[table]><thead class=$styleClasses[thead]><tr class=$styleClasses[row]>";
            foreach ($headers as $head) {
                $table .= "<th class=" . $styleClasses['item-head'] . ">" . $head . "</th>";
            }
            $table .= "</tr></thead><tbody class=$styleClasses[tbody]>";

            foreach ($data as $row) {
                $table .= "<tr class=$styleClasses[row]>";
                foreach ($headers as $header) {
                    $table .= "<td class=$styleClasses[item]>" . $row[$header] . "</td>";
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
                        'item-head' => 'item-head-default',
                        'thead' => 'thead-default',
                        'tbody' => 'tbody-default'
                    );
                
                default:
                    break;
            }
        }
    }