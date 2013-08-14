<?php
 


/**
 * Description of Role
 *
 * @file    Role
 * @author  ryan <cumt.xiaochi@gmail.com>
 * @created Jul 17, 2012 10:13:11 AM
 */
class Role extends Model {

    static $table = 'role';

    public function getInfo($whose_view=0) {
        $r = self::orm()->find_one($this->id)->as_array();
        if ($whose_view) {
            $user_id = $whose_view;
            $r['watch'] = $this->watchBy($user_id);
        }
        return $r;
    }

    public static function hasName($name)
    {
        $r = self::orm()->where('name', $name)->find_one();

        if ($r) {
            return new self($r->as_array());
        } else {
            return false;
        }
    }

    private function watchBy($user_id) {
        //....
        return Pdb::exists('watch', array(
            'user=?'=>$user_id,
            'role=?'=>$this->id
        ));
    }

    public static function add($args) {
        $r = self::orm()->create();
        $r->set($args);
        $r->save();
        return new self($r->id);
    }

    public function edit ($arr) {
        //....
        Pdb::update($arr, self::$table, array('id=?'=>$this->id));
    }

    public static function listR ($conds=array()) { // list??
        extract(array_merge(array(
            'keyword' => '',
            'num'     => 6,
            'view_from'=>0,
            'tag'=>'',
        ), $conds));
        //....
        $conds = array();
        $fields = array(
            'role.id',
            'role.name',
            'role.avatar',
            'role.is_v',
            'role.hot',
        );
        $orders = array();
        if ($keyword) {
            $conds['role.name LIKE ?'] = "%$keyword%";
            $fields[] = 'IF(role.name=?, 1, 0) as found';
            $conds = array_merge(array('1=1'=>$keyword), $conds);
            $orders[] = 'found DESC';
        }
        $orders[] = 'role.hot DESC';
        $orders[] = 'role.id DESC';
        $tables = array('role');
        if ($tag) {
            $conds['role_tag_relation.tag=?'] = Tag::getIdByText($tag);
            $conds['role_tag_relation.role=role.id'] = false;
            $tables[] = 'role_tag_relation';
        }
        $r = Pdb::fetchAll($fields, $tables, $conds, $orders, "LIMIT $num");
        return array_map(function ($role) {
            $role_o = new Role($role['id']);
            $role['tag'] = $role_o->getTags();
            return $role;
        }, $r);
    }

    public function tweet ($text, $image_path='', $scene=0) {

        //....
        $arr = array(
            'author' => $this->id,
            'text' => $text,
            'image' => $image_path,
            'scene' => $scene,
            'time=NOW()'=>false,
        );
        Pdb::insert($arr, 'twit');

        $ip = $_SERVER['REMOTE_ADDR'];
        Log::update($ip, $this->id);

        if ($scene) {
            $scene = new Scene($scene);
            $scene->hit(); // 我被青春撞了一下腰
        }
    }

    public function top() {
        $roles = new Xcon(get_set($_COOKIE['top_role']));
        $roles->push($this->id);
        setcookie('top_role', $roles->stringify(), time() + 3600*24*365);
    }

    public function untop() {
        $roles = new Xcon(get_set($_COOKIE['top_role']));
        $roles->del($this->id);
        setcookie('top_role', $roles->stringify(), time() + 3600*24*180);
    }

    public function addTag($tag) {
        $tag_id = Tag::getIdByText($tag);
        //....
        Pdb::insert(array(
            'role'=>$this->id,
            'tag'=>$tag_id,
        ), 'role_tag_relation', 'ON DUPLICATE KEY UPDATE role=role');
    }

    public function getTags() {
        //....
        $r = Pdb::fetchAll('role_tag.tag', 'role_tag,role,role_tag_relation', array(
            'role.id=?'=>$this->id,
            'role.id=role_tag_relation.role'=>false,
            'role_tag_relation.tag=role_tag.id'=>false
        ));
        return array_map(function ($elem) {
            return $elem['tag'];
        }, $r);
    }

    public function countRecentTwit($days=30) {
        //....
        return Pdb::count('twit', array(
            'author=?'=>$this->id,
            "time>(CURDATE()-INTERVAL $days DAY)"=>false
        ));
    }

    public function recentTwit($conds=array()) {
        $conds = array_merge(array(
            'num'=>10,
            'role'=>$this->id,
        ), $conds);
        return Twit::listT($conds);
    }

    public static function getIdByName($name) {
        //....
        $r = Pdb::fetchRow('id', 'role', array('name=?'=>$name));
        return $r? $r['id'] : $r;
    }

    public function hot() {
        //....
        Pdb::update(array('hot=hot+1'=>null), self::$table, array('id=?'=>$this->id));
        if (rand(1, 1000) == 23) { // 千分之一的几率冷却
            Pdb::update(array('hot=hot/2'), self::$table);
        }
    }
    
    public function addToHistory() {
        $history = array();
        if (isset($_COOKIE['rh'])) { // role history
            $history = json_decode($_COOKIE['rh']);
        }
        array_unshift($history, $this->id);
        $history = array_unique($history);
        while (count($history) > 3) {
            array_pop($history);
        }
        
        $historyStr = json_encode(array_values($history));
        if (!setcookie('rh', $historyStr, strtotime('+180 days'), ROOT)) { // 180 days
            throw new Exception('set cookie fail');
        }
    }
    
    public function __get($name) {
        if ($name == 'id') return $this->id;
        if (empty($this->info)) $this->info = $this->info();
        return $this->info[$name];
    }
    
    public function info() {
        if (!empty($this->info)) return $this->info;
        return Pdb::fetchRow('*', self::$table, $this->selfCond());
    }
}

