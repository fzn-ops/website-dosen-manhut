<?php

namespace App\Http\Controllers\LandingPage;

use App\Http\Controllers\Controller;
use App\Http\Service\ActivityService;
use Illuminate\Http\Request;
use App\Models\User;

class ActivityController extends Controller
{
    protected ActivityService $activityService;

    public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

    public function index(Request $request)
    {
        // Tangkap semua input dari URL
        $search = $request->input('search');
        $kategori = $request->input('kategori');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
    
        // Lempar ke Service secara berurutan
        $activities = $this->activityService->getAllActivitiesPaginated(
            $search, 
            $kategori, 
            $startDate, 
            $endDate, 
            9
        );
    
        return view('pages.activity.activities', [
            'activities' => $activities
        ]);
    }

    public function show($id)
    {
        $activity = $this->activityService->getActivityById($id);

        $relatedActivities = $this->activityService->getRandomActivities(
            $activity->id
        );

        return view('pages.activity.show', [
            'activity'          => $activity,
            'relatedActivities' => $relatedActivities
        ]);
    }
}
