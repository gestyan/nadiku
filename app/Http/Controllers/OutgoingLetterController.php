<?php

namespace App\Http\Controllers;

use App\Enums\LetterType;
use App\Enums\Config as ConfigEnum;
use App\Http\Requests\StoreLetterRequest;
use App\Http\Requests\UpdateLetterRequest;
use App\Http\Requests\UpdateFileRequest;
use App\Models\Attachment;
use App\Models\Classification;
use App\Models\Config;
use App\Models\Letter;
use App\Models\LetterStatus;
use App\Models\User;
use App\Models\Satker;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use App\Mail\EsignRequestMail;
use App\Mail\EsignSuccessMail;
use App\Mail\UpdateOutgoingMail;
use Illuminate\Support\Facades\Mail;

class OutgoingLetterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {

        if(auth()->user()->role != 'admin'){
            $data = Letter::outgoing()->where('user_id', auth()->user()->id)
            ->with(['attachments', 'classification'])
            ->orderBy('number', 'DESC')
            ->filter($request->esign, $request->since, $request->until, $request->to)
            ->search($request->search)
            ->paginate(Config::getValueByCode(ConfigEnum::PAGE_SIZE))
            ->appends([
                'search' => $request->search,
            ]);
        } else {
            $data = Letter::outgoing()
            ->with(['attachments', 'classification'])
            ->orderBy('number', 'DESC')
            ->filter($request->esign, $request->since, $request->until, $request->from)
            ->search($request->search)
            ->paginate(Config::getValueByCode(ConfigEnum::PAGE_SIZE))
            ->appends([
                'search' => $request->search,
            ]);
        }

