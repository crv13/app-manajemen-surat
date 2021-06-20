<?php

namespace App\Http\Controllers;

use App\SuratMasuk;
use App\Instansi;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;
use Excel;


class SuratMasukController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data_suratmasuk = SuratMasuk::all();
        return view('suratmasuk.index',['data_suratmasuk'=> $data_suratmasuk]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data_klasifikasi = \App\Klasifikasi::all();
        return view('suratmasuk/create', ['data_klasifikasi' => $data_klasifikasi]);
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
            'filemasuk'  => 'mimes:jpg,jpeg,png,doc,docx,pdf',
            'no_surat'   => 'unique:suratmasuk|min:5',
            'isi'        => 'min:5',
            'keterangan' => 'min:5',
        ]);
        $suratmasuk = new SuratMasuk();
        $suratmasuk->no_surat   = $request->input('no_surat');
        $suratmasuk->asal_surat = $request->input('asal_surat');
        $suratmasuk->isi        = $request->input('isi');
        $suratmasuk->kode       = $request->input('kode');
        $suratmasuk->tgl_surat  = $request->input('tgl_surat');
        $suratmasuk->tgl_terima = $request->input('tgl_terima');
        $suratmasuk->keterangan = $request->input('keterangan');
        $file                   = $request->file('filemasuk');
        $fileName   = 'suratMasuk-'. $file->getClientOriginalName();
        $file->move('datasuratmasuk/', $fileName);
        $suratmasuk->users_id = Auth::id();
        $suratmasuk->filemasuk  = $fileName;
        $suratmasuk->save();
        return redirect('/suratmasuk/index')->with("sukses", "Data Surat Masuk Berhasil Ditambahkan");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id_suratmasuk)
    {
        $suratmasuk = SuratMasuk::find($id_suratmasuk);
        return view('suratmasuk/show',['suratmasuk'=>$suratmasuk]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id_suratmasuk)
    {
        $data_klasifikasi = Klasifikasi::all();
        $suratmasuk = SuratMasuk::find($id_suratmasuk);
        return view('suratmasuk/edit',['suratmasuk'=>$suratmasuk],['data_klasifikasi'=>$data_klasifikasi]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id_suratmasuk)
    {
        $request->validate([
            'filemasuk' => 'mimes:jpg,jpeg,png,doc,docx,pdf',
            'no_surat' => 'min:5',
            'isi' => 'min:5',
            'keterangan' => 'min:5',
        ]);
        $suratmasuk = SuratMasuk::find($id_suratmasuk);
        $suratmasuk->update($request->all());
        //Untuk Update File
        if($request->hasFile('filemasuk')){
            $path = "datasuratmasuk";
            File::delete($path. $suratmasuk->filemasuk);
            $request->file('filemasuk')->move('datasuratmasuk/','suratMasuk-'. $request->file('filemasuk')->getClientOriginalName());
            $suratmasuk->filemasuk = 'suratMasuk-'. $request->file('filemasuk')->getClientOriginalName();
            $suratmasuk->save();
        }
        return redirect('suratmasuk/index') ->with('sukses','Data Surat Masuk Berhasil Diedit');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_suratmasuk)
    {
        $suratmasuk= SuratMasuk::find($id_suratmasuk);
        $suratmasuk->delete();

        $path = "datasuratmasuk/";
        File::delete($path . $suratmasuk->filemasuk);
        return redirect('suratmasuk/index')->with('sukses','Data Surat Masuk Berhasil Dihapus');
    }

    //Function Untuk Agenda Surat Masuk
    public function agenda(Request $request)
    {
        $data_suratmasuk = SuratMasuk::all();
        return view('suratmasuk.agenda', compact('data_suratmasuk'));
    }

    //Function Untuk Galeri Surat Masuk
    public function galeri(Request $request)
    {
        $data_suratmasuk = SuratMasuk::all();
       return view('suratmasuk.galeri',['data_suratmasuk'=> $data_suratmasuk]);
    }

    //Function Untuk Download Agenda Surat Masuk
    public function agendamasukcetak_pdf(Request $request)
    {
        $inst = Instansi::first();
        $suratmasuk = \App\SuratMasuk::all();
        $pdf = PDF::loadview('suratmasuk.cetakagendaPDF', compact('inst','suratmasuk','pdf'));
        return $pdf->stream();
    }

    //function untuk download file
    public function downfunc(){

        $downloads=DB::table('suratmasuk')->get();
        return view('suratmasuk.tampil',compact('downloads'));
    }

    public function agendamasukdownload_excel(){
        $suratmasuk = \App\SuratMasuk::select('id', 'isi', 'asal_surat', 'kode', 'no_surat', 'tgl_surat', 'tgl_terima', 'keterangan')->get();
        return Excel::create('Agenda_Surat_Masuk', function($excel) use ($suratmasuk){
            $excel->sheet('Agenda_Surat_Masuk',function($sheet) use ($suratmasuk){
                $sheet->fromArray($suratmasuk);
            });
        })->download('xls');
    }

}
