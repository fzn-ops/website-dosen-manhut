<?php

namespace App\Http\Controllers\Dashboard\Dosen;

use App\Http\Controllers\Controller;
use App\Http\Service\ActivityService;
use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DosenDashboardController extends Controller
{
    protected ActivityService $activityService;

    public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

    /**
     * Menampilkan halaman Dashboard Dosen dengan data statistik, chart, dan 5 aktivitas terbaru.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $user->load('profileDosen');
        $hasProfile = !empty($user->profileDosen);

        $currentLecturer = [
            'name' => $user->name,
            'nip' => $user->NIP ?? '-',
            'division' => $user->profileDosen?->division ?? '-',
            'email' => $user->email,
        ];

        // 1. Ambil 5 aktivitas terbaru milik dosen yang sedang login
        $allActivities = $this->activityService->getActivitiesForLecturer($user->id);
        $recentActivities = array_slice($allActivities, 0, 5);

        // 2. Data Card Statistik (Seminar, Lokakarya, Workshop, Lainnya, Total Aktivitas)
        $userActivitiesCollection = Activity::where('user_id', $user->id)->get();
        $totalActivities = $userActivitiesCollection->count();

        $filterByCategory = function ($categoryName) use ($userActivitiesCollection) {
            return $userActivitiesCollection->filter(function ($act) use ($categoryName) {
                $types = is_array($act->activity_type)
                    ? $act->activity_type
                    : (json_decode($act->activity_type, true) ?? []);

                if ($categoryName === 'Lainnya') {
                    $known = ['Seminar', 'Lokakarya', 'Workshop'];
                    return in_array('Lainnya', $types) || count(array_diff($types, $known)) > 0 || count($types) === 0;
                }

                return in_array($categoryName, $types);
            });
        };

        $seminarActivities = $filterByCategory('Seminar');
        $seminarCount = $seminarActivities->count();
        $seminarPemateri = $seminarActivities->filter(function ($a) {
            $role = strtolower($a->job ?? '');
            return str_contains($role, 'pemateri') || str_contains($role, 'narasumber') || str_contains($role, 'speaker');
        })->count();
        $seminarDetail = $seminarPemateri > 0 ? "{$seminarPemateri} sebagai pemateri" : "{$seminarCount} kegiatan seminar";

        $lokakaryaActivities = $filterByCategory('Lokakarya');
        $lokakaryaCount = $lokakaryaActivities->count();
        $lokakaryaDetail = "{$lokakaryaCount} kegiatan lokakarya";

        $workshopActivities = $filterByCategory('Workshop');
        $workshopCount = $workshopActivities->count();
        $workshopDetail = "{$workshopCount} kegiatan workshop";

        $lainnyaActivities = $filterByCategory('Lainnya');
        $lainnyaCount = $lainnyaActivities->count();
        $lainnyaDetail = "{$lainnyaCount} kegiatan penunjang";

        $stats = [
            [
                'label' => 'Seminar',
                'value' => (string)$seminarCount,
                'color' => 'bg-[#7c72ff]',
                'isTotal' => false,
            ],
            [
                'label' => 'Lokakarya',
                'value' => (string)$lokakaryaCount,
                'color' => 'bg-[#ff8b85]',
                'isTotal' => false,
            ],
            [
                'label' => 'Workshop',
                'value' => (string)$workshopCount,
                'color' => 'bg-[#56d4f8]',
                'isTotal' => false,
            ],
            [
                'label' => 'Lainnya',
                'value' => (string)$lainnyaCount,
                'color' => 'bg-[#ffbb66]',
                'isTotal' => false,
            ],
            [
                'label' => 'Total Aktivitas',
                'value' => (string)$totalActivities,
                'color' => 'bg-[#183669]',
                'isTotal' => true,
            ],
        ];

        // 3. Data Chart Statistik 4 Tahun Kebelakang
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

        $chartSeries = [];
        foreach ($categories as $cat) {
            $values = [];
            foreach ($years as $yr) {
                $count = $userActivitiesCollection->filter(function ($act) use ($cat, $yr) {
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

        return Inertia::render('Dosen/dashboard', [
            'currentLecturer' => $currentLecturer,
            'hasProfile' => $hasProfile,
            'stats' => $stats,
            'years' => $years,
            'chartSeries' => $chartSeries,
            'activities' => $recentActivities,
        ]);
    }
}
