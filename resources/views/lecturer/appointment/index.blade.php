@extends('layouts.lecturer')

@section('title', 'Appointment Management')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Appointment Management</h2>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($appointments as $appointment)
                <tr>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $appointment->student->name }}</div>
                        <div class="text-sm text-gray-500">{{ $appointment->student->matric_id }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $appointment->title }}</div>
                        <div class="text-sm text-gray-500">{{ Str::limit($appointment->description, 50) }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ \Carbon\Carbon::parse($appointment->date)->format('d M Y') }}<br>
                        {{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $appointment->location }}
                        @if($appointment->meeting_link)
                        <br>
                        <a href="{{ $appointment->meeting_link }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                            Meeting Link
                        </a>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($appointment->status === 'approved') bg-green-100 text-green-800
                            @elseif($appointment->status === 'rejected') bg-red-100 text-red-800
                            @elseif($appointment->status === 'completed') bg-gray-100 text-gray-600
                            @else bg-yellow-100 text-yellow-800 @endif">
                            {{ ucfirst($appointment->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium">
                        <div class="flex items-center gap-2">
                            @if($appointment->status === 'pending')
                            <button onclick="showReviewModal({{ $appointment->id }}, '{{ addslashes($appointment->title) }}')" 
                                    class="bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-md text-xs font-semibold cursor-pointer transition text-center whitespace-nowrap">
                                Review
                            </button>
                            @elseif($appointment->status === 'approved')
                            <button onclick="showCompleteModal({{ $appointment->id }})" 
                                    class="bg-green-50 text-green-700 hover:bg-green-100 px-3 py-1.5 rounded-md text-xs font-semibold cursor-pointer transition text-center whitespace-nowrap">
                                Mark Complete
                            </button>
                            @endif

                            @if($appointment->feedback)
                            <button onclick="viewFeedback('{{ addslashes($appointment->feedback) }}')" 
                                    class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-3 py-1.5 rounded-md text-xs font-semibold cursor-pointer transition text-center whitespace-nowrap">
                                View Feedback
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        No appointments scheduled yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="reviewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" style="display: none; z-index: 9999;">
        <div class="relative top-20 mx-auto p-6 border max-w-lg w-full shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium" id="reviewAppointmentTitle">Review Appointment</h3>
                <button type="button" onclick="closeModal('reviewModal')" class="text-gray-600 hover:text-gray-900 text-xl font-bold">
                    ✕
                </button>
            </div>
            
            <form id="reviewForm" method="POST" action="/lecturer/appointments">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" required id="appointmentStatus"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                            <option value="approved">Approve</option>
                            <option value="rejected">Reject</option>
                        </select>
                    </div>
                    
                    <div id="meetingLinkDiv">
                        <div class="flex justify-between items-center mb-1">
                            <label class="block text-sm font-medium text-gray-700">Meeting Link (if online)</label>

                             <button type="button" onclick="openGoogleMeet()" 
                                    class="text-sm bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1 rounded-md border border-blue-200 shadow-sm transition-all duration-200 font-semibold cursor-pointer inline-flex items-center gap-1.5">
                                🔗 Open Google Meet
                            </button>

                        </div>
                        <input type="text" name="meeting_link" id="meetingLinkInput"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                               placeholder="https://meet.google.com/...">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Feedback</label>
                        <textarea name="feedback" id="reviewFeedbackInput" rows="4" required
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                                  placeholder="Provide feedback to the student..."></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('reviewModal')"
                            class="px-4 py-2 border rounded-md text-gray-600 hover:bg-gray-50 cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 cursor-pointer">
                        Submit Review
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="completeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" style="display: none; z-index: 9999;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Complete Appointment</h3>
                <button type="button" onclick="closeModal('completeModal')" class="text-gray-600 hover:text-gray-900 text-xl font-bold">
                    ✕
                </button>
            </div>
            <form id="completeForm" method="POST" action="/lecturer/appointments">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="completed">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Feedback</label>
                        <textarea name="feedback" rows="4" required
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                                  placeholder="Provide feedback about the meeting..."></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('completeModal')"
                            class="px-4 py-2 border rounded-md text-gray-600 hover:bg-gray-50 cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 cursor-pointer">
                        Mark as Complete
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="feedbackModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" style="display: none; z-index: 9999;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Feedback</h3>
                <button type="button" onclick="closeModal('feedbackModal')" class="text-gray-600 hover:text-gray-900 text-xl font-bold">
                    ✕
                </button>
            </div>
            <div id="feedbackContent" class="text-gray-600 whitespace-pre-wrap"></div>
            <div class="mt-6 flex justify-end">
                <button type="button" onclick="closeModal('feedbackModal')"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 cursor-pointer">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>

function openGoogleMeet() {
    window.open(
        'https://meet.google.com/', 
        'GoogleMeetPopup', 
        'width=950,height=650,scrollbars=yes,resizable=yes'
    );
}

function showReviewModal(appointmentId, title) {
    console.log("Review triggered for layout entry ID: " + appointmentId);
    
    document.getElementById('reviewAppointmentTitle').textContent = "Review: " + title;
    
    const targetUrl = window.location.origin + "/lecturer/appointments/" + appointmentId;
    document.getElementById('reviewForm').action = targetUrl;
    
    console.log("Form destination enforced to: " + targetUrl);
    document.getElementById('reviewModal').style.display = 'block';
}

function showCompleteModal(appointmentId) {
    const targetUrl = window.location.origin + "/lecturer/appointments/" + appointmentId;
    document.getElementById('completeForm').action = targetUrl;
    document.getElementById('completeModal').style.display = 'block';
}

function viewFeedback(feedback) {
    document.getElementById('feedbackContent').textContent = feedback;
    document.getElementById('feedbackModal').style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

document.addEventListener("DOMContentLoaded", function() {
    const statusSelect = document.getElementById('appointmentStatus');
    if(statusSelect) {
        statusSelect.addEventListener('change', function() {
            const meetingLinkDiv = document.getElementById('meetingLinkDiv');
            if(meetingLinkDiv) {
                meetingLinkDiv.style.display = this.value === 'approved' ? 'block' : 'none';
            }
        });
    }
});
</script>
@endsection