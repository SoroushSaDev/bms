<?php

namespace App\Http\Controllers;

use App\Http\Requests\TemplateRequest;
use App\Models\Template;
use App\Models\TemplateItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TemplateController extends Controller
{
    public function index()
    {
        $templates = Template::when(auth()->user()->type != 'admin', function ($query) {
            $query->where('user_id', auth()->id());
        })->get();
        return response()->json([
            'status' => 'success',
            'data' => $templates,
            'message' => 'Templates fetched successfully',
        ], 200);
    }

    public function store(TemplateRequest $request)
    {
        DB::beginTransaction();
        try {
            $template = Template::create([
                'user_id' => auth()->id(),
                'title' => $request['title'],
                'description' => $request['description'],
                'columns' => $request['columns'],
                'rows' => $request['rows'],
            ]);
            foreach ($request['devices'] as $i => $device) {
                if (!is_null($device)) {
                    TemplateItem::create([
                        'order' => $i,
                        'template_id' => $template->id,
                        'count' => $request['count'][$i],
                        'registers' => json_encode($request['registers'][$i]),
                    ]);
                }
            }
            DB::commit();
            return response()->json([
                'status' => 'success',
                'data' => $template,
                'message' => 'Template stored successfully',
            ], 200);
        } catch (\Exception $exception) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'data' => $exception->getMessage(),
                'message' => 'Error while storing template'
            ], 500);
        }
    }
}
