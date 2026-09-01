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
        $kategori = $request->input('kategori');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $lecturers = $this->dosenService->getProfilesForBlade();
        $activities = $this->activityService->getAllActivitiesPaginated(
            $search, 
            $kategori, 
            $startDate, 
            $endDate, 
            4
        );
        $allCategories = $this->activityService->countAllActivitiesByCategory();
        /* dd($allCategories); */
        return view('pages.landing', [
            'lecturers' => $lecturers,
            'activities' => $activities,
            'allCategories' => $allCategories
        ]);
    }

}