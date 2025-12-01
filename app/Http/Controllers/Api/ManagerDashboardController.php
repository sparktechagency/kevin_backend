<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GoalGererate\GoalReqeust;
use App\Service\AdminDashboard\AnalyticsService;
use App\Service\ManagerDashboard\DashbaordService;
use App\Service\ManagerDashboard\DreamMemberService;
use App\Service\ManagerDashboard\GoalGenerateService;
use App\Service\ManagerDashboard\MemberDetailsService;
use Illuminate\Http\Request;

class ManagerDashboardController extends Controller
{
    protected $dashboardService;
    protected $dreamMemberService;
    protected $analyticsService;
    protected $memberDetailsService;
    protected $goalGenerateService;
    public function __construct(
        DashbaordService $dashbaordService,
        DreamMemberService $dreamMemberService,
        AnalyticsService $analyticsService,
        MemberDetailsService $memberDetailsService,
        GoalGenerateService $goalGenerateService,
    ){
        $this->dashboardService = $dashbaordService;
        $this->analyticsService = $analyticsService;
        $this->dreamMemberService = $dreamMemberService;
        $this->memberDetailsService = $memberDetailsService;
        $this->goalGenerateService = $goalGenerateService;
    }

    public function dashboard(Request $request)
    {
        return $this->execute(function ()use($request){
            return $this->dashboardService->dashboard($request);
        });
    }
    // public function memberDetails($id)
    // {
    //     return $this->execute(function()use($id){
    //         return $this->memberDetailsService->memberDetails($id);
    //     });
    // }
    public function memberDetails($id)
    {
        return $this->execute(function()use($id){
            return $this->memberDetailsService->memberDetails($id);
        });
    }
     public function goalGenerate(GoalReqeust $goalReqeust)
    {
        return $this->execute(function()use($goalReqeust){
            $data = $goalReqeust->validated();
            return $this->goalGenerateService->goalGenerate($data);
        });
    }
     public function dreamMember(Request $request)
    {
        return $this->execute(function()use($request){
            return $this->dreamMemberService->dreamMember($request);
        });
    }
}
