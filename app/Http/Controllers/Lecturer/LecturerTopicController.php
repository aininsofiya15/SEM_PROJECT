<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Models\Quota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LecturerTopicController extends Controller
{
    public function index()
    {
        $lecturer = Auth::guard('lecturer')->user();
        $topics = Topic::where('lecturer_id', $lecturer->id)
                      ->with('student')
                      ->latest()
                      ->get();
        
        return view('lecturer.topic.index', compact('topics'));
    }

    public function update(Request $request, Topic $topic)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'feedback' => 'required|string'
        ]);

        try {
            // MAINTENANCE FIX: Quota Validation Logic
            // Before approving, check if the lecturer has reached their maximum supervisee limit
            if ($request->status === 'approved' && $topic->status !== 'approved') {                
                $quota = Quota::where('lecturer_id', $topic->lecturer_id)->first();
                if ($quota && $quota->current_supervisees >= $quota->max_supervisees) {
                    // MAINTENANCE FIX: Return specific error key
                    return back()->withInput()->withErrors(['error' => "Limit reached! You can only supervise {$quota->max_supervisees} students."]);
                }
            }
            
        // Capture the old status before updating to check for transitions
        $oldStatus = $topic->status;

        $topic->update([
            'status' => $request->status,
            'feedback' => $request->feedback
        ]);

        // MAINTENANCE FIX: Synchronize Quota Count
        // Case 1: Topic is newly approved -> Increment
        if ($request->status === 'approved' && $oldStatus !== 'approved') {
            Quota::where('lecturer_id', $topic->lecturer_id)->increment('current_supervisees');
        }
        
        // Case 2: Topic was approved but now is rejected -> Decrement
        // This ensures the quota slot is freed up if a student is removed
        if ($request->status === 'rejected' && $oldStatus === 'approved') {
            Quota::where('lecturer_id', $topic->lecturer_id)->decrement('current_supervisees');
        }

        return back()->with('success', 'Topic ' . $request->status . ' successfully.');
    }catch (\Exception $e) {
            // MAINTENANCE FIX: Exception Handling
            // Return a friendly error message if the database update fails
            return back()->withErrors(['error' => 'Something went wrong: ' . $e->getMessage()]);        }
    }

    public function show(Topic $topic)
    {
        $topic->load('student');
        return view('lecturer.topic.show', compact('topic'));
    }
} 