<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Plan\UpdatePlanRequest;
use App\Http\Requests\Report\CreateReportRequest;
use App\Service\AdminDashboard\AnalyticsService;
use App\Service\AdminDashboard\IndexService;
use App\Service\Report\CreateReportService;
use App\Service\Report\DeleteReportService;
use App\Service\Report\GetReportService;
use App\Service\Report\UpdateReportService;
use App\Service\Subscription\UpdatePlanService;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    protected $indexService;
    protected $analyticsService;
    protected $createReportService;
    protected $getReportSErvice;
    protected $updateReportService;
    protected $deleteReportService;
    protected $updatePlanService;

    public function __construct(
        IndexService $indexService,
        AnalyticsService $analyticsService,
        CreateReportService $createReportService,
        GetReportService $getReportService,
        UpdateReportService $updateReportService,
        DeleteReportService $deleteReportService,
        UpdatePlanService $updatePlanService,
    ){
        $this->indexService = $indexService;
        $this->analyticsService = $analyticsService;
        $this->createReportService = $createReportService;
        $this->getReportSErvice = $getReportService;
        $this->updateReportService = $updateReportService;
        $this->deleteReportService = $deleteReportService;
        $this->updatePlanService = $updatePlanService;
    }
    public function index(Request $request)
    {
        return $this->execute(function()use($request){
            return $this->indexService->index($request);
        });
    }
    public function analytics(Request $request)
    {
        return $this->execute(function()use($request){
            return $this->analyticsService->analytics($request);
        });
    }
    public function createReport(CreateReportRequest $createReportRequest)
    {
        return $this->execute(function()use($createReportRequest){
            $data = $createReportRequest->validated();
            return $this->createReportService->createReport($data);
        });
    }
    public function getReport()
    {
        return $this->execute(function(){
            return $this->getReportSErvice->getReport();
        });
    }
    public function updateReport(CreateReportRequest $createReportRequest, $report_id)
    {
        return $this->execute(function()use($createReportRequest, $report_id){
            $data = $createReportRequest->validated();
            return $this->updateReportService->updateReport($data, $report_id);
        });
    }
    public function deleteReport($report_id)
    {
        return $this->execute(function()use($report_id){
            return $this->deleteReportService->deleteReport($report_id);
        });
    }
    public function updatePlan(UpdatePlanRequest $updatePlanRequest, $plan)
    {
        return $this->execute(function()use($updatePlanRequest, $plan){
            $data = $updatePlanRequest->validated();
            return $this->updatePlanService->updatePlan($data, $plan);
        });
    }
}
