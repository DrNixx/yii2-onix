<?php
namespace onix\assets;

use onix\Env;
use yii\web\AssetBundle;

/**
 * Base asset bundle class.
 */
class BaseAssetBundle extends AssetBundle
{
    /**
     * Unique value to set an empty asset via Yii AssetManager configuration.
     */
    const EMPTY_ASSET = 'N0/@$$3T$';

    /**
     * Unique value to set an empty asset file path via Yii AssetManager configuration.
     */
    const EMPTY_PATH = 'N0/P@T#';

    /**
     * Unique value identifying asset
     */
    const DUMMY_ASSET = 'K3/@$$3T$';

    /**
     * Unique value identifying a file path
     */
    const DUMMY_PATH = 'K3/P@T#';

    /**
     * @inheritdoc
     */
    public $js = self::DUMMY_ASSET;

    /**
     * @inheritdoc
     */
    public $css = self::DUMMY_ASSET;

    /**
     * @inheritdoc
     */
    public $sourcePath = self::DUMMY_PATH;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->js === self::DUMMY_ASSET) {
            $this->js = [];
        }

        if ($this->css === self::DUMMY_ASSET) {
            $this->css = [];
        }

        if ($this->sourcePath === self::DUMMY_PATH) {
            $this->sourcePath = null;
        }
    }
    /**
     * Adds a language JS locale file
     *
     * @param string $lang the ISO language code
     * @param string $prefix the language locale file name prefix
     * @param string $dir the language file directory relative to source path
     * @param bool $min whether to auto use minified version
     *
     * @return AssetBundle instance
     */
    public function addLanguage($lang = '', $prefix = '', $dir = null, $min = false)
    {
        if (empty($lang) || substr($lang, 0, 2) == 'en') {
            return $this;
        }

        $ext = $min ? (YII_DEBUG ? ".min.js" : ".js") : ".js";
        $file = "{$prefix}{$lang}{$ext}";
        if ($dir === null) {
            $dir = 'js';
        } elseif ($dir === "/") {
            $dir = '';
        }

        $path = $this->sourcePath . '/' . $dir;
        if (!Env::fileExists("{$path}/{$file}")) {
            $lang = Env::getLang($lang);
            $file = "{$prefix}{$lang}{$ext}";
        }

        if (Env::fileExists("{$path}/{$file}")) {
            $this->js[] = empty($dir) ? $file : "{$dir}/{$file}";
        }

        return $this;
    }

    /**
     * Set up CSS and JS asset arrays based on the base-file names
     *
     * @param string $type whether 'css' or 'js'
     * @param array $files the list of 'css' or 'js' basefile names
     */
    protected function setupAssets($type, $files = [])
    {
        if ($this->$type === self::DUMMY_ASSET) {
            $srcFiles = [];
            $minFiles = [];
            foreach ($files as $file) {
                $srcFiles[] = "{$file}.{$type}";
                $minFiles[] = "{$file}.min.{$type}";
            }

            $this->$type = YII_DEBUG ? $srcFiles : $minFiles;
        } elseif ($this->$type === self::EMPTY_ASSET) {
            $this->$type = [];
        }
    }

    /**
     * Sets the source path if empty
     *
     * @param string $path the path to be set
     */
    protected function setSourcePath($path)
    {
        if ($this->sourcePath === self::DUMMY_PATH) {
            $this->sourcePath = $path;
        } elseif ($this->sourcePath === self::EMPTY_PATH) {
            $this->sourcePath = null;
        }
    }
}
