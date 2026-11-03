<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class TutorialController extends Controller
{
    /**
     * Tampilkan halaman panduan dan tutorial penggunaan sistem secara lengkap.
     */
    public function index(): View
    {
        return view('tutorial');
    }
}
