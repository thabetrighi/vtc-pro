<?php

declare(strict_types=1);

namespace App\Orchid\Screens;

use App\Orchid\Layouts\Settings\ChartBarSetting;
use App\Orchid\Layouts\Settings\ChartLineSetting;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class PlatformScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'charts'  => [
                [
                    'name'   => 'Completed Rides',
                    'values' => [25, 40, 30, 35, 38, 52, 47],
                    'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                ],
                [
                    'name'   => 'Revenue (USD)',
                    'values' => [250, 500, 450, 480, 600, 750, 680],
                    'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                ],
                [
                    'name'   => 'Active Drivers',
                    'values' => [15, 20, 18, 22, 25, 28, 24],
                    'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                ],
                [
                    'name'   => 'Customer Ratings',
                    'values' => [4.5, 4.3, 4.8, 4.6, 4.7, 4.4, 4.6],
                    'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                ],
            ],
            'metrics' => [
                'rides'      => ['value' => number_format(1), 'diff' => 12.5],
                'past_rides' => ['value' => number_format(3), 'diff' => 12.5],
                'pending'    => ['value' => number_format(1), 'diff' => -15.2],
                'revenue'    => '$' . number_format(8560),
            ],
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Taxi Booking Dashboard';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Real-time overview of booking metrics and performance';
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
     * @return string[]|\Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            Layout::metrics([
                'Today\'s Bookings'    => 'metrics.rides',
                'Previous Bookings'    => 'metrics.past_rides',
                'Pending Requests'     => 'metrics.pending',
                'Today\'s Revenue'     => 'metrics.revenue',
            ]),

            Layout::columns([
                ChartLineSetting::make('charts', 'Weekly Performance')
                    ->description('Track key metrics over the past week'),

                ChartBarSetting::make('charts', 'Daily Statistics')
                    ->description('Compare daily booking and revenue data'),
            ]),
        ];
    }
}
