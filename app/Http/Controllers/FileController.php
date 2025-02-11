<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\User;
use App\Models\VirtualRealityData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FileController extends Controller
{
    public function index(Request $request)
    {
        $files = File::with('Category')->where('user_id', auth()->id())->when($request->has('category'), function($query) use ($request) {
            $query->where('category_id', $request['category']);
        })->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully fetched files',
            'data' => $files,
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'files' => 'required',
            'files.*' => 'required|file',
            'category' => 'nullable|exists:categories,id',
        ]);
        DB::beginTransaction();
        try {
            $files = [];
            $id = auth()->id();
            foreach($request->file('files') as $file) {
                $day = Carbon::today()->day;
                $year = Carbon::today()->year;
                $month = Carbon::today()->month;
                $destinationPath = "img/Users/$id/$year/$month/$day";
                $extension = $file->getClientOriginalExtension();
                $fileName = rand(11111, 99999) . '.' . $extension;
                $fileSize = $file->getSize();
                $file->move($destinationPath, $fileName);
                $files[] = File::create([
                    'user_id' => $id,
                    'fileable_type' => User::class,
                    'fileable_id' => $id,
                    'path' => 'https://api.bms.behinstart.ir/' . $destinationPath . '/' . $fileName,
                    'extension' => $extension,
                    'size' => $fileSize,
                    'category_id' => $request->has('category') ? $request['category'] : 0,
                ]);
            }
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully uploaded files',
                'data' => $files,
            ], 200);
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error while uploading files',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(File $file)
    {
        DB::beginTransaction();
        try {
            $file->delete();
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully deleted file',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error while uploading files',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function VR(Request $request)
    {
        $request->validate([
            'files' => 'required',
            'files.*' => 'required|file',
        ]);
        DB::beginTransaction();
        try {
            $files = [];
            foreach($request->file('files') as $file) {
                $day = Carbon::today()->day;
                $year = Carbon::today()->year;
                $month = Carbon::today()->month;
                $destinationPath = "VR/$year/$month/$day";
                $extension = $file->getClientOriginalExtension();
                $fileName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                $file->move($destinationPath, $fileName);
                $files[] = File::create([
                    'user_id' => 0,
                    'fileable_type' => VirtualRealityData::class,
                    'fileable_id' => 0,
                    'path' => 'https://api.bms.behinstart.ir/' . $destinationPath . '/' . $fileName,
                    'extension' => $extension,
                    'size' => $fileSize,
                    'use_type' => 'VR',
                ]);
            }
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully uploaded files',
                'data' => $files,
            ], 200);
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error while uploading files',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }
}
