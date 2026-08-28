<?php

namespace App\Http\Controllers\LandingPage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Service\DosenService;
use App\Http\Service\ActivityService;

class LandingController extends Controller
{
    //
    protected DosenService $dosenService;
    protected ActivityService $activityService;
    public function __construct(DosenService $dosenService, ActivityService $activityService)
    {
        $this->dosenService = $dosenService;
        $this->activityService = $activityService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $lecturers = $this->dosenService->getProfilesForBlade();
        $activities = $this->activityService->getAllActivitiesPaginated($search, 4);

        return view('pages.landing', [
            'lecturers' => $lecturers,
            'activities' => $activities
        ]);
    }

}