<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Auth;
use Redirect;
use App\Models\User;
use App\Models\Organization;
use App\Models\Session;
use App\Models\AvailabilityDate;
use App\Models\UnAvailabilityDate;
use App\Models\Coach;
use App\Models\Admin;
use Artisan;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Hash;
use DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function logout()
    {
          
        Auth::logout();
        $notification = array(
            'message'    => 'You have been logged out.',
            'alert-type' => 'success'
        );

        return redirect('/');
    }
}
