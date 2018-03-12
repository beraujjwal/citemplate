<?php
/**
 * Created by PhpStorm.
 * User: sunil
 * Date: 12-03-2018
 * Time: 03:57 PM
 */

class MY_Form_validation{
    public $CI;

    /**
     * @param $str
     * @param $field
     * @return bool
     */
    public function is_unique($str, $field){
        sscanf($field, '%[^.].%[^.]', $table, $field);
        return is_object($this->CI->db)
            ? ($this->CI->db->limit(1)->get_where($table, array($field => $str))->num_rows() === 0)
            : FALSE;
    }
}