<?php

namespace App\Http\Controllers\LandingPage;

use App\Http\Controllers\Controller;
use App\Http\Service\DosenService;
use Illuminate\Http\Request;

class LandingProfileController extends Controller
{
    //
    protected DosenService $dosenService;

    public function __construct(DosenService $dosenService)
    {
        $this->dosenService = $dosenService;
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

        return view('pages.lecturer.show', [
            'lecturer' => $lecturer
        ]);
    }
}
