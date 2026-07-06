<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminUsersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {
        $users = User::join('roles', 'roles.id', '=', 'users.role_id')
                       ->select('users.*', 'roles.role_name as roleName')->where('users.id', '!=', 1)->get();
        $users->map(function ($user) {
            if (!empty($user->email)) {
                $user->email = $this->shortEmail($user->email);
            }
        });
        return view('admin_users.index', compact(['users']));
    }
    public function create()
    {
        $roles = Role::all();
        return view('admin_users.create', compact(['roles']));
    }
    public function store(Request $request)
    {
        $name = $request->input('name');
        $password = $request->input('password');
        $email = $request->input('email');
        $role = $request->input('role');
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',

        ]);
        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return Redirect()->back()->with(['message' => $error]);
        }

        // Only the super administrator (user id 1) may assign the super-admin role.
        if ((int) $role === 1 && Auth::id() !== 1) {
            return Redirect()->back()->with(['message' => 'You are not allowed to assign the super administrator role.']);
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role_id' => $role,
        ]);

        return redirect('admin-users');

    }


    public function edit($id)
    {
        $user = User::join('roles', 'roles.id', '=', 'users.role_id')->select('users.*', 'roles.role_name as roleName')->find($id);
        $roles = Role::all();
        if (!empty($user->email)) {
            $user->email = $this->shortEmail($user->email);
        }
        return view('admin_users.edit', compact(['user', 'roles']));
    }

    public function update(Request $request, $id)
    {
        $name = $request->input('name');
        $password = $request->input('password');
        $old_password = $request->input('old_password');
        $email = $request->input('email');
        $role = ($id == 1) ? 1 : $request->input('role');

        $targetUser = User::find($id);
        if (!$targetUser) {
            return Redirect()->back()->with(['message' => 'User not found']);
        }
        // Only the super administrator (user id 1) may modify a super-admin account
        // or grant the super-admin role to anyone.
        if (Auth::id() !== 1) {
            if ((int) $id === 1 || (int) $targetUser->role_id === 1) {
                return Redirect()->back()->with(['message' => 'You are not allowed to modify a super administrator account.']);
            }
            if ((int) $role === 1) {
                return Redirect()->back()->with(['message' => 'You are not allowed to assign the super administrator role.']);
            }
        }

        if ($password == '') {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'email' => 'required|email'
            ]);
        } else {
            $user = User::find($id);
            if (password_verify($old_password, $user->password)) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required|max:255',
                    'password' => 'required|min:8',
                    'confirm_password' => 'required|same:password',
                    'email' => 'required|email'
                ]);

            } else {
                return Redirect()->back()->with(['message' => "Please enter correct old password"]);
            }

        }

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return Redirect()->back()->with(['message' => $error]);
        }

        $user = User::find($id);

        if ($user) {

            $user->name = $name;
            $user->email = $email;
            if ($password != '') {
                $user->password = Hash::make($password);
            }
            $user->role_id = $role;
            $user->save();
        }

        return redirect('admin-users');
    }
    public function delete($id)
    {
        $id = json_decode($id);

        $ids = is_array($id) ? $id : [$id];

        foreach ($ids as $delId) {
            // Never allow deleting the super administrator account.
            if ((int) $delId === 1) {
                continue;
            }
            $user = User::find($delId);
            if (!$user) {
                continue;
            }
            // Only the super administrator may delete another super-admin account.
            if ((int) $user->role_id === 1 && Auth::id() !== 1) {
                continue;
            }
            $user->delete();
        }

        return redirect()->back();
    }

    public function shortEmail($email, $mask = "**********")
    {
        $atposition = strrpos($email, "@");
        $name = substr($email, 0, $atposition);
        $domain = substr($email, $atposition);
        $shortname = substr($name, 0, 1);
        return $shortname . $mask . $domain;
    }

}
