<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Http\Service\ActivityService;
use App\Models\Activity;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    protected ActivityService $activityService;

    public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

    /**
     * Menampilkan halaman Admin Dashboard dengan data statistik dan 5 aktivitas terbaru.
     */
    public function index(): Response
    {
        $allActivities = $this->activityService->getAllActivitiesForAdmin();
        $recentActivities = array_slice($allActivities, 0, 5);
        $availableProfiles = $this->activityService->getAvailableProfiles();

        // 1. Siapkan data chart statistik aktivitas 4 tahun kebelakang
        $currentYear = (int) date('Y');
        $years = [
            (string)($currentYear - 3),
            (string)($currentYear - 2),
            (string)($currentYear - 1),
            (string)$currentYear,
        ];

        $categories = [
            ['name' => 'Seminar', 'color' => '#7c72ff'],
            ['name' => 'Lokakarya', 'color' => '#ff8b85'],
            ['name' => 'Workshop', 'color' => '#56d4f8'],
            ['name' => 'Lainnya', 'color' => '#ffbb66'],
        ];

        $activitiesCollection = Activity::all();

        $chartSeries = [];
        foreach ($categories as $cat) {
            $values = [];
            foreach ($years as $yr) {
                $count = $activitiesCollection->filter(function ($act) use ($cat, $yr) {
                    $date = $act->activity_date_start ?? $act->created_at;
                    if (!$date) return false;
                    $actYear = Carbon::parse($date)->format('Y');
                    if ($actYear !== $yr) return false;

                    $types = is_array($act->activity_type)
                        ? $act->activity_type
                        : (json_decode($act->activity_type, true) ?? []);

                    if ($cat['name'] === 'Lainnya') {
                        $known = ['Seminar', 'Lokakarya', 'Workshop'];
                        return in_array('Lainnya', $types) || count(array_diff($types, $known)) > 0 || count($types) === 0;
                    }

                    return in_array($cat['name'], $types);
                })->count();

                $values[] = $count;
            }

            $chartSeries[] = [
                'name' => $cat['name'],
                'color' => $cat['color'],
                'values' => $values,
            ];
        }

        return Inertia::render('Admin/dashboard', [
            'activities' => $recentActivities,
            'availableProfiles' => $availableProfiles,
            'years' => $years,
            'chartSeries' => $chartSeries,
        ]);
    }
}
