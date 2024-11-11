<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Settings;

use Orchid\Screen\Layouts\Chart;

class ChartPercentageSetting extends Chart
{
    /**
     * Available options:
     * 'bar', 'line',
     * 'pie', 'percentage'.
     *
     * @var string
     */
    protected $type = self::TYPE_PERCENTAGE;

    /**
     * @var int
     */
    protected $height = 160;
}
