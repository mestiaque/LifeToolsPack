<?php

namespace ME\EmCore\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use ME\EmCore\Models\Loan;
use ME\EmCore\Models\NotifyPerson;
use ME\Models\UserActivity;
use ME\Http\Controllers\Controller;


class NotifyPersonController extends Controller
{
    public function __construct()
    {
        $this->middleware('authorization:notify_person.view')->only(['index']);
        $this->middleware('authorization:notify_person.create')->only(['store']);
        $this->middleware('authorization:notify_person.edit')->only(['update']);
        $this->middleware('authorization:notify_person.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = NotifyPerson::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }

        $people = $query->orderByDesc('id')->get();

        return view('em_core::notify_people.index', compact('people'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        NotifyPerson::create($data);

        return redirect()->route('admin.notify-people.index')->with('success', __('ব্যক্তি সফলভাবে যুক্ত হয়েছে।'));
    }

    public function update(Request $request, $id)
    {
        $person = NotifyPerson::findOrFail($id);
        $data = $this->validatedData($request);
        $person->update($data);

        return redirect()->route('admin.notify-people.index')->with('success', __('ব্যক্তির তথ্য সফলভাবে হালনাগাদ হয়েছে।'));
    }

    public function destroy($id)
    {
        $person = NotifyPerson::findOrFail($id);
        $person->delete();

        return redirect()->route('admin.notify-people.index')->with('success', __('ব্যক্তি সফলভাবে মুছে ফেলা হয়েছে।'));
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:30',
            'types' => 'nullable|string|max:255',
            'user_type' => 'nullable|string|max:100',
            'user_id' => 'nullable|integer',
        ]);

        $rawTypes = $data['types'] ?? '';

        $data['types'] = array_values(array_unique(array_filter(array_map(
            static fn ($type) => strtolower(trim((string) $type)),
            explode(',', (string) $rawTypes)
        ))));

        return $data;
    }

    public function trigger(Request $request)
    {
        $lastLogIn = UserActivity::where('activity_type', 'login')
            ->orderByDesc('id')
            ->first();

        $is30DaysSinceLastLogin = $lastLogIn
            ? $lastLogIn->activity_at->diffInDays(now()) >= 30
            : true;

        // if (!$is30DaysSinceLastLogin) {
        //     return response()->json([
        //         'success' => true,
        //         'message' => 'Last login is within 30 days, no notification sent.',
        //     ]);
        // }

        $people = NotifyPerson::all();
        $sent = 0;
        $phoneTargets = [];

        foreach ($people as $person) {
            $types = is_array($person->types) ? $person->types : [];

            // 👉 LOAN (Type 1)
            if (in_array('1', $types, true)) {
                $loanData = $this->buildLoanData();
                $emailMsg = $this->formatLoanForEmail($person, $loanData);
                $this->sendNotification($person, 'ঋণের তথ্য', $emailMsg);
                $smsMsg = $this->formatLoanForSMS($loanData);
                // $this->sendSMSNotification($person, $smsMsg);
                $sent++;
            }

            // 👉 ACCOUNT (Type 2)
            // if (in_array('2', $types, true)) {
            //     $accData = $this->buildAccountData($person);
            //     $accEmailMsg = $this->formatAccountForEmail($person, $accData);
            //     // $this->sendNotification($person, 'অ্যাকাউন্ট তথ্য', $accEmailMsg);
            //     $accSmsMsg = $this->formatAccountForSMS($accData);
            //     $sent++;
            // }

            if (!empty($person->phone)) {
                $phoneTargets[] = $person->phone;
            }
        }

        $this->sendMsgLoanUser();

        return response()->json([
            'success' => true,
            'message' => 'নোটিফিকেশন সফলভাবে পাঠানো হয়েছে।',
            'total_people' => $people->count(),
            'total_notifications' => $sent,
            'phone_targets' => array_values(array_unique($phoneTargets)),
        ]);
    }

    private function buildLoanMessage(NotifyPerson $person): string
    {
        $query = Loan::with('loanUser')->orderByDesc('date');

        if ((string) $person->user_type === 'customer' && $person->user_id) {
            $query->where('loan_user_id', (int) $person->user_id);
        }

        $loan = $query->get();

        if ($loan->isEmpty()) {
            return "ঋণের তথ্য\n"
                . "User Name: {$person->name}\n"
                . "Phone: {$person->phone}\n"
                . "Email: {$person->email}\n"
                . "Address: {$person->address}\n"
                . "Amount: N/A\n"
                . "Type: N/A\n"
                . "Loan Date: N/A";
        }

        $direction = $loan->type === 'given' ? 'প্রাপ্য' : 'পরিশোধযোগ্য';
        $loanUserName = optional($loan->loanUser)->name ?: $person->name;

        return "ঋণের তথ্য\n"
            . "User Name: {$loanUserName}\n"
            . "Phone: {$person->phone}\n"
            . "Email: {$person->email}\n"
            . "Address: {$person->address}\n"
            . "Amount: " . number_format((float) $loan->amount, 2) . "\n"
            . "Type: {$direction}\n"
            . "Loan Date: {$loan->date}";
    }

    private function buildLoanData()
    {
        $query = Loan::with('loanUser', 'repayments')->orderByDesc('date');

        $loans = $query->get()->filter(fn ($loan) => $loan->dueAmount() > 0);

        return $loans->map(function ($loan) {
            return [
                'user_name'  => optional($loan->loanUser)->name,
                'phone'      => optional($loan->loanUser)->phone,
                'email'      => optional($loan->loanUser)->email,
                'address'    => optional($loan->loanUser)->address,
                // 'amount'     => (float) $loan->amount,
                'due_amount' => (float) $loan->dueAmount(),
                'type'       => $loan->type === 'given' ? 'প্রাপ্য' : 'পরিশোধযোগ্য',
                'date'       => $loan->date,
                'note'       => $loan->note,
            ];
        })->values();
    }

    private function formatLoanForEmail($person, $loans): string
    {
        if ($loans->isEmpty()) {
            return "ঋণের তথ্য\nকোনো ঋণের তথ্য পাওয়া যায়নি।";
        }

        $message = '<div style="font-family:Arial, sans-serif; font-size:13px;">';
        $message .= '<h3 style="margin-bottom:10px;">পরিশোধযোগ্য ও প্রাপ্য হিসাব</h3>';

        foreach ($loans as $loan) {
            $bg = $loan['type'] === 'প্রাপ্য' ? '#73ff0018' : '#ff000018';
            $message .= '
            <div style="border:1px solid #ddd; padding:10px; margin-bottom:10px; background-color:' . $bg . ';">
                <strong>নাম:</strong> ' . ($loan['user_name'] ?? 'N/A') . '<br>
                <strong>ফোন:</strong> ' . ($loan['phone'] ?? 'N/A') . ' |
                <strong>ইমেইল:</strong> ' . ($loan['email'] ?? 'N/A') . '<br>
                <strong>ঠিকানা:</strong> ' . ($loan['address'] ?? 'N/A') . '<br>
                <strong>বাকি:</strong> <b>' . number_format($loan['due_amount'], 2) . '</b><br>
                <strong>ধরন:</strong> ' . ($loan['type'] ?? 'N/A') . ' |
                <strong>তারিখ:</strong> ' . ($loan['date'] ?? 'N/A') . '
                <br><strong>নোট:</strong> ' . ($loan['note'] ?? 'N/A') . '
            </div>';
        }

        $message .= '</div>';

        return $message;
    }

    private function formatLoanForSMS($loans): string
    {
        if ($loans->isEmpty()) {
            return 'কোনো ঋণের তথ্য নেই।';
        }

        $grouped = [];
        foreach ($loans as $loan) {
            $name = $loan['user_name'];
            if (!isset($grouped[$name])) {
                $grouped[$name] = ['পরিশোধযোগ্য' => 0.0, 'প্রাপ্য' => 0.0];
            }
            $grouped[$name][$loan['type']] += $loan['due_amount'];
        }

        $lines = [];
        foreach ($grouped as $name => $totals) {
            $pay  = $totals['পরিশোধযোগ্য'];
            $recv = $totals['প্রাপ্য'];
            $net  = $recv - $pay;

            $parts = [];
            if ($pay > 0)  { $parts[] = 'পরিশোধযোগ্য ' . number_format($pay, 2); }
            if ($recv > 0) { $parts[] = 'প্রাপ্য ' . number_format($recv, 2); }

            if ($net > 0) {
                $parts[] = 'নিট প্রাপ্য ' . number_format($net, 2);
            } elseif ($net < 0) {
                $parts[] = 'নিট পরিশোধযোগ্য ' . number_format(abs($net), 2);
            } else {
                $parts[] = 'নিট 0';
            }

            $lines[] = $name . ': ' . implode(', ', $parts);
        }

        return implode("\n", $lines);
    }

    private function buildAccountData(NotifyPerson $person): array
    {
        return [
            'name'    => $person->name,
            'phone'   => $person->phone,
            'email'   => $person->email,
            'address' => $person->address,
        ];
    }

    private function formatAccountForEmail(NotifyPerson $person, array $accData): string
    {
        return "অ্যাকাউন্ট তথ্য\n"
            . "------------------\n"
            . "নাম: {$accData['name']}\n"
            . "ফোন: {$accData['phone']}\n"
            . "ইমেইল: {$accData['email']}\n"
            . "ঠিকানা: {$accData['address']}";
    }

    private function formatAccountForSMS(array $accData): string
    {
        return "অ্যাকাউন্ট: {$accData['name']}, ফোন: {$accData['phone']}";
    }

    private function sendNotification(NotifyPerson $person, string $subject, string $message): void
    {
        if (!empty($person->email)) {
            \Mail::to($person->email)->send(new \ME\Mail\NoticeMailLayout([
                'title' => $subject,
                'content' => $message,
                'showGreeting' => false,
            ]));
        }
    }

    private function sendSMSNotification(NotifyPerson $person, string $message): void
    {
        if (!empty($person->phone)) {
            Http::get("https://bulksmsbd.net/api/smsapi", [
                'api_key' => 'dBG4rYOLWW28f3ip15yW',
                'type' => 'text',
                'number' => $person->phone,
                'senderid' => '8809617624082',
                'message' => $message
            ]);
        }
    }

    private function sendMsgLoanUser(): void
    {
        $loans = Loan::with('loanUser', 'repayments')
            ->orderBy('date')
            ->get()
            ->filter(fn ($loan) => $loan->loanUser && $loan->dueAmount() > 0);

        $groupedByUser = $loans->groupBy('loan_user_id');

        foreach ($groupedByUser as $userLoans) {
            $loanUser = optional($userLoans->first())->loanUser;

            if (!$loanUser) {
                continue;
            }

            $takenLoan = (float) $userLoans->where('type', 'taken')->sum('amount');
            $givenLoan = (float) $userLoans->where('type', 'given')->sum('amount');

            $takenRepayment = (float) $userLoans->where('type', 'taken')->sum(function ($loan) {
                return $loan->repayments->sum('amount');
            });

            $givenRepayment = (float) $userLoans->where('type', 'given')->sum(function ($loan) {
                return $loan->repayments->sum('amount');
            });

            $takenDue = $takenLoan - $takenRepayment;
            $givenDue = $givenLoan - $givenRepayment;

            $historyRows = '';

            foreach ($userLoans as $loan) {
                $typeLabel = $loan->type === 'taken' ? 'নেওয়া' : 'দেওয়া';

                $historyRows .= '<tr>'
                    . '<td>' . e($loan->date) . '</td>'
                    . '<td>' . e($typeLabel) . '</td>'
                    . '<td style="text-align:right;">' . number_format((float) $loan->amount, 2) . '</td>'
                    . '<td>' . e($loan->note ?: '-') . '</td>'
                    . '</tr>';

                foreach ($loan->repayments->sortBy('date') as $repayment) {
                    $historyRows .= '<tr>'
                        . '<td>' . e($repayment->date) . '</td>'
                        . '<td>পরিশোধ (' . e($typeLabel) . ')</td>'
                        . '<td style="text-align:right;">' . number_format((float) $repayment->amount, 2) . '</td>'
                        . '<td>' . e($repayment->note ?: '-') . '</td>'
                        . '</tr>';
                }
            }

            $netMessage = $this->buildLoanNetMessage($takenDue, $givenDue);
            $smsMessage = $this->buildLoanSmsMessage($loanUser->name, $takenDue, $givenDue, $netMessage);
            $contactHtml = $this->buildContactPeopleHtml();

            if (!empty($loanUser->email)) {
                $emailMsg = '<div style="font-family:Arial,sans-serif;font-size:13px;line-height:1.5;">'
                    . '<p>প্রিয় ' . e($loanUser->name) . ',</p>'
                    . '<p>আপনার লেনদেনের ইতিহাস নিচে দেওয়া হলো:</p>'
                    . '<table style="width:100%;border-collapse:collapse;" border="1" cellpadding="6">'
                    . '<thead>'
                    . '<tr style="background:#f5f5f5;">'
                    . '<th style="text-align:left;">তারিখ</th>'
                    . '<th style="text-align:left;">ধরন</th>'
                    . '<th style="text-align:right;">পরিমাণ</th>'
                    . '<th style="text-align:left;">মন্তব্য</th>'
                    . '</tr>'
                    . '</thead>'
                    . '<tbody>' . $historyRows . '</tbody>'
                    . '</table>'
                    . '<p style="margin-top:12px;"><strong>মোট পরিশোধযোগ্য:</strong> ' . number_format($takenDue, 2) . ' টাকা</p>'
                    . '<p><strong>মোট প্রাপ্য:</strong> ' . number_format($givenDue, 2) . ' টাকা</p>'
                    . '<p><strong>' . e($netMessage) . '</strong></p>'
                    . '<p style="margin-top:14px;">আমি আর বেঁচে নেই। আমার ভুল-ত্রুটি ক্ষমা করে দেবেন, আমি আন্তরিকভাবে ক্ষমাপ্রার্থী।</p>'
                    . '<p>আমার অনুপস্থিতিতে আপনার পাওনা/দেনার বিষয়ে যোগাযোগের জন্য নিচের ব্যক্তিদের সাথে যোগাযোগ করুন:</p>'
                    . $contactHtml
                    . '<p>ধন্যবাদ।</p>'
                    . '</div>';

                \Mail::to($loanUser->email)->send(new \ME\Mail\NoticeMailLayout([
                    'title' => 'ঋণ লেনদেনের নোটিশ',
                    'content' => $emailMsg,
                    'showGreeting' => false,
                ]));
            }

            if (!empty($loanUser->phone)) {
                // Http::get("https://bulksmsbd.net/api/smsapi", [
                //     'api_key' => 'dBG4rYOLWW28f3ip15yW',
                //     'type' => 'text',
                //     'number' => $loanUser->phone,
                //     'senderid' => '8809617624082',
                //     'message' => $smsMessage,
                // ]);
            }
        }
    }

    private function buildLoanNetMessage(float $takenDue, float $givenDue): string
    {
        $takenVsGiven = $takenDue - $givenDue;
        $givenVsTaken = $givenDue - $takenDue;

        if ($takenVsGiven > 0) {
            return 'নিট পরিশোধযোগ্য ' . number_format($takenVsGiven, 2) . ' টাকা';
        }

        if ($givenVsTaken > 0) {
            return 'নিট প্রাপ্য ' . number_format($givenVsTaken, 2) . ' টাকা';
        }

        return 'নিট প্রাপ্য/পরিশোধযোগ্য 0.00 টাকা';
    }

    private function buildLoanSmsMessage(string $name, float $takenDue, float $givenDue, string $netMessage): string
    {
        $displayName = trim($name) !== '' ? $name : 'সম্মানিত গ্রাহক';

        return 'প্রিয় ' . $displayName . ', '
            . 'আপনার বর্তমান হিসাব: '
            . 'পরিশোধযোগ্য ' . number_format($takenDue, 2) . ' টাকা, '
            . 'প্রাপ্য ' . number_format($givenDue, 2) . ' টাকা। '
            . $netMessage . '। '
            . 'বিস্তারিত মেইলে প্রেরণ করা হয়েছে।';
    }

    private function buildContactPeopleHtml(): string
    {
        $contactPeople = [
            [
                'name' => 'Noor Alahi Khan',
                'relation' => 'Father',
                'phone' => '01XXXXXXXXX',
                'email' => 'person1@example.com',
                'address' => 'ঠিকানা ১',
            ],
            [
                'name' => 'ব্যক্তির নাম ২',
                'relation' => 'সম্পর্ক ২',
                'phone' => '01XXXXXXXXX',
                'email' => 'person2@example.com',
                'address' => 'ঠিকানা ২',
            ],
        ];

        $cards = '';

        foreach ($contactPeople as $person) {
            $cards .= '<div style="border:1px solid #e2e2e2;border-radius:8px;padding:10px;margin-top:8px;background:#fafafa;">'
                . '<p style="margin:0 0 6px 0;"><strong>ব্যক্তির নাম:</strong> ' . e($person['name'] ?? 'প্রযোজ্য নয়') . '</p>'
                . '<p style="margin:0 0 6px 0;"><strong>সম্পর্ক:</strong> ' . e($person['relation'] ?? 'প্রযোজ্য নয়') . '</p>'
                . '<p style="margin:0 0 6px 0;"><strong>ফোন:</strong> ' . e($person['phone'] ?? 'প্রযোজ্য নয়') . '</p>'
                . '<p style="margin:0 0 6px 0;"><strong>ইমেইল:</strong> ' . e($person['email'] ?? 'প্রযোজ্য নয়') . '</p>'
                . '<p style="margin:0;"><strong>ঠিকানা:</strong> ' . e($person['address'] ?? 'প্রযোজ্য নয়') . '</p>'
                . '</div>';
        }

        return '<div style="margin-top:8px;">' . $cards . '</div>';
    }

}
