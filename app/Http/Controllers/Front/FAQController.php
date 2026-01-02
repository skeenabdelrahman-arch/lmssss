<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FAQController extends Controller
{
    /**
     * عرض صفحة الأسئلة الشائعة
     */
    public function index()
    {
        return view('front.faq.index');
    }
}

