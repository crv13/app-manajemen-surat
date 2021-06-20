<?php

namespace App\Http\Controllers;

use App\SuratKeluar;
use Illuminate\Http\Request;
use PDF;
use Illuminate\Support\Facades\Auth;
use Excel;
use App\Instansi;

class SuratKeluarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data_suratkeluar = SuratKeluar::all();
        return view('suratkeluar.index', ['data_suratkeluar' => $data_suratkeluar]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data_klasifikasi = Klasifikasi::all();
        return view('suratkeluar/create',['data_klasifikasi'=> $data_klasifikasi]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'filekeluar' => 'mimes:jpg,jpeg,png,doc,docx,pdf',
            'no_surat' => 'unique:suratkeluar|min:5',
            'isi' => 'min:5',
            'keterangan' => 'min:5',
       ]);
       $suratkeluar = new SuratKeluar();
       $suratkeluar->no_surat     = $request->input('no_surat');
       $suratkeluar->tujuan_surat = $request->input('tujuan_surat');
       $suratkeluar->isi          = $request->input('isi');
       $suratkeluar->kode         = $request->input('kode');
       $suratkeluar->tgl_surat    = $request->input('tgl_surat');
       $suratkeluar->tgl_catat    = $request->input('tgl_catat');
       $suratkeluar->keterangan   = $request->input('keterangan');
       $file                      = $request->file('filekeluar');
       $fileName   = 'suratKeluar-'. $file->getClientOriginalName();
       $file->move('datasuratkeluar/', $fileName);
       $suratkeluar->users_id = Auth::id();
       $suratkeluar->filekeluar  = $fileName;
       $suratkeluar->save();
       return redirect('/suratkeluar/index')->with("sukses", "Data Surat Keluar Berhasil Ditambahkan");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id_suratkeluar)
    {
        $suratkeluar = SuratKeluar::find($id_suratkeluar);
        return view('suratkeluar/show',['suratkeluar'=>$suratkeluar]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id_suratkeluar)
    {
        $data_klasifikasi = Klasifikasi::all();
        $suratkeluar = SuratKeluar::find($id_suratkeluar);
        return view('suratkeluar/edit',['suratkeluar'=>$suratkeluar],['data_klasifikasi'=>$data_klasifikasi]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id_suratkeluar)
    {
        $request->validate([
            'filekeluar' => 'mimes:jpg,jpeg,png,doc,docx,pdf',
            'no_surat' => 'min:5',
            'isi' => 'min:5',
            'keterangan' => 'min:5',
        ]);
        $suratkeluar = SuratKeluar::find($id_suratkeluar);
        $suratkeluar->update($request->all());
        //Untuk Update File
        if($request->hasFile('filekeluar')){
            $path = "datasuratkeluar";
            File::delete($path. $suratkeluar->filekeluar);
            $request->file('filekeluar')->move('datasuratkeluar/', 'suratKeluar-'. $request->file('filekeluar')->getClientOriginalName());
            $suratkeluar->filekeluar = 'suratKeluar-'. $request->file('filekeluar')->getClientOriginalName();
            $suratkeluar->save();
        }
        return redirect('suratkeluar/index') ->with('sukses','Data Surat Keluar Berhasil Diedit');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_suratkeluar)
    {
        $suratkeluar = SuratKeluar::find($id_suratkeluar);
        $suratkeluar->delete();

        $path = "datasuratkeluar/";
        File::delete($path . $suratkeluar->filekeluar);
        return redirect('suratkeluar/index') ->with('sukses','Data Surat Keluar Berhasil Dihapus');
    }

    //Function Untuk Agenda Surat keluar
    public function agenda(Request $request)
    {
       $data_suratkeluar = \App\SuratKeluar::all();
       return view('suratkeluar.agenda', compact('data_suratkeluar'));
    }
    public function agendakeluarcetak_pdf()
    {
       $inst = Instansi::first();
        $suratkeluar = SuratKeluar::all();
        $pdf = PDF::loadview('suratkeluar.cetakagendaPDF', compact('inst','suratkeluar'));
        return $pdf->stream();
    }

   public function galeri(Request $request)
   {
       $data_suratkeluar = \App\SuratKeluar::all();
       return view('suratkeluar.galeri',['data_suratkeluar'=> $data_suratkeluar]);
   }

    //function untuk download file
    public function downfunc(){

    $downloads=DB::table('suratkeluar')->get();
    return view('suratkeluar.show',compact('downloads'));
    }

    public function agendakeluardownload_excel(){
        $suratkeluar = \App\SuratKeluar::select('id', 'isi', 'tujuan_surat', 'kode', 'no_surat', 'tgl_surat', 'tgl_catat', 'keterangan')->get();
        return Excel::create('Agenda_Surat_Keluar', function($excel) use ($suratkeluar){
            $excel->sheet('Agenda_Surat_Keluar',function($sheet) use ($suratkeluar){
                $sheet->fromArray($suratkeluar);
            });
        })->download('xls');
    }
}
