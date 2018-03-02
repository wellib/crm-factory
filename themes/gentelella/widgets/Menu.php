<?php

namespace app\themes\gentelella\widgets;


use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use rmrevin\yii\fontawesome\component\Icon;

class Menu extends \yii\widgets\Menu
{
    /**
     * @inheritdoc
     */
    public $labelTemplate = '{label}';
    /**
     * @inheritdoc
     */
    public $linkTemplate = '<a href="{url}">{icon}<span>{label}</span>{badge}</a>';
    /**
     * @inheritdoc
     */
    public $submenuTemplate = "\n<ul class=\"nav child_menu\">\n{items}\n</ul>\n";
    /**
     * @inheritdoc
     */
    public $activateParents = true;
    /**
     * @inheritdoc
     */
    public function init()
    {
        Html::addCssClass($this->options, 'nav side-menu');
        parent::init();
    }
    /**
     * @inheritdoc
     */
    protected function renderItem($item)
    {
        $renderedItem = parent::renderItem($item);
        if (isset($item['badge'])) {
            $badgeOptions = ArrayHelper::getValue($item, 'badgeOptions', []);
            $badgeOptions['style'] = 'margin-left: 5px;';
            Html::addCssClass($badgeOptions, 'label');
        } else {
            $badgeOptions = null;
        }

        $multipleBadges = [];
        if (isset($item['multipleBadges']) && is_array($item['multipleBadges'])) {
            $multipleBadges = [];
            foreach ($item['multipleBadges'] as $badge) {
                $badgeTagOptions = $badge['options'];
                $badgeTagOptions['style'] = 'margin-left: 2px;';
                Html::addCssClass($badgeTagOptions, 'label');
                $multipleBadges[] = [
                    'label' => $badge['label'],
                    'options' => $badgeTagOptions,
                ];
            }
        }

        return strtr(
            $renderedItem,
            [
                '{icon}' => isset($item['icon'])
                    ? new Icon($item['icon'], ArrayHelper::getValue($item, 'iconOptions', []))
                    : '',
                '{badge}' => (
                    isset($item['items']) && count($item['items']) > 0
                        ? (new Icon('chevron-down'))->tag('span')
                        : ''
                    ) . (
                    isset($item['badge'])
                        ? Html::tag('small', $item['badge'], $badgeOptions)
                        : ''
                    ) . implode('', array_map(function($badge){
                        return Html::tag('small', $badge['label'], $badge['options']);
                    }, $multipleBadges)),
            ]
        );
    }
}