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
     * 刷新
     *
     * @param array $obj
     * @return mixed
     */
    public function refresh($obj = []);

    /**
     * 加密
     *
     * @param $plaintext
     * @param $survival_time
     * @return mixed
     */
    public function encryption($plaintext, $survival_time);

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
     * 调用顺序应确保处于已验证状态，无需再次验证
     *
     * @return mixed
     */
    public function currentUser();

    /**
     * 检查 JWT 完整性（在数据表修改后，此表相关用户的令牌失效）
     *
     * @return mixed
     */
    // public function checkIntact($data);
}