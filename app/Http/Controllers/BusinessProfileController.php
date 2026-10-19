<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateBusinessProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BusinessProfileController extends Controller
{
    public function edit(): View
    {
        $profile = auth()->user()->businessProfile;

        return view('business-profile.edit', compact('profile'));
    }

    public function update(
        UpdateBusinessProfileRequest $request
    ): RedirectResponse {
        $user = $request->user();

        $user->businessProfile()->updateOrCreate(
            [],
            $request->validated()
        );

        return redirect()
            ->route('business-profile.edit')
            ->with('success', 'Business profile saved successfully.');
    }
}
