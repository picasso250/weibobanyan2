<?php

 

/**
 * Description of Twit
 *
 * @file    Twit
 * @author  ryan <cumt.xiaochi@gmail.com>
 * @created Jul 17, 2012 3:15:17 PM
 */
class Twit extends CoreModel {

    protected $table = 'twit';

    const ORIGIN_EXPLODE = 1;

    public function getInfo($origin_explode=1) {
        //....
        $fields = array(
            'role.name as author',
            'role.id as role_id',
            'role.avatar',
            'role.is_v',
            'twit.text',
            'twit.image',
            'twit.comment_num',
            'twit.retweet_num',
            'twit.origin',
            'twit.scene',
            'twit.time',
            'twit.will_del',
        );
        $tables = array('role', 'twit');
        $conds = array(
            "twit.id=?"=>$this->id,
            'twit.author=role.id'=>false,
        );
        $r = Pdb::fetchRow($fields, $tables, $conds);
        if ($r['origin'] && $origin_explode) {
            $origin = new self($r['origin']);
            $origin = $origin->getInfo();
            $origin['text'] = self::addLink($origin['text']);
            $r['origin'] = $origin;
        }
        return $r;
    }

    public function getComments() {
        //....
        $fields = array(
            'comment.text',
            'comment.time',
            'role.name as author',
            'role.id as author_id',
            'role.avatar',
            'role.is_v',
        );
        $tables = array('twit', 'comment', 'role');
        $conds = array(
            "twit.id=?"=>$this->id,
            'twit.id=comment.twit'=>false,
            'role.id=comment.author'=>false,
        );
        $orders = array('`comment`.`time` ASC');
        return array_map(function ($comment) {
            $comment['text'] = Twit::addLink($comment['text']);
            return $comment;
        }, Pdb::fetchAll($fields, $tables, $conds, $orders));
    }

    public static function addLink($text) { // this should be private, but...
        return preg_replace("/(@[^\s]+)(\sv)?($|\s)/", '[$1$2]', $text);
    }

    public function comment($text, $author_id) {
        //....
        $arr = array(
            'twit'=>$this->id,
            'text'=>$text,
            'author'=>$author_id,
            'time=NOW()'=>false,
        );
        Pdb::insert($arr, 'comment');
        $this->plusOne('comment_num');
        $this->hot(2);
    }

    public function retweet($text, $role_id) {
        //....
        $info = $this->getInfo(!self::ORIGIN_EXPLODE);
        $origin_id = $this->id;
        $scene = $info['scene'];
        if ($info['origin'] != 0) { // is origin
            $text .= '//@'.$info['author'].($info['is_v']?' v':'').' ：'.$info['text'];
            $origin_id = $info['origin'];
        }
        $arr = array(
            'origin'     => $origin_id,
            'text'       => $text,
            'author'     => $role_id,
            'scene'      => $scene,
            'time=NOW()' => null,
        );
        Pdb::insert($arr, 'twit');
        $orgin = new self($this->id);
        $orgin->plusOne('retweet_num');

        $ip = $_SERVER['REMOTE_ADDR'];
        Log::update($ip, $role_id);

        if ($scene) {
            $scene = new Scene($scene);
            $scene->hit();
        }

        $this->hot(3);
    }

    private function plusOne($para) {
        //....
        $arr = array("$para=$para+1"=>false); // null is better than false
        $conds = array('id=?'=>$this->id);
        Pdb::update($arr, 'twit', $conds);
    }

    public function del() {
        //....
        $conds = array('id=?'=>$this->id);
        return Pdb::del($this->table, $conds);
    }

    public function prepareDel($will_del=1) {
        //....
        $conds = array('id=?'=>$this->id);
        Pdb::update(compact('will_del'), $this->table, $conds);
    }

    public function edit($arr) {
        //....
        $conds = array("id=?"=>$this->id);
        Pdb::update($arr, 'twit', $conds);
    }

    public static function getTotal() {
        //....
        return Pdb::count('twit');
    }

    public static function listT($conds=array()) {
        extract(self::defaultConds($conds));
        //....
        $fields = array(
            "role.name as author",
            "role.id as role_id",
            "role.avatar",
            "role.is_v",
            "twit.origin",
            "twit.text",
            "twit.image",
            "twit.comment_num",
            "twit.retweet_num",
            "twit.time",
            "twit.id",
            "twit.will_del",
            "IF(scene.id=0,'',scene.name) as scene",
            "scene.id as scene_id",
        );
        $tables = '(role, twit) LEFT JOIN scene ON (twit.scene=scene.id)';
        $conds = array(
            'twit.author=role.id'=>false,
        );
        if ($scene != -1) {
            $conds['twit.scene=?'] = $scene;
        }
        if ($will_del) {
            $conds['twit.will_del=?'] = 1;
        }
        if ($role) {
            $conds['role.id=?'] = $role;
        }

        $orders = array();
        if ($hot) {
            $orders[] = 'twit.hot DESC';
        }
        $orders[] = 'time DESC';
        $page_pos = "LIMIT $num OFFSET $offset";
        $ret = Pdb::fetchAll($fields, $tables, $conds, $orders, $page_pos);
        foreach($ret as $k=>$tw) {
            $ret[$k]['text'] = self::addLink($ret[$k]['text']);
            $origin = $tw['origin'];
            if ($origin) {
                $origin = new self($origin);
                $origin = $origin->getInfo();
                $origin['text'] = self::addLink($origin['text']);
                $ret[$k]['origin'] = $origin;
            } else {
                $ret[$k]['origin'] = null;
            }
            $role = new Role($tw['role_id']);
            $ret[$k]['tag'] = $role->getTags();
        }
        return $ret;
    }

    public static function count($para) {
        extract(user_input($para, array('time', 'interval')));
        //....
        if ($time) {
            $conds = array('time>?'=>$time);
        }
        if ($interval) {
            $conds = array('time>DATE_SUB(NOW(),INTERVAL ? SECOND)'=>$interval);
        }
        return Pdb::count('twit', $conds);
    }

    private static function defaultConds($conds) {
        return array_merge(array(
            'num'=>15, //和新浪一样
            'offset'=>0,
            'scene'=>-1,
            'will_del'=>null,
            'role'=>null,
            'hot'=>0,
        ), $conds);
    }

    public function up($user_id, $temperature=1) { // 可以用接口
        $this->hot(1);
        //....
        // 已经顶过
        Pdb::insert(array('user'=>$user_id,'twit'=>$this->id,), 'user_up_twit');
    }

    public function hot($temperature=1) {
        //....
        Pdb::update(array("hot=hot+$temperature"=>null), $this->table, array('id=?'=>$this->id));
        if (rand(1, 1000) == 23) { // 千分之一的几率冷却
            Pdb::update(array('hot=hot/2'), $this->table); // 效率问题？ where hot > 8???
        }
    }

    public function canUpBy($user_id) {
        //....
        return !Pdb::exists('user_up_twit', array(
            'user=?'=>$user_id,
            'twit=?'=>$this->id,
        ));
    }
}

?>
