<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\ProfileService;
use Exception;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Show the authenticated user's profile.
     */
    public function show(Request $request)
    {
        return view('profile.index', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Show the edit form.
     */
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Persist profile changes.
     */
    public function update(UpdateProfileRequest $request)
    {
        try {
            $service = new ProfileService();
            $service->updateProfile($request);
            return redirect()->route('profile.show')->with('status', __('profile.flash.profile_updated'));
        } catch (Exception $exception) {
            return back()
                ->withErrors(['general' => __('profile.flash.general_error')])
                ->withInput();
        }
    }

    public function editPassword()
    {
        return view('profile.password.edit');
    }

    public function updatePassword(ProfileUpdatePasswordRequest $request)
    {
        try {
            $service = new ProfileService();
            $service->updatePassword($request->get('password'));
            return redirect()->route('profile.show')->with('status', __('profile.flash.profile_updated'));
        } catch (Exception $exception) {
            return back()
                ->withErrors(['general' => __('profile.flash.general_error')])
                ->withInput();
        }
    }

}
