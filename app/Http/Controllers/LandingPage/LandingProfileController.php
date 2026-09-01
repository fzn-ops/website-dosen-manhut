<?php

namespace App\Http\Controllers\LandingPage;

use App\Http\Controllers\Controller;
use App\Http\Service\DosenService;
use App\Http\Service\ActivityService;
use App\Http\Service\PublicationService;
use Illuminate\Http\Request;

class LandingProfileController extends Controller
{
    //
    protected DosenService $dosenService;
    protected ActivityService $activityService;
    protected PublicationService $publicationService;

    public function __construct(DosenService $dosenService, ActivityService $activityService, PublicationService $publicationService)
    {
        $this->dosenService = $dosenService;
        $this->activityService = $activityService;
        $this->publicationService = $publicationService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $lecturers = $this->dosenService->getProfilesForBlade();

        return view('pages.lecturer.lecturers', [
            'lecturers' => $lecturers
        ]);
    }

    public function show($id)
    {
        $lecturer = $this->dosenService->getProfileById($id);
        $activities = $this->activityService->getActivitiesByUserIdPaginated($lecturer->user_id);
        $publications = $this->publicationService->getPublicationsByUserId($lecturer->user_id);

        return view('pages.lecturer.show', [
            'lecturer' => $lecturer,
            'activities' => $activities,
            'publications' => $publications
        ]);
    }
}
