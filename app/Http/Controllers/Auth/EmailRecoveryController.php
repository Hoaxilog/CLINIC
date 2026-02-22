<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class EmailRecoveryController extends Controller
{
    protected $usesPatientUserId = null;

    public function showRequestForm()
    {
        return view('auth.account-recovery-request');
    }

    public function submitRequest(Request $request)
    {
        $validated = $request->validate([
            'identifier' => ['nullable', 'string', 'max:255'],
            'full_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'contact_number' => ['required', 'string', 'max:50'],
            'last_visit_date' => ['nullable', 'date'],
            'government_id_last4' => ['nullable', 'string', 'max:20'],
            'new_email' => ['required', 'email', 'max:255'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $identifier = !empty($validated['identifier'])
            ? Str::lower(trim($validated['identifier']))
            : null;

        $user = null;
        if ($identifier) {
            $user = DB::table('users')
                ->whereRaw('LOWER(username) = ?', [$identifier])
                ->orWhereRaw('LOWER(email) = ?', [$identifier])
                ->first();
        }

        $existingPending = DB::table('account_recovery_requests')
            ->where(function ($q) use ($user, $identifier, $validated) {
                if ($user?->id) {
                    $q->where('user_id', $user->id);
                } elseif ($identifier) {
                    $q->where('lookup_identifier', $identifier);
                } else {
                    $q->where('full_name', $validated['full_name'])
                      ->where('date_of_birth', $validated['date_of_birth']);
                }
            })
            ->where('status', 'pending')
            ->exists();

        if (!$existingPending) {
            DB::table('account_recovery_requests')->insert([
                'user_id' => $user?->id,
                'lookup_identifier' => $identifier,
                'full_name' => $validated['full_name'],
                'date_of_birth' => $validated['date_of_birth'],
                'contact_number' => $validated['contact_number'],
                'last_visit_date' => $validated['last_visit_date'] ?? null,
                'government_id_last4' => $validated['government_id_last4'] ?? null,
                'new_email' => Str::lower(trim($validated['new_email'])),
                'reason' => $validated['reason'] ?? null,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'Recovery request submitted. Please visit the clinic for in-person identity verification.');
    }

    public function index(Request $request)
    {
        $focusId = $request->query('focus');

        $query = DB::table('account_recovery_requests as arr')
            ->leftJoin('users as target', 'arr.user_id', '=', 'target.id')
            ->leftJoin('users as reviewer', 'arr.reviewed_by', '=', 'reviewer.id')
            ->select(
                'arr.*',
                'target.username as target_username',
                'target.email as current_email',
                'reviewer.username as reviewer_username'
            );

        if ($focusId) {
            $query->where('arr.id', $focusId);
        } else {
            $query->orderByRaw("CASE WHEN arr.status = 'pending' THEN 0 ELSE 1 END")
                ->orderBy('arr.created_at', 'desc');
        }

        $requests = $query->paginate(20)->withQueryString();

        return view('staff.recovery-requests-index', compact('requests', 'focusId'));
    }

    public function approve(Request $request, $id)
    {
        $validated = $request->validate([
            'confirm_in_person' => ['required', 'accepted'],
            'confirm_id_document' => ['required', 'accepted'],
            'confirm_patient_record_match' => ['required', 'accepted'],
            'reviewer_notes' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'confirm_in_person.accepted' => 'In-person identity verification must be confirmed before approval.',
            'confirm_id_document.accepted' => 'Government ID check must be confirmed before approval.',
            'confirm_patient_record_match.accepted' => 'Patient record match must be confirmed before approval.',
        ]);

        $actorId = auth()->id();
        $usesPatientUserId = $this->patientsUsesUserId();

        [$recoveryRequest, $newToken] = DB::transaction(function () use ($id, $actorId, $validated, $usesPatientUserId) {
            $recoveryRequest = DB::table('account_recovery_requests')
                ->where('id', $id)
                ->lockForUpdate()
                ->first();

            if (!$recoveryRequest || $recoveryRequest->status !== 'pending') {
                return [null, null];
            }

            if (!$recoveryRequest->user_id) {
                return [false, null];
            }

            $currentUser = DB::table('users')
                ->where('id', $recoveryRequest->user_id)
                ->lockForUpdate()
                ->first();

            $oldEmail = $currentUser?->email;

            $emailInUse = DB::table('users')
                ->where('email', $recoveryRequest->new_email)
                ->where('id', '!=', $recoveryRequest->user_id)
                ->exists();

            if ($emailInUse) {
                return [true, null];
            }

            $newToken = Str::random(64);

            DB::table('users')
                ->where('id', $recoveryRequest->user_id)
                ->update([
                    'email' => $recoveryRequest->new_email,
                    'email_verified_at' => null,
                    'verification_token' => $newToken,
                    'updated_at' => now(),
                ]);

            if ($usesPatientUserId) {
                DB::table('patients')
                    ->where('user_id', $recoveryRequest->user_id)
                    ->update([
                        'email_address' => $recoveryRequest->new_email,
                        'updated_at' => now(),
                    ]);
            } elseif (!empty($oldEmail)) {
                DB::table('patients')
                    ->where('email_address', $oldEmail)
                    ->update([
                        'email_address' => $recoveryRequest->new_email,
                        'updated_at' => now(),
                    ]);
            }

            DB::table('account_recovery_requests')
                ->where('id', $id)
                ->update([
                    'status' => 'approved',
                    'identity_verified_at' => now(),
                    'reviewed_by' => $actorId,
                    'reviewed_at' => now(),
                    'reviewer_notes' => $validated['reviewer_notes'] ?? null,
                    'updated_at' => now(),
                ]);

            return [$recoveryRequest, $newToken];
        });

        if ($recoveryRequest === null) {
            return back()->with('error', 'Request not found or already processed.');
        }

        if ($recoveryRequest === false) {
            return back()->with('error', 'This request is not linked to any user account yet. Find the user first, then update the request link in database before approval.');
        }

        if ($newToken === null) {
            return back()->with('error', 'New email is already used by another account.');
        }

        Mail::send('auth.emails.verify-email', [
            'token' => $newToken,
            'id' => $recoveryRequest->user_id,
            'name' => 'Patient',
        ], function ($message) use ($recoveryRequest) {
            $message->to($recoveryRequest->new_email);
            $message->subject('Verify Your New Email Address - Tejadent');
        });

        return back()->with('success', 'Recovery approved. Verification link sent to the new email address.');
    }

    public function linkUser(Request $request, $id)
    {
        $validated = $request->validate([
            'account_identifier' => ['required', 'string', 'max:255'],
        ]);

        $identifier = Str::lower(trim($validated['account_identifier']));

        $user = DB::table('users')
            ->whereRaw('LOWER(username) = ?', [$identifier])
            ->orWhereRaw('LOWER(email) = ?', [$identifier])
            ->first();

        if (!$user) {
            return back()->with('error', 'No user account found for that username/email.');
        }

        $updated = DB::table('account_recovery_requests')
            ->where('id', $id)
            ->where('status', 'pending')
            ->update([
                'user_id' => $user->id,
                'lookup_identifier' => $identifier,
                'updated_at' => now(),
            ]);

        if (!$updated) {
            return back()->with('error', 'Request not found or already processed.');
        }

        return back()->with('success', 'Recovery request linked to user account successfully.');
    }

    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'reviewer_notes' => ['required', 'string', 'max:1000'],
        ]);

        $updated = DB::table('account_recovery_requests')
            ->where('id', $id)
            ->where('status', 'pending')
            ->update([
                'status' => 'rejected',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'reviewer_notes' => $validated['reviewer_notes'],
                'updated_at' => now(),
            ]);

        if (!$updated) {
            return back()->with('error', 'Request not found or already processed.');
        }

        return back()->with('success', 'Recovery request rejected.');
    }

    protected function patientsUsesUserId(): bool
    {
        if ($this->usesPatientUserId !== null) {
            return $this->usesPatientUserId;
        }

        try {
            $this->usesPatientUserId = Schema::hasColumn('patients', 'user_id');
        } catch (Throwable $e) {
            $this->usesPatientUserId = false;
        }

        return $this->usesPatientUserId;
    }
}
