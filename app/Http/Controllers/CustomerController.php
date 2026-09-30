<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = auth()->user()->customers()->get();

        return view('customers.index', compact('customers'));
    }
}
