<?php


namespace app\common\interfaces;


interface iAvatarStore
{
    /**
     * 存储
     *
     * @return mixed
     */
    public function store();

    /**
     * 查看
     *
     * @param $id
     * @return mixed
     */
    public function view($id);

    /**
     * 统计
     *
     * @return mixed
     */
    public function statistics();

    /**
     * 头像库
     *
     * @return mixed
     */
    public function library();

    /**
     * 下载
     *
     * @return mixed
     */
    public function download();

    /**
     * 关系（实验性功能）
     *
     * 用于统计头像使用者、所有者信息
     * @todo 有可能需要额外创建 头像 --- 使用者、所有者 关系表，因此需要涉及到：从现有数据结构关系分析关系的行为，这一部分的具体实现逻辑在 <内部运算中心> 处理
     *
     * @return mixed
     */
    public function relation();
}