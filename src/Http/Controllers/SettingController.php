<?php

namespace ME\EmCore\Http\Controllers;

use ME\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use ME\Http\Controllers\Controller;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('authorization:setting.edit')->only(['edit', 'update']);
    }

    public function edit()
    {
        // Only new fields
        $settings = [
            'banner_image' => Setting::get('banner_image'),
            'profile_image' => Setting::get('profile_image'),
            'signature_image' => Setting::get('signature_image'),
            'mobile' => Setting::get('mobile', ''),
            'email' => Setting::get('email', ''),
            'present_address' => Setting::get('present_address', ''),
            'map_link' => Setting::get('map_link', ''),
            'designation' => Setting::get('designation', ''),
            'facebook_link' => Setting::get('facebook_link', ''),
            'instagram_link' => Setting::get('instagram_link', ''),
            'skype_link' => Setting::get('skype_link', ''),
            'twitter_link' => Setting::get('twitter_link', ''),
            'linkedin_link' => Setting::get('linkedin_link', ''),
        ];

        return view('em_core::settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'banner_image' => 'nullable|image|max:4096',
            'profile_image' => 'nullable|image|max:4096',
            'signature_image' => 'nullable|image|max:4096',
            'mobile' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'present_address' => 'nullable|string|max:255',
            'map_link' => 'nullable|url|max:255',
            'designation' => 'nullable|string|max:255',
            'facebook_link' => 'nullable|url|max:255',
            'instagram_link' => 'nullable|url|max:255',
            'skype_link' => 'nullable|string|max:255',
            'twitter_link' => 'nullable|url|max:255',
            'linkedin_link' => 'nullable|url|max:255',
        ]);

        // Text fields
        Setting::set('mobile', $request->mobile);
        Setting::set('email', $request->email);
        Setting::set('present_address', $request->present_address);
        Setting::set('map_link', $request->map_link);
        Setting::set('designation', $request->designation);
        Setting::set('facebook_link', $request->facebook_link);
        Setting::set('instagram_link', $request->instagram_link);
        Setting::set('skype_link', $request->skype_link);
        Setting::set('twitter_link', $request->twitter_link);
        Setting::set('linkedin_link', $request->linkedin_link);

        // Image fields
        foreach (['banner_image', 'profile_image', 'signature_image'] as $imgField) {
            if ($request->hasFile($imgField)) {
                $image = $request->file($imgField);
                $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
                $imagePath = storage_path("app/public/images/{$imgField}");

                if (!file_exists($imagePath)) {
                    mkdir($imagePath, 0755, true);
                }

                $image->move($imagePath, $imageName);
                Setting::set($imgField, $imageName);
            }
        }

        return redirect()->route('admin.settings.edit')
            ->with('success', 'Settings updated successfully');
    }


}
