<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailProviderController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:System Admin');
    }

    /**
     * Show the form for creating a new email provider.
     */
    public function create()
    {
        return view('admin.settings.communication.email-providers.create');
    }
}

