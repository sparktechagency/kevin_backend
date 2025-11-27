<?php

namespace App\Service\Report;

use App\Models\Report;
use App\Traits\ResponseHelper;

class CreateReportService
{
   use ResponseHelper;

  public function createReport($data)
    {
        if (isset($data['metrics'])) {
            $data['metrics'] = json_encode($data['metrics']);
        }
        $report = Report::create($data);
        return $this->successResponse($report, "Report created successfully.");
    }
}
