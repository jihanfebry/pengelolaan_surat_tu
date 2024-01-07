<?php

namespace App\Http\Controllers;

use App\Models\Letter_type;
use App\Models\Letter;
use Illuminate\Http\Request;
use App\Models\User;
use Excel;
use App\Exports\LetterTypeExport;

class LetterTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function getClassificate()
    {
        // Ambil semua jenis surat
        $letterTypes = Letter_type::orderBy('letter_code', 'ASC')->simplePaginate(5);
        $letters = Letter::get();

        // Inisialisasi array untuk menyimpan jumlah data letter untuk setiap jenis surat
        $letterCounts = [];

        // Loop melalui setiap jenis surat
        foreach ($letters as $letter) {
            // Hitung jumlah surat untuk setiap letter_type_id
            if (!isset($letterCounts[$letter->letter_type_id])) {
                $letterCounts[$letter->letter_type_id] = 1;
            } else {
                $letterCounts[$letter->letter_type_id]++;
            }
        }

        return view('klasifikasi.index', compact('letterTypes', 'letterCounts'));
    }

    
    public function searchClassificate(Request $request)
    {
        $keyword = $request->input('name');
        $letterTypes = Letter_type::where('name_type', 'like', "%$keyword%")->orderBy('name_type', 'ASC')->simplePaginate(5);
        $letters = Letter::get();

        // Inisialisasi array untuk menyimpan jumlah data letter untuk setiap jenis surat
        $letterCounts = [];

        // Loop melalui setiap jenis surat
        foreach ($letters as $letter) {
            // Hitung jumlah surat untuk setiap letter_type_id
            if (!isset($letterCounts[$letter->letter_type_id])) {
                $letterCounts[$letter->letter_type_id] = 1;
            } else {
                $letterCounts[$letter->letter_type_id]++;
            }
        }

        return view('klasifikasi.index', compact('letterTypes', 'letterCounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createClassificate()
    {
        return view('klasifikasi.create');
    }

    public function createLetters()
    {
        $user = User::where('role', 'guru')->orderBy('name', 'ASC');
        return view('klasifikasi.create', compact('user'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'letter_code' => 'required|min:6',
            'name_type' => 'required',
        ]);
    
        // Buat klasifikasi surat baru dengan data yang valid
        Letter_type::create([
            'letter_code' => $request->letter_code,
            'name_type' => $request->name_type
        ]);
        return redirect()->route('klasifikasi.data')->with('success', 'Berhasil Menambahkan Klasifikasi Surat Baru!');
    }

    public function downloadExcel()
    {
        // Lakukan perhitungan jumlah surat di sini dan simpan dalam $letterCounts
        $letterCounts = [];

        // ... (lakukan perhitungan sesuai kebutuhan)

        $file_name = 'Klasifikasi Surat.xlsx';

        // Buat instance dari LetterExport dan berikan $letterCounts
        $export = new LetterTypeExport($letterCounts);

        return Excel::download($export, $file_name);
    }

    

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $letterTypes = Letter_type::find($id);
        $dataLetter = Letter::where('letter_type_id', $id)->get();

        // Inisialisasi array untuk menyimpan jumlah data letter untuk setiap jenis surat
        $letterCounts = [];

        // Loop melalui setiap jenis surat
        foreach ($dataLetter as $letter) {
            // Parse kolom recipients (asumsi dalam bentuk array)
            $recipientId = json_decode($letter->recipients, true);

            // Ambil data pengguna berdasarkan ID
            $recipients = User::whereIn('id', $recipientId)->get();

            // Tambahkan data pengguna ke dalam model surat
            $letter->recipientsData = $recipients;

            // Hitung jumlah surat untuk setiap letter_type_id
            if (!isset($letterCounts[$letter->letter_type_id])) {
                $letterCounts[$letter->letter_type_id] = 1;
            } else {
                $letterCounts[$letter->letter_type_id]++;
            }
        }

        return view('klasifikasi.detail', compact('letterTypes','dataLetter', 'letterCounts'));
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $letter_type = Letter_type::find($id);
        $letter_code = $letter_type['letter_code'];
        return view('klasifikasi.edit', compact('letter_type', 'letter_code'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'letter_code' => 'required|min:6',
            'name_type' => 'required',
        ]);

        Letter_type::where('id', $id)->update([
            'letter_code' => $request->letter_code,
            'name_type' => $request->name_type
        ]);

        return redirect()->route('klasifikasi.data')->with('success', 'Berhasil Mengubah Data Klasifikasi Surat!');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // cari dan hapus data
        Letter_type::where('id', $id)->delete();
        return redirect()->back()->with('delete', 'Berhasil Menghapus Data Surat Klasifikasi');
    }
}
