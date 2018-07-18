<?php
namespace onix\assets;

/**
 * Asset bundle used for extensions with jquery dependency.
 */
class AssetBundle extends BaseAssetBundle
{
    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
