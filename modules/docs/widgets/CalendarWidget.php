<?php

namespace app\modules\docs\widgets;

use app\modules\docs\models\CalendarPeriod;
use app\modules\docs\models\Task;
use rmrevin\yii\fontawesome\FA;
use yii\base\Widget;
use app\modules\docs\Module;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\web\View;

/**
 * Class CalendarWidget
 *
 * @property null|string $month Месяц
 * @property null|string $year Год
 * @property array $tableOptions
 * @property boolean $weekdaysAbbr Сокращенные название дней недели
 *
 * @package app\modules\docs\widgets
 */
class CalendarWidget extends Widget
{
    /**
     * @var null|string qwe
     */
    public $month;
    /**
     * @var null|string
     */
    public $year;
    /**
     * @var array
     */
    public $tableOptions = [];

    public $weekdaysAbbr = true;
    
    public function init()
    {
        if (!$this->month) {
            $this->month = date('m');
        }
        if (!$this->year) {
            $this->year = date('Y');
        }
    }

    /**
     * Дни недели
     *
     * @param bool $abbr Сокращенные название дней недели
     * @return array
     */
    protected function getDaysOfWeek($abbr = false)
    {
        $suffix = $abbr === true ? '_SHORT' : '';
        return [
            'Пн',
            'Вт',
            'Ср',
            'Чт',
            'Пт',
            'Сб',
            'Вс',
            //Module::t('calendar-widget', 'MON' . $suffix),
            //Module::t('calendar-widget', 'TUE' . $suffix),
            //Module::t('calendar-widget', 'WED' . $suffix),
            //Module::t('calendar-widget', 'THU' . $suffix),
            //Module::t('calendar-widget', 'FRI' . $suffix),
            //Module::t('calendar-widget', 'SAT' . $suffix),
            //Module::t('calendar-widget', 'SUN' . $suffix),
        ];
    }

    public function run()
    {
        ob_start();
        ob_implicit_flush(false);

        $month = $this->month;
        $year  = $this->year;

        // Create array containing abbreviations of days of week.
        $daysOfWeek = $this->getDaysOfWeek($this->weekdaysAbbr);

        // What is the first day of the month in question?
        $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);

        // How many days does this month contain?
        $numberDays = date('t',$firstDayOfMonth);

        // Retrieve some information about the first day of the
        // month in question.
        $dateComponents = getdate($firstDayOfMonth);
        //var_dump($dateComponents);
        //VarDumper::dump($dateComponents, 10, true);
        // What is the name of the month in question?
        $monthName = $dateComponents['month'];

        // What is the index value (0-6) of the first day of the
        // month in question.
        $dayOfWeek = $dateComponents['wday'];

        $next = getdate(strtotime('+1month',$firstDayOfMonth));
        $prev = getdate(strtotime('-1month',$firstDayOfMonth));

        //var_dump(date('Y-m',strtotime('+1month',$firstDayOfMonth)));
        //var_dump(date('Y-m',strtotime('-1month',$firstDayOfMonth)));



        echo Html::beginTag('div', ['class' => 'pull-left']);
            echo Html::a(FA::i('arrow-circle-o-left') . ' ' . $prev['mon'] . '.' . $prev['year'], ['calendar', 'month' => $prev['mon'], 'year' => $prev['year']], [
                'class' => 'btn btn-primary',
            ]);
        echo Html::endTag('div');

        echo Html::beginTag('div', ['class' => 'pull-right']);
            echo Html::a($next['mon'] . '.' . $next['year'] . ' ' . FA::i('arrow-circle-o-right'), ['calendar', 'month' => $next['mon'], 'year' => $next['year']], [
            'class' => 'btn btn-primary',
            ]);
        echo Html::endTag('div');


        // Create the table tag opener and day headers
        echo Html::beginTag('table', ArrayHelper::merge([
            'class' => 'table table-bordered',
        ], $this->tableOptions));
        //echo Html::tag('caption', "$monthName $year");

        // Create the calendar headers
        echo Html::beginTag('thead');
        echo Html::beginTag('tr');
        foreach($daysOfWeek as $day) {
            echo Html::tag('th', $day, ['class' => 'header']);
        }
        echo Html::endTag('tr');
        echo Html::endTag('thead');


        // Create the rest of the calendar

        // Initiate the day counter, starting with the 1st.
        $currentDay = 1;

        echo Html::beginTag('tbody');
        echo Html::beginTag('tr');


        // The variable $dayOfWeek is used to
        // ensure that the calendar
        // display consists of exactly 7 columns.
        if ($dayOfWeek > 1 && $dayOfWeek <= 6) {
            echo Html::tag('td', '&nbsp;', [
                'colspan' => $dayOfWeek - 1,
            ]);
        }

        $month = str_pad($month, 2, "0", STR_PAD_LEFT);

        while ($currentDay <= $numberDays) {

            // Seventh column (Saturday) reached. Start a new row.
            if ($dayOfWeek == 8) {
                $dayOfWeek = 1;
                echo Html::endTag('tr');
                echo Html::beginTag('tr');
            }

            $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
            $date = "$currentDayRel.$month.$year";
            $ts = strtotime($date);

            $class = $dayOfWeek > 5 ? 'danger' : 'success';

            $model = CalendarPeriod::find()->where(['from_date' => $ts, 'to_date' => $ts])->one();
            /** @var CalendarPeriod $model */

            if ($model !== null) {
                if ($model->type == CalendarPeriod::TYPE_WORKDAYS) {
                    $class = 'success';
                }
                if ($model->type == CalendarPeriod::TYPE_HOLIDAYS) {
                    $class = 'danger';
                }
            }

            echo Html::tag('td', $currentDay, [
                'class' => 'day' . $currentDay . ' ' . $class,
                'rel' => $date,
                'onclick' => 'setDay(this);',
                'style' => 'cursor: pointer;'
            ]);

            $url = Url::to(['/docs/calendar-period/calendar-up']);

            $type_workdays = CalendarPeriod::TYPE_WORKDAYS;
            $type_holidays = CalendarPeriod::TYPE_HOLIDAYS;
            
            $this->view->registerJs(<<<JS
                setDay = function(element) {
                    var el = $(element),
                        date = el.attr('rel');
                    if (el.hasClass('success')) {
                        el.removeClass('success');
                        el.addClass('danger');
                        setDayAjax(date, '$type_holidays')
                    } else if(el.hasClass('danger')) {
                        el.removeClass('danger');
                        el.addClass('success');
                        setDayAjax(date, '$type_workdays')
                    }
                    
                };
                setDayAjax = function(date, type) {
                    $.ajax({
                        url: '$url',
                        type: 'post',
                        data: {
                            date: date,
                            type: type
                        }
                    });
                };
JS
            , View::POS_READY, 'qqqwelqkwelqkwe');

            // Increment counters
            $currentDay++;
            $dayOfWeek++;

        }



        // Complete the row of the last week in month, if necessary

        if ($dayOfWeek != 8) {

            $remainingDays = 8 - $dayOfWeek;
            echo Html::tag('td', '&nbsp;', [
                'colspan' => $remainingDays,
            ]);

        }

        echo Html::endTag('tr');
        echo Html::endTag('tbody');
        echo Html::endTag('table');

        return ob_get_clean();
    }
}