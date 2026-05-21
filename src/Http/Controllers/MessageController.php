<?php

namespace ME\EmCore\Http\Controllers;

use ME\Models\Setting;
use ME\EmCore\Models\Message;
use Illuminate\Http\Request;
use ME\Http\Controllers\Controller;
use ME\Services\TelegramBotService;
use App\Http\Middleware\AuthorizationMiddleware;

class MessageController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramBotService $telegramService)
    {
        $this->middleware('authorization:message.show')->only(['index', 'read', 'readAll']);
        $this->middleware('authorization:message.edit')->only(['edit', 'update']);
        $this->middleware('authorization:message.delete')->only(['destroy']);

        $this->telegramService = $telegramService;
    }

    public function index(Request $request)
    {
        $query = Message::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }
        if ($request->filled('subject')) {
            $query->where('subject', 'like', '%' . $request->subject . '%');
        }

        $messages = $query->orderByDesc('id')->paginate(get_setting('pagination', 10));
        $settings = Setting::latest()->first();

        return view('em_core::messages.index', compact('messages', 'settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $message = Message::create($request->all());

        // Send notification to Telegram
        try {
            $telegramMessage = "📨 *New Message Received*\n\n"
                . "👤 *Name:* " . $message->name . "\n"
                . "📧 *Email:* " . $message->email . "\n"
                . "📝 *Subject:* " . $message->subject . "\n\n"
                . "💬 *Message:*\n" . $message->message;

            $this->telegramService->sendMessage($telegramMessage);
        } catch (\Exception $e) {
            // \Log::error('Failed to send Telegram notification: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Message sent!']);
    }

    public function edit($id)
    {
        $message = Message::findOrFail($id);
        $settings = Setting::latest()->first();

        return view('em_core::messages.edit', compact('message', 'settings'));
    }

    public function update(Request $request, $id)
    {
        $message = Message::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);
        $message->update($request->all());

        return redirect()->route('admin.messages.index')->with('success', 'Message updated!');
    }

    public function destroy($id)
    {
        $message = Message::findOrFail($id);
        $message->delete();

        return redirect()->route('admin.messages.index')->with('success', 'Message deleted!');
    }

    public function read($id)
    {
        $message = Message::findOrFail($id);
        $message->is_read = true;
        $message->save();

        return redirect()->route('admin.messages.index')->with('success', 'Message marked as read!');
    }

    public function readAll()
    {
        Message::where('is_read', false)->update(['is_read' => true]);

        return redirect()->route('admin.messages.index')->with('success', 'All messages marked as read!');
    }
}
