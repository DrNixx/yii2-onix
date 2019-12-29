<?php
namespace onix\cache;

use yii\caching\TagDependency;

class CacheHelperTest extends \Codeception\Test\Unit
{
    public function testInvalidate()
    {
        $cache = \Yii::$app->cache;
        $key = 'key1';
        $tags = ['tag1', 'tag2'];
        $value = '1234';
        $cache->set($key, $value, 600, CacheHelper::joinDependencies($tags));
        $result = $cache->get($key);
        $this->assertEquals($value, $result);

        CacheHelper::invalidate('tag1');

        $result = $cache->get($key);
        $this->assertFalse($result);
    }

    /**
     * @throws \Exception
     */
    public function testJoinDependencies()
    {
        $deps = null;
        $result = CacheHelper::joinDependencies($deps);
        $this->assertNull($result);

        $deps = ['a', 'b', 'c', 'd'];
        $result = CacheHelper::joinDependencies($deps);
        $this->assertInstanceOf(TagDependency::class, $result);
        $this->assertEquals('abcd', implode('', $result->tags));

        $deps = [
            'a',
            new TagDependency(['tags' => ['b', 'c']]),
            ['d', 'e', ['f', 'g']]
        ];
        $result = CacheHelper::joinDependencies($deps);
        $this->assertInstanceOf(TagDependency::class, $result);
        $this->assertEquals('abcdefg', implode('', $result->tags));

        $deps = ['a', 'b', 'a', 'b', new TagDependency(['tags' => 'b'])];
        $result = CacheHelper::joinDependencies($deps);
        $this->assertInstanceOf(TagDependency::class, $result);
        $this->assertEquals('ab', implode('', $result->tags));
    }
}