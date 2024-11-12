<?php

declare(strict_types=1);

namespace App\Orchid\Screens;

use App\Models\Reservation;
use Orchid\Screen\Actions\Link;
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
            'metrics' => [
                'pending'   => Reservation::where('status', 'pending')->count(),
                'ongoing'   => Reservation::where('status', 'ongoing')->count(),
                'completed' => Reservation::where('status', 'completed')->count(),
                'canceled'  => Reservation::where('status', 'canceled')->count(),
            ],
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return settings('dashboard_name', 'Taxi Booking Dashboard');
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return settings('dashboard_description', 'Real-time overview of booking metrics and performance');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('New Reservation'))
                ->icon('bs.ticket-perforated')
                ->route('platform.reservations.create'),
        ];
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
                settings('metric_pending_title', 'Pending Reservations') => 'metrics.pending',
                settings('metric_ongoing_title', 'Ongoing Reservations') => 'metrics.ongoing',
                settings('metric_completed_title', 'Completed Reservations') => 'metrics.completed',
                settings('metric_canceled_title', 'Canceled Reservations') => 'metrics.canceled',
            ]),
        ];
    }
}
