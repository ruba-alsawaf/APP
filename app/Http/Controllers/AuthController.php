<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Expert;
use App\Models\ExpertAvailability;
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
            $expert = Auth::guard('expert')->user();
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
        'email' => 'required|email',
        'password' => 'required|string|min:3',
        'role' => 'required|in:user,expert',
    ]);

    $guard = $credentials['role'] === 'expert' ? 'expert' : 'api';

    if ($token = Auth::guard($guard)->attempt([
        'email' => $credentials['email'], 
        'password' => $credentials['password'],
    ])) {
        $user = Auth::guard($guard)->user();
        
        return response()->json([
            'message' => 'Login successful.',
            'user' => $user,
            'token' => $token,
        ], 200);
    } else {
        return response()->json(['error' => 'Invalid credentials'], 401);
    }
}
 

public function logout(Request $request)
{
    try {
        // تحديد الـ Guard بناءً على حالة تسجيل الدخول للمستخدم الحالي
        $guard = Auth::guard('expert')->check() ? 'expert' : 'api';

        // تسجيل الخروج وإبطال التوكن الحالي
        Auth::guard($guard)->logout();

        // اختيار هذه الخطوة في حال كنت تريد إبطال التوكن بشكل يدوي إذا كنت تستخدم طريقة أخرى
        // JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Successfully logged out'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'An error occurred while logging out. Please try again.'], 500);
    }
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
        'consultation_category_id' => 'required|exists:consultation_categories,id',
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
    $expert->consultation_category_id = intval($request->consultation_category_id);
    
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

    public function getCategoriesWithExperts()
{
    $categories = ConsultationCategory::with('experts')->get();
    
    return response()->json($categories);
}
    public function getConsultationCategories()
{
    $categories = ConsultationCategory::all();
    return response()->json($categories);
}


    public function searchName(Request $request)
{
    $query = $request->input('query');  

    if (!$query) {
        return response()->json(['message' => 'Query not provided'], 400);
    }

    $experts = Expert::where('name', 'like', "%$query%")
        ->get();

    if ($experts->isEmpty()) {
        return response()->json(['message' => 'No experts found'], 404);
    }

    return response()->json($experts);
}


public function searchConsultation_categories(Request $request)
{
    $query = $request->input('query');  

    if (!$query) {
        return response()->json(['message' => 'Query not provided'], 400);
    }

    $experts = Expert::where('consultation_category_id', 'like', "%$query%")
        ->get();

    if ($experts->isEmpty()) {
        return response()->json(['message' => 'No Consultation_categories found'], 404);
    }

    return response()->json($experts);
}

public function showExpert($id)
{
    $expert = Expert::find($id);

    if (!$expert) {
        return response()->json(['message' => 'Expert not found'], 404);
    }

    return response()->json($expert);
}

public function addAvailableTime(Request $request)
{
    $request->validate([
        'expert_id' => 'required',
        'date' => 'required|date',
        'time_slot' => 'required|string',
    ]);

    if (!Expert::where('id', $request->expert_id)->exists()) {
        return response()->json(['message' => 'expert not found'], 404);
    }

    $availability = ExpertAvailability::create([
        'expert_id' => $request->expert_id,
        'date' => $request->date,
        'time_slot' => $request->time_slot,
        'is_booked' => false, 
    ]);

    return response()->json(['message' => 'Time slot added successfully!', 'availability' => $availability], 201);
}
public function getAvailableTimes($expert_id)
{
    $availableTimes = ExpertAvailability::where('expert_id', $expert_id)
        ->where('is_booked', false)
        ->get();

    if ($availableTimes->isEmpty()) {
        return response()->json(['message' => 'No available times found'], 404);
    }

    return response()->json($availableTimes, 200);
}
public function bookAppointment(Request $request)
{
    $request->validate([
        'availability_id' => 'required|exists:expert_availabilities,id',
        'user_id' => 'required', 
    ]);

    $availability = ExpertAvailability::find($request->availability_id);

    if ($availability->is_booked) {
        return response()->json(['message' => 'Time slot is already booked'], 400);
    }

    if (!User::where('id', $request->user_id)->exists()) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $availability->is_booked = true;
    $availability->user_id = $request->user_id; 
    $availability->save();

    return response()->json(['message' => 'Appointment booked successfully!'], 200);
}


public function getBookedAppointments(Request $request)
{
    $request->validate([
        'expert_id' => 'required|exists:experts,id', 
    ]);

    $appointments = ExpertAvailability::where('expert_id', $request->expert_id)
        ->where('is_booked', true)
        ->get();

    return response()->json(['appointments' => $appointments], 200);
}


}