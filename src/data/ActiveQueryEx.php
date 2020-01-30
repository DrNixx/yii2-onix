<?php
namespace onix\data;

use yii\base\UnknownPropertyException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception as DbException;

/**
 * @property string $fieldPrefix
 * @property string $now
 * @property string $nowMinutes
 * @property integer $timestamp
 * @property string $prefix
 */
class ActiveQueryEx extends ActiveQuery
{
    /**
     * Current timestamp
     *
     * @var int
     */
    protected $fTimestamp;

    /**
     * Default values
     *
     * @var array
     */
    protected $defaultScope = [];

    /**
     * @var string
     */
    private $as;

    public function __construct($modelClass, $config = [])
    {
        parent::__construct($modelClass, $config);
        $this->alias($this->as);
    }

    /**
     * Gets the values of the properties of the class
     *
     * @param $name
     *
     * @return mixed
     *
     * @throws UnknownPropertyException
     */
    public function __get($name)
    {
        switch ($name) {
            case "timestamp":
                if (!isset($this->fTimestamp)) {
                    $this->fTimestamp = time();
                }

                return $this->fTimestamp;
            case "now":
                return date("Y-m-d H:i:s", $this->timestamp);
            case "nowMinutes":
                return date("Y-m-d H:i:00", $this->timestamp);
            case "nowHours":
                return date("Y-m-d H:00:00", $this->timestamp);
            case "prefix":
                return $this->getFieldPrefix();
            default:
                return parent::__get($name);
        }
    }

    /**
     * Set class properties value
     *
     * @param string $name
     * @param mixed $value
     *
     * @throws UnknownPropertyException
     */
    public function __set($name, $value)
    {
        switch ($name) {
            case "timestamp":
                $this->fTimestamp = intval($value);
                break;
            default:
                parent::__set($name, $value);
                break;
        }
    }

    /**
     * @param $value
     */
    protected function setAs($value)
    {
        $this->as = $value;
    }

    /**
     * @param string $alias
     * @return $this
     */
    public function alias($alias)
    {
        $this->as = $alias;

        /* @var $modelClass ActiveRecord */
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();

        if (empty($this->as)) {
            $this->as = str_replace(".", "_", $tableName);
        }

        $this->from([$this->as => $tableName]);

        return $this;
    }

    /**
     * @return string
     */
    public function getFieldPrefix()
    {
        return (!empty($this->as)) ? "{$this->as}." : "";
    }

    /**
     * @param null $db
     *
     * @return array
     *
     * @throws DbException
     */
    public function allRaw($db = null)
    {
        return $this->createCommand($db)->queryAll();
    }

    /**
     * @return array
     */
    public function getDefaultScope()
    {
        $result = [];

        if (is_array($this->defaultScope)) {
            $result['select'] = $this->copyDefaultScope('select');
            $result['condition'] = $this->copyDefaultScope('condition');
            $result['order'] = $this->copyDefaultScope('order');
            $result['limit'] = $this->copyDefaultScope('limit');
        }

        return $result;
    }

    /**
     * @param $name
     *
     * @return array|mixed|null
     */
    private function copyDefaultScope($name)
    {
        $result = null;
        if (isset($this->defaultScope[$name])) {
            $prefix = (!empty($this->as)) ? "{$this->as}." : "";
            if (!empty($prefix) && is_array($this->defaultScope[$name])) {
                $result = [];
                foreach ($this->defaultScope[$name] as $key => $val) {
                    $newKey = (is_string($key)) ? str_replace('@.', $prefix, $key) : $key;
                    $newVal = (is_string($val)) ? str_replace('@.', $prefix, $val) : $val;
                    $result[$newKey] = $newVal;
                }
            } else {
                $result = $this->defaultScope[$name];
            }
        }

        return $result;
    }

    /**
     * @param $name
     * @param $value
     *
     * @return mixed
     */
    public function formatImportValue($name, $value)
    {
        return $value;
    }

    /**
     * @param $name
     * @param $value
     * @param bool $strip
     *
     * @return mixed
     */
    public function formatExportValue($name, $value, $strip = false)
    {
        return $value;
    }
}
