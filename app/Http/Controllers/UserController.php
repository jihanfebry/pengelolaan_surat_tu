<?php

namespace App\Http\Controllers;

use App\Models\Letter_type;
use App\Models\Letter;
use App\Models\Result;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $usersStaff = User::where('role', 'staff')->count();
    //     $usersGuru = User::where('role', 'guru')->count();
    //     $allClassificate = Letter_type::count();
    //     $allLetters = Letter::count();
    //     return view('home', compact('usersGuru','usersStaff', 'allClassificate', 'allLetters'));
    // }
    

    public function getDataGuru()
    {
        $users = User::where('role', 'guru')->orderBy('name', 'ASC')->simplePaginate(5);
        return view('user.guru.index', compact('users'));
    }

    public function getDataStaff()
    {
        $users = User::where('role', 'staff')->orderBy('name', 'ASC')->simplePaginate(5);
        return view('user.staff.index', compact('users'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function createGuru()
    {
        return view('user.guru.create');
    }

    public function createStaff()
    {
        return view('user.staff.create');
    }

    public function searchGuru(Request $request)
    {
        $keyword = $request->input('name');
        $users = User::where('name', 'like', "%$keyword%")->where('role', 'guru')->orderBy('name', 'ASC')->simplePaginate(5);

        return view('user.guru.index', compact('users'));
    }

    public function searchStaff(Request $request)
    {
        $keyword = $request->input('name');
        $users = User::where('name', 'like', "%$keyword%")->where('role', 'staff')->orderBy('name', 'ASC')->simplePaginate(5);

        return view('user.staff.index', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|min:5',
            'role' => 'required'
        ]);

        // Ambil tiga karakter pertama dari nama dan email
        $namaUser = substr($request->name, 0, 3);
        $emailUser = substr($request->email, 0, 3);

        // Gabungkan tiga karakter pertama dari nama dan email sebagai password default
        $defaultPassword = Hash::make($namaUser . $emailUser);

        // Buat pengguna baru dengan data yang valid
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => $defaultPassword
        ]);
        if ($request->role == 'staff') {
            return redirect()->route('user.staff.data')->with('success', 'Berhasil Menambahkan Data Pengguna!');
        }
        else {
            return redirect()->route('user.guru.data')->with('success', 'Berhasil Menambahkan Data Pengguna!');
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */

    public function edit($id)
    {
        $user = User::find($id);

        if ($user->role == 'staff') {
            return view('user.staff.edit', compact('user'));
        }
        else {
            return view('user.guru.edit', compact('user'));
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|min:5',
            'password' => 'required'
        ]);

        $hashedPassword = Hash::make($request->password);

        User::where('id', $id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $hashedPassword,
        ]);

        if ($request->role == 'guru') {
            return redirect()->back()->with('success', 'Berhasil Mengubah Data Pengguna!');
        }
        else {
            return redirect()->route('user.staff.data')->with('success', 'Berhasil Mengubah Data Pengguna!');
        }

    }

    public function loginAuth(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        $credentials = $request->only(['email', 'password']);
    
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
    
            if ($user->role == 'staff') {
                return redirect()->route('home.page')->with('success', 'Berhasil login!');
            } elseif ($user->role == 'guru') {
                return redirect()->route('home.page');
            }
        }
    
        return redirect()->back()->with('failed', 'Proses login gagal, silahkan coba kembali dengan data yang benar');
    }
    
    
        public function logout()
        {
            Auth::logout();
            return redirect()->route('login')->with('logout', 'anda telah logout!');
        }
    


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // cari dan hapus data
        User::where('id', $id)->delete();
        return redirect()->back()->with('delete', 'Berhasil Menghapus Data Pengguna');
    }
}
