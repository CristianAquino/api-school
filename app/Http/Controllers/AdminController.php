<?php

namespace App\Http\Controllers;

use App\DTOs\UserDTO;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $response = Gate::inspect('viewAny', Admin::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $code = strtolower($request->query('code'));
        $admins = Admin::query()
            ->when($code, function ($query) use ($code) {
                $query->whereHas('user', function ($query) use ($code) {
                    $query->whereRaw('LOWER(code) LIKE ?', "%$code%");
                });
            })
            ->paginate(10);

        $adminsDTO = UserDTO::fromPagination($admins);
        return response()->json($adminsDTO, Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource remove soft.
     */
    public function softList(Request $request)
    {
        //
        $response = Gate::inspect('softList', Admin::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $code = strtolower($request->query('code'));
        $deletedAdmins = Admin::onlyTrashed()
            ->when($code, function ($query) use ($code) {
                $query->whereHas('user', function ($query) use ($code) {
                    $query->whereRaw('LOWER(code) LIKE ?', "%$code%");
                });
            })
            ->paginate(10);

        $deletedAdminsDTO = UserDTO::fromPagination($deletedAdmins);
        return response()->json($deletedAdminsDTO, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $response = Gate::inspect('store', Admin::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $i = Admin::count();

        if ($i == 0) {
            $code = 'AD' . (int)date('Y') * 10000 + $i;
        } else {
            $c = User::where('userable_type', Admin::class)
                ->latest("id")
                ->first()->code;
            $i = (int)substr($c, 2) + 1;
            $code = 'AD' . $i;
        }

        $admin = Admin::create([
            'role' => User::ROLE_SUPERADMIN
        ]);

        $new_datos = $request->validated_data;
        $new_datos['code'] = $code;
        $new_datos['password'] = $code . $new_datos['dni'];

        $admin->user()->create($new_datos);

        if (is_null($admin->user)) {
            $admin->delete();
            return response()->json([
                "message" => "The registration for admin $request->first_name $request->second_name $request->name has not been created successfully"
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json([
            "message" => "admin $request->first_name $request->second_name $request->name has been added successfully with code $code"
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function me()
    {
        //
        $me = Auth::user()->userable_id;
        $user = Admin::where('id', $me)->first();

        if (is_null($user)) {
            return response()->json([
                "message" => "You do not have the role allowed to perform this action"
            ], Response::HTTP_NOT_FOUND);
        }

        $teacherDTO = UserDTO::fromPartialModel($user);
        return response()->json($teacherDTO, Response::HTTP_OK);
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
        $response = Gate::inspect('update', Admin::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $admin->user()->update($request->validated_data);

        return response()->json(["message" => "The admin with code " . $admin->user->code . " has been successfully updated"], Response::HTTP_ACCEPTED);
    }

    /**
     * Remove soft the specified resource from storage.
     */
    public function softDestroy(Admin $admin)
    {
        //
        $response = Gate::inspect('softDestroy', Admin::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $admin->delete();
        return response()->json([
            "message" => "the admin with code $admin->code has been successfully deleted"
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        //
        $response = Gate::inspect('restore', Admin::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $admin = Admin::onlyTrashed()->find($id);

        if (is_null($admin)) {
            return response()->json([
                "message" => "the admin does not exist"
            ], Response::HTTP_BAD_REQUEST);
        }

        $admin->restore();
        return response()->json([
            "message" => "the admin with code " . $admin->user->code . " has been successfully restored"
        ], Response::HTTP_OK);
    }

    /**
     * Remove permanently the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        $response = Gate::inspect('destroy', Admin::class);

        if (!$response->allowed()) {
            return response()->json([
                "message" => $response->message()
            ], Response::HTTP_FORBIDDEN);
        }

        $admin = Admin::onlyTrashed()->find($id);
        if (is_null($admin)) {
            return response()->json([
                "message" => "the admin does not exist"
            ], Response::HTTP_BAD_REQUEST);
        }

        $code = $admin->user->code;

        $admin->forceDelete();
        User::where('userable_id', $admin->id)->delete();
        return response()->json([
            "message" => "the admin with code $code has been successfully deleted permanently"
        ], Response::HTTP_ACCEPTED);
    }
}
