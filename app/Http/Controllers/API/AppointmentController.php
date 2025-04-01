<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    // View all appointments (Admin & doctors only)
    public function index() {
        if (Auth::user()->role === 'Admin' || Auth::user()->role === 'Doctor') {
            $appointments = Appointment::all();
            return response()->json($appointments, 200);
        }

        return response()->json([
            'message' => 'Unauthorized'
        ], 403);
    }

    public function userAppointments() {
        $appointments = Appointment::where('patient_id', Auth::id())->get();
        return response()->json($appointments, 200);
    }

    public function show($id) {
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json([
                'message'=> 'Appointment not found'
            ], 404);
        }

        // Allow user to view only their own appointments
        if ($appointment->patient_id !== Auth::id() && Auth::user()->role !== 'Admin' && Auth::user()->role !== 'Doctor') {
            return response()->json([
                'message'=> 'Unauthorized'
            ], 403);
        }

        return response()->json($appointment, 200);
    }

    // create new appointment
    public function store(Request $request) {
        $rules = [
            'date'=> 'required|date_format:Y-m-d H:i:s',
            'doctor_id'=> 'required|exists:users,id'
        ];

        $validatedData = $request->validate($rules);

        $validatedData['patient_id'] = Auth::id();
        $validatedData['status'] = 'Pending';

        $appointment = Appointment::create($validatedData);

        return response()->json([
            'message' => 'Appointment created successfully',
            'appointment' => $appointment
        ], 201);
    }

    // Update appointment details, can be done by admin only
    public function update(Request $request, $id) {
        $appointment = Appointment::find($id);

        if(!$appointment) {
            return response()->json([
                'message'=> 'Appointment not found'
            ], 404);
        }

        if (Auth::user()->role !== 'Admin') {
            return response()->json([
                'message'=> 'Unauthorized'
            ], 403);
        }

        $rules = [
            'date' => 'sometimes|date_format:Y-m-d H:i:s',
            'status' => 'sometimes|in:Pending,Confirmed,Completed,Canceled',
            'doctor_id' => 'sometimes|exists:users,id'
        ];

        $validatedData = $request->validate($rules);
        $appointment->update($validatedData);

        return response()->json([
            'message'=> 'Appointment details updated successfully',
            'appointment'=> $appointment
        ], 200);
    }

    // Delete appointment, can be done by appointment concerned patient and admin only
    public function destroy($id) {
        $appointment = Appointment::find($id);

        if(!$appointment) {
            return response()->json([
                'message'=> 'Appointment not found'
            ], 404);
        }

        if($appointment->patient_id !== Auth::id() && Auth::user()->role !== 'Admin') {
            return response()->json([
                'message'=> 'Unauthorized'
            ], 403);
        }

        $appointment->delete();

        return response()->json([
            'message'=> 'Appointment deleted successfully'
        ], 200);
    }
}
