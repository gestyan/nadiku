@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="['Template Surat']">
    </x-breadcrumb>

    <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
        <div class="col">
            <x-template-card
                :title="'SURAT TUGAS'"
                :filename="'surat_tugas.docx'"
                :extension="'docx'"
                :path="asset('storage/template_letter/surat_tugas.docx')"
            />
        </div>
      	<div class="col">
            <x-template-card
                :title="'CONTOH SURAT KELUAR'"
                :filename="'surat_keluar.docx'"
                :extension="'docx'"
                :path="asset('storage/template_letter/contoh surat keluar Pusdiklat.docx')"
            />
        </div>
      	<div class="col">
            <x-template-card
                :title="'CONTOH SURAT KELUAR (UNDANGAN)'"
                :filename="'surat_keluar_undangan.docx'"
                :extension="'docx'"
                :path="asset('storage/template_letter/contoh surat keluar (undangan) Pusdiklat.docx')"
            />
        </div>
    </div>



@endsection
