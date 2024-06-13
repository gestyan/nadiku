@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('menu.transaction.menu'), __('menu.transaction.outgoing_letter'), __('menu.general.create')]">
    </x-breadcrumb>

    <div class="card mb-4">
        <form action="{{ route('transaction.outgoing.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body row">
                <input type="hidden" name="type" value="outgoing">
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <div class="mb-3">
                        <label for="status"
                               class="form-label">{{ __('model.letter.status') }}</label>
                        <select class="form-select" id="status" name="status">
                            @foreach($statuses as $status)
                                <option
                                    value="{{ $status->code }}"
                                    @if ($status->code == 'B')
                            			selected="selected"
                        			@endif
                                    @selected(old('status_code') == $status->code)>
                                    {{ $status->status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="number" id="number" label="{{ __('model.letter.number') }}" value="{{ $num2600+1 }}"/>
                    <a href="{{ route('agenda.outgoing.list') }}" target="_blank"> Klik untuk melihat Referensi Surat </a>
                </div>
              	<div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <div class="mb-3">
                        <label for="satker"
                               class="form-label">Satker</label>
                        <select class="form-select" id="satker" name="satker" data-num2600="{{ $num2600 }}" data-num2610="{{ $num2610 }}">
                            @foreach($satkers as $satker)
                                <option
                                    value="{{ $satker->kode }}"
                                    @selected(old('sarker_kode') == $satker->kode)>
                                    {{ $satker->kode }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <div class="mb-3">
                        <label for="classification_code" class="form-label">{{ __('model.letter.classification_code') }}</label>
                        <select class="form-select" id="classification_code" name="classification_code">
                            @foreach($classifications as $classification)
                                <option
                                    value="{{ $classification->code }}"
                                    @selected(old('classification_code') == $classification->code)>
                                    {{ $classification->code . " : " . $classification->type }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="reference_number" :label="__('model.letter.reference_number')"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4 d-flex justify-content-start align-items-center">
                    <button class="btn btn-danger" type="button" id="get_reference_number">Generate Nomor Surat</button>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="to" :label="__('model.letter.to')"/>
                </div>
              	<div class="col-sm-12 col-12 col-md-6 col-lg-4">
                	<x-input-form name="to_email" :label="__('model.letter.to_email').' (Pisahkan dengan koma)'" />
                	<!-- <span class="text-danger"> Pisahkan dengan koma </span> -->
            	</div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="letter_date" :label="__('model.letter.letter_date')" type="date"/>
                </div>
                
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="note" :label="__('model.letter.note')"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <div class="mb-3">
                        <label for="attachments" class="form-label">{{ __('model.letter.attachment') }}</label>
                        <input type="file" class="form-control @error('attachments') is-invalid @enderror" id="attachments" name="attachments[]" multiple required />
                        <span class="error invalid-feedback">{{ $errors->first('attachments') }}</span>
                    </div>
                </div>
              	<div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <div class="mb-3">
                        <label for="others" class="form-label">{{ __('model.letter.other') }}</label>
                        <input type="file" class="form-control @error('others') is-invalid @enderror" id="others"
                            name="others[]" multiple />
                        <span class="error invalid-feedback">{{ $errors->first('others') }}</span>
                    </div>
            	</div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="cc" :label="__('model.letter.cc').' [email]'" type="text" />
                </div>
              	<div class="col-sm-12 col-12 col-md-6 col-lg-4">
                	<x-input-form name="bcc" :label="__('model.letter.bcc').' [email]'" type="text" />
            	</div>
              	<div class="col-sm-12 col-12 col-md-12 col-lg-12">
                	<x-input-textarea-form name="description" :label="__('model.letter.description')" />
            	</div>
              	<div class="col-sm-12 col-12 col-md-12 col-lg-12">
                <p><b>Note:</b></p>
                	<ul>
                    	<li>Penerima : Instansi atau orang yang akan menerima surat</li>
                    	<li>Email bisa lebih dari satu, tetapi dipisahkan dengan koma ","</li>
                    	<li>Badan Email berisi pesan yang akan dikirim melalui email</li>
                    	<li>Dokumen yang berhasil E-Sign akan diproses dan dikirim
                        	melalui email Pusdiklat BPS oleh sekretaris”</li>
                	</ul>
            	</div>
            </div>
            <div class="card-footer pt-0">
                <button class="btn btn-primary" type="submit">{{ __('menu.general.save') }}</button>
            </div>
        </form>
    </div>
@endsection
