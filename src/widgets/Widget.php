<?php
namespace onix\widgets;

use onix\i18n\TranslationTrait;
use yii\base\Widget as YiiWidget;
use yii\helpers\ArrayHelper;
use yii\web\View;

/**
 * Base class for widgets extending [[YiiWidget]]
 */
class Widget extends YiiWidget
{
    use TranslationTrait;
    use WidgetTrait;

    /**
     * @var array default HTML attributes or other settings for widgets.
     */
    public $defaultOptions = [];

    /**
     * @var array HTML attributes or other settings for widgets.
     */
    public $options = [];

    /**
     * @var string the javascript that will be used to destroy the jQuery plugin
     */
    public $pluginDestroyJs;

    /**
     * @var string the name of the jQuery plugin.
     */
    public $pluginName = '';

    /**
     * @var array widget plugin options.
     */
    public $defaultPluginOptions = [];

    /**
     * @var array widget plugin options.
     */
    public $pluginOptions = [];

    /**
     * @var array widget JQuery events. You must define events in `event-name => event-function` format. For example:
     *
     * ~~~
     * pluginEvents = [
     *     'change' => 'function() { log("change"); }',
     *     'open' => 'function() { log("open"); }',
     * ];
     * ~~~
     */
    public $pluginEvents = [];

    /**
     * @var string a pjax container identifier if applicable inside which the widget will be rendered. If this is set,
     * the widget will automatically reinitialize on pjax render completion.
     */
    public $pjaxContainerId;

    /**
     * @var boolean enable pop state fix for pjax container on press of browser back & forward buttons.
     */
    public $enablePopStateFix = true;

    /**
     * @var integer the position where the client JS hash variables for the widget will be loaded.
     * Defaults to `View::POS_HEAD`. This can be set to `View::POS_READY` for specific scenarios like when
     * rendering the widget via `renderAjax`.
     */
    public $hashVarLoadPosition = View::POS_HEAD;

    /**
     * @var array the the internalization configuration for this widget.
     *
     * @see [[\yii\i18n\I18N]] component for understanding the configuration details.
     */
    public $i18n = [];

    /**
     * @var string translation message file category name for i18n.
     *
     * @see [[\yii\i18n\I18N]]
     */
    protected $_msgCat = '';

    /**
     * @var string the HTML5 data variable name that will be used to store the Json encoded pluginOptions within the
     * element on which the jQuery plugin will be initialized.
     */
    protected $_dataVar;

    /**
     * @var string the generated hashed variable name that will store the JSON encoded pluginOptions in
     * [[View::POS_HEAD]].
     */
    protected $_hashVar;

    /**
     * @var string the JSON encoded plugin options.
     */
    protected $_encOptions = '';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->pluginOptions = ArrayHelper::merge($this->defaultPluginOptions, $this->pluginOptions);
        $this->options = ArrayHelper::merge($this->defaultOptions, $this->options);
        if (empty($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        $this->initDestroyJs();
    }
}
