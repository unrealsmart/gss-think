<?php


namespace app\common\interfaces;


interface iJsonWebToken
{
    /**
     * 创建
     *
     * @param array $data
     * @return mixed
     */
    public function create($data = []);

    /**
     * 验证
     *
     * @param $token
     * @return mixed
     */
    public function verification($token);

    /**
     * 加密
     *
     * @param $plaintext
     * @return mixed
     */
    public function encryption($plaintext);

    /**
     * 解密
     *
     * @param $ciphertext
     * @param $hex
     * @return mixed
     */
    public function decryption($ciphertext, $hex);

    /**
     * 用户
     *
     * @return mixed
     */
    public function currentUser();
}