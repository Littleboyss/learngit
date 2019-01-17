<?php
/**
 * 文件上传接口
 * Class Eapi_File
 */

Class Eapi_File
{
    /**
     * 配置信息
     * @var Object
     */
    private $config=null;

    /**
     * 主服务
     * @var MogileFs
     */
    private $client=null;


    public function __construct($domain = 'file')
    {
        $this->client = new MogileFs();
        $this->config = Yaf_Registry::get("config")->file;
        $this->client->connect($this->config->host, $this->config->port, $domain);
    }

    public function upload($file, $name, $class)
    {
        return $this->client->put($file, $name, $class);
    }

    public function get($name){
        return $this->client->get($name);
    }
}
