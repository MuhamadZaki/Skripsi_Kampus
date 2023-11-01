<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Validator, Storage};
use App\User;
use App\Model\Judul;
use App\Model\Divisi;
use App\Model\Surat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MailController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (Auth::user()->role == 'Super Admin') {
            // Super Admin Bisa Melihat Semua Surat
            $surats = Surat::with(['to_user', 'divisi', 'judul'])->latest()->get();
        } else {
            // User Dapat Melihat Surat Sesuai Divisinya,
            // Melihat Surat Yang Dikirim Ke User Lain
            // Dan Melihat Surat Dari User Lain
            $surats = Surat::with(['to_user', 'divisi', 'judul'])
                            ->where('id_divisi', Auth::user()->id_divisi)
                            ->orWhere('id_pengirim', Auth::user()->id)
                            ->orWhere('id_penerima', Auth::user()->id)
                            ->latest()->get();
        }

        return view('mail', [
            'surats' => $surats,
            'juduls' => Judul::all(),
            'divisis' => Divisi::all()
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_penerima' => [
                'required_without:id_divisi',
                'string',
                Rule::exists('users', 'email')->where(function ($query) {
                    $query->where('role', 'User');
                })
            ],
            'id_divisi' => [
                'required_without:email_penerima',
                'integer',
                'exists:divisi,id'
            ],
            'id_judul' => ['required', 'integer', 'exists:judul,id'],
            'perihal' => ['required', 'string'],
            'attachment' => ['file', 'mimes:pdf,doc,docx'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'code' => 400], 400);
        }

        $fields = $request->except(['attachment']);
        $fields['id_pengirim'] = Auth::user()->id;

        if ($request->filled('email_penerima')) {
            $id_penerima = User::where('email', $request->input('email_penerima'))
                                ->value('id');
            $fields['id_penerima'] = $id_penerima;
        }

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = $file->getClientOriginalName();

            // Simpan File Ke Directory (generate a unique ID for file name)
            $path = Storage::disk('public')->putFile('pdf', $file);

            $fields['filename'] = $filename;
            $fields['path'] = $path;
        }

        $surat = Surat::create($fields);

        return response()->json([
            'message' => 'Berhasil Mengirim Surat',
            'code' => 201,
            'data' => $surat
        ], 201);
    }

    public function destroy($mailID)
    {
        $surat = Surat::where('id', $mailID)->first();

        Storage::disk('public')->delete($surat->path);

        $surat->delete();
    }

    /**
     * Jika User Download File PDF
     * Ubah Status Surat Menjadi Sudah Dibaca
     */
    public function read($mailID)
    {
        if (Auth::user()->role == 'User') {
            $surat = Surat::where('id', $mailID)->first();
            $surat->status = 'Sudah Dibaca';
            $surat->save();
        }
    }
}
