<div class="card mb-4">
    <div class="card-header pb-0">
        <div class="d-flex justify-content-between flex-column flex-sm-row">
            <div class="card-title">
                <h5 class="text-nowrap mb-0 fw-bold">{{ $letter->reference_number }}
                    @if($letter->esign_status == 1 && $letter->type == 'outgoing')
                    <span class="text-success">[Sudah E-Sign]</span>
                    @elseif($letter->esign_status == 0 && $letter->type == 'outgoing')
                    <span class="text-danger">[Belum E-Sign]</span>
                    @endif

                </h5>
                <small class="text-black">
                    {{ $letter->type == 'incoming' ? $letter->from : $letter->to }} |
                    <span class="text-secondary">{{ __('model.letter.disposition_number') }}:</span> {{
                    $letter->disposition_number }}
                    |
                    {{ $letter->classification?->type }}
                </small>
            </div>
            <div class="card-title d-flex flex-row">
                <div class="d-inline-block mx-2 text-end text-black">
                    <small class="d-block text-secondary">{{ __('model.letter.letter_date') }}</small>
                    {{ $letter->formatted_letter_date }}
                </div>
                @if($letter->type == 'incoming')
                <div class="mx-3">
                    <a href="{{ route('transaction.disposition.index', $letter) }}" class="btn btn-primary btn">{{
                        __('model.letter.dispose') }} <span>({{ $letter->dispositions->count() }})</span></a>
                </div>
                @endif
                <div class="dropdown d-inline-block">
                    <button class="btn p-0" type="button" id="dropdown-{{ $letter->type }}-{{ $letter->id }}"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    @if($letter->type == 'incoming')
                    <div class="dropdown-menu dropdown-menu-end"
                        aria-labelledby="dropdown-{{ $letter->type }}-{{ $letter->id }}">
                        @if(!\Illuminate\Support\Facades\Route::is('*.show'))
                        <a class="dropdown-item" href="{{ route('transaction.incoming.show', $letter) }}">{{
                            __('menu.general.view') }}</a>
                        @endif
                        @if(auth()->user()->role != 'staff')
                        <a class="dropdown-item" href="{{ route('transaction.incoming.edit', $letter) }}">{{
                            __('menu.general.request') }}</a>
                        <form action="{{ route('transaction.incoming.destroy', $letter) }}" class="d-inline"
                            method="post">
                            @csrf
                            @method('DELETE')
                            <span class="dropdown-item cursor-pointer btn-delete">{{ __('menu.general.delete') }}</span>
                        </form>
                        @endif
                    </div>
                    @else
                    <div class="dropdown-menu dropdown-menu-end"
                        aria-labelledby="dropdown-{{ $letter->type }}-{{ $letter->id }}">
                        @if(!\Illuminate\Support\Facades\Route::is('*.show'))
                        <a class="dropdown-item" href="{{ route('transaction.outgoing.show', $letter) }}">{{
                            __('menu.general.view') }}</a>
                        @endif
                      	@if($letter->user_id == auth()->user()->id || auth()->user()->role == 'admin')
                        <a class="dropdown-item" href="{{ route('transaction.outgoing.edit', $letter) }}">{{
                            __('menu.general.request') }}</a>
                        <form action="{{ route('transaction.outgoing.destroy', $letter) }}" class="d-inline"
                            method="post">
                            @csrf
                            @method('DELETE')
                            <span class="dropdown-item cursor-pointer btn-delete">{{ __('menu.general.delete') }}</span>
                        </form>
                      	@endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <hr>
        <!--<p>{!! nl2br(e($letter->description)) !!}</p> -->
        <div class="d-flex justify-content-between flex-column flex-sm-row">
          	@if($letter->type == 'incoming')
            <small class="text-secondary w-50 me-4">{{ $letter->note }}</small>
            @else
            <div class="d-flex justify-content-between flex-column w-50">
                <small class="text-secondary w-50 me-4">{{ $letter->note }}</small>
                <br>
                <small class="text-secondary w-50 me-4 text-primary">From : {{ $letter->user->name }}</small>
            </div>
            @endif
            @if(count($letter->attachments))
            <div class="d-flex align-items-end w-auto">
                @if(auth()->user()->role == 'admin' && $letter->type == 'outgoing')
                <form class="d-flex align-items-end me-3" action="{{ route('transaction.outgoing.update_file', $letter->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="me-3">
                        <label for="attachments" class="form-label">Lampiran E-Sign</label>
                        <input type="file" class="form-control @error('attachments') is-invalid @enderror"
                            id="attachments" name="attachments[]" multiple />
                        <span class="error invalid-feedback">{{ $errors->first('attachments') }}</span>
                    </div>
                    <div class="pt-0">
                        <button class="btn btn-primary" type="submit">Tambahkan</button>
                    </div>
                </form>
                @endif
                @foreach($letter->attachments as $attachment)
                <a href="{{ $attachment->path_url }}" target="_blank">
                  	@if($attachment->esign_file != 1)
                      @if($attachment->extension == 'pdf')
                      <i class="bx bxs-file-pdf display-6 cursor-pointer text-primary"></i>
                      @elseif(in_array($attachment->extension, ['jpg', 'jpeg']))
                      <i class="bx bxs-file-jpg display-6 cursor-pointer text-primary"></i>
                      @elseif($attachment->extension == 'png')
                      <i class="bx bxs-file-png display-6 cursor-pointer text-primary"></i>
                      @elseif(in_array($attachment->extension, ['doc', 'docx']))
                      <i class="bx bxs-file-doc display-6 cursor-pointer text-primary"></i>
                  	  @elseif(in_array($attachment->extension, ['xlsx', 'xls', 'csv']))
                      <i class="bx bx-spreadsheet display-6 cursor-pointer text-primary"></i>
                      @endif
                  	@elseif($attachment->esign_status == 1)
                        @if($attachment->extension == 'pdf')
                        <i class="bx bxs-file-pdf display-6 cursor-pointer text-success"></i>
                        @elseif(in_array($attachment->extension, ['jpg', 'jpeg']))
                        <i class="bx bxs-file-jpg display-6 cursor-pointer text-success"></i>
                        @elseif($attachment->extension == 'png')
                        <i class="bx bxs-file-png display-6 cursor-pointer text-success"></i>
                        @elseif(in_array($attachment->extension, ['doc', 'docx']))
                        <i class="bx bxs-file-doc display-6 cursor-pointer text-success"></i>
                        @endif
                  	@else
                        @if($attachment->extension == 'pdf')
                        <i class="bx bxs-file-pdf display-6 cursor-pointer text-danger"></i>
                        @elseif(in_array($attachment->extension, ['jpg', 'jpeg']))
                        <i class="bx bxs-file-jpg display-6 cursor-pointer text-danger"></i>
                        @elseif($attachment->extension == 'png')
                        <i class="bx bxs-file-png display-6 cursor-pointer text-danger"></i>
                        @elseif(in_array($attachment->extension, ['doc', 'docx']))
                        <i class="bx bxs-file-doc display-6 cursor-pointer text-danger"></i>
                        @endif
                    @endif
                </a>
                @endforeach
            </div>
            @endif
        </div>
      	@if(( auth()->user()->role == 'staff' || ( auth()->user()->role == 'kabagum' & $letter->disposition_to == auth()->user()->id )) & ($letter->type == 'incoming'))
        <hr>
        <div class="d-flex justify-content-between flex-column flex-sm-row">
            <h5 class="text-nowrap mb-0 fw-bold">Dis : {{ $letter->disposition_from_name?->name }} ({{
                $letter->status_?->status }})</h5>
            <div class="d-inline-block mx-2 text-end text-black">
                <small class="d-block text-secondary">{{ __('model.disposition.due_date') }}</small>
                {{ $letter->formatted_due_date }}
            </div>
        </div>
        <p>{!! nl2br(e($letter->content)) !!}</p>
        <small class="text-secondary">Disposition to : {{ implode(", ", $letter->dispositionNames()->toArray()) }}</small>
        @endif
        {{ $slot }}
    </div>
</div>