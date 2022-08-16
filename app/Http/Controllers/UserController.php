<?php

namespace App\Http\Controllers;

use App\LogAuthUser;
use App\Traits\UserTrait;
use App\Unit;
use App\User;
use Illuminate\Http\Request;
use Auth;

class UserController extends Controller
{
    use UserTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }




    public function login()
    {
        return view('user.login');
    }


    public function login_process(Request $request)
    {
        if (!empty($request->username) && !empty($request->password))
        {
            // Begin LDAP
            // =============================================================
            $user = User::where('user_email', $request->username)
                ->where('is_active', User::USER_ACTIVE)
                ->first();

            if (!$user) {
                return redirect('/login')->with('msg', 'Anda tidak memiliki akses');
            }

            if ($this->check_ldap($user, $request->username, $request->password))
            {
                $unit_id_user = $user->unit_id;
                $unit_inews = Unit::UNIT_INEWS;

                session()->flush();
                if (count($user) > 0)
                {
                    session(['user_sess' => $request->username]);
                    session(['user_id_sess' => $user->id]);
                    session(['user_name_sess' => $user->name]);
                    session(['unit_id_sess' => $user->unit->id]);
                    session(['unit_name_sess' => $user->unit->unit_name]);
                    session(['category_id_sess' => $user->category->id]);
                    session(['category_name_sess' => $user->category->category_name]);
                    // session(['auto_delete_sess' => $user->is_auto_delete]);
                    Auth::login($user);

                    //Save logging session
                    $this->createUserAuthLogs(LogAuthUser::TYPE_LOGIN);

                    if($unit_id_user == $unit_inews){
                        return redirect('/newstickers-inews');
                    }else{
                        return redirect('/');
                    }
                }
                else
                {
                    return redirect('/login')->with('status', 'User atau Password Anda salah');
                }
            }
            else
            {
                return redirect('/login')->with('msg', 'User atau Password Anda salah');
            }
        }
        else
        {
            return redirect('/login')->with('msg', 'User dan Password tidak boleh kosong');
        }

        // End LDAP
        // ==========

    }



    private function check_ldap($user, $username, $password)
    {
        $ldap_ip = '172.18.8.10';
        //$ldap_ip = '172.18.20.6'; 
        $ldap_port = 389;
        $ldap_domain = "@mncgroup.com";

        $ds = ldap_connect($ldap_ip, $ldap_port) or die("Could not connect");

        $ldapbind = @ldap_bind($ds, $usercode, $password);

        if ($ds)
        {
            try
            {
                $usercode = $username . $ldap_domain;
                $ldapbind = @ldap_bind($ds, $usercode, $password);
                // $ldap_dn = 'OU=MNCMedia,DC=mncgroup,DC=com';
                // $results = @ldap_search($ds, $ldap_dn, "(mail=$usercode)");
                // $entries = @ldap_get_entries($ds, $results);
                if (@$ldapbind)
                {
                    // if($entries['count'] > 0)
                    // {
                    //     session(['user_name_sess' => $entries[0]['name'][0]]);
                    // }

                    return true;
                }
                else
                {
                    //non active user
                    $user->is_active = User::USER_NON_ACTIVE;
                    // $user->save();

                    return false;
                }
            }
            catch (Exception $e)
            {
                return false;
            }
        }
    }



    public function logout()
    {
        //Save logging session
        $this->createUserAuthLogs(LogAuthUser::TYPE_LOGOUT);

        session()->flush();
        return redirect('/login');
    }

}
