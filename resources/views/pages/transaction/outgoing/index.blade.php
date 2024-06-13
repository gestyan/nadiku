@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('menu.transaction.menu'), __('menu.transaction.outgoing_letter')]">
        <a href="{{ route('transaction.outgoing.create') }}" class="btn btn-primary">{{ __('menu.general.create') }}</a>
    </x-breadcrumb>

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
    @if(auth()->user()->role != 'staff')
    <div class="col-2">
        <div class="mb-3">
            <label for="from" class="form-label">From</label>
            <select multiple class="form-select multiple-select" id="from" name="from[]">
                <!-- <option value="" disabled selected></option> -->
                @foreach($users as $user)
                <option value="{{ $user->id }}" @if(in_array($user->id, $from ?? []))selected="selected" @endif>
                    {{ $user->name }}
                </option>
                @endforeach
            </select>
        </div>
        <!-- <x-input-form name="to" :label="__('model.letter.to')"/> -->
    </div>
    @endif
    <div class="col-2 d-flex flex-column">
        <label for="" class="form-label text-secondary">&nbsp</label>
        <button class="btn btn-secondary">Filter</button>
    </div>
    <!-- <button class="btn btn-secondary">E-Sign</button> -->
	</form>

    @foreach($data as $letter)
        <x-letter-card
            :letter="$letter"
        />
    @endforeach

    {!! $data->appends(['search' => $search, 'esign' => $esign, 'since' => $since, 'until' => $until, 'from' => $from])->links() !!}

@endsection
