<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class FileController extends Controller
{
    public function index()
    {

        return Inertia::render('File/Page');
    }
    public function detail($table_name)
    {

        return Inertia::render('File/Page', ['table_name' => $table_name]);
    }

    public function download($id)
    {
        // Find the record in the History model
        $history = File::findOrFail($id);

        // Get the file path
        $filePath = storage_path("app/{$history->path}");

        // Check if the file exists
        if (file_exists($filePath)) {
            // Provide a suitable filename for download
            $filename = pathinfo($filePath, PATHINFO_BASENAME);

            // Return the file as a response for download
            return response()->download($filePath, $filename);
        } else {
            // Handle the case where the file does not exist
            return response()->json(['error' => 'File not found'], 404);
        }
    }

    public function delete($id)
    {
        $file = File::findOrFail($id);

        $filePath = storage_path("app/{$file->path}");

        // Check if the file exists and delete it
        if (file_exists($filePath)) {
            unlink($filePath); // Delete the file
        }

        $file->delete();

        return Redirect::back()->with(['status' => 'success', 'message' => 'File berhasil dihapus']);
    }
}
