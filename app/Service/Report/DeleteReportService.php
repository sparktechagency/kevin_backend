<?php

namespace App\Service\Report;

use App\Models\Report;
use App\Traits\ResponseHelper;

class DeleteReportService
{
    use ResponseHelper;

    public function deleteReport($report_id)
    {
        $report = Report::find($report_id);

        if (!$report) {
            return $this->errorResponse("Report not found");
        }

        $report->delete();

        return $this->successResponse(null, "Report deleted successfully.");
    }
}
