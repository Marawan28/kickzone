<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class FirebaseAuthMiddleware
{
    protected $firebaseAuth;

    // حقن مكتبة فايربيز تلقائياً
    public function __construct(FirebaseAuth $firebaseAuth)
    {
        $this->firebaseAuth = $firebaseAuth;
    }

    public function handle(Request $request, Closure $next): Response
    {
        // 1. التأكد من وجود التوكن في الـ Header
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Unauthorized: Token missing.'], 401);
        }

        try {
            // 2. التحقق من التوكن عبر سيرفرات فايربيز
            $verifiedIdToken = $this->firebaseAuth->verifyIdToken($token);
            
            // 3. جلب بيانات المستخدم من التوكن
            $firebaseUid = $verifiedIdToken->claims()->get('sub');
            $email = $verifiedIdToken->claims()->get('email');
            $name = $verifiedIdToken->claims()->get('name') ?? 'Firebase User';

            // 4. البحث عن المستخدم في قاعدتنا أو إنشاؤه لو أول مرة يدخل الأبليكيشن
            $user = User::firstOrCreate(
                ['firebase_uid' => $firebaseUid],
                [
                    'name' => $name,
                    'email' => $email,
                    'password' => bcrypt(str()->random(16)), // كلمة مرور عشوائية لأن التحقق خارجي
                ]
            );

            // 5. تسجيل الدخول في الـ Runtime الحالي للطلب
            Auth::login($user);

            return $next($request);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Unauthorized: Invalid or expired Firebase token.',
                'error' => $e->getMessage()
            ], 401);
        }
    }
}