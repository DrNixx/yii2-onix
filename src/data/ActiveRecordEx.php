<?php
namespace onix\data;

use onix\cache\CacheHelper;
use yii\base\Exception as BaseException;
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
        $result = \Yii::$app->cache->get($key);
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

                \Yii::$app->cache->set($key, $result, $duration, $dependency);
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
        if (\Yii::$app->cache === null) {
            throw new InvalidConfigException('ActiveRecordEx class required valid Yii::app->cache component');
        }

        parent::init();
    }

    /**
     * {@inheritdoc}
     *
     * @throws BaseException
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        try {
            if ($this->getIsNewRecord()) {
                return $this->insert($runValidation, $attributeNames);
            }

            return $this->update($runValidation, $attributeNames) !== false;
        } catch (BaseException $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            throw new BaseException($ex->getMessage(), $ex->getCode(), $ex);
        } catch (\Throwable $ex) {
            throw new BaseException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws BaseException
     */
    public function insert($runValidation = true, $attributes = null)
    {
        try {
            return parent::insert($runValidation, $attributes);
        } catch (\Exception $ex) {
            throw new BaseException($ex->getMessage(), $ex->getCode(), $ex);
        } catch (\Throwable $ex) {
            throw new BaseException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws BaseException
     */
    public function update($runValidation = true, $attributeNames = null)
    {
        try {
            $result = parent::update($runValidation, $attributeNames);
        } catch (BaseException $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            throw new BaseException($ex->getMessage(), $ex->getCode(), $ex);
        } catch (\Throwable $ex) {
            throw new BaseException($ex->getMessage(), $ex->getCode(), $ex);
        }
        if ($result) {
            $this->invalidateCache();
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @throws BaseException
     */
    public function delete()
    {
        try {
            $result = parent::delete();
        } catch (BaseException $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            throw new BaseException($ex->getMessage(), $ex->getCode(), $ex);
        } catch (\Throwable $ex) {
            throw new BaseException($ex->getMessage(), $ex->getCode(), $ex);
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
            return \Yii::$app->cache->buildKey([$strClass, ":{$wkey}:", $id]);
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
                TagDependency::invalidate(\Yii::$app->cache, $tags);
            }

            \Yii::$app->cache->delete($key);
        }
    }
}
