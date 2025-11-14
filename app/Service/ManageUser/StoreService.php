<?php

namespace App\Service\ManageUser;

use App\Models\Department;
use App\Models\ManagerUser;
use App\Models\User;
use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class StoreService
{
    use ResponseHelper;

    public function store($data)
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'employee_pin' => $data['employee_code'],
            'role'     => $data['role'],
        ]);
        if (isset($data['avatar']) && $data['avatar']->isValid()) {
            $path = $data['avatar']->store('avatars', 'public');
            $user->update(['avatar' => 'storage/' . $path]);
        }
        ManagerUser::create([
            'user_id'       => $user->id,
            'manager_id'    => Auth::id(),
            'department_id' => $data['department_id'],
            'status'        => $data['status'] ?? 'Pending',
        ]);
        return $this->successResponse([
            'message' => 'User created successfully.',
            'user'    => $user,
        ]);
    }
}
