<?php

namespace EmCore\Services;

use Illuminate\Support\Facades\Http;

class TelegramBotService
{
    protected $token;
    protected $chatId;

    public function __construct()
    {
        $this->token = config('telegram.bot_token');
        $this->chatId = config('telegram.chat_id');
    }

    public function sendMessage($text)
    {
        return Http::post("https://api.telegram.org/bot{$this->token}/sendMessage", [
            'chat_id' => $this->chatId,
            'text' => $text,
        ]);
    }

    // Send photo by HTTPS URL
    public function sendPhoto($photoUrl, $caption = null)
    {
        return Http::post("https://api.telegram.org/bot{$this->token}/sendPhoto", [
            'chat_id' => $this->chatId,
            'photo' => $photoUrl,
            'caption' => $caption,
        ]);
    }

    // Send photo by uploading file directly (if URL doesn't work)
    public function sendPhotoFile($filePath, $caption = null)
    {
        return Http::attach(
            'photo', file_get_contents($filePath), basename($filePath)
        )->post("https://api.telegram.org/bot{$this->token}/sendPhoto", [
            'chat_id' => $this->chatId,
            'caption' => $caption,
        ]);
    }

    public function sendLoanSummary($loanSummary)
    {
        $message = "*Loan Summary*\n";

        $message .= "-------------------------\n";

        foreach ($loanSummary['userSummary'] as $summary) {
            $netBalance = $summary['given_due'] - $summary['taken_due'];

            if ($netBalance == 0) {
                $status = 'Settled';
            } else {
                $status = $netBalance > 0 ? 'Receivable' : 'Payable';
            }

            $userName = $summary['user']->name ?? '-';
            $balanceAmount = number_format(abs($netBalance), 2);

            $message .= "{$userName} | {$balanceAmount} | {$status}\n";
        }

        $message .= "\n";

        // Overall Summary
        $message .= "*Overall Summary:*\n";
        $netBalance = $loanSummary['totalGivenDue'] - $loanSummary['totalTakenDue'];

        if ($netBalance == 0) {
            $message .= "All accounts are settled\n";
        } elseif ($netBalance > 0) {
            $message .= "Total Receivable: " . number_format(abs($netBalance), 2) . "\n";
        } else {
            $message .= "Total Payable: " . number_format(abs($netBalance), 2) . "\n";
        }

        return $this->sendMessage($message);
    }

}
