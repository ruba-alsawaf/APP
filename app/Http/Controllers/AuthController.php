<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Expert;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\ConsultationCategory;

class AuthController extends Controller
{
        public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:3',
            'role' => 'required|string|in:user,expert',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ];
    
        if ($request->role == 'expert') {
            $expert = Expert::create($userData);
            Auth::guard('expert')->login($expert); 
            return response()->json(['message' => 'Registration successful. Complete your profile.', 'expert' => $expert], 201);
        } else {
            $user = User::create($userData);
            Auth::login($user);  
            return response()->json(['message' => 'Registration successful.', 'user' => $user], 201);
        }
    }

    public function login(Request $request)
{

    $credentials = $request->validate([
        'name' => 'required',
        'email' => 'required|email',
        'password' => 'required',
        'role' => 'required|in:user,expert',
    ]);

    if ($credentials['role'] === 'expert') {
        if (Auth::guard('expert')->attempt([
            'name' => $credentials['name'],
            'email' => $credentials['email'], 
            'password' => $credentials['password'],
        ])) {
            $expert = Auth::guard('expert')->user();
            $token = auth('expert')->attempt($credentials);
            return response()->json([
                'message' => 'Login successful.',
                'expert' => $expert,
                'token' => $token
            ], 200);
        } else {
            return response()->json(['error' => 'Invalid credentials for expert'], 401);
        }
    } else {
        if (Auth::guard('web')->attempt([
            'name' => $credentials['name'],
            'email' => $credentials['email'], 
            'password' => $credentials['password'],
        ])) {
            $user = Auth::guard('web')->user();
            $token = auth('api')->attempt($credentials);
            return response()->json([
                'message' => 'Login successful.',
                'user' => $user,
                'token' => $token
            ], 200);
        } else {
            return response()->json(['error' => 'Invalid credentials for user'], 401);
        }
    }
}
public function logout(Request $request)
{
    $user = Auth::guard('api')->user();
    $expert = Auth::guard('expert')->user();

    if ($user) {
        auth('api')->logout();
        return response()->json(['message' => 'Successfully logged out'], 200);
    }

    if ($expert) {
        auth('expert')->logout();
        return response()->json(['message' => 'Successfully logged out'], 200);
    }

    return response()->json(['error' => 'Unable to logout, user not found'], 400);
}


    public function completeProfile(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string|min:3',
        'name' => 'string|max:255',
        'role' => 'required|in:expert',
        'phone' => 'string|max:20',
        'address' => 'string|max:255',
        'specialization' => 'nullable|string|max:255',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'experiences' => 'nullable|string|max:5000',
        'availability' => 'nullable|string|max:255',
        'consultation_categories.*' => 'nullable|array',
    ]);

    $credentials = $request->only('email', 'password');

    if (!Auth::guard('expert')->attempt($credentials)) {
        return response()->json(['error' => 'Invalid email or password'], 401);
    }

    $expert = Auth::guard('expert')->user();
    $expert = Expert::find(Auth::guard('expert')->id());
    if ($request->hasFile('photo')) {
        $photoPath = $request->file('photo')->store('photos', 'public');
        $expert->photo = $photoPath;
    }

    $expert->name = $request->name;
    $expert->role = $request->role;
    $expert->phone = $request->phone;
    $expert->address = $request->address;
    $expert->specialization = $request->specialization;
    $expert->experiences = $request->experiences;
    $expert->availability = $request->availability;
    $expert->consultation_categories = json_encode($request->consultation_categories);
    
    $expert->save();

    return response()->json(['message' => 'Profile completed successfully!', 'expert' => $expert], 200);
}

public function getUser(Request $request)
{
    $token = $request;

    if (!$token) {
        return response()->json(['error' => 'Token not provided'], 400);
    }

    try {
        $user = Auth::guard('api')->user();
        $expert = Auth::guard('expert')->user();

        if ($user) { 
            return response()->json(['user' => $user], 200);
        }
        if($expert){
            return response()->json(['expert' => $expert], 200);
        }

        return response()->json(['error' => 'Invalid token or user not found'], 401);

    } catch (\Exception $e) {
        return response()->json(['error' => 'An error occurred while fetching user information'], 500);
    }
}

    public function getExpertsByCategory($category)
    {
            $experts = Expert::where('consultation_categories', 'LIKE', "%{$category}%")->get();
        
            if ($experts->isEmpty()) {
                return response()->json(['message' => 'No experts found in this category'], 404);
            }
        
            return response()->json($experts);
    }
}