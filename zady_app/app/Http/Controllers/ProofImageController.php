<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class ProofImageController extends Controller
{
    /**
     * Securely stream a payment proof image.
     * 
     * Authorization rules (PRD §7):
     * - Admin / Secretary can view any proof.
     * - Parent can only view proofs for their own children's payments.
     */
    public function show($payment_code)
    {
        $payment = Payment::where('payment_code', $payment_code)->firstOrFail();

        // Check if user is Admin or Secretary
        if (in_array(auth()->user()->role, ['admin', 'secretary'])) {
            return $this->serveFile($payment->proof_image);
        }

        // Check if user is the Parent of the student in this payment
        if (auth()->user()->role === 'parent' && $payment->subscription->student->parent_id === auth()->id()) {
            return $this->serveFile($payment->proof_image);
        }

        abort(403, 'غير مصرح لك بمشاهدة هذا الملف.');
    }

    private function serveFile($path)
    {
        if (!$path || !Storage::exists($path)) {
            // Check if it's in public storage (for seeded data)
            if (Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->response($path);
            }
            abort(404, 'الملف غير موجود.');
        }

        return Storage::response($path);
    }
}
