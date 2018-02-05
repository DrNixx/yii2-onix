<?php
namespace onix\data;

use Yii;
use yii\base\Exception;
use yii\caching\TagDependency;
use yii\db\ActiveRecord;

class ActiveRecordEx extends ActiveRecord
{
    /**
     * @param bool $runValidation
     * @param null $attributeNames
     *
     * @return bool
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
        } catch (\Throwable $ex) {
            throw new Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    /**
     * @param bool $runValidation
     * @param null $attributeNames
     *
     * @return false|int
     *
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function update($runValidation = true, $attributeNames = null)
    {
        $result = parent::update($runValidation, $attributeNames);
        if ($result) {
            $this->invalidateCache();
        }

        return $result;
    }

    /**
     * @return false|int
     * @throws \Exception
     * @throws \Throwable
     */
    public function delete()
    {
        $result = parent::delete();
        if ($result) {
            $this->invalidateCache();
        }

        return $result;
    }

    /**
     * @return string|null
     */
    public function getCacheKey()
    {
        return null;
    }

    /**
     * @param int|string|array $id
     * @param string $wkey
     * @return null|string
     */
    public static function buildCacheKey($id, $wkey = "Key")
    {
        $cache = Yii::$app->cache;
        if ($cache !== null) {
            $strClass = get_called_class();
            $key = $cache->buildKey([$strClass, ":{$wkey}:", $id]);
            Yii::trace('Build cache key: '.json_encode([$strClass, ":{$wkey}:", $id]).' = '.$key);
            return $key;
        }

        return null;
    }

    /**
     * @return string|string[]|null
     */
    public function getCacheTag()
    {
        return null;
    }

    /**
     * @param int|string|array $id
     * @param string $wkey
     *
     * @return null|string[]
     */
    public static function buildCacheTag($id, $wkey = "Key")
    {
        $cache = Yii::$app->cache;
        if ($cache !== null) {
            $strClass = get_called_class();
            Yii::trace('Build cache tag: '.json_encode([$strClass, 'Tag', $id]));
            return [$cache->buildKey([$strClass, ":Tag:{$wkey}:", $id])];
        }

        return null;
    }

    /**
     * @return TagDependency|null
     */
    public function getCacheDependency()
    {
        $tags = $this->getCacheTag();
        if ($tags !== null) {
            return new TagDependency([
                'tags' => $tags
            ]);
        }

        return null;
    }

    /**
     * @param int|string|array $id
     * @return TagDependency
     */
    public static function buildCacheDependency($id)
    {
        $tags = static::buildCacheTag($id);
        if ($tags !== null) {
            return new TagDependency([
                'tags' => $tags
            ]);
        }

        return null;
    }

    /**
     * Invalidate cache related for object
     */
    public function invalidateCache()
    {
        if (!$this->isNewRecord) {
            $cache = Yii::$app->cache;
            if ($cache !== null) {
                $key = $this->getCacheKey();
                if ($key != null) {
                    Yii::trace(sprintf("Clear cache key %s", $key));
                    $cache->delete($key);
                }

                $tags = $this->getCacheTag();
                if ($tags !== null) {
                    TagDependency::invalidate($cache, $tags);
                }
            }
        }
    }

    /**
     * @param string|int $id
     */
    public static function invalidate($id)
    {
        $cache = Yii::$app->cache;
        if ($cache !== null) {
            $key = static::buildCacheKey($id);
            if ($key != null) {
                $cache->delete($key);
            }

            $tags = static::buildCacheTag($id);
            if ($tags !== null) {
                TagDependency::invalidate($cache, $tags);
            }
        }
    }
}