        return view('pages.transaction.outgoing.index', [
            'data' => $data,
            'search' => $request->search,
            'esign' => $request->esign,
            'since' => $request->since,
            'until' => $request->until,
            'from' => $request->from,
            'query' => $request->getQueryString(),
            'users' => User::all(),
        ]);
    }

  	public function all(Request $request): View
    {

        $data = Letter::outgoing()->where('status', '<>', 'R')
            ->with(['attachments', 'classification'])
            ->orderBy('number', 'DESC')
            ->filter($request->esign, $request->since, $request->until, $request->from)
            ->search($request->search)
            ->paginate(Config::getValueByCode(ConfigEnum::PAGE_SIZE))
            ->appends([
                'search' => $request->search,
            ]);

        return view('pages.transaction.outgoing.all', [
            'data' => $data,
            'search' => $request->search,
            'esign' => $request->esign,
            'since' => $request->since,
            'until' => $request->until,
            'from' => $request->from,
            'query' => $request->getQueryString(),
            'users' => User::all(),
        ]);
    }

    /**
     * Display a listing of the outgoing letter agenda.
     *
     * @param Request $request
     * @return View
     */
    public function agenda(Request $request): View
    {
        if (auth()->user()->role == 'staff') {
            $data = Letter::outgoing()->where('user_id', '=', auth()->user()->id)
                ->orderBy('number', 'DESC')
                ->agenda($request->since, $request->until, $request->filter)
                ->paginate(Config::getValueByCode(ConfigEnum::PAGE_SIZE))
                ->appends([
                    'search' => $request->search,
                ]);
        } else {
            $data = Letter::outgoing()->agenda($request->since, $request->until, $request->filter)->render($request->search);
        }
        return view('pages.transaction.outgoing.agenda', [
            'data' => $data,
            'search' => $request->search,
            'since' => $request->since,
            'until' => $request->until,
            'filter' => $request->filter,
            'query' => $request->getQueryString(),
        ]);
    }

    /**
     * Display a listing of the ALL letter agenda.
     *
     * @param Request $request
     * @return View
     */
    public function list(Request $request): View
    {
        $data = Letter::outgoing()->where('status', '<>', 'R')
        ->orderBy('number', 'DESC')
        ->filter($request->esign, $request->since, $request->until, $request->to)
        // ->agenda($request->since, $request->until, $request->filter)
        ->paginate(Config::getValueByCode(ConfigEnum::PAGE_SIZE))
        ->appends([
            'search' => $request->search,
        ]);
        return view('pages.transaction.outgoing.list', [
            'data' => $data,
            'search' => $request->search,
            'esign' => $request->esign,
            'since' => $request->since,
            'until' => $request->until,
            'to' => $request->to,
            'query' => $request->getQueryString(),
            'users' => User::all(),
        ]);
    }

    /**
     * @param Request $request
     * @return View
     */
    public function print(Request $request): View
    {
        $agenda = __('menu.agenda.menu');
        $letter = __('menu.agenda.outgoing_letter');
        $title = App::getLocale() == 'id' ? "$agenda $letter" : "$letter $agenda";
        return view('pages.transaction.outgoing.print', [
            'data' => Letter::outgoing()->agenda($request->since, $request->until, $request->filter)->get(),
            'search' => $request->search,
            'since' => $request->since,
            'until' => $request->until,
            'filter' => $request->filter,
            'config' => Config::pluck('value', 'code')->toArray(),
            'title' => $title,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
      	$num2600 = Letter::outgoing()->where('satker', '11110')->orderBy('number', 'DESC')->first();
      	$num2610 = Letter::outgoing()->where('satker', '11111')->orderBy('number', 'DESC')->first();
      	if($num2600 == NULL) $num2600 = '0000'; else $num2600 = (int) $num2600->number;
        if($num2610 == NULL) $num2610 = '0000'; else $num2610 = (int) $num2610->number;

      	return view('pages.transaction.outgoing.create', [
                'classifications' => Classification::orderBy('code')->get(),
                'statuses' => LetterStatus::all(),
                'users' => User::all(),
              	'satkers' => Satker::all(),
              	'num2600' => $num2600,
              	'num2610' => $num2610,
                'numLetter' => Letter::outgoing()->orderBy('number', 'DESC')->first(),
            ]);

      	/**
      	if(Letter::outgoing()->count() != 0){
          	if($num2600 == NULL) $num2600 = '0000';
          	if($num2610 == NULL) $num2610 = '0000';
            return view('pages.transaction.outgoing.create', [
                'classifications' => Classification::all(),
                'statuses' => LetterStatus::all(),
                'users' => User::all(),
              	'satkers' => Satker::all(),
              	'num2600' => $num2600->number,
              	'num2610' => $num2610->number,
                'numLetter' => Letter::outgoing()->orderBy('number', 'DESC')->first()->number,
            ]);
        } else {
          	if($num2600 == NULL) $num2600 = '0000';
          	if($num2610 == NULL) $num2610 = '0000';
        	return view('pages.transaction.outgoing.create', [
                'classifications' => Classification::all(),
                'statuses' => LetterStatus::all(),
                'users' => User::all(),
              	'satkers' => Satker::all(),
              	'num2600' => $num2600,
              	'num2610' => $num2610,
              	'numLetter' => '0000',
            ]);
        }
        */
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreLetterRequest $request
     * @return RedirectResponse
     */
    public function store(StoreLetterRequest $request): RedirectResponse
    {
        // dd($lastNumber = Letter::where('satker', $request->satker)->orderBy('number', 'DESC')->first());
        try {
            $user = auth()->user();

            if ($request->type != LetterType::OUTGOING->type())
                throw new \Exception(__('menu.transaction.outgoing_letter'));
            $newLetter = $request->validated();

            $lastNumber = Letter::where('satker', $request->satker)->orderBy('number', 'DESC')->first();

            if($lastNumber == NULL){
                $lastNumber = 1;
            }else{
                $lastNumber = Letter::where('satker', $request->satker)->orderBy('number', 'DESC')->first()->number;
            }

          	// Check String Using preg_match() Function
			$pattern = '/^[0-9]+$/'; // only number string

          	if(preg_match($pattern, $request->number)){
            	if($request->number <= (int) $lastNumber){
                    $newNumber = (int)$lastNumber;
                    $newNumber += 1;
                    $newNumber = str_pad($newNumber, 4, "0", STR_PAD_LEFT);
                    $newReferenceNumber = str_replace($request->number, $newNumber, $request->reference_number);
            	} else {
                	$newNumber = $request->number;
                	$newReferenceNumber = $request->reference_number;
                }
            } else {
            	$newNumber = $request->number;
                $newReferenceNumber = $request->reference_number;
            }

            // if($request->number <= $lastNumber){
            //    $newNumber = (int)$lastNumber;
            //    $newNumber += 1;
            //    $newNumber = str_pad($newNumber, 4, "0", STR_PAD_LEFT);
            //    $newReferenceNumber = str_replace($request->number, $newNumber, $request->reference_number);
            //} else {
            //    $newNumber = $request->number;
            //    $newReferenceNumber = $request->reference_number;
            //}

            $newLetter['user_id'] = $user->id;
          	$newLetter['number'] = $newNumber;
            $newLetter['reference_number'] = $newReferenceNumber;
            $newLetter['esign_status'] = 0;
            $newLetter['send_status'] = 0;
            $letter = Letter::create($newLetter);
            if ($request->hasFile('attachments')) {
                foreach ($request->attachments as $attachment) {
                    $extension = $attachment->getClientOriginalExtension();
                    if (!in_array($extension, ['docx', 'doc']))
                        continue;
                    $filename = time() . '-' . $attachment->getClientOriginalName();
                    $filename = str_replace(' ', '-', $filename);
                    $attachment->storeAs('public/attachments', $filename);
                    Attachment::create([
                        'filename' => $filename,
                        'extension' => $extension,
                        'user_id' => $user->id,
                        'letter_id' => $letter->id,
                      	'esign_file' => 1,
                        'esign_status' => 0,
                    ]);
                }
            }

          	if ($request->hasFile('others')) {
                foreach ($request->others as $other) {
                    $extension = $other->getClientOriginalExtension();
                    if (!in_array($extension, ['png', 'jpg', 'jpeg', 'pdf', 'docx', 'doc', 'xlsx', 'xls', 'csv']))
                        continue;
                    $filename = time() . '-' . $other->getClientOriginalName();
                    $filename = str_replace(' ', '-', $filename);
                    $other->storeAs('public/attachments', $filename);
                    Attachment::create([
                        'filename' => $filename,
                        'extension' => $extension,
                        'user_id' => $user->id,
                        'letter_id' => $letter->id,
                        'esign_file' => 0,
                        'esign_status' => 0,
                    ]);
                }
            }

          	// $cc = ['hafis.sani39@gmail.com'];
            // $target = 'sekre.kapusdiklat@gmail.com';

            // $pesan = [
            //     'nomor_surat' => $newLetter['reference_number'],
            //     'dari' => $user->name,
            //     'tertuju' => $newLetter['to'],
            //     'tanggal_surat' => $newLetter['letter_date'],
            //     'kode_klasifikasi' => $newLetter['classification_code'],
            //     'link' => route('transaction.outgoing.index') . "/" . $letter->id,
            // ];

            // Mail::to($target)->cc($cc)->send(new EsignRequestMail($pesan));

            return redirect()
                ->route('transaction.outgoing.index')
                ->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Letter $outgoing
     * @return View
     */
    public function show(Letter $outgoing): View
    {
        return view('pages.transaction.outgoing.show', [
            'data' => $outgoing->load(['classification', 'user', 'attachments']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Letter $outgoing
     * @return View
     */
    public function edit(Letter $outgoing): View
    {
        return view('pages.transaction.outgoing.edit', [
            'data' => $outgoing,
            'classifications' => Classification::orderBy('code')->get(),
            'statuses' => LetterStatus::all(),
          	'satkers' => Satker::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateLetterRequest $request
     * @param Letter $outgoing
     * @return RedirectResponse
     */
    public function update(UpdateLetterRequest $request, Letter $outgoing): RedirectResponse
    {
        try {
            $outgoing->update($request->validated());
            if ($request->hasFile('attachments')) {
              	$outgoing->update(['esign_status' => 0]);
              	$attach = Attachment::where('letter_id', $outgoing->id)->where('esign_status', 1)->first();
                if(isset($attach)){
                    Storage::delete('public/attachments/' . $attach->filename);
                    Attachment::where('id', $attach->id)->delete();
                }
                foreach ($request->attachments as $attachment) {
                    $extension = $attachment->getClientOriginalExtension();
                    if (!in_array($extension, ['docx', 'doc']))
                        continue;
                    $filename = time() . '-' . $attachment->getClientOriginalName();
                    $filename = str_replace(' ', '-', $filename);
                    $attachment->storeAs('public/attachments', $filename);
                    Attachment::create([
                        'filename' => $filename,
                        'extension' => $extension,
                        'user_id' => auth()->user()->id,
                        'letter_id' => $outgoing->id,
                      	'esign_file' => 1,
                        'esign_status' => 0,
                    ]);
                }
            }

          	if ($request->hasFile('others')) {
                foreach ($request->others as $other) {
                    $extension = $other->getClientOriginalExtension();
                    if (!in_array($extension, ['png', 'jpg', 'jpeg', 'pdf', 'docx', 'doc', 'xlsx', 'xls', 'csv']))
                        continue;
                    $filename = time() . '-' . $other->getClientOriginalName();
                    $filename = str_replace(' ', '-', $filename);
                    $other->storeAs('public/attachments', $filename);
                    Attachment::create([
                        'filename' => $filename,
                        'extension' => $extension,
                        'user_id' => auth()->user()->id,
                        'letter_id' => $outgoing->id,
                        'esign_file' => 0,
                        'esign_status' => 0,
                    ]);
                }
            }


          	// $cc = ['hafis.sani39@gmail.com'];
            // $user = auth()->user();
            // $target = 'sekre.kapusdiklat@gmail.com';

            // $pesan = [
            //     'nomor_surat' => $outgoing['reference_number'],
            //     'dari' => $user->name,
            //     'tertuju' => $outgoing['to'],
            //     'tanggal_surat' => $outgoing['letter_date'],
            //     'kode_klasifikasi' => $outgoing['classification_code'],
            //     'link' => route('transaction.outgoing.index') . "/" . $outgoing->id,
            // ];

            // Mail::to($target)->cc($cc)->send(new UpdateOutgoingMail($pesan));

            return redirect()->route('transaction.outgoing.index')->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function update_file(Request $request, $outgoing_id)
    {

        try {
            // $outgoing->update($request->validated());
            if ($request->hasFile('attachments')) {
                $attach = Attachment::where('letter_id', $outgoing_id)->where('esign_status', 1)->first();

              	if (isset($attach)) {
                    Storage::delete('public/attachments/' . $attach->filename);
                    foreach ($request->attachments as $attachment) {
                        $extension = $attachment->getClientOriginalExtension();
                        if (!in_array($extension, ['png', 'jpg', 'jpeg', 'pdf', 'docx', 'doc', 'xlsx', 'xls', 'csv']))
                            continue;
                        $filename = time() . '-' . $attachment->getClientOriginalName();
                        $filename = str_replace(' ', '-', $filename);
                        $attachment->storeAs('public/attachments', $filename);
                        Attachment::where('id', $attach->id)->update([
                            'filename' => $filename,
                            'extension' => $extension,
                            // 'user_id' => auth()->user()->id,
                            // 'letter_id' => $outgoing->id,
                        ]);
                        Letter::where('id', $outgoing_id)->update([
                            'esign_status' => 1,
                        ]);
                    }
                } else {
                    foreach ($request->attachments as $attachment) {
                        $extension = $attachment->getClientOriginalExtension();
                        if (!in_array($extension, ['png', 'jpg', 'jpeg', 'pdf', 'docx', 'doc', 'xlsx', 'xls', 'csv']))
                            continue;
                        $filename = time() . '-' . $attachment->getClientOriginalName();
                        $filename = str_replace(' ', '-', $filename);
                        $attachment->storeAs('public/attachments', $filename);
                        Attachment::create([
                            'filename' => $filename,
                            'extension' => $extension,
                            'user_id' => auth()->user()->id,
                            'letter_id' => $outgoing_id,
                            'esign_file' => 1,
                            'esign_status' => 1,
                        ]);
                        Letter::where('id', $outgoing_id)->update([
                            'esign_status' => 1,
                        ]);
                    }
                }


				// SEND WA

              	$updateLetter = Letter::where('id', $outgoing_id)->first();
              	$user = User::where('id', '=', $updateLetter['user_id'])->first();

                // $cc = ['hafis.sani39@gmail.com'];
                // $target = $user->email;

                // $pesan = [
                //     'nomor_surat' => $updateLetter['reference_number'],
                //     'dari' => $user->name,
                //     'tertuju' => $updateLetter['to'],
                //     'tanggal_surat' => $updateLetter['letter_date'],
                //     'kode_klasifikasi' => $updateLetter['classification_code'],
                //     'link' => asset('storage/attachments/' . $filename),
                // ];

                // Mail::to($target)->cc($cc)->send(new EsignSuccessMail($pesan));

                return back()->with('success', __('menu.general.success'));
            }
            return back()->with('error', 'File tidak ada');
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Letter $outgoing
     * @return RedirectResponse
     */
    public function destroy(Letter $outgoing): RedirectResponse
    {
        try {
            $outgoing->delete();
            return redirect()
                ->route('transaction.outgoing.index')
                ->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }
}
