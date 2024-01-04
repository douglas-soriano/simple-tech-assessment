<?php

# QUERY DEBUG :: Return the Eloquent query statement.
if (! function_exists('real_query')) {
    function real_query ($query, $dump = false) {
        $params = array_map(function ($item) {
            return "'{$item}'";
        }, $query->getBindings());
        $result = \Str::replaceArray('?', $params, $query->toSql());
        $result = str_replace('`', '', $result);
        $array = ['select', 'inner', 'where', ' and ', ' or ', 'order by'];
        if ($dump):
            foreach ($array as $a):
                $result = str_replace($a, '<br>'.$a, $result);
            endforeach;
            echo '<pre>'; print_r($result); exit;
        endif;
        return $result;
    }
}

# HELPER :: Convert array/collection to table for easy understanding.
if (! function_exists('to_debug_table')) {
    function to_debug_table ($array, $heading = null) {
        $array = is_array($array) ? $array : $array->toArray();
        if (count($array)):
            echo '<style>
                table { font-family:sans-serif; font-size:12px; border:1px solid #ccc; border-collapse:collapse; margin:0; padding:0; width:100%; }
                table tr { padding:5px; }
                table th, table td { padding:10px; text-align:center; border:1px solid #ddd; }
                table th { font-size:14px; letter-spacing:1px; text-transform:uppercase; }
                @media screen and (max-width:600px) { table { border:0; } table thead { display:none; } table tr { border-bottom:2px solid #ddd; display:block; margin-bottom:10px; } table td { border-bottom:1px dotted #ccc; display:block; font-size:13px; text-align:right; } table td:last-child { border-bottom:0; } table td:before { content:attr(data-label); float:left; font-weight:bold; text-transform:uppercase; } }
            </style>';
            echo '<table>';
            echo '<thead>';
            if ($heading):
                $header = $heading;
            else:
                $header = array_keys($array[0]);
            endif;
            foreach ($header as $key):
                echo '<th>'.$key.'</th>';
            endforeach;
            echo '</thead>';
            echo '<tbody>';
            foreach ($array as $row):
                echo '<tr>';
                foreach ($row as $col => $value):
                    echo '<td>'.(is_array($value) ? implode(",", $value) : $value).'</td>';
                endforeach;
                echo '</tr>';
            endforeach;
            echo '</tbody>';
            echo '</table>';
        else:
            echo 'Empty Array.';
        endif;
        dd(":: DEBUG TABLE ::");
    }
}