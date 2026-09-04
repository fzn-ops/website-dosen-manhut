<?php

namespace App\Http\Controllers\Dashboard\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Service\PublicationService;
use Illuminate\Support\Facades\Process;
use Inertia\Inertia;

class PublikasiController extends Controller
{
    protected PublicationService $publicationService;

    public function __construct(PublicationService $publicationService)
    {
        $this->publicationService = $publicationService;
    }

    public function index()
    {
        $lecturers = \App\Models\User::where('role', 'dosen')
            ->with('profileDosen')
            ->withCount('publication')
            ->orderBy('name')
            ->get()
            ->map(function ($dosen) {
                return [
                    'id' => $dosen->id,
                    'name' => $dosen->name,
                    'email' => $dosen->email,
                    'profile_picture' => $dosen->profile_picture,
                    'scholar_link' => $dosen->profileDosen?->scholar_link,
                    'has_scholar' => !empty($dosen->profileDosen?->scholar_link),
                    'publications_count' => $dosen->publication_count,
                ];
            });

        return Inertia::render('Admin/publikasi', [
            'publications' => $this->publicationService->getAllPublications(),
            'lecturers' => $lecturers,
            'availableProfiles' => $lecturers,
        ]);
    }

    public function runScraper(Request $request)
    {
        $dosenIds = $request->input('dosen_ids', []);

        $arg = 'all';
        if (!empty($dosenIds) && is_array($dosenIds)) {
            $cleanIds = array_filter(array_map('intval', $dosenIds));
            if (!empty($cleanIds)) {
                $arg = implode(',', $cleanIds);
            }
        }

        $command = "python scholar_sync.py " . $arg;
        $result = Process::path(base_path('python'))->run($command);

        if ($result->successful()) {
            return response()->json([
                'success' => true,
                'message' => 'Sukses! Data publikasi berhasil ditarik dari Google Scholar.'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal sinkronisasi: ' . $result->errorOutput()
        ], 500);
    }

    public function destroyPublication($id)
    {
        $this->publicationService->destroyPublication((int) $id);
        return redirect()->back()->with('success', 'Data publikasi berhasil dihapus.');
    }

    public function destroyAllPublications(Request $request)
    {
        $type = $request->input('type', 'all'); // 'all', 'lecturers', 'years'
        $dosenIds = $request->input('dosen_ids', []);
        $years = $request->input('years', []);

        $count = $this->publicationService->deletePublicationsBatch($type, $dosenIds, $years);

        if ($count >= 0) {
            $msg = match($type) {
                'lecturers' => "Sebanyak {$count} data publikasi dari dosen terpilih berhasil dihapus.",
                'years' => "Sebanyak {$count} data publikasi dari tahun terpilih berhasil dihapus.",
                default => 'Seluruh data publikasi berhasil dikosongkan.',
            };

            return response()->json([
                'success' => true,
                'message' => $msg,
                'deleted_count' => $count,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal menghapus data publikasi.'
        ], 500);
    }
}