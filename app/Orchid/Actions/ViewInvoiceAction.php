<?php

declare(strict_types=1);

namespace App\Orchid\Actions;

use App\Models\Reservation;
use App\Services\InvoiceService;
use Illuminate\Http\Response;

class ViewInvoiceAction
{
    public function __construct(private InvoiceService $invoiceService)
    {
    }

    public function handle(Reservation $reservation): Response
    {
        return $this->invoiceService->view($reservation);
    }
} 