<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\Register;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $registers = Register::when($request->has('device_id'), function ($query) use ($request) {
            $query->where('device_id', $request['device_id']);
        })->get();
        $registers->map(function ($register) {
            $register->Translate();
        });
        return response()->json([
            'status' => 'success',
            'message' => 'Registers fetched successfully',
            'data' => $registers,
        ], 200);
    }

    public function store(RegisterRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $register = new Register();
            $register->title = $request['title'];
            $register->device_id = $request['device_id'];
            $register->unit = $request->has('unit') ? $request['unit'] : null;
            $register->type = $request->has('type') ? $request['type'] : null;
            $register->value = $request->has('value') ? $request['value'] : null;
            $register->save();
            DB::commit();
            $register->Translate();
            return response()->json([
                'status' => 'success',
                'data' => $register,
                'message' => __('register.created')
            ], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    public function show(Register $register): JsonResponse
    {
        $register->Translate();
        return response()->json([
            'status' => 'success',
            'data' => $register,
        ], 200);
    }

    public function update(Register $register, RegisterRequest $request): JsonResponse
    {
        if($request->has('value') && $request['value'] == $register->value) {
            return response()->json([
                'status' => 'error',
                'data' => 'input value is the same as the current value',
                'message' => 'Error while updating register',
            ], 422);
        }
        DB::beginTransaction();
        try {
            $register->title = $request['title'];
            $register->device_id = $request['device_id'];
            $register->unit = $request->has('unit') ? $request['unit'] : $register->unit;
            $register->type = $request->has('type') ? $request['type'] : $register->type;
            $register->value = $request->has('value') ? $request['value'] : $request->value;
            $register->save();
            DB::commit();
            // $register->Translate();
            return response()->json([
                'status' => 'success',
                'data' => $register,
                'message' => __('register.updated'),
            ], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    public function destroy(Register $register): JsonResponse
    {
        DB::beginTransaction();
        try {
            $register->delete();
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => __('register.deleted'),
            ], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    public function test()
    {
        try {
            $temp1 = Register::firstWhere('title', 'Temp 1');
            $temp2 = Register::firstWhere('title', 'Temp 2');
            $temp3 = Register::firstWhere('title', 'Temp 3');
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully fetched test data',
                'data' => [
                    'temp1' => $temp1->value,
                    // 'temp2' => $temp2->value,
                    'temp3' => $temp3->value,
                ],
            ], 200);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching test data',
                'errors' => $e,
            ], 500);
        }
    }
}
