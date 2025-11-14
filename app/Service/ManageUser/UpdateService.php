<?php

namespace App\Service\ManageUser;

use App\Models\ManagerUser;
use App\Models\User;
use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\Auth;

class UpdateService
{
    use ResponseHelper;

    public function update($data, $userId)
    {
        $user = User::findOrFail($userId);
        // Update basic user info
        $user->update([
            'name'         => $data['name'],
            'email'        => $data['email'],
            'employee_pin' => $data['employee_code'],
            'role'         => $data['role'],
        ]);

        // Update avatar if provided
        if (isset($data['avatar']) && $data['avatar']->isValid()) {
            $path = $data['avatar']->store('avatars', 'public');
            $user->update(['avatar' => 'storage/' . $path]);
        }

        // Update manager-user relationship
        $managerUser = ManagerUser::where('user_id', $userId)->first();
        if ($managerUser) {
            $managerUser->update([
                'manager_id'    => Auth::id(),
                'department_id' => $data['department_id'],
                'status'        => $data['status'],
            ]);
        }

        return $this->successResponse([
            'message' => 'User updated successfully.',
            'user'    => $user,
        ]);
    }
}
