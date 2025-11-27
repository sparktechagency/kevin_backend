<?php

namespace App\Service\Report;

use App\Models\Report;
use App\Traits\ResponseHelper;

class UpdateReportService
{
    use ResponseHelper;

    public function updateReport($data, $report_id)
    {
        $report = Report::find($report_id);

        if (!$report) {
            return $this->errorResponse("Report not found", 404);
        }
        if (isset($data['metrics'])) {
            $data['metrics'] = json_encode($data['metrics']);
        }
        $report->update($data);
        return $this->successResponse($report, "Report updated successfully.");
    }
}
