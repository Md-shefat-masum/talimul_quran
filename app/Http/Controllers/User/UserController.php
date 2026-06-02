<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\Services\UserService;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
    ) {
    }

    public function index(): View
    {
        return view('backend.pages.users.index');
    }

    public function data(Request $request): JsonResponse
    {
        return response()->json($this->userService->getDataTableData($request));
    }

    public function create(): View
    {
        return view('backend.pages.users.create', [
            'user' => null,
        ]);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->createUser($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'User created successfully.',
            'data' => ['id' => $user->id],
        ], 201);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->userService->findUser($user),
        ]);
    }

    public function edit(User $user): View
    {
        return view('backend.pages.users.edit', [
            'user' => $user->loadMissing('userType:id,name'),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $updatedUser = $this->userService->updateUser($user, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'data' => ['id' => $updatedUser->id],
        ]);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        $this->userService->deleteUser($user, $request->user()?->id);

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
        ]);
    }

    public function userTypeOptions(Request $request): JsonResponse
    {
        return response()->json($this->userService->getUserTypeOptions(
            (string) $request->input('q', ''),
            (int) $request->input('page', 1),
        ));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        return $this->userService->exportUsersCsv([
            'search' => $request->input('search', ''),
            'status' => $request->input('status'),
            'user_type_id' => $request->input('user_type_id'),
        ]);
    }
}
