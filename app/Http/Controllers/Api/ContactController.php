<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Customer $customer): JsonResponse
    {
        $contacts = $customer->contacts()->latest()->get();

        return response()->json(ContactResource::collection($contacts));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactRequest $request): JsonResponse
    {
        $contact = Contact::create($request->validated());
        $contact->load('customer');

        return response()->json(new ContactResource($contact), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $contact = Contact::with('customer')->findOrFail($id);

        return response()->json(new ContactResource($contact));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContactRequest $request, string $id): JsonResponse
    {
        $contact = Contact::findOrFail($id);
        $contact->update($request->validated());
        $contact->load('customer');

        return response()->json(new ContactResource($contact));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return response()->json(['message' => 'Contact deleted successfully']);
    }
}
