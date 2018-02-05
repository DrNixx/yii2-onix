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
     * @param TagDependency[] $deps
     * @return TagDependency|null
     */
    public static function joinDependencies($deps)
    {
        if (Yii::$app->cache !== null) {
            $tags = [];
            foreach ($deps as $dep) {
                if (is_array($dep)) {
                    $tags = ArrayHelper::merge($tags, $dep);
                } else {
                    $tags[] = $dep;
                }
            }

            if (count($tags) > 0) {
                return new TagDependency([
                    'tags' => $tags
                ]);
            }
        }

        return null;
    }
}
