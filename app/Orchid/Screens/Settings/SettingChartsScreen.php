<?php

namespace App\Orchid\Screens\Settings;

use App\Orchid\Layouts\Settings\ChartBarSetting;
use App\Orchid\Layouts\Settings\ChartLineSetting;
use App\Orchid\Layouts\Settings\ChartPercentageSetting;
use App\Orchid\Layouts\Settings\ChartPieSetting;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class SettingChartsScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'charts' => [
                [
                    'name'   => 'Some Data',
                    'values' => [25, 40, 30, 35, 8, 52, 17],
                    'labels' => ['12am-3am', '3am-6am', '6am-9am', '9am-12pm', '12pm-3pm', '3pm-6pm', '6pm-9pm'],
                ],
                [
                    'name'   => 'Another Set',
                    'values' => [25, 50, -10, 15, 18, 32, 27],
                    'labels' => ['12am-3am', '3am-6am', '6am-9am', '9am-12pm', '12pm-3pm', '3pm-6pm', '6pm-9pm'],
                ],
                [
                    'name'   => 'Yet Another',
                    'values' => [15, 20, -3, -15, 58, 12, -17],
                    'labels' => ['12am-3am', '3am-6am', '6am-9am', '9am-12pm', '12pm-3pm', '3pm-6pm', '6pm-9pm'],
                ],
                [
                    'name'   => 'And Last',
                    'values' => [10, 33, -8, -3, 70, 20, -34],
                    'labels' => ['12am-3am', '3am-6am', '6am-9am', '9am-12pm', '12pm-3pm', '3pm-6pm', '6pm-9pm'],
                ],
            ],
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Charts';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'A comprehensive guide to creating and customizing various types of charts, including bar, line, and pie charts.';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @throws \Throwable
     *
     * @return string[]|\Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            ChartLineSetting::make('charts', 'Actions with a Tweet')
                ->description('The total number of interactions a user has with a tweet. This includes all clicks on any links in the tweet (including hashtags, links, avatar, username, and expand button), retweets, replies, likes, and additions to the read list.'),

            Layout::columns([
                ChartLineSetting::make('charts', 'Line Chart')
                    ->description('Visualize data trends with multi-colored line graphs.'),
                ChartBarSetting::make('charts', 'Bar Chart')
                    ->description('Compare data sets with colorful bar graphs.'),
            ]),

            Layout::columns([
                ChartPercentageSetting::make('charts', 'Percentage Chart')
                    ->description('Display data as visually appealing and modern percentage graphs.'),

                ChartPieSetting::make('charts', 'Pie Chart')
                    ->description('Break down data into easy-to-understand pie graphs with modern design.'),
            ]),
        ];
    }
}
