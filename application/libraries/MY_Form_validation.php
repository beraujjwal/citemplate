<?php
/**
* Old class is can't user
* fb.com/xuandung38
* Having an error with the old code, I updated and corrected it by letting it inherit from CI_Form_validation
**/

Class MY_Form_validation extends CI_Form_validation

{
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
