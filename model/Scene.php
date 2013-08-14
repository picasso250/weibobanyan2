<?php
 

/**
 * Description of Scene
 *
 * @file    Scene
 * @author  ryan <cumt.xiaochi@gmail.com>
 * @created Jul 26, 2012 10:39:27 AM
 */
class Scene extends XcModel {

    protected $id = 0;
    protected $table = 'scene';

    function __construct($id) {
        $this->id = $id;
    }

    public function getInfo() {
        //....
        return Pdb::fetchRow('*', $this->table, array('id=?'=>$this->id));
    }

    public static function creat($name, $description='') {
        //....
        Pdb::insert(compact('name', 'description'), 'scene');
    }

    public static function ListS($conds=array()) {
        extract(array_merge(array(
            'num'=>10,
            'will_del'=>0,
        ), $conds));
        //....
        $conds = array();
        if ($will_del) {
            $conds['will_del=?'] = 1;
        }
        $order = 'hot DESC';
        return Pdb::fetchAll('*', 'scene', $conds, $order, "LIMIT $num");
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

    public function hit() {
        //....
        Pdb::update(array('hot=hot+1'=>null), $this->table, array('id=?'=>$this->id));
        if (rand(1, 1000) == 23) { // 千分之一的几率冷却
            Pdb::update(array('hot=hot/2'), $this->table);
        }
    }

    public function edit($para) {
        //....
        Pdb::update($para, 'scene', array('id=?'=>$this->id));
    }
}

?>
