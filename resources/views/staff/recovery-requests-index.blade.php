<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recovery Requests - Tejadent</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 min-h-screen p-6">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Email Recovery Requests</h1>
        <p class="text-sm text-gray-600 mb-6">Approve only after in-person identity verification.</p>

        @if (session('success'))
            <div class="mb-4 rounded border border-green-200 bg-green-50 p-3 text-green-700 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 rounded border border-red-200 bg-red-50 p-3 text-red-700 text-sm">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-4 rounded border border-red-200 bg-red-50 p-3 text-red-700 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="overflow-x-auto bg-white rounded-xl shadow">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-4 py-3 text-left">Requested</th>
                        <th class="px-4 py-3 text-left">Identity Details</th>
                        <th class="px-4 py-3 text-left">Current Email</th>
                        <th class="px-4 py-3 text-left">New Email</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($requests as $req)
                        <tr class="{{ (!empty($focusId) && (int) $focusId === (int) $req->id) ? 'bg-blue-50' : '' }}">
                            <td class="px-4 py-3">{{ $req->created_at }}</td>
                            <td class="px-4 py-3">
                                <div class="text-xs text-gray-700 space-y-1">
                                    <div><span class="font-semibold">Lookup:</span> {{ $req->lookup_identifier ?? 'Not provided' }}</div>
                                    <div><span class="font-semibold">Full Name:</span> {{ $req->full_name ?? 'N/A' }}</div>
                                    <div><span class="font-semibold">DOB:</span> {{ $req->date_of_birth ?? 'N/A' }}</div>
                                    <div><span class="font-semibold">Contact:</span> {{ $req->contact_number ?? 'N/A' }}</div>
                                    <div><span class="font-semibold">Last Visit:</span> {{ $req->last_visit_date ?? 'N/A' }}</div>
                                    <div><span class="font-semibold">Gov ID Last4:</span> {{ $req->government_id_last4 ?? 'N/A' }}</div>
                                    <div><span class="font-semibold">Matched User:</span> {{ $req->target_username ?? 'Not matched' }}</div>
                                    @if (!empty($req->reason))
                                        <div><span class="font-semibold">Reason:</span> {{ $req->reason }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">{{ $req->current_email ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $req->new_email }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded text-xs font-semibold
                                    @if($req->status === 'pending') bg-yellow-100 text-yellow-800 @elseif($req->status === 'approved') bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                    {{ strtoupper($req->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 space-y-2">
                                @if ($req->status === 'pending')
                                    @if (empty($req->target_username))
                                        <form action="{{ route('recovery.link-user', $req->id) }}" method="POST" class="space-y-2">
                                            @csrf
                                            <input type="text" name="account_identifier" placeholder="Match user: username or email"
                                                class="w-64 border rounded px-2 py-1 text-xs" required>
                                            <button class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-1 rounded">
                                                Link User
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('recovery.approve', $req->id) }}" method="POST" class="space-y-2">
                                        @csrf
                                        <label class="flex items-center gap-2 text-xs text-gray-700">
                                            <input type="checkbox" name="confirm_in_person" value="1" required>
                                            In-person identity verified
                                        </label>
                                        <label class="flex items-center gap-2 text-xs text-gray-700">
                                            <input type="checkbox" name="confirm_id_document" value="1" required>
                                            Government ID checked
                                        </label>
                                        <label class="flex items-center gap-2 text-xs text-gray-700">
                                            <input type="checkbox" name="confirm_patient_record_match" value="1" required>
                                            Matches patient record details
                                        </label>
                                        <textarea name="reviewer_notes" rows="2" placeholder="Approval notes (required)"
                                            class="w-64 border rounded px-2 py-1 text-xs" required></textarea>
                                        <button class="bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-3 py-1 rounded" @if(empty($req->target_username)) disabled @endif>
                                            Approve
                                        </button>
                                    </form>
                                    @if (empty($req->target_username))
                                        <p class="text-xs text-amber-700">Link a user account first before approval.</p>
                                    @endif

                                    <form action="{{ route('recovery.reject', $req->id) }}" method="POST" class="space-y-2">
                                        @csrf
                                        <textarea name="reviewer_notes" rows="2" placeholder="Rejection reason (required)"
                                            class="w-64 border rounded px-2 py-1 text-xs" required></textarea>
                                        <button class="bg-red-600 hover:bg-red-700 text-white text-xs font-semibold px-3 py-1 rounded">
                                            Reject
                                        </button>
                                    </form>
                                @else
                                    <div class="text-xs text-gray-600">
                                        Reviewed by: {{ $req->reviewer_username ?? 'N/A' }}<br>
                                        At: {{ $req->reviewed_at ?? 'N/A' }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">No recovery requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $requests->links() }}
        </div>
    </div>
</body>
</html>
