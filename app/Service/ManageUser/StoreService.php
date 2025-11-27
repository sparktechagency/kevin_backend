<?php

namespace App\Service\ManageUser;

use App\Mail\UserWelcomeEmail;
use App\Models\Department;
use App\Models\ManagerUser;
use App\Models\User;
use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class StoreService
{
    use ResponseHelper;

    public function store($data)
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'employee_pin' => $data['employee_pin'],
            'role'     => $data['role'],
        ]);
        if (isset($data['avatar']) && $data['avatar']->isValid()) {
            $path = $data['avatar']->store('avatars', 'public');
            $user->update(['avatar' => 'storage/' . $path]);
        }
       $manager = ManagerUser::create([
            'user_id'       => $user->id,
            'manager_id'    => Auth::id(),
            'department_id' => $data['department_id'],
            'status'        => $data['status'] ?? 'Pending',
        ]);
        if($data['send_welcome_email'] == true){
               Mail::to($manager->manager->email)->queue(new UserWelcomeEmail($manager));
        }
        return $this->successResponse([
            'message' => 'User created successfully.',
            'user'    => $user,
        ]);
    }
}
