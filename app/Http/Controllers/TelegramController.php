<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{

    private $telegramApiUrl;
    private $botToken;

    public function __construct()
    {
        $this->botToken = env('TELEGRAM_BOT_TOKEN');
        $this->telegramApiUrl = "https://api.telegram.org/bot{$this->botToken}/";
    }

    public function index()
    {
        return view('telegram.sendMessage');
    }

    public function indexFile()
    {
        return view('telegram.sendMessageWithFile');
    }

    public function indexSelection()
    {
        return view('telegram.sendMessageBySelection');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'text' => 'required'
        ]);
        $token = 'https://api.telegram.org/bot7552280930:AAHKxj0v2bVLh_mbHJLE66FjwI3mXkER9q4';

        // $response = Http::post($token . '/sendMessage', [
        //     'parse_mode' => 'HTML',
        //     'chat_id' => '743745215',
        //     'text' => "<i>Lorem ipsum dolor sit amet consectetur adipisicing elit.\n 
        //             Rerum assumenda sint quod in, sapiente labore tenetur possimus voluptates,\n 
        //             quo non nisi earum veritatis facilis odio inventore quidem est consequatur\n
        //             molestiae itaque iure! Esse error, dignissimos numquam magni quaerat eum tempora\n
        //             veniam voluptatibus nam aspernatur at vitae temporibus fugiat consequuntur amet!\n </i>",
        //     'reply_markup' => json_encode([
        //         // 'keyboard' => [
        //         //     [
        //         //         ['text' => 'Button 1'],
        //         //     ],
        //         //     [
        //         //         ['text' => 'Button 2'],
        //         //         ['text' => 'Button 3']
        //         //     ],
        //         //     [
        //         //         ['text' => 'Button 4'],
        //         //         ['text' => 'Button 5'],
        //         //         ['text' => 'Button 6']
        //         //     ]
        //         // ],
        //         'inline_keyboard' => [
        //             [
        //                 ['text' => 'Receive ✅', 'callback_data' => 'button_1'],
        //                 ['text' => 'Reject ❌', 'callback_data' => 'button_2'],
        //             ]
        //         ],
        //         // 'resize_keyboard' => true
        //     ]),
        // ]);

        $response = Http::post($token . '/sendMessage', [
            'parse_mode' => 'HTML',
            'chat_id' => '743745215',
            'text' => "<i>Lorem ipsum dolor sit amet consectetur adipisicing elit.\n 
                    Rerum assumenda sint quod in, sapiente labore tenetur possimus voluptates,\n 
                    quo non nisi earum veritatis facilis odio inventore quidem est consequatur\n
                    molestiae itaque iure! Esse error, dignissimos numquam magni quaerat eum tempora\n
                    veniam voluptatibus nam aspernatur at vitae temporibus fugiat consequuntur amet!\n </i>",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => 'Receive ✅', 'callback_data' => 'button_1'],
                        ['text' => 'Reject ❌', 'callback_data' => 'button_2'],
                    ]
                ],
                // 'resize_keyboard' => true
            ]),
        ]);

        return back()->with('success', 'Message sent');
    }

    public function sendMessageWithFile(Request $request)
    {
        $data = $request->validate([
            'text' => 'required|string',
            'file' => 'file'
        ]);

        $token = 'https://api.telegram.org/bot7552280930:AAHKxj0v2bVLh_mbHJLE66FjwI3mXkER9q4';

        $filePath = null;
        $extension = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $filename = now()->format('Y-m-d_H-i-s') . '_' . time() . '.' . $extension;

            switch ($extension) {
                case 'jpg':
                case 'png':
                case 'jpeg':
                    $filePath = $file->move('images/', $filename);
                    break;

                case 'mp4':
                case 'avi':
                case 'mkv':
                    $filePath = $file->move('videos/', $filename);
                    break;

                default:
                    $filePath = $file->move('files/', $filename);
                    break;
            }
        }

        $chatId = '743745215';
        $text = $data['text'];

        try {
            if ($filePath && in_array($extension, ['jpg', 'png', 'jpeg'])) {
                Http::attach('photo', fopen(public_path($filePath), 'r'))
                    ->post($token . '/sendPhoto', [
                        'chat_id' => $chatId,
                        'caption' => $text,
                    ]);
            } elseif ($filePath && in_array($extension, ['mp4', 'avi', 'mkv'])) {
                Http::attach('video', fopen(public_path($filePath), 'r'))
                    ->post($token . '/sendVideo', [
                        'chat_id' => $chatId,
                        'caption' => $text,
                    ]);
            } elseif ($filePath) {
                Http::attach('document', fopen(public_path($filePath), 'r'))
                    ->post($token . '/sendDocument', [
                        'chat_id' => $chatId,
                        'caption' => $text,
                    ]);
            } else {
                Http::post($token . '/sendMessage', [
                    'chat_id' => $chatId,
                    'text' => $text,
                    'parse_mode' => 'HTML',
                ]);
            }
        } catch (\Exception $e) {
            return back()->with('danger', 'A problem occured!');
        }

        return back()->with('success', 'Message send with a message');
    }

    public function sendMessageBySelecting(Request $request)
    {
        $data = $request->validate([
            'names' => 'required|array|min:1',
            'names.*' => 'string'
        ]);
        $token = 'https://api.telegram.org/bot7552280930:AAHKxj0v2bVLh_mbHJLE66FjwI3mXkER9q4';

        $nameList = implode("\n", $data['names']);

        $response = Http::post($token . '/sendMessage', [
            'parse_mode' => 'HTML',
            'chat_id' => '743745215',
            'text' => "Selected Users:\n" . $nameList
        ]);

        return back()->with('success', 'Message sent');
    }

    public function sendReverse(Request $request)
    {
        try {
            $data = $request->all();
            $chat_id = $data['message']['chat']['id'];
            $text = $data['message']['text'];
            Log::info('Telegram: ', $data);
            $this->sendReverseMessage($text, $chat_id);

            return response()->json([
                'status' => 'success'
            ], 200);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function sendReverseMessage(string $text, int $chat_id)
    {
        $token = 'https://api.telegram.org/bot7552280930:AAHKxj0v2bVLh_mbHJLE66FjwI3mXkER9q4';

        $response = Http::post($token . '/sendMessage', [
            'parse_mode' => 'HTML',
            'chat_id' => $chat_id,
            'text' => "<b>{$text}</b>",
        ]);

        return back()->with('success', 'Message sent');
    }

    public function handle(Request $request)
    {
        $update = $request->all();
        $chatId = $update['message']['chat']['id'] ?? null;
        $text = $update['message']['text'] ?? null;

        if (!$chatId || !$text) {
            return response()->json(['status' => 'ignored']);
        }

        // Handle user registration steps
        $step = cache()->get("registration_step_{$chatId}", 'start');

        switch ($step) {
            case 'start':
                $this->sendMessage($chatId, "Welcome! Please enter your name:");
                cache()->put("registration_step_{$chatId}", 'name');
                break;

            case 'name':
                cache()->put("user_name_{$chatId}", $text);
                $this->sendMessage($chatId, "Great! Now, please enter your email:");
                cache()->put("registration_step_{$chatId}", 'email');
                break;

            case 'email':
                cache()->put("user_email_{$chatId}", $text);
                $this->sendMessage($chatId, "Thanks! Please enter your password:");
                cache()->put("registration_step_{$chatId}", 'password');
                break;

            case 'password':
                cache()->put("user_password_{$chatId}", $text);
                $this->sendMessage($chatId, "Almost done! Please send your profile picture:");
                cache()->put("registration_step_{$chatId}", 'image');
                break;

            case 'image':
                if (!isset($update['message']['photo'])) {
                    $this->sendMessage($chatId, "Please send a valid photo.");
                    break;
                }

                $photo = end($update['message']['photo']); // Get highest resolution photo
                $fileId = $photo['file_id'];
                $fileInfo = $this->getFile($fileId);
                $fileUrl = "https://api.telegram.org/file/bot{$this->botToken}/{$fileInfo['result']['file_path']}";

                // Save the photo locally
                $photoPath = public_path("profile_pictures/{$chatId}.jpg");
                file_put_contents($photoPath, file_get_contents($fileUrl));

                // Store user data
                $name = cache()->get("user_name_{$chatId}");
                $email = cache()->get("user_email_{$chatId}");
                $password = cache()->get("user_password_{$chatId}");
                // Save to database (example)
                User::create(['name' => $name, 'email' => $email, 'password' => Hash::make($password), 'photo' => $photoPath, 'chat_id' => $chatId]);

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
}
