<?php

namespace app\themes\gentelella\widgets;

use rmrevin\yii\fontawesome\component\Icon;
use yii\base\Widget;
use yii\helpers\Html;

class Panel extends Widget
{
    /**
     * @var string[]
     */
    protected $tools = [];

    /**
     * @var array the HTML attributes for the widget container tag
     */
    public $options = ['class' => 'x_panel'];

    /**
     * @var string the panel header
     */
    public $header;

    /**
     * @var string the icon name
     */
    public $icon;

    /**
     * @var boolean whether the expand button is shown
     */
    public $expandable = false;

    /**
     * @var boolean whether the collapse button shown
     */
    public $collapsable = false;

    /**
     * @var boolean whether the remove button shown
     */
    public $removable = false;

    /**
     * Inits tool buttons
     */
    protected function initTools()
    {
        if ($this->expandable === true || $this->collapsable === true) {
            $this->tools[] = Html::tag(
                'a',
                new Icon('chevron-' . ($this->expandable === true ? 'down' : 'up')),
                ['class' => 'collapse-link']
            );
        }
        if ($this->removable === true) {
            $this->tools[] = Html::tag(
                'a',
                new Icon('close'),
                ['class' => 'close-link']
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->options['id'] = $this->id;
        echo Html::beginTag('div', $this->options);
        if ($this->header !== null) {
            $this->initTools();
            echo Html::beginTag('div', ['class' => 'x_title']);
            echo Html::tag('h2', ($this->icon !== null ? new Icon($this->icon) . ' ' : '') . $this->header);
            if (empty($this->tools) === false) {
                echo Html::tag(
                    'ul',
                    '<li>' . implode("</li>\n<li>", $this->tools) . '</li>',
                    ['class' => 'nav navbar-right panel_toolbox']
                );
            }
            echo Html::tag('div', null, ['class' => 'clearfix']);
            echo Html::endTag('div');
        }
        echo Html::beginTag(
            'div',
            [
                'class' => 'x_content',
                'style' => $this->expandable === true ? 'display: none;' : null
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        echo Html::endTag('div');
        echo Html::endTag('div');
        parent::run();
    }
}
