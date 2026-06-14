@extends('layouts.lecturer')

@section('title', 'Topic Management')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Topic Management</h2>
    </div>

    {{-- MAINTENANCE FIX: Display Quota Limit Errors --}}
    @if($errors->has('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 shadow-sm" role="alert">
        <strong class="font-bold">Limit Reached!</strong>
        <span class="block sm:inline">{{ $errors->first('error') }}</span>
    </div>
    @endif

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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Research Area</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($topics as $topic)
                <tr>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $topic->student->name }}</div>
                        <div class="text-sm text-gray-500">{{ $topic->student->matric_id }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $topic->title }}</div>
                        <div class="text-sm text-gray-500">{{ \Illuminate\Support\Str::limit($topic->description, 50) }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $topic->research_area }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($topic->status === 'approved') bg-green-100 text-green-800
                            @elseif($topic->status === 'rejected') bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800 @endif">
                            {{ ucfirst($topic->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium">
                        @if($topic->status === 'pending')
                            <button type="button" onclick="openReviewModal('{{ $topic->id }}', '{{ addslashes($topic->title) }}')" 
                                    class="text-blue-600 hover:text-blue-900 cursor-pointer">
                                Review
                            </button>
                        @else
                            <button type="button" onclick="openFeedbackModal('{{ addslashes($topic->feedback) }}')" 
                                    class="text-gray-600 hover:text-gray-900 cursor-pointer">
                                View Feedback
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                        No topics submitted yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="reviewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 overflow-y-auto h-full w-full flex items-center justify-center">
        <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="reviewTopicTitle">Review Topic</h3>
                <button type="button" onclick="closeAllModals()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="reviewForm" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="approved">Approve</option>
                            <option value="rejected">Reject</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Feedback</label>
                        <textarea name="feedback" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Provide feedback..."></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="closeAllModals()" class="px-4 py-2 border rounded-md text-gray-600 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Submit Review</button>
                </div>
            </form>
        </div>
    </div>

    <div id="feedbackModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 overflow-y-auto h-full w-full flex items-center justify-center">
        <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Feedback</h3>
                <button type="button" onclick="closeAllModals()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="feedbackContent" class="text-gray-600 text-sm py-2"></div>
            <div class="mt-6 flex justify-end">
                <button type="button" onclick="closeAllModals()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Script placed directly inside @section to avoid @push errors --}}
<script>
    function openReviewModal(topicId, title) {
        const modal = document.getElementById('reviewModal');
        const form = document.getElementById('reviewForm');
        const titleText = document.getElementById('reviewTopicTitle');
        
        if (modal && form) {
            titleText.innerText = title;
            form.action = '/lecturer/topics/' + topicId;
            modal.classList.remove('hidden');
        }
    }

    function openFeedbackModal(feedback) {
        const modal = document.getElementById('feedbackModal');
        const content = document.getElementById('feedbackContent');
        if (modal && content) {
            content.innerText = feedback || "No feedback available.";
            modal.classList.remove('hidden');
        }
    }

    function closeAllModals() {
        document.getElementById('reviewModal').classList.add('hidden');
        document.getElementById('feedbackModal').classList.add('hidden');
    }

    // Close when clicking background
    window.onclick = function(event) {
        if (event.target.id === 'reviewModal' || event.target.id === 'feedbackModal') {
            closeAllModals();
        }
    }
</script>
@endsection