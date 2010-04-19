<?php

class String extends Object {
    public static function insert($string, $data) {
        foreach($data as $key => $value):
            $regex = '%(:' . $key . ')%';
            $string = preg_replace($regex, $value, $string);
        endforeach;
        return $string;
    }
    public static function extract($string) {
        preg_match_all('%:([a-zA-Z-_]+)%', $string, $extracted);
        return $extracted[1];
    }
}