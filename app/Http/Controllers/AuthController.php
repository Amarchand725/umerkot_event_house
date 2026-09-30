<?php

namespace App\Http\Controllers;

use App\Helpers\FileUploader;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Enum\GenderEnum;
use App\Models\LogEntityStatus;

class AuthController extends Controller
{
    public function __construct()
    {
    }
    public function dashboard(){
        $title = Auth::user()->name . "'s Dashboard";
        
        return view('back-office.dashboard.dashboard', [
            'title' => $title,
        ]);
    }
    public function profile(){
        $title = Auth::user()->name . "'s Profile";

        $authUser = auth()->user();
        $isAdmin = $authUser->hasRole('Admin'); // adjust according to your role system
        $agentId = $authUser->id;

        // 1️⃣ Lead Activities
        $leadQuery = LogEntityStatus::with(['assignee', 'lead']);

        $leadActivities = null;
        if (!$isAdmin) {
            $leadQuery->where('assignee_id', $agentId);
        }
            
        return view('back-office.dashboard.profile', get_defined_vars());
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        if (!Hash::check($request->old_password, auth()->user()->password)) {
            return response()->json(['error' => false, 'message' => 'The provided old password is incorrect.'], 422);
        }

        auth()->user()->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json(['success' => true, 'message' => 'You have changed password successfully!.'], 200);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function updateProfile(Request $request)
    {
        $model = auth()->user();
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => [
                'nullable',
                'intl_phone', // validates full international number
                Rule::unique('users', 'phone')->ignore($model),
            ],
            'gender' => ['required', Rule::in(GenderEnum::cases())],
            'avatar' => [ 'nullable'],
        ]);

        if (!empty($request->avatar) && $request->avatar instanceof \Illuminate\Http\UploadedFile) {
            // Delete existing avatar if exists
            if ($model?->avatar?->path) {
                FileUploader::deleteFile($model?->avatar?->path);
            }

            // Upload new avatar
            $model->avatar_id = FileUploader::uploadFile(
                $request->avatar, 
                $model, 
                'avatars', 
                size: 64
            )?->id;
        }

        $model->name = $request->name;
        $model->phone = $request->phone;
        $model->gender = $request->gender;
        $model->save();

        return response()->json(['success' => true, 'message' => 'You have updated profile successfully!.'], 200);
    }
}