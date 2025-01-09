<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TelegramRegistrationConstroller extends Controller
{


    private $telegramApiUrl;
    private $botToken;

    public function __construct()
    {
        $this->botToken = env('TELEGRAM_BOT_TOKEN');
        $this->telegramApiUrl = "https://api.telegram.org/bot{$this->botToken}/";
    }


    public function handle(Request $request)
    {
        $update = $request->all();
        $chatId = $update['message']['chat']['id'] ?? null;
        $text = $update['message']['text'] ?? null;
        Log::info([$update,'Message Keldi']);

        if (!$chatId || !$text) {
            return response()->json(['status' => 'ignored']);
        }
        $step = cache()->get("registration_step_{$chatId}", 'start');

        Log::info([$update, $chatId, $text, $step, "Munda 4 narsa bo"]);
        $this->sendMessage($chatId, 'Nagap');

        switch ($step) {
            case 'start':
                $this->sendMessage($chatId, "Welcome! Please enter your name:");
                cache()->put("registration_step_{$chatId}", 'name');
                break;

            case 'name':
                cache()->put("user_name_{$chatId}", $text);
                $this->sendMessage($chatId, "Great! Now, please enter your email:");
                cache()->put("registration_step_{$chatId}", 'email');
                // $name = cache()->get("user_name_{$chatId}", $text);
                // Log::info($name);
                break;

            case 'email':
                cache()->put("user_email_{$chatId}", $text);
                $this->sendMessage($chatId, "Thanks! Please enter your password:");
                cache()->put("registration_step_{$chatId}", 'password');
                // $email = cache()->get("user_name_{$chatId}", $text);
                // Log::info($email);
                break;

            case 'password':
                cache()->put("user_password_{$chatId}", $text);
                $this->sendMessage($chatId, "Almost done! Please send your profile picture:");
                cache()->put("registration_step_{$chatId}", 'image');
                break;

            case 'image':

                if (!isset($update['message']['photo']) || empty($update['message']['photo'])) {
                    $this->sendMessage($chatId, "Please send a valid photo.");
                    break;
                }

                $photo = end($update['message']['photo']);
                $fileId = $photo['file_id'];
                $fileInfo = $this->getFile($fileId);

                Log::info([$photo, 'FileInfo']);

                if (!$fileInfo || !isset($fileInfo['result']['file_path'])) {
                    $this->sendMessage($chatId, "Failed to retrieve file information. Please try again.");
                    break;
                }

                $fileUrl = "https://api.telegram.org/file/bot{$this->botToken}/{$fileInfo['result']['file_path']}";

                $photoPath = public_path("profile_pictures/{$chatId}.jpg");

                if (!file_exists(public_path('profile_pictures'))) {
                    mkdir(public_path('profile_pictures'), 0777, true);
                }

                $fileContent = @file_get_contents($fileUrl);

                if (!$fileContent) {
                    $this->sendMessage($chatId, "Failed to download the photo. Please try again.");
                    break;
                }

                file_put_contents($photoPath, $fileContent);

                $name = cache()->get("user_name_{$chatId}");
                $email = cache()->get("user_email_{$chatId}");
                $password = cache()->get("user_password_{$chatId}");


                User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'photo' => $photoPath,
                    'chat_id' => $chatId
                ]);

                $this->sendMessage($chatId, "Registration complete!\nName: $name\nEmail: $email\nPhoto saved.");
                cache()->forget("registration_step_{$chatId}");
                break;

            default:
                $this->sendMessage($chatId, "Unknown step. Type /start to begin again.");
                cache()->forget("registration_step_{$chatId}");
        }


        return response()->json(['status' => 'success']);
    }

    private function sendMessage($chatId, $text)
    {
        $url = $this->telegramApiUrl . "sendMessage";
        Http::post($url, [
            'chat_id' => $chatId,
            'text' => $text,
        ]);
    }

    private function getFile($fileId)
    {
        $url = $this->telegramApiUrl . "getFile";
        $response = Http::post($url, ['file_id' => $fileId]);
        return $response->json();
    }













    public function store(int $chatId, string $text, $replyMarkup = null)
    {
        $token = "https://api.telegram.org/bot" . $this->botToken;
        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ];

        if ($replyMarkup) {
            $payload['reply_markup'] = json_encode($replyMarkup);
        }

        Http::post($token . '/sendMessage', $payload);
    }

    public function bot(Request $request)
    {
        try {
            $data = $request->all();
            $chat_id = $data['message']['chat']['id'];
            $text = $data['message']['text'] ?? null;
            $photo = $data['message']['photo'] ?? null;

            if ($text === '/start') {
                $this->store($chat_id, "Assalomu alaykum! Iltimos, tanlang:", [
                    'keyboard' => [
                        [
                            ['text' => 'Register'],
                            ['text' => 'Login']
                        ]
                    ],
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true,
                ]);
                return;
            }

            if ($text === 'Register') {
                Cache::put("register_step_{$chat_id}", 'name');
                $this->store($chat_id, "Iltimos, ismingizni kiriting:");
                return;
            }

            if (Cache::get("register_step_{$chat_id}") === 'name') {
                Cache::put("register_name_{$chat_id}", $text);
                Cache::put("register_step_{$chat_id}", 'email');
                $this->store($chat_id, "Iltimos, elektron pochta manzilingizni kiriting:");
                return;
            }

            if (Cache::get("register_step_{$chat_id}") === 'email') {
                Cache::put("register_email_{$chat_id}", $text);
                Cache::put("register_step_{$chat_id}", 'password');
                $this->store($chat_id, "Iltimos, parolingizni kiriting:");
                return;
            }

            if (Cache::get("register_step_{$chat_id}") === 'password') {
                Cache::put("register_password_{$chat_id}", $text);
                Cache::put("register_step_{$chat_id}", 'confirmation_code');

                // $confirmation_code = Str::random(6);

                $email = Cache::get("register_email_{$chat_id}");
                $name = Cache::get("register_name_{$chat_id}");

                try {
                    // Mail::to($email)->send(new SendMessage($name, $confirmation_code));
                    Log::info('Email sent successfully');
                    $this->store($chat_id, "Emailizga tasdiqlash kodi yuborildi. Iltimos, uni kiriting.");
                } catch (\Exception $e) {
                    Log::error('Email sending failed: ' . $e->getMessage());
                    $this->store($chat_id, "Tasdiqlash kodi yuborishda xatolik yuz berdi. Iltimos, qaytadan urinib ko'ring.");
                }

                // Cache::put("confirmation_code_{$chat_id}", $confirmation_code);
                return;
            }
            if (Cache::get("register_step_{$chat_id}") === 'confirmation_code') {
                if ($text === Cache::get("confirmation_code_{$chat_id}")) {
                    Cache::put("register_password_{$chat_id}", bcrypt(Cache::get("register_password_{$chat_id}")));
                    Cache::put("register_step_{$chat_id}", 'image');
                    $this->store($chat_id, "Tasdiqlash kodi to'g'ri. Iltimos, profilingiz uchun rasm yuboring.");
                    Cache::forget("confirmation_code_{$chat_id}");
                } else {
                    $this->store($chat_id, "Tasdiqlash kodi noto'g'ri. Iltimos, to'g'ri kodi kiriting.");
                }
                return;
            }

            if (Cache::get("register_step_{$chat_id}") === 'image') {
                if ($photo) {
                    $file_id = end($photo)['file_id'];

                    $telegram_api = "https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN');
                    $file_path_response = file_get_contents("{$telegram_api}/getFile?file_id={$file_id}");
                    $response = json_decode($file_path_response, true);

                    if (isset($response['result']['file_path'])) {
                        $file_path = $response['result']['file_path'];
                        $download_url = "https://api.telegram.org/file/bot" . env('TELEGRAM_BOT_TOKEN') . "/{$file_path}";

                        $image_name = uniqid() . '.jpg';
                        $image_content = file_get_contents($download_url);

                        if ($image_content) {
                            Storage::disk('public')->put("uploads/{$image_name}", $image_content);
                            $image_path = "uploads/{$image_name}";
                        } else {
                            $this->store($chat_id, "Rasmni yuklab olishda xatolik yuz berdi. Iltimos, qaytadan urinib ko'ring.");
                            return;
                        }
                        $user = User::create([
                            'name' => Cache::get("register_name_{$chat_id}"),
                            'email' => Cache::get("register_email_{$chat_id}"),
                            'password' => Cache::get("register_password_{$chat_id}"),
                            'chat_id' => $chat_id,
                            'image' => "uploads/{$image_name}",
                            'email_verified_at' => now(),
                        ]);

                        Http::post(env('TELEGRAM_BOT_TOKEN') . '/sendMessage', [
                            'chat_id' => User::where('role', 'admin')->first()->chat_id,
                            'text' => $user,
                            'parse_mode' => 'HTML',
                            'reply_markup' => json_encode([
                                'keyboard' => [
                                    [
                                        ['text' => 'Tasdiqlash✅'],
                                        ['text' => 'Bekor qilish⛔️'],
                                    ]
                                ],
                                'resize_keyboard' => true,
                                'one_time_keyboard' => true,
                            ]),
                        ]);

                        $this->store($chat_id, "Siz muvaffaqiyatli ro'yxatdan o'tdingiz!");


                        Cache::forget("register_step_{$chat_id}");
                        Cache::forget("register_name_{$chat_id}");
                        Cache::forget("register_email_{$chat_id}");
                        Cache::forget("register_password_{$chat_id}");
                        Cache::forget("confirmation_code_{$chat_id}");
                    } else {
                        $this->store($chat_id, "Rasmni yuklab olishda muammo yuz berdi. Iltimos, qaytadan urinib ko'ring.");
                    }
                } else {
                    $this->store($chat_id, "Iltimos, rasm yuboring!");
                }
                return;
            }
            if ($text === 'Tasdiqlash✅') {
                Log::info('keldi');
                $user = User::latest()->first()->update([
                    'status' => '1',
                ]);
                $this->store(User::where('role', 'admin')->first()->chat_id, "Yangi user ro'yxatdan to'liq o'tdi!");
                Log::info($user);
            }

            if ($text === 'Bekor qilish⛔️') {
                $user = User::latest()->first()->update([
                    'status' => '0',
                ]);
                $this->store(User::where('role', 'admin')->first()->chat_id, "Yangi user to'liq o'tmadi");
                Log::info($user);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ]);
        }
    }
}