<?php

namespace App\Orchid\Layouts\Reservation;

// use App\Orchid\Filters\RoleFilter;
use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;

class ReservationFiltersLayout extends Selection
{
    /**
     * @return string[]|Filter[]
     */
    public function filters(): array
    {
        return [
            // RoleFilter::class,
        ];
    }
}
