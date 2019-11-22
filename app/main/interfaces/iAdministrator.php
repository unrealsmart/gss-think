<?php


namespace app\main\interfaces;


interface iAdministrator
{
    /**
     * 保存
     *
     * @return mixed
     */
    public function save();

    /**
     * 更新
     *
     * @param $id
     * @return mixed
     */
    public function update($id);

    /**
     * 读取
     *
     * @param $id
     * @return mixed
     */
    public function read($id);

    /**
     * 删除
     *
     * @param $id
     * @return mixed
     */
    public function delete($id);

    /**
     * 列表
     *
     * @return mixed
     */
    public function index();

    /**
     * 验证
     *
     * @param $username
     * @param $password
     * @param $entrance
     * @return mixed
     */
    public function verification($username, $password, $entrance);
}