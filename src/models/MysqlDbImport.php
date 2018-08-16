<?php

namespace nullref\useful\models;

use nullref\useful\helpers\Db;
use yii\base\Model;
use yii\db\Connection;
use yii\web\UploadedFile;

/**
 * @author    Dmytro Karpovych
 * @copyright 2018 NRE
 */
class  MysqlDbImport extends Model
{
    public $file;

    public $component = 'db';

    protected $directory = '@webroot/db_backups/';

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['file', 'file']
        ];
    }

    /**
     * @return string
     */
    public function run()
    {
        $file = UploadedFile::getInstance($this, 'file');
        $path = Yii::getAlias($this->directory . $file->name);
        $file->saveAs($path);

        /** @var Connection $db */
        $db = Yii::$app->get($this->component);
        $user = $db->username;
        $pass = $db->password;
        $name = Db::getDsnAttribute('dbname', $db->dsn);
        $host = Db::getDsnAttribute('host', $db->dsn);
        $cmd = "mysql -u $user -p$pass $name --host=$host < $path";

        return exec($cmd);
    }
}
