<?php
!defined('IN_KC') && exit('ILLEGAL EXECUTION');
/**
 * Description of Model
 *
 * @author ryan
 */
class Model {
    
    protected $id = null;
    protected $info = null;

    public static function orm()
    {
        return ORM::for_table(static::table());
    }

    public static function get($id)
    {
        return ORM::for_table(static::table())->find_one($id);
    }

    public static function table()
    {
        if (isset(static::$_table)) {
            return static::$_table;
        } else {
            return self::camel_to_under_score(get_called_class());
        }
    }
    
    public static function camel_to_under_score($str)
    {
        return ltrim(strtolower(preg_replace('/([A-Z])/', '_$1', $str)), '_');
    }

    public function under_score_to_camel_case($str)
    {
        return ucfirst(preg_replace_callback('/_[a-z]/', function($s){return strtoupper(trim($s, '_'));}, $str));
    }

    // 已废弃
    function __construct($para) {
        if (is_array($para) && isset($para['id'])) { // info array
            $this->id = $para['id'];
            $this->info = $para;
        } else { // id
            $this->id = $para;
        }
    }
    
    protected function selfCond() {
        return array('id=?' => $this->id);
    }
}
