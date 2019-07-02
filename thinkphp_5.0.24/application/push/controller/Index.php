<?php
/**
 * author xtj
 * createtime 2019/6/28 0028 上午 10:40
 * return array obj json bool
 * param
 */
namespace app\push\controller;
use think\Controller;

class Index extends Controller{
    public function index(){

        return $this->fetch();
    }
}
