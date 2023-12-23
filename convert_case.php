<?php

$arr = array(
    'stringStringf',
);

foreach($arr as $value) {
    echo '\''.snakeToCamel($value).'\',' . PHP_EOL;
}

echo '--------------------'.PHP_EOL;

foreach($arr as $value) {
    echo '\''.camelToSnake($value).'\',' . PHP_EOL;
}

function snakeToCamel(string $str) : string
{
    return lcfirst(
        str_replace(' ', '', ucwords(
            str_replace('_', ' ', $str)
        ))
    );
}
function camelToSnake($string)
{
    return strtolower(
        preg_replace('/(?<!^)[A-Z]/', '_$0', $string)
    );
}
