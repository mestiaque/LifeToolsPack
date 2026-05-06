<?php

namespace ME\EmCore\Http\Controllers;

use Illuminate\Http\Request;
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

        return redirect()->route('admin.notify-people.index')->with('success', __('Person created successfully.'));
    }

    public function update(Request $request, $id)
    {
        $person = NotifyPerson::findOrFail($id);
        $data = $this->validatedData($request);
        $person->update($data);

        return redirect()->route('admin.notify-people.index')->with('success', __('Person updated successfully.'));
    }

    public function destroy($id)
    {
        $person = NotifyPerson::findOrFail($id);
        $person->delete();

        return redirect()->route('admin.notify-people.index')->with('success', __('Person deleted successfully.'));
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

        if (!$is30DaysSinceLastLogin) {
            return response()->json([
                'success' => true,
                'message' => 'Last login is within 30 days, no notification sent.',
            ]);
        }

        $people = NotifyPerson::all();
        $sent = 0;
        $phoneTargets = [];

        foreach ($people as $person) {
            $types = is_array($person->types) ? $person->types : [];

            // 👉 LOAN (Type 1)
            if (in_array('1', $types, true)) {
                $loanData = $this->buildLoanData();
                $emailMsg = $this->formatLoanForEmail($person, $loanData);
                $this->sendNotification($person, 'Loan Info', $emailMsg);
                $smsMsg = $this->formatLoanForSMS($loanData);
                $this->sendSMSNotification($person, $smsMsg);
                $sent++;
            }

            // 👉 ACCOUNT (Type 2)
            if (in_array('2', $types, true)) {
                $accData = $this->buildAccountData($person);
                $accEmailMsg = $this->formatAccountForEmail($person, $accData);
                // $this->sendNotification($person, 'Account Info', $accEmailMsg);
                $accSmsMsg = $this->formatAccountForSMS($accData);
                $sent++;
            }

            if (!empty($person->phone)) {
                $phoneTargets[] = $person->phone;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification sent successfully.',
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
            return "Loan Info\n"
                . "User Name: {$person->name}\n"
                . "Phone: {$person->phone}\n"
                . "Email: {$person->email}\n"
                . "Address: {$person->address}\n"
                . "Amount: N/A\n"
                . "Type: N/A\n"
                . "Loan Date: N/A";
        }

        $direction = $loan->type === 'given' ? 'Receivable' : 'Payable';
        $loanUserName = optional($loan->loanUser)->name ?: $person->name;

        return "Loan Info\n"
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
                'type'       => $loan->type === 'given' ? 'Receivable' : 'Payable',
                'date'       => $loan->date,
                'note'       => $loan->note,
            ];
        })->values();
    }

    private function formatLoanForEmail($person, $loans): string
    {
        if ($loans->isEmpty()) {
            return "Loan Info\nNo loan data found.";
        }

        $message = '<div style="font-family:Arial, sans-serif; font-size:13px;">';
        $message .= '<h3 style="margin-bottom:10px;">Payable & Receivable Ledger</h3>';

        foreach ($loans as $loan) {
            $bg = $loan['type'] === 'Receivable' ? '#73ff0018' : '#ff000018';
            $message .= '
            <div style="border:1px solid #ddd; padding:10px; margin-bottom:10px; background-color:' . $bg . ';">
                <strong>Name:</strong> ' . ($loan['user_name'] ?? 'N/A') . '<br>
                <strong>Phone:</strong> ' . ($loan['phone'] ?? 'N/A') . ' |
                <strong>Email:</strong> ' . ($loan['email'] ?? 'N/A') . '<br>
                <strong>Address:</strong> ' . ($loan['address'] ?? 'N/A') . '<br>
                <strong>Due:</strong> <b>' . number_format($loan['due_amount'], 2) . '</b><br>
                <strong>Type:</strong> ' . ($loan['type'] ?? 'N/A') . ' |
                <strong>Date:</strong> ' . ($loan['date'] ?? 'N/A') . '
                <br><strong>Note:</strong> ' . ($loan['note'] ?? 'N/A') . '
            </div>';
        }

        $message .= '</div>';

        return $message;
    }

    private function formatLoanForSMS($loans): string
    {
        if ($loans->isEmpty()) {
            return 'No loan data.';
        }

        $grouped = [];
        foreach ($loans as $loan) {
            $name = $loan['user_name'];
            if (!isset($grouped[$name])) {
                $grouped[$name] = ['Payable' => 0.0, 'Receivable' => 0.0];
            }
            $grouped[$name][$loan['type']] += $loan['due_amount'];
        }

        $lines = [];
        foreach ($grouped as $name => $totals) {
            $pay  = $totals['Payable'];
            $recv = $totals['Receivable'];
            $net  = $recv - $pay;

            $parts = [];
            if ($pay > 0)  { $parts[] = 'Pay ' . number_format($pay, 2); }
            if ($recv > 0) { $parts[] = 'Recv ' . number_format($recv, 2); }

            if ($net > 0) {
                $parts[] = 'Net Recv ' . number_format($net, 2);
            } elseif ($net < 0) {
                $parts[] = 'Net Pay ' . number_format(abs($net), 2);
            } else {
                $parts[] = 'Net 0';
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
        return "Account Info\n"
            . "------------------\n"
            . "Name: {$accData['name']}\n"
            . "Phone: {$accData['phone']}\n"
            . "Email: {$accData['email']}\n"
            . "Address: {$accData['address']}";
    }

    private function formatAccountForSMS(array $accData): string
    {
        return "Account: {$accData['name']}, Ph: {$accData['phone']}";
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

}
