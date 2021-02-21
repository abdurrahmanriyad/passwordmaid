<?php

namespace App\Http\Controllers\Auth;

use App\Events\PasswordChanged;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\MessageBag;

class ChangePasswordController extends Controller
{
    public function showPasswordChangeForm()
    {
        return view('auth.passwords.change');
    }

    public function changePassword(Request $request)
    {
        $data = $this->validate($request, [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed']
        ]);

        if (!$this->isAuthPassword($data['current_password'])) {
            return back()->withErrors((new MessageBag())->add('current_password', 'Incorrect existing password!'));
        }

        if ($this->updatePassword($data['password'])) {
            event(new PasswordChanged(auth()->user()));
            return back()->with(['success' => 'Successfully changed password!']);
        }
        return back()->with(['error' => 'Failed to change password!']);
    }

    public function isAuthPassword($password)
    {
        return Hash::check($password, auth()->user()->password);
    }

    public function updatePassword($newPassword)
    {
        $user = auth()->user();
        $user->password = Hash::make($newPassword);
        return $user->save();
    }
}
