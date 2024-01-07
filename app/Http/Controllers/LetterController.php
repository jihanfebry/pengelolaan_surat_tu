<?php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Models\Letter;
use App\Models\Letter_type;
use App\Models\User;
use Illuminate\Http\Request;
use Excel;
use PDF;
use App\Exports\AllLetterExport;

class LetterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $letters = Letter::all();

        return view('surat.index', compact('letters'));
    }

    public function getLetters()
    {
        // Mengambil data surat dari database
        $letters = Letter::orderBy('letter_type_id', 'ASC')->simplePaginate(5);
        $letterTypes = Letter_type::get(); // Mengasumsikan bahwa LetterType adalah model untuk tabel letter_types
        $results = Result::get();

        // Inisialisasi array untuk menyimpan jumlah surat untuk setiap letter_type_id
        $letterCounts = [];

        foreach ($letters as $letter) {
            // Parse kolom recipients (asumsi dalam bentuk array)
            $recipientId = json_decode($letter->recipients, true);

            $letterTypeId = Letter_type::find($letter->letter_type_id);

            // Tambahkan data pengguna ke dalam model surat
            $letter->letterTypeId = $letterTypeId;

            // Ambil data pengguna berdasarkan ID
            $recipients = User::whereIn('id', $recipientId)->get();

            // Tambahkan data pengguna ke dalam model surat
            $letter->recipientsData = $recipients;

            // Ambil data pengguna notulis
            $notulisUser = User::find($letter->notulis);

            // Tambahkan data pengguna notulis ke dalam model surat
            $letter->notulisUserData = $notulisUser;

            // Hitung jumlah surat untuk setiap letter_type_id
            if (!isset($letterCounts[$letter->letter_type_id])) {
                $letterCounts[$letter->letter_type_id] = 1;
            } else {
                $letterCounts[$letter->letter_type_id]++;
            }
        }   

        return view('result.index', compact('letters', 'results', 'letterTypes', 'letterCounts'));
    }




    public function searchLetters(Request $request)
    {
        $keyword = $request->input('name');
        $letters = Letter::where('letter_perihal', 'like', "%$keyword%")->orderBy('letter_type_id', 'ASC')->simplePaginate(5);
        $results = Result::get();

        $recipientsArray = [];

        $users = []; 

        foreach ($letters as $letter) {
            $recipientsArray[$letter->id] = explode(' ', $letter->recipients);

            $user = User::find($letter->notulis);

            $users[$letter->id] = $user;
        }

        return view('letter.letters.index', compact('letters', 'recipientsArray', 'users', 'results'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function createLetters()
    {
        $classificate = Letter_type::get();
        $user = User::where('role', 'guru')->get();
        
        return view('dataSurat.create.create', compact('user', 'classificate'));
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'letter_type_id' => 'required',
            'letter_perihal' => 'required',
            'recipients' => 'required|array',
            'content' => 'required',
            'notulis' => 'required'
        ]);
       
        Letter::create([
            'letter_type_id' => $request->letter_type_id,
            'letter_perihal' => $request->letter_perihal,
            'recipients' => json_encode($request->recipients), // Simpan sebagai JSON
            'content' => $request->content,
            'attachment' => $request->attachment,
            'notulis' => $request->notulis
        ]);

        return redirect()->route('dataSurat.data')->with('success', 'Berhasil Menambahkan Surat Baru!');
    }

    public function downloadPDF($id) {
        set_time_limit(300); // Set batas waktu menjadi 5 menit

        // get data yang akan ditampilkan di pdf
        $letter = Letter::find($id);
        $name = $letter->letter_perihal;

        // Inisialisasi array untuk menyimpan penerima berdasarkan ID surat
        $recipientsArray = [];

        // Memisahkan penerima pada setiap surat
        $recipientsArray[$letter->id] = explode(' ', $letter->recipients);
    
        // lokasi dan nama blade yang akan di-download ke pdf serta data yang akan ditampilkan
        $pdf = PDF::loadView('dataSurat.download', compact('letter', 'recipientsArray'));
    
        // ketika di-download, nama file nya apa
        return $pdf->download($name . '.pdf');
    }
    

    public function downloadExcel(){
        $file_name = 'Klasifikasi Surat.xlsx';
        return Excel::download(new AllLetterExport, $file_name);
    }
    


    /**
     * Display the specified resource.
     */
    public function show(letter $letter, $id)
    {
        $letterType = Letter_type::get();
        $letter = Letter::find($id);
        $user = User::where('role', 'guru')->get();

        // Inisialisasi array untuk menyimpan penerima berdasarkan ID surat
        $recipientsArray = [];

    
        // Memisahkan penerima pada setiap surat
        $recipientsArray[$letter->id] = json_decode($letter->recipients, true);
        

        return view('dataSurat.result', compact('user', 'letter', 'letterType', 'recipientsArray'));
    }


    public function showw(letter $letter, $id)
    {
        $letterType = Letter_type::get();
        $letter = Letter::find($id);
        $user = User::where('role', 'guru')->get();

        // Inisialisasi array untuk menyimpan penerima berdasarkan ID surat
        $recipientsArray = [];

    
        // Memisahkan penerima pada setiap surat
        $recipientsArray[$letter->id] = json_decode($letter->recipients, true);
        

        return view('result.index', compact('user', 'letter', 'letterType', 'recipientsArray'));
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $letterType = Letter_type::get();
        $letters = Letter::find($id);
        $user = User::where('role', 'guru')->get();

        return view('dataSurat.edit', compact('user', 'letters', 'letterType'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, letter $letter, $id)
    {
        $request->validate([
            'letter_type_id' => 'required',
            'letter_perihal' => 'required',
            'recipients' => 'required|array',
            'content' => 'required',
            'notulis' => 'required'
        ]);
       
        Letter::where('id', $id)->update([
            'letter_type_id' => $request->letter_type_id,
            'letter_perihal' => $request->letter_perihal,
            'recipients' => json_encode($request->recipients), // Simpan sebagai JSON
            'content' => $request->content,
            'attachment' => $request->attachment,
            'notulis' => $request->notulis
        ]);

        return redirect()->route('dataSurat.data')->with('success', 'Berhasil Mengubah Data Surat!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // cari dan hapus data
        Letter::where('id', $id)->delete();
        return redirect()->back()->with('delete', 'Berhasil Menghapus Data Surat');
    }
}
