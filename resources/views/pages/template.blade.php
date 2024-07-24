@extends('layout.main')

@section('content')
    <x-breadcrumb :values="['Template Naskah Dinas']">
    </x-breadcrumb>

    <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
        <div class="col">
            <x-template-card :title="'Surat Edaran'" :filename="'SuratEdaran.docx'" :extension="'docx'" :path="asset('storage/template_letter/2-1_SuratEdaran.docx')" />
        </div>
        <div class="col">
            <x-template-card :title="'Surat Keputusan'" :filename="'SuratKeputusan.docx'" :extension="'docx'" :path="asset('storage/template_letter/3-1_SuratKeputusan.docx')" />
        </div>
        <div class="col">
            <x-template-card :title="'Surat Tugas/Perintah'" :filename="'SuratTugas_Perintah.docx'" :extension="'docx'" :path="asset('storage/template_letter/4-1-2_SuratTugasdanSuratPerintah.docx')" />
        </div>
        <div class="col">
            <x-template-card :title="'Nota Dinas'" :filename="'NotaDinas.docx'" :extension="'docx'" :path="asset('storage/template_letter/5_NotaDinas.docx')" />
        </div>
        <div class="col">
            <x-template-card :title="'Memorandum'" :filename="'Memorandum.docx'" :extension="'docx'" :path="asset('storage/template_letter/6_Memorandum.docx')" />
        </div>
        <div class="col">
            <x-template-card :title="'Surat Undangan Internal'" :filename="'Surat Undangan Internal.docx'" :extension="'docx'" :path="asset('storage/template_letter/7-3_Surat Undangan Internal.docx')" />
        </div>
        <div class="col">
            <x-template-card :title="'Surat Keluar Dinas'" :filename="'SuratKeluarDinas.docx'" :extension="'docx'" :path="asset('storage/template_letter/8-2_SuratKeluarDinas.docx')" />
        </div>
        <div class="col">
            <x-template-card :title="'Surat Kuasa'" :filename="'SuratKuasa.docx'" :extension="'docx'" :path="asset('storage/template_letter/10_SuratKuasa.docx')" />
        </div>
        <div class="col">
            <x-template-card :title="'Berita Acara'" :filename="'BeritaAcara.docx'" :extension="'docx'" :path="asset('storage/template_letter/11-1_BeritaAcara.docx')" />
        </div>
        <div class="col">
            <x-template-card :title="'Surat Keterangan'" :filename="'SuratKeterangan.docx'" :extension="'docx'" :path="asset('storage/template_letter/12-1_SuratKeterangan.docx')" />
        </div>
        <div class="col">
            <x-template-card :title="'Surat Keterangan Peristiwa'" :filename="'SuratKeteranganPeristiwa.docx'" :extension="'docx'" :path="asset('storage/template_letter/12-2_SuratKeteranganPeristiwa.docx')" />
        </div>
        <div class="col">
            <x-template-card :title="'Surat Pengantar'" :filename="'SuratPengantar.docx'" :extension="'docx'" :path="asset('storage/template_letter/13-1_SuratPengantar.docx')" />
        </div>
        <div class="col">
            <x-template-card :title="'Pengumuman'" :filename="'Pengumuman.docx'" :extension="'docx'" :path="asset('storage/template_letter/14_Pengumuman.docx')" />
        </div>
        <div class="col">
            <x-template-card :title="'Telaah Staff'" :filename="'TelaahStaff.docx'" :extension="'docx'" :path="asset('storage/template_letter/16_TelaahStaff.docx')" />
        </div>
        <div class="col">
            <x-template-card :title="'Notula'" :filename="'Notula.docx'" :extension="'docx'" :path="asset('storage/template_letter/18_Notula.docx')" />
        </div>
    </div>
@endsection
