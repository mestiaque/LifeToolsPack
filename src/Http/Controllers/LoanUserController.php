<?php

namespace ME\EmCore\Http\Controllers;

use ME\EmCore\Models\LoanUser;
use Illuminate\Http\Request;
use ME\Http\Controllers\Controller;
use App\Http\Middleware\AuthorizationMiddleware;

class LoanUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('authorization:loan_user.show')->only(['index']);
        $this->middleware('authorization:loan_user.store')->only(['store', 'update']);
        $this->middleware('authorization:loan_user.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = LoanUser::query();

        if ($request->name) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        $loanUsers = $query->get();

        return view('em_core::loan_users.index', compact('loanUsers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'required|boolean'
        ]);
        LoanUser::create($request->only('name', 'is_active'));
        return redirect()->route('admin.loan-users.index')->with('success', __('Loan user created successfully.'));
    }

    public function update(Request $request, $id)
    {
        $loanUser = LoanUser::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'required|boolean'
        ]);
        $loanUser->update($request->only('name', 'is_active'));
        return redirect()->route('admin.loan-users.index')->with('success', __('Loan user updated successfully.'));
    }

    public function destroy($id)
    {
        LoanUser::findOrFail($id)->delete();
        return redirect()->route('admin.loan-users.index')->with('success', __('Loan user deleted successfully.'));
    }
}

