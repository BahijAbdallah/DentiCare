<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MedicalRecord;

class MedicalRecordController extends Controller
{
    public function index($patient_id) {
        $user = Auth::user();

        if($user->role !== 'Admin' && $user->role !== 'Doctor' && $user->id !== (int) $patient_id) {
            return response()->json([
                'message'=> 'Unauthorized'
            ], 403);
        }

        $records = MedicalRecord::where('patient_id', $patient_id)->get();
        return response()->json($records, 200);
    }

    public function show($id) {
        $record = MedicalRecord::find($id);

        if (!$record) {
            return response()->json(['message' => 'Medical record not found'], 404);
        }

        $user = Auth::user();
        if ($user->role !== 'Admin' && $user->role !== 'Doctor' && $user->id !== $record->patient_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($record, 200);
    }

    public function store(Request $request) {
        $user = Auth::user();
        if($user->role !== 'Admin') {
            return response()->json(['message'=> 'Unauthorized'], 403);
        }

        $rules = [
            'type' => 'required|in:X-ray,Prescription,Lab Report',
            'url' => 'required|string|max:255',
            'patient_id' => 'required|exists:users,id'
        ];

        $validatedData = $request->validate($rules);
        $validatedData['uploaded_by'] = $user->id;

        $record = MedicalRecord::create($validatedData);

        return response()->json([
            'message' => 'Medical record created successfully',
            'record' => $record
        ], 201);
    }

    public function update(Request $request, $id) {
        $record = MedicalRecord::find($id);

        if (!$record) {
            return response()->json(['message' => 'Medical record not found'], 404);
        }

        if (Auth::user()->role !== 'Admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $rules = [
            'type' => 'sometimes|in:X-ray,Prescription,Lab Report',
            'url' => 'sometimes|string|max:255',
        ];

        $validatedData = $request->validate($rules);
        $record->update($validatedData);

        return response()->json([
            'message' => 'Medical record updated successfully',
            'record' => $record
        ], 200);
    }

    public function destroy($id) {
        $record = MedicalRecord::find($id);

        if (!$record) {
            return response()->json(['message' => 'Medical record not found'], 404);
        }

        $user = Auth::user();
        if ($user->role !== 'Admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $record->delete();
        return response()->json(['message' => 'Medical record deleted successfully'], 200);
    }

}
