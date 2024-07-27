@extends('layout.main')

@section('content')
    <x-breadcrumb :values="[__('menu.transaction.menu'), __('menu.transaction.outgoing_letter'), __('menu.general.edit')]">
    </x-breadcrumb>

    <div class="card mb-4">
        <form action="{{ route('transaction.outgoing.update', $data) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body row">
                <input type="hidden" name="id" value="{{ $data->id }}">
                <input type="hidden" name="type" value="{{ $data->type }}">
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <div class="mb-3">
                        <label for="status" class="form-label">{{ __('model.letter.status') }}</label>
                        <select class="form-select" id="status" name="status">
                            @foreach ($statuses as $status)
                                <option {{ $status->code == $data->status ? 'selected' : '' }} value="{{ $status->code }}">
                                    {{ $status->status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form :value="$data->number" name="number" id="number" :label="__('model.letter.number')" />
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <div class="mb-3">
                        <label for="satker" class="form-label">Satker</label>
                        <select class="form-select" id="satker" name="satker">
                            @foreach ($satkers as $satker)
                                <option {{ $satker->kode == $data->satker ? 'selected' : '' }} value="{{ $satker->kode }}">
                                    {{ $satker->kode }}
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
                            @foreach ($classifications as $classification)
                                <option @selected(old('classification_code', $data->classification_code) == $classification->code) value="{{ $classification->code }}">
                                    {{ $classification->code . ' : ' . $classification->type }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form :value="$data->reference_number" name="reference_number" id="reference_number" :label="__('model.letter.reference_number')" />
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4 d-flex justify-content-start align-items-center">
                    <button class="btn btn-danger" type="button" id="get_reference_number">Generate Nomor Surat</button>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form :value="$data->to" name="to" :label="__('model.letter.to')" />
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form :value="$data->to_email ?? ''" name="to_email" :label="__('model.letter.to_email') . ' (Pisahkan dengan koma)'" />
                    <!-- <span class="text-danger"> Pisahkan dengan koma </span> -->
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form :value="date('Y-m-d', strtotime($data->letter_date))" name="letter_date" :label="__('model.letter.letter_date')" type="date" />
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form :value="$data->note ?? ''" name="note" :label="__('model.letter.note')" />
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form :value="$data->cc ?? ''" name="cc" :label="__('model.letter.cc') . ' [email]'" type="text" />
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <x-input-form :value="$data->bcc ?? ''" name="bcc" :label="__('model.letter.bcc') . ' [email]'" type="text" />
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <div class="mb-3">
                        <label for="attachments" class="form-label">{{ __('model.letter.attachment') }}</label>
                        <input type="file" class="form-control @error('attachments') is-invalid @enderror"
                            id="attachments" name="attachments[]" multiple />
                        <span class="error invalid-feedback">{{ $errors->first('attachments') }}</span>
                    </div>
                    <ul class="list-group">
                        @foreach ($data->attachments_esign as $attachment)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{ $attachment->path_url }}" target="_blank">{{ $attachment->filename }}</a>
                                <span class="badge bg-danger rounded-pill cursor-pointer btn-remove-attachment"
                                    data-id="{{ $attachment->id }}">
                                    <i class="bx bx-trash"></i>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                    <div class="mb-3">
                        <label for="others" class="form-label">{{ __('model.letter.other') }}</label>
                        <input type="file" class="form-control @error('others') is-invalid @enderror" id="others"
                            name="others[]" multiple />
                        <span class="error invalid-feedback">{{ $errors->first('others') }}</span>
                    </div>
                    <ul class="list-group">
                        @foreach ($data->attachments_other as $attachment)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{ $attachment->path_url }}" target="_blank">{{ $attachment->filename }}</a>
                                <span class="badge bg-danger rounded-pill cursor-pointer btn-remove-attachment"
                                    data-id="{{ $attachment->id }}">
                                    <i class="bx bx-trash"></i>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-sm-12 col-12 col-md-12 col-lg-12 mt-2">
                    <x-input-textarea-form :value="$data->description ?? ''" name="description" :label="__('model.letter.description')" />
                </div>
                <div class="col-sm-12 col-12 col-md-12 col-lg-12">
                    <p><b>Note:</b></p>
                    <ul>
                        <li>Penerima : Instansi atau orang yang akan menerima surat</li>
                        <li>Email bisa lebih dari satu, tetapi dipisahkan dengan koma ","</li>
                        <li>Badan Email berisi pesan yang akan dikirim melalui email</li>
                        <li>Dokumen yang berhasil E-Sign akan diproses dan dikirim
                            melalui email BPS Kab. Aceh Utara oleh Sub Bagian Umum</li>
                    </ul>
                </div>
            </div>
            <div class="card-footer pt-0">
                <button class="btn btn-primary" type="submit">{{ __('menu.general.update') }}</button>
            </div>
        </form>
    </div>
    <form action="{{ route('attachment.destroy') }}" method="post" id="form-to-remove-attachment">
        @csrf
        @method('DELETE')
        <input type="hidden" name="id" id="attachment-id-to-remove">
    </form>
@endsection

@push('script')
    <script>
        $(document).on('click', '.btn-remove-attachment', function(req) {
            $('input#attachment-id-to-remove').val($(this).data('id'));
            Swal.fire({
                title: '{{ __('menu.general.delete_confirm') }}',
                text: "{{ __('menu.general.delete_warning') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#696cff',
                confirmButtonText: '{{ __('menu.general.delete') }}',
                cancelButtonText: '{{ __('menu.general.cancel') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('form#form-to-remove-attachment').submit();
                }
            })
        });
    </script>
@endpush
