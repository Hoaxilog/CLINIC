<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Recovery - Tejadent</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-lg">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Lost Access to Your Email?</h1>
        <p class="text-sm text-gray-600 mb-6">
            Submit this request, then visit the clinic for in-person identity verification before we update your email.
        </p>

        @if (session('success'))
            <div class="mb-4 rounded border border-green-200 bg-green-50 p-3 text-green-700 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded border border-red-200 bg-red-50 p-3 text-red-700 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('account.recovery.submit') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="identifier" class="block text-sm font-medium text-gray-700 mb-1">Username or Current Email (Optional)</label>
                <input id="identifier" name="identifier" type="text" value="{{ old('identifier') }}"
                    class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:border-[#0086DA]">
            </div>

            <div>
                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input id="full_name" name="full_name" type="text" value="{{ old('full_name') }}" required
                    class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:border-[#0086DA]">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                    <input id="date_of_birth" name="date_of_birth" type="date" value="{{ old('date_of_birth') }}" required
                        class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:border-[#0086DA]">
                </div>
                <div>
                    <label for="contact_number" class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                    <input id="contact_number" name="contact_number" type="text" value="{{ old('contact_number') }}" required
                        class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:border-[#0086DA]">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="last_visit_date" class="block text-sm font-medium text-gray-700 mb-1">Last Visit Date (Optional)</label>
                    <input id="last_visit_date" name="last_visit_date" type="date" value="{{ old('last_visit_date') }}"
                        class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:border-[#0086DA]">
                </div>
                <div>
                    <label for="government_id_last4" class="block text-sm font-medium text-gray-700 mb-1">Gov ID Last 4 Digits (Optional)</label>
                    <input id="government_id_last4" name="government_id_last4" type="text" value="{{ old('government_id_last4') }}"
                        class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:border-[#0086DA]">
                </div>
            </div>

            <div>
                <label for="new_email" class="block text-sm font-medium text-gray-700 mb-1">New Email Address</label>
                <input id="new_email" name="new_email" type="email" value="{{ old('new_email') }}" required
                    class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:border-[#0086DA]">
            </div>

            <div>
                <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Reason (Optional)</label>
                <textarea id="reason" name="reason" rows="3"
                    class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:border-[#0086DA]">{{ old('reason') }}</textarea>
            </div>

            <button type="submit" class="w-full bg-[#0086DA] text-white font-semibold py-3 rounded-lg hover:bg-[#0073A8] transition">
                Submit Recovery Request
            </button>
        </form>

        <a href="{{ route('login') }}" class="inline-block mt-4 text-sm text-[#0086DA] hover:underline">
            Back to login
        </a>
    </div>
</body>
</html>
