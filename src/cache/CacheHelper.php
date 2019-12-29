<?php
namespace onix\cache;

use Yii;
use yii\caching\TagDependency;
use yii\helpers\ArrayHelper;

class CacheHelper
{
    /**
     * Use to invalidate cache.
     * @param string|array $tags
     */
    public static function invalidate($tags)
    {
        if (Yii::$app->cache !== null) {
            TagDependency::invalidate(Yii::$app->cache, $tags);
        }
    }

    /**
     * @param string|string[]|TagDependency|TagDependency[] $deps
     * @return TagDependency|null
     */
    public static function joinDependencies($deps)
    {
        if ((Yii::$app->cache !== null) && (!empty($deps))) {
            $tags = [];
            foreach ($deps as $dep) {
                if (is_array($dep)) {
                    $inner = self::joinDependencies($dep);
                    if ($inner !== null) {
                        $tags = ArrayHelper::merge($tags, $inner->tags);
                    }
                } else {
                    if ($dep instanceof TagDependency) {
                        $tags = ArrayHelper::merge($tags, (array)$dep->tags);
                    } else {
                        $tags[] = $dep;
                    }
                }
            }

            if (count($tags) > 0) {
                return new TagDependency([
                    'tags' => array_unique($tags)
                ]);
            }
        }

        return null;
    }
}
