<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TelegramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
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


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
