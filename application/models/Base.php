<?php
use Illuminate\Support\Facades\Facade;
use Illuminate\Database\Capsule\Manager as IlluminateCapsule;
use Illuminate\Database\Eloquent\Model as IlluminateModel;

use Yaf\Registry as Registry;
/**
 * model操作基类
 */
abstract class BaseModel  extends IlluminateModel
{
    protected        $config  = null;
    protected static $capsule = null;
    protected static $fakeApp = [];
    protected $connection = 'db';
    public function __construct(array $attributes = array())
    {
        // parent::__construct($attributes);
        $this->config = Registry::get('config');
        if (!$this->config->database) {
            throw new Exception("Must configure database in .ini first");
        }
        if (!self::$capsule) {
            self::$capsule = new IlluminateCapsule();
            self::$capsule->bootEloquent();
            self::$fakeApp = Facade::getFacadeApplication();
            self::$fakeApp['db'] = self::$capsule->getDatabaseManager();
            Facade::setFacadeApplication(self::$fakeApp);
        }
        $dbconfig = $this->config->database->{$this->connection}->toArray(); 
        self::$capsule->addConnection($dbconfig, $this->connection);
    }
}
