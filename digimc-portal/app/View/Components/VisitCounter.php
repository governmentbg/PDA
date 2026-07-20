<?php

namespace App\View\Components;

use App\Services\MatomoReportingService;
use Illuminate\View\Component;

class VisitCounter extends Component
{
    public int $today = 0;
    public int $total = 0;
    public function __construct(private MatomoReportingService $service) {

    }

    public function render() {
        try {
            ['today'=>$this->today,'total'=>$this->total] = $this->service->getCounters();
        } catch (\Throwable) {

        }
        return view('components.visit-counter');
    }
}
