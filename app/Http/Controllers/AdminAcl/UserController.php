<?php
/*
** Author : Sands, muhammad.arisandi@mncgroup.com
** Date   : November 2020
*/
namespace App\Http\Controllers\AdminAcl;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\User;
use Auth;
use App\Http\Library\Serializer;
use App\Unit;

class UserController extends Controller
{
    private function getValidator($method, Request $request,$id=null)
    {
        if($method == 'userStore'){
            return Validator::make($request->all(),[
                'name' => 'required|string|max:255',
                'user_email' => 'required|unique:users',
                'is_active' => 'required',
                'username' => 'required',
                'unit_id' => 'required',
                'is_mcr' => 'required'
            ]);
        }else if ($method =='userUpdate'){
            return Validator::make($request->all(),[
                'name' => 'required|string|max:255',
                'user_email' => 'required|unique:users,user_email,'.$id,
                'is_active' => 'required',
                'username' => 'required',
                'unit_id' => 'required',
                'is_mcr' => 'required'
            ]);
        }

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $statusses = User::$statusses;
        $users = User::with('category', 'unit')->get();
        $units = Unit::all();
        $categories = Category::all();

        return view('acl.user.index', compact('users', 'statusses', 'units', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function userStore(Request $request)
    {
        $validator = $this->getValidator('userStore',$request);

        if($validator->fails()){
            $messages = implode(',', array_column($validator->messages()->toArray(), 0));
            return redirect()->back()->with('msg', $messages);
        }else{
            DB::beginTransaction();
            try {
                $messages =  "Gagal Simpan Data";
                $user = new User;
                $req = $request->all();
                $user->fill($req);

                if($user->save()){
                    $messages = "Berhasil Simpan Data";
                    DB::commit();
                }
            } catch (\Exception $e) {
                DB::rollback();
                $messages = $e;
                //throw $e;
            } catch (\Throwable $e) {
                DB::rollback();
                $messages = $e;
                //throw $e;
            }

            return redirect()->back()->with('msg', $messages);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userShow(User $user)
    {
        $statusses = User::$statusses;
        $units = Unit::all();
        $categories = Category::all();
        return view('acl.user._show', compact('user', 'statusses', 'units', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userUpdate(Request $request, User $user)
    {
        $id = !empty($user->id) ? $user->id : null;
        $validator = $this->getValidator('userUpdate',$request, $id);
        $messages = "";

        if($validator->fails()){
            $messages = implode(',',array_column($validator->messages()->toArray(),0));
        }else{
            DB::beginTransaction();
            try {
                if($user === null){
                    $messages = "User does not exist";
                }else{
                    $data = $request->all();
                    $user->fill($data);

                    if($user->save()){
                        $messages = "Berhasil Simpan Data";
                        DB::commit();
                    }
                }
            } catch (\Exception $e) {
                DB::rollback();
                $messages = $e;
                //throw $e;
            } catch (\Throwable $e) {
                DB::rollback();
                $messages = $e;
                //throw $e;
            }
        }

        return redirect()->back()->with('msg', $messages);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userDelete(User $user)
    {
        $statusCode = Response::HTTP_BAD_REQUEST;
        $message = '';
        $status = false;

        if($user === null){
            $message = "User does not exist";
            $resource = Serializer::serializeItem($status, $message);
            return response()->json($resource,$statusCode);
        }

        DB::beginTransaction();
        try {
            $message = "Delete Data Tidak Berhasil, Data tidak ditemukan";
            if($user->delete()){
                DB::commit();
                $message = "Delete Data Berhasil";
                $statusCode = Response::HTTP_OK;
                $status = true;
            }
        } catch (\Exception $e) {
            DB::rollback();
            $message = $e;
            //throw $e;
        } catch (\Throwable $e) {
            DB::rollback();
            $message = $e;
            //throw $e;
        }
        $resource = Serializer::serializeItem($status, $message);
        return response()->json($resource, $statusCode);
    }



}