<?php

namespace nullref\useful\models;

use nullref\useful\helpers\Db;
use Yii;
use yii\base\BaseObject;
use yii\db\Connection;

/**
 * @author    Dmytro Karpovych
 * @copyright 2018 NRE
 */
class MysqlDbExport extends BaseObject
{
    public $component = 'db';

    protected $directory = '@webroot/db_backups/';

    /**
     * @return bool|string
     */
    public function export()
    {
        $filename = $this->getFilename();

        /** @var Connection $db */
        $db = Yii::$app->get($this->component);
        $user = $db->username;
        $pass = $db->password;
        $name = Db::getDsnAttribute('dbname', $db->dsn);
        $host = Db::getDsnAttribute('host', $db->dsn);
        $cmd = "mysqldump -u $user -p$pass $name --host=$host --single-transaction | gzip -c > $filename";

        exec($cmd);

        return $filename;
    }

    /**
     * @return bool|string
     */
    protected function getFilename()
    {
        $date = date('d-m-Y_H-i-s');
        $filename = Yii::getAlias($this->directory . Yii::$app->id . '_full_backup_' . $date . '.sql.gz');
        return $filename;
    }
}