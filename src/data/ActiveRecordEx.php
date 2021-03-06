<?php
namespace onix\data;

use onix\cache\CacheHelper;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\caching\TagDependency;
use yii\db\ActiveRecord;

class ActiveRecordEx extends ActiveRecord
{

    /**
     * @param mixed $id
     * @param int|null $duration
     * @param TagDependency|string[]|null $dependency
     * @return static|null
     */
    protected static function getById($id, $duration = null, $dependency = null)
    {
        $key = static::buildCacheKey($id);
        if (!empty($key)) {
            return self::getOrSet($key, $id, $duration, $dependency);
        } else {
            return static::findById($id);
        }
    }

    /**
     * @param mixed $id
     * @return static|null
     */
    protected static function findById($id)
    {
        return static::findOne($id);
    }

    /**
     * @param static $obj
     *
     * @return string[]|null
     *
     * @noinspection PhpUnusedParameterInspection
     */
    protected static function buildCacheDependency($obj) {
        return null;
    }

    /**
     * @param string $key
     * @param mixed $id
     * @param int|null $duration
     * @param string|string[]|null $dependency
     * @return static|null
     */
    private static function getOrSet($key, $id, $duration = null, $dependency = null)
    {
        $result = Yii::$app->cache->get($key);
        if ($result === false) {
            $result = static::findById($id);
            if ($result !== null) {
                if (empty($dependency)) {
                    $dependency = static::buildCacheDependency($result);
                }

                if (!empty($dependency)) {
                    if (is_array($dependency)) {
                        $dependency = CacheHelper::joinDependencies($dependency);
                    } elseif (is_string($dependency)) {
                        $dependency = new TagDependency($dependency);
                    }
                }

                Yii::$app->cache->set($key, $result, $duration, $dependency);
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (Yii::$app->cache === null) {
            throw new InvalidConfigException('ActiveRecordEx class required valid Yii::app->cache component');
        }

        parent::init();
    }

    /**
     * Saves the current record.
     *
     * This method will call [[insert()]] when [[isNewRecord]] is `true`, or [[update()]]
     * when [[isNewRecord]] is `false`.
     *
     * For example, to save a customer record:
     *
     * ```php
     * $customer = new Customer; // or $customer = Customer::findOne($id);
     * $customer->name = $name;
     * $customer->email = $email;
     * $customer->save();
     * ```
     *
     * @param bool $runValidation whether to perform validation (calling [[validate()]])
     * before saving the record. Defaults to `true`. If the validation fails, the record
     * will not be saved to the database and this method will return `false`.
     * @param array $attributeNames list of attribute names that need to be saved. Defaults to null,
     * meaning all attributes that are loaded from DB will be saved.
     *
     * @return bool whether the saving succeeded (i.e. no validation errors occurred).
     *
     * @throws Exception
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        try {
            if ($this->getIsNewRecord()) {
                return $this->insert($runValidation, $attributeNames);
            }

            return $this->update($runValidation, $attributeNames) !== false;
        } catch (Exception $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            throw new Exception($ex->getMessage(), $ex->getCode(), $ex);
        } /** @noinspection PhpUndefinedClassInspection */ catch (\Throwable $ex) {
            throw new Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    /**
     * Inserts a row into the associated database table using the attribute values of this record.
     *
     * This method performs the following steps in order:
     *
     * 1. call [[beforeValidate()]] when `$runValidation` is `true`. If [[beforeValidate()]]
     *    returns `false`, the rest of the steps will be skipped;
     * 2. call [[afterValidate()]] when `$runValidation` is `true`. If validation
     *    failed, the rest of the steps will be skipped;
     * 3. call [[beforeSave()]]. If [[beforeSave()]] returns `false`,
     *    the rest of the steps will be skipped;
     * 4. insert the record into database. If this fails, it will skip the rest of the steps;
     * 5. call [[afterSave()]];
     *
     * In the above step 1, 2, 3 and 5, events [[EVENT_BEFORE_VALIDATE]],
     * [[EVENT_AFTER_VALIDATE]], [[EVENT_BEFORE_INSERT]], and [[EVENT_AFTER_INSERT]]
     * will be raised by the corresponding methods.
     *
     * Only the [[dirtyAttributes|changed attribute values]] will be inserted into database.
     *
     * If the table's primary key is auto-incremental and is `null` during insertion,
     * it will be populated with the actual value after insertion.
     *
     * For example, to insert a customer record:
     *
     * ```php
     * $customer = new Customer;
     * $customer->name = $name;
     * $customer->email = $email;
     * $customer->insert();
     * ```
     *
     * @param bool $runValidation whether to perform validation (calling [[validate()]])
     * before saving the record. Defaults to `true`. If the validation fails, the record
     * will not be saved to the database and this method will return `false`.
     * @param array $attributes list of attributes that need to be saved. Defaults to `null`,
     * meaning all attributes that are loaded from DB will be saved.
     *
     * @return bool whether the attributes are valid and the record is inserted successfully.
     *
     * @throws Exception
     */
    public function insert($runValidation = true, $attributes = null)
    {
        try {
            return parent::insert($runValidation, $attributes);
        } catch (\Exception $ex) {
            throw new Exception($ex->getMessage(), $ex->getCode(), $ex);
        } /** @noinspection PhpUndefinedClassInspection */ catch (\Throwable $ex) {
            throw new Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    /**
     * Saves the changes to this active record into the associated database table.
     *
     * This method performs the following steps in order:
     *
     * 1. call [[beforeValidate()]] when `$runValidation` is `true`. If [[beforeValidate()]]
     *    returns `false`, the rest of the steps will be skipped;
     * 2. call [[afterValidate()]] when `$runValidation` is `true`. If validation
     *    failed, the rest of the steps will be skipped;
     * 3. call [[beforeSave()]]. If [[beforeSave()]] returns `false`,
     *    the rest of the steps will be skipped;
     * 4. save the record into database. If this fails, it will skip the rest of the steps;
     * 5. call [[afterSave()]];
     *
     * In the above step 1, 2, 3 and 5, events [[EVENT_BEFORE_VALIDATE]],
     * [[EVENT_AFTER_VALIDATE]], [[EVENT_BEFORE_UPDATE]], and [[EVENT_AFTER_UPDATE]]
     * will be raised by the corresponding methods.
     *
     * Only the [[dirtyAttributes|changed attribute values]] will be saved into database.
     *
     * For example, to update a customer record:
     *
     * ```php
     * $customer = Customer::findOne($id);
     * $customer->name = $name;
     * $customer->email = $email;
     * $customer->update();
     * ```
     *
     * Note that it is possible the update does not affect any row in the table.
     * In this case, this method will return 0. For this reason, you should use the following
     * code to check if update() is successful or not:
     *
     * ```php
     * if ($customer->update() !== false) {
     *     // update successful
     * } else {
     *     // update failed
     * }
     * ```
     *
     * @param bool $runValidation whether to perform validation (calling [[validate()]])
     * before saving the record. Defaults to `true`. If the validation fails, the record
     * will not be saved to the database and this method will return `false`.
     *
     * @param array $attributeNames list of attribute names that need to be saved. Defaults to null,
     * meaning all attributes that are loaded from DB will be saved.
     *
     * @return int|false the number of rows affected, or `false` if validation fails
     * or [[beforeSave()]] stops the updating process.
     *
     * @throws Exception
     */
    public function update($runValidation = true, $attributeNames = null)
    {
        try {
            $result = parent::update($runValidation, $attributeNames);
        } catch (Exception $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            throw new Exception($ex->getMessage(), $ex->getCode(), $ex);
        } /** @noinspection PhpUndefinedClassInspection */ catch (\Throwable $ex) {
            throw new Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
        if ($result) {
            $this->invalidateCache();
        }

        return $result;
    }

    /**
     * Deletes the table row corresponding to this active record.
     *
     * This method performs the following steps in order:
     *
     * 1. call [[beforeDelete()]]. If the method returns `false`, it will skip the
     *    rest of the steps;
     * 2. delete the record from the database;
     * 3. call [[afterDelete()]].
     *
     * In the above step 1 and 3, events named [[EVENT_BEFORE_DELETE]] and [[EVENT_AFTER_DELETE]]
     * will be raised by the corresponding methods.
     *
     * @return int|false the number of rows deleted, or `false` if the deletion is unsuccessful for some reason.
     * Note that it is possible the number of rows deleted is 0, even though the deletion execution is successful.
     *
     * @throws Exception
     */
    public function delete()
    {
        try {
            $result = parent::delete();
        } catch (Exception $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            throw new Exception($ex->getMessage(), $ex->getCode(), $ex);
        } /** @noinspection PhpUndefinedClassInspection */ catch (\Throwable $ex) {
            throw new Exception($ex->getMessage(), $ex->getCode(), $ex);
        }

        if ($result) {
            $this->invalidateCache();
        }

        return $result;
    }

    /**
     * @return array|string|int
     */
    public function getKeyValues()
    {
        $keys = $this->getPrimaryKey(true);
        if (count($keys) === 1) {
            $ids = array_shift($keys);
        } else {
            $ids = array_values($keys);
        }

        return $ids;
    }

    /**
     * @return string|null
     */
    public function getCacheKey()
    {
        return static::buildCacheKey($this->getKeyValues());
    }

    /**
     * @param int|string|array $id
     * @param string $wkey
     *
     * @return null|string
     */
    public static function buildCacheKey($id, $wkey = "Key")
    {
        if (!empty($id)) {
            $strClass = get_called_class();
            return Yii::$app->cache->buildKey([$strClass, ":{$wkey}:", $id]);
        }

        return null;
    }

    /**
     * @param int|string|array $id
     * @param string $wkey
     *
     * @return string|null
     */
    public static function buildCacheTag($id, $wkey = "Key")
    {
        return static::buildCacheKey($id, ":Tag:{$wkey}:");
    }

    /**
     * Invalidate cache related for object
     */
    public function invalidateCache()
    {
        $ids = $this->getKeyValues();
        static::invalidate($ids);
    }

    /**
     * @param array|string|int $id
     */
    public static function invalidate($id)
    {
        $key = static::buildCacheKey($id);
        if ($key != null) {
            $tags = static::buildCacheTag($id);
            if ($tags !== null) {
                TagDependency::invalidate(Yii::$app->cache, $tags);
            }

            Yii::$app->cache->delete($key);
        }
    }
}
