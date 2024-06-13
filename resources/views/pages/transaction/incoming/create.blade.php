@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('menu.transaction.menu'), __('menu.transaction.incoming_letter'), __('menu.general.create')]">
    </x-breadcrumb>

    <div class="card mb-4">
        <form action="{{ route('transaction.incoming.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body row">
                <input type="hidden" name="type" value="incoming">
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <div class="mb-3">
                        <label for="status"
                               class="form-label">{{ __('model.letter.status') }}</label>
                        <select class="form-select" id="status" name="status">
                            @foreach($statuses as $status)
                                <option
                                    value="{{ $status->code }}"
                                    @selected(old('status_code') == $status->code)>
                                    {{ $status->status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <div class="mb-3">
                        <label for="classification_code"
                               class="form-label">{{ __('model.letter.classification_code') }}</label>
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
                <div class="col-sm-12 col-12 col-md-6 col-lg-6">
                    <x-input-form name="reference_number" id="reference_number" :label="__('model.letter.reference_number')"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-6 d-flex justify-content-start align-items-center">
                    <button class="btn btn-danger d-none" type="button" id="get_reference_number">Generate Nomor Surat</button>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="from" :label="__('model.letter.from')"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="disposition_number" :label="__('model.letter.disposition_number')"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="letter_date" :label="__('model.letter.letter_date')" type="date"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="received_date" :label="__('model.letter.received_date')" type="date"/>
                </div>
                <!-- <div class="col-sm-12 col-12 col-md-12 col-lg-12">
                    <x-input-textarea-form name="description" :label="__('model.letter.description')"/>
                </div> -->
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form name="note" :label="__('model.letter.note')"/>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <div class="mb-3">
                        <label for="attachments" class="form-label">Lampiran</label>
                        <input type="file" class="form-control @error('attachments') is-invalid @enderror" id="attachments"
                               name="attachments[]" multiple/>
                        <span class="error invalid-feedback">{{ $errors->first('attachments') }}</span>
                    </div>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <div class="mb-3">
                        <label for="cc" class="form-label">{{ __('model.letter.cc') }}</label>
                        <label class="form-control " for="cc">
                            <input type="checkbox" class="" id="cc"name="cc">
                        </label>
                    </div>
                </div>
            </div>
            <div class="card-footer pt-0">
                <button class="btn btn-primary" type="submit">{{ __('menu.general.save') }}</button>
            </div>
        </form>
    </div>
@endsection
