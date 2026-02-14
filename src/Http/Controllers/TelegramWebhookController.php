<?php

namespace EmCore\Http\Controllers;

use Illuminate\Http\Request;
use EmCore\Models\DailyExpense;
use Illuminate\Support\Facades\Log;
use ME\Http\Controllers\Controller;
use EmCore\Services\TelegramBotService;

class TelegramWebhookController extends Controller
{
    public function handle(Request $request, $secret)
    {
        if ($secret !== config('telegram.webhook_secret')) {
            abort(403, 'Invalid secret');
        }

        $data = $request->all();
        $chatId = data_get($data, 'message.chat.id');

        if ($chatId != config('telegram.chat_id')) {
            abort(403, 'Invalid chat');
        }

        // Log or process the message securely
        Log::info('Telegram webhook received', $data);

        // Handle /aloan command
        $message = data_get($data, 'message.text');
        if ($message === '/alloan') {
            $this->handleLoanSummary();
        }
        elseif (str_starts_with($message, '/tad')) {
            $this->handleAddTransaction($message);
        }

        return response()->json(['status' => 'ok']);
    }

    private function handleLoanSummary()
    {
        $telegramService = new TelegramBotService();
        $loanController = new LoanController();

        $loanSummary = $loanController->getTelegramLoanSummary();
        $telegramService->sendLoanSummary($loanSummary);
    }

    private function handleAddTransaction(string $message)
    {
        // Remove command
        $text = trim(str_replace('/addtnx', '', $message));

        // Regex to match key:value, value can have spaces, stops at next key
        preg_match_all('/(\w):([^\s].*?)(?=\s\w:|$)/', $text, $matches, PREG_SET_ORDER);

        $data = [];
        foreach ($matches as $m) {
            $key = strtolower($m[1]);
            $value = trim($m[2]);
            $data[$key] = $value;
        }

        // Validate required fields
        if (!isset($data['t']) || !isset($data['a'])) {
            (new TelegramBotService())->sendMessage("Error: title (t) or amount (a) missing");
            return;
        }

        $transaction = [
            'title' => $data['t'],
            'amount' => $data['a'],
            'description' => $data['d'] ?? null,
            'date' => now(),
        ];

        // Save to database
        DailyExpense::create($transaction);

        // Confirmation message
        (new TelegramBotService())->sendMessage(
            "Transaction added ✅\nTitle: {$transaction['title']}\nAmount: {$transaction['amount']}\nDescription: {$transaction['description']}"
        );
    }



}
