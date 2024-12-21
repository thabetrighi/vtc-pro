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
        return config('app.name', 'Taxi Booking Dashboard') . ' ' . __('app.dashboard');
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return settings('dashboard_description', __('app.dashboard_description'));
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('app.new_reservation'))
                ->icon('bs.ticket-perforated')
                ->class('border btn btn-lg btn-link btn-primary icon-link p-3 rounded-3')
                ->permission('platform.reservations.create')
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
                settings('metric_pending_title', __('app.pending_reservations')) => 'metrics.pending',
                settings('metric_ongoing_title', __('app.ongoing_reservations')) => 'metrics.ongoing',
                settings('metric_completed_title', __('app.completed_reservations')) => 'metrics.completed',
                settings('metric_canceled_title', __('app.canceled_reservations')) => 'metrics.canceled',
            ]),
        ];
    }
}
