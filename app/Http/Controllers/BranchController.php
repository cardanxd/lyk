<?php

namespace App\Http\Controllers;

use App\Models\Branch;

class BranchController extends Controller
{
    public function __invoke()
    {
        return view('pages.branches', ['branches' => Branch::active()->get()]);
    }
}
