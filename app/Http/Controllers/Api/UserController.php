<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\UserSignup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'user'
        ])
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'username' => ['required', 'string', 'unique:users,username'],
            'avatar' => ['dimensions:width=256,height=256'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'confirmed'],
        ]);

        if ($request->has('avatar')) {
            $data['avatar'] = $data['avatar']->store('avatars');
        }

        $user = new User($data);
        $user->registered_at = now();
        $user->email_verification_pin = sprintf("%06d", mt_rand(1, 999999));
        $user->save();

        Mail::to($user)->send(new UserSignup($user));

        return response(['message' => 'User successfully created!', 'data' => $user]);
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    public function verify(Request $request, User $user)
    {
        if ($user->hasVerifiedEmail()) {
            return response([
                'errors' => [
                    'user' => 'User already been verified!'
                ]
            ]);
        }

        $data = $request->validate([
            'pin' => ['required', 'string']
        ]);

        if ($user->email_verification_pin !== $data['pin']) {
            return response(
                [
                    'errors' => [
                        'pin' => 'Invalid pin!'
                    ]
                ]
            );
        }

        $user->markEmailAsVerified();

        return response(['message' => 'Successfully verified!']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
