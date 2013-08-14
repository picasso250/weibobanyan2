<?php

 

/**
 * Description of User
 *
 * @file    User
 * @author  ryan <cumt.xiaochi@gmail.com>
 * @created Jul 23, 2012 1:18:01 PM
 */
class User extends XcModel {

    protected $id = null;
    protected $table = 'user';

    public static function getOnPlatform($platform, $id) {
        $i = ORM::for_table('open_id')
            ->where('platform', $platform)
            ->where('open_id', $id)
            ->select('user')
            ->find_one();
        return ($i === false) ? $i : new self($i->user);
    }

    public static function createFromOpenId($platform, $id) {
        // create a dissociate openid and get the id
        $open_id = self::createOpenId($platform, $id);

        // create user
        //....
        Pdb::insert(array(
            'open_id'=>$open_id,
            'create_time=NOW()'=>false,
            'active_time=NOW()'=>false,
        ), 'user');

        $user_id = Pdb::lastInsertId();
        Pdb::update(array('user'=>$user_id), 'open_id', array('id=?'=>$open_id));
        return new self($user_id);
    }

    function __construct($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function getInfo() {
        //....
        return Pdb::fetchRow('*', $this->table, array('id=?'=>$this->id));
    }

    public function attachOpenId($platform, $id) {

    }

    private static function createOpenId($platform, $id) {
        //....
        Pdb::insert(array(
            'platform'=>$platform,
            'open_id'=>$id,
            'time=NOW()'=>false,
        ), 'open_id');
        return Pdb::lastInsertId();
    }

    public function active() {
        //....
        Pdb::update(array('active_time=NOW()'=>false), 'user', array(
            'id=?'=>$this->id
        ));
    }

    public function watch ($role_id) {
        //....
        Pdb::insert(array(
            'user'=>$this->id,
            'role'=>$role_id,
        ), 'watch');
    }

    public function unwatch ($role_id) {
        //....
        Pdb::del('watch', array(
            'user=?'=>$this->id,
            'role=?'=>$role_id,
        ));
    }

    public function getWatching() {
        //....
        return Pdb::fetchAll('role', 'watch', array(
            'user=?'=>$this->id
        ));
    }

    public function getReminds() {
        //....
        return Pdb::fetchAll(array(
            'twit.id',
            'role.name',
        ), 'user,twit,comment,watch,role', array(
            'user.id=?'=>$this->id,
            'twit.author=role.id'=>false,
            'watch.user=user.id'=>false,
            'watch.role=twit.author'=>false,
            'comment.twit=twit.id'=>false,
            'comment.time>user.active_time'=>false,
        ), array(
            'comment.time DESC'
        ), 'LIMIT 100');
    }
}

