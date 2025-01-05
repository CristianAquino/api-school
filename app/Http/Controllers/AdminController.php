<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $admins = Admin::with('user')->get();
        return response()->json($admins, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:64',
            'first_name' => 'nullable|string|max:32',
            'second_name' => 'nullable|string|max:32',
            'phone' => 'nullable|string|max:32',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string|max:128',
            'dni' => 'nullable|string|max:8|unique:users,dni',
            'email' => 'required|email|unique:users,email',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $i = Admin::count();
        $code = 'AD' . (int)date('Y') * 10000 + $i;

        $admin = Admin::create([
            'code_admin' => $code,
            'role' => User::ROLE_SUPERADMIN
        ]);

        $new_datos = $validate->validated();
        $new_datos['password'] = $code . '1234';

        $admin->user()->create($new_datos);

        if (is_null($admin->user)) {
            $admin->delete();
        }

        return response()->json(["message" => "admin $request->name has been added successfully with code $admin->code_admin"], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        //
        $admin->load('user');
        return response()->json($admin, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Admin $admin)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        //
        $admin->user()->delete();
        $admin->delete();

        return response()->json(["message" => "the admin with code $admin->code_admin has been successfully deleted"], Response::HTTP_ACCEPTED);
    }
}
