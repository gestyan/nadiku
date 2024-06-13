@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('menu.agenda.menu'), __('menu.agenda.outgoing_letter')]">
    </x-breadcrumb>

    <div class="card mb-5">
        <div class="card-header">
            <form class="d-flex gap-3" action="{{ url()->current() }}">
            <input type="hidden" name="search" value="{{ $search ?? '' }}">
            <div class="col-2">
                <div class="mb-3">
                    <label for="esign" class="form-label">Status E-Sign</label>
                    <select class="form-select" id="esign" name="esign">
                        <option value="" @selected(old('esign', $esign)==NULL)>Semua Surat</option>
                        <option value="0" @selected(old('esign', $esign)==0)>Belum E-Sign</option>
                        <option value="1" @selected(old('esign', $esign)==1)>Sudah E-Sign</option>
                    </select>
                </div>
            </div>
            <div class="col-2">
                <x-input-form name="since" :label="__('menu.agenda.start_date')" type="date"
                    :value="$since ? date('Y-m-d', strtotime($since)) : ''" />
            </div>
            <div class="col-2">
                <x-input-form name="until" :label="__('menu.agenda.end_date')" type="date"
                    :value="$until ? date('Y-m-d', strtotime($until)) : ''" />
            </div>
            <div class="col-2">
                <div class="mb-3">
                    <label for="to" class="form-label">From</label>
                    <select multiple class="form-select multiple-select" id="to" name="to[]">
                        <!-- <option value="" disabled selected></option> -->
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" @if(in_array($user->id, $to ?? []))selected="selected" @endif>
                            {{ $user->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <!-- <x-input-form name="to" :label="__('model.letter.to')"/> -->
            </div>
            <div class="col-2 d-flex flex-column">
                <label for="" class="form-label text-secondary">&nbsp</label>
                <button class="btn btn-secondary">Filter</button>
            </div>
            <!-- <button class="btn btn-secondary">E-Sign</button> -->
        </form>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ __('model.letter.reference_number') }}</th>
                    <th>Pembuat</th>
                    <th>{{ __('model.letter.to') }}</th>
                    <th>{{ __('model.letter.letter_date') }}</th>
                    <th>Status Esign</th>
                    <th>{{ __('model.letter.note') }}</th>
                    <!-- <th>Status Kirim Email</th> -->
                </tr>
                </thead>
                @if($data)
                    <tbody>
                    @foreach($data as $agenda)
                        <tr>
                            <td>
                                <a href="{{ route('transaction.outgoing.show', $agenda) }}">{{ $agenda->reference_number }}</a>
                            </td>
                            <td>{{ $agenda->user->name }}</td>
                            <td>{{ $agenda->to }}</td>
                            <td>{{ $agenda->formatted_letter_date }}</td>
                            <td class="text-center">
                                @if($agenda->esign_status == 1)
                                <i class="text-success menu-icon tf-icons bx bxs-check-square"></i>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $agenda->note }}</strong>
                            </td>
                    		<!-- <td class="text-center"><i class="text-success menu-icon tf-icons bx bxs-check-square"></i></td> -->
                        </tr>
                    @endforeach
                    </tbody>
                @else
                    <tbody>
                    <tr>
                        <td colspan="4" class="text-center">
                            {{ __('menu.general.empty') }}
                        </td>
                    </tr>
                    </tbody>
                @endif
                <tfoot class="table-border-bottom-0">
                <tr>
                    <th>{{ __('model.letter.reference_number') }}</th>
                    <th>Pembuat</th>
                    <th>{{ __('model.letter.to') }}</th>
                    <th>{{ __('model.letter.letter_date') }}</th>
                    <th>Status Esign</th>
                    <th>{{ __('model.letter.note') }}</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {!! $data->appends(['search' => $search, 'esign' => $esign, 'since' => $since, 'until' => $until, 'to' => $to])->links() !!}

@endsection
