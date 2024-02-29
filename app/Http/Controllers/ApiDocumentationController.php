<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use cebe\markdown\MarkdownExtra;

class ApiDocumentationController extends Controller
{
    public function index()
    {
        $baseDocumentationPath = storage_path('app/APIdocs');

        $totalAPIs = $this->getTotalAPIsCount($baseDocumentationPath);

        $folders = collect(File::directories($baseDocumentationPath))->map(function ($folder) {
            return basename($folder);
        });

        $indices = [];

        foreach ($folders as $folder) {
            $folderPath = "{$baseDocumentationPath}/{$folder}";
            $files = File::files($folderPath);

            $routeIndices = [];

            foreach ($files as $file) {
                $fileName = pathinfo($file, PATHINFO_FILENAME);
                $routeIndices[] = [
                    'route' => $fileName,
                    'link' => route('documentation.show', ['folder' => $folder, 'route' => $fileName]),
                ];
            }

            $indices[$folder] = $routeIndices;
        }

        return view('documentation.index', compact('indices', 'totalAPIs'));
    }

    public function show($folder, $route)
    {
        $documentationPath = storage_path("app/APIdocs/{$folder}/{$route}.md");
        $content = File::exists($documentationPath) ? File::get($documentationPath) : null;

        // Converti il contenuto RTF in Markdown
        $markdown = new MarkdownExtra();
        $formattedContent = $markdown->parse($content);

        return view('documentation.show', compact('formattedContent'));
    }

    private function getTotalAPIsCount($baseDocumentationPath)
    {
        $folders = File::directories($baseDocumentationPath);
        $totalAPIs = 0;

        foreach ($folders as $folder) {
            $files = File::files($folder);
            $totalAPIs += count($files);
        }

        return $totalAPIs;
    }
}
