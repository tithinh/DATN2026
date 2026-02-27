<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Submit contact form (public)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string'
        ]);

        $contact = Contact::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'subject' => $validated['subject'] ?? null,
            'content' => $validated['content'],
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Gửi liên hệ thành công! Chúng tôi sẽ phản hồi sớm nhất có thể.',
            'contact' => $contact
        ], 201);
    }

    // ======================
    // ADMIN ROUTES
    // ======================

    /**
     * Get all contacts for admin
     */
    public function adminIndex(Request $request)
    {
        $query = Contact::orderBy('created_at', 'desc');

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $perPage = $request->per_page ?? 15;
        $contacts = $query->paginate($perPage);

        return response()->json($contacts);
    }

    /**
     * Get single contact for admin
     */
    public function adminShow($id)
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return response()->json(['message' => 'Không tìm thấy liên hệ'], 404);
        }

        return response()->json($contact);
    }

    /**
     * Update contact status
     */
    public function updateStatus(Request $request, $id)
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return response()->json(['message' => 'Không tìm thấy liên hệ'], 404);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,replied,spam'
        ]);

        $contact->status = $validated['status'];
        $contact->save();

        return response()->json($contact);
    }

    /**
     * Delete contact
     */
    public function destroy($id)
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return response()->json(['message' => 'Không tìm thấy liên hệ'], 404);
        }

        $contact->delete();

        return response()->json(['message' => 'Xóa liên hệ thành công']);
    }

    /**
     * Mark as spam
     */
    public function markAsSpam($id)
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return response()->json(['message' => 'Không tìm thấy liên hệ'], 404);
        }

        $contact->status = 'spam';
        $contact->save();

        return response()->json($contact);
    }
}
