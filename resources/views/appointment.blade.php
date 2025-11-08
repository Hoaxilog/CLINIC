@extends('index')

@section('content')
    <main id="mainContent" class="min-h-screen bg-gray-100 p-6 lg:p-8 ml-64 mt-14 transition-all duration-300 peer-[.collapsed]:ml-16">
        <div class="space-y-6">
            <section class="space-y-6">
                <div class="flex justify-between ">
                    <h1>Appointments</h1>
                    <button class="bg-[#0086DA] text-white px-6 py-2 rounded flex gap-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus-icon lucide-plus"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                        Book Appointment
                    </button>
                </div>
                <div class="flex justify-between ">
                    <div>
                        <select id="filterDate" name="filterDate"
                            class="px-8 py-2 text-center w-48 block rounded-sm border">
                            <option value="Today">Today</option>
                            <option value="Week">This Week</option>
                            <option value="Month">This Month</option>
                        </select>
                    </div>
                    <div>
                        <label for="search" class="sr-only">Search</label>
                        <input type="text" placeholder="Search..." class="border rounded-sm px-4 py-2 w-48">
                    </div>
                </div>
            </section>

            <div class="w-max px-4 bg-[#D9D9D947] py-4 space-x-4">
                <button class="bg-white border rounded-sm px-4 py-2 cursor-pointer">Upcoming()</button>
                <button class="border rounded-sm px-4 py-2 cursor-pointer">Completed()</button>
                <button class="border rounded-sm px-4 py-2 cursor-pointer">Cancelled()</button>
            </div>

            {{-- LOOP THROUGH HERE --}}
            <div class="mx-auto p-4 bg-white rounded-xl shadow-lg">
                <div class="flex justify-between items-center">
                    <div class="flex-grow">
                        <h2 class="text-3xl font-semibold text-gray-800 mb-4">
                            Jhon Stephen Nicolas
                        </h2>

                        <div class="flex items-center space-x-6 text-gray-500 text-sm">
                            <div class="flex items-center space-x-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>April 3, 2025</span>
                            </div>

                            <div class="flex items-center space-x-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>9:00 AM (60 min)</span>
                            </div>

                            <div class="flex items-center space-x-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span>Regular 6-month checkup and cleaning</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col space-y-2 ml-6">
                        <button class="px-6 py-2 text-white font-medium rounded-lg shadow-md transition duration-150 ease-in-out bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50">
                            Complete
                        </button>

                        <button class="px-6 py-2 text-white font-medium rounded-lg shadow-md transition duration-150 ease-in-out bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                            Reschedule
                        </button>

                        <button class="px-6 py-2 text-white font-medium rounded-lg shadow-md transition duration-150 ease-in-out bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
    </main>
@endsection