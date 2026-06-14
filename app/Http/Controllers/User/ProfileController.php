<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Models\User;
use App\Services\FileManager\FileManagerUsageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private readonly FileManagerUsageService $usageService,
    ) {
    }

    public function edit(): View
    {
        /** @var User $user */
        $user = auth()->user();

        return view('backend.pages.profile.edit', [
            'user' => $user->loadMissing(['roles:id,name,slug', 'userType:id,name']),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $oldProfileImagePath = $user->profile_image_path;
        $validated = $request->validated();
        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'profile_image_path' => $validated['profile_image_path'] ?? null,
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = $validated['password'];
        }

        $user->update($updateData);
        $user = $user->refresh();

        if ($oldProfileImagePath && $oldProfileImagePath !== $user->profile_image_path) {
            $this->usageService->forget($oldProfileImagePath, [
                'module' => 'user-profile',
                'owner_type' => User::class,
                'owner_id' => (string) $user->id,
                'field_name' => 'profile_image_path',
            ]);
        }

        if ($user->profile_image_path) {
            $this->usageService->track([
                ['path' => $user->profile_image_path],
            ], [
                'module' => 'user-profile',
                'owner_type' => User::class,
                'owner_id' => (string) $user->id,
                'field_name' => 'profile_image_path',
                'label' => $user->name.' profile image',
            ]);
        }

        return redirect()
            ->route('backend.profile.edit')
            ->with('success', 'Profile updated successfully.');
    }
}
