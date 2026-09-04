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
        return Inertia::render('Admin/publikasi', [
            'publications'=> $this->publicationService->getAllPublications(),
        ]);
    }
    public function runScraper()
    {
        $result = Process::path(base_path('python'))->run("python scholar_sync.py");

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

    public function destroyAllPublications()
    {
        $deleted = $this->publicationService->destroyAllPublications();

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Semua publikasi berhasil dihapus.'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal menghapus publikasi.'
        ], 500);
    }
}