<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Models\UserSetting;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $settings = $user->setting?->toFormValues() ?? UserSetting::defaultValues();

        return view('settings.index', [
            'user' => $user,
            'settings' => $settings,
        ]);
    }

    public function update(UpdateSettingsRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        $user->update([
            'name' => $validated['name'],
        ]);

        $user->setting()->updateOrCreate(
            ['user_id' => $user->id],
            $validated['settings']
        );

        return redirect()
            ->route('settings.index')
            ->with('success', '設定を保存しました。');
    }

    public function email()
    {
        return view('settings.email', [
            'user' => Auth::user(),
        ]);
    }

    public function password()
    {
        return view('settings.password');
    }
}
