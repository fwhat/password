<?php

namespace Dowte\Password\pass\components;
/*
+--------------------------------------------------------------------------
|   OpensslEncryptHelper
|   先将提供的密码sha256
|   再截取出一定长度的向量和key
+---------------------------------------------------------------------------
*/
class OpensslEncryptHelper
{
    public $_method = 'AES-256-CBC';

    private $_password;

    /**向量
     * @var string
     */
    private $_iv;

    /**
     * 默认秘钥
     */
    private $_key;

    public function __construct()
    {
    }

    public function setPassword($password)
    {
        $this->_password = hash('sha256', $password);
        return $this;
    }

    public static function cipher()
    {
        return new self();
    }

    public function setCipherMethod($method)
    {
        if (! in_array($method, openssl_get_md_methods()))
            return false;
        $this->_method = $method;
        return $this;
    }

    /**
     * 解密字符串
     * @param string $data 字符串
     * @return string
     */
    public function decryptWithOpenssl($data)
    {
        return openssl_decrypt(base64_decode($data), $this->_method, $this->getKey(), OPENSSL_RAW_DATA, $this->getIv());
    }

    /**
     * 加密字符串
     * @param string $data 字符串
     * @return string
     */
    public function encryptWithOpenssl($data)
    {
        return base64_encode(openssl_encrypt($data, $this->_method, $this->getKey(), OPENSSL_RAW_DATA, $this->getIv()));
    }


    protected function getIv()
    {
        if ($this->_iv === null) {
            $this->_iv = substr($this->_password, 0, $this->getIvLength());
        }
        return $this->_iv;
    }

    protected function getIvLength()
    {
        return openssl_cipher_iv_length($this->_method);
    }

    protected function getKey()
    {
        if ($this->_key === null) {
            $this->_key = substr($this->_password, 0, -($this->getIvLength()));
        }
        return $this->_key;
    }
}