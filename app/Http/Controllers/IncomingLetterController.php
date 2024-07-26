<?php

namespace App\Http\Controllers;

use App\Enums\LetterType;
use App\Enums\Config as ConfigEnum;
use App\Http\Requests\StoreLetterRequest;
use App\Http\Requests\UpdateLetterRequest;
use App\Models\Attachment;
use App\Models\Classification;
use App\Models\Config;
use App\Models\Letter;
use App\Models\LetterStatus;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class IncomingLetterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        switch (auth()->user()->role) {
            case 'admin':
                return view('pages.transaction.incoming.index', [
                    'data' => Letter::incoming()->render($request->search),
                    'search' => $request->search,
                ]);
                break;

            case 'kapus':
                return view('pages.transaction.incoming.index', [
                    'data' => Letter::incoming()->render($request->search),
                    'search' => $request->search,
                ]);
                break;

            case 'kabagum':
            	return view('pages.transaction.incoming.index', [
                    'data' => Letter::incoming()->render($request->search),
                    'search' => $request->search,
                ]);
                break;

            case 'staff':
                $data = Letter::join('dispositions', 'letters.id', '=', 'dispositions.letter_id')
                    ->where('dispositions.to', '=', auth()->user()->id)
                    ->select('letters.*')
                    ->addSelect('dispositions.to as disposition_to', 'dispositions.due_date', 'dispositions.content', 'dispositions.note as disposition_note', 'dispositions.letter_status', 'dispositions.user_id as disposition_from')
                    ->orderBy('dispositions.created_at', 'DESC')
                    ->paginate(Config::getValueByCode(ConfigEnum::PAGE_SIZE))
                    ->appends([
                        'search' => $request->search,
                    ]);

                $search = $request->search;
                return view(
                    'pages.transaction.incoming.index',
                    compact(['data', 'search'])
                );
                break;

            default:
                # code...
                break;
        }
    }


    /**
     * Display a listing of the resource for self.
     *
     * @param Request $request
     * @return View
     */
    public function self(Request $request): View
    {
        $data = Letter::join('dispositions', 'letters.id', '=', 'dispositions.letter_id')
            ->where('dispositions.to', '=', auth()->user()->id)
            ->select('letters.*')
            ->addSelect('dispositions.to as disposition_to', 'dispositions.due_date', 'dispositions.content', 'dispositions.note as disposition_note', 'dispositions.letter_status', 'dispositions.user_id as disposition_from')
            ->orderBy('dispositions.created_at', 'DESC')
            ->paginate(Config::getValueByCode(ConfigEnum::PAGE_SIZE))
            ->appends([
                'search' => $request->search,
            ]);

        $search = $request->search;
        return view(
            'pages.transaction.incoming.self',
            compact(['data', 'search'])
        );
    }

    /**
     * Display a listing of the incoming letter agenda.
     *
     * @param Request $request
     * @return View
     */
    public function agenda(Request $request): View
    {

        if (in_array(auth()->user()->role, ['admin', 'kabagum'])) {
            return view('pages.transaction.incoming.agenda', [
                'data' => Letter::incoming()->agenda($request->since, $request->until, $request->filter)->render($request->search),
                'search' => $request->search,
                'since' => $request->since,
                'until' => $request->until,
                'filter' => $request->filter,
                'query' => $request->getQueryString(),
            ]);
        } else {
            $data = Letter::join('dispositions', 'letters.id', '=', 'dispositions.letter_id')
            ->where('dispositions.to', '=', auth()->user()->id)
            // ->select('letters.id as id', 'reference_number', 'agenda_number', 'from', 'letter_date', 'received_date', 'description', 'letters.note as note', 'type', 'classification_code', 'letters.user_id as user_id', '')
            ->select('letters.*')
            ->addSelect('dispositions.to as disposition_to', 'dispositions.due_date', 'dispositions.content', 'dispositions.note as disposition_note', 'dispositions.letter_status', 'dispositions.user_id as disposition_from')
            ->orderBy('letters.letter_date', 'DESC')
            ->agenda($request->since, $request->until, $request->filter)
            ->paginate(Config::getValueByCode(ConfigEnum::PAGE_SIZE))
            ->appends([
                'search' => $request->search,
            ]);
            return view('pages.transaction.incoming.agenda', [
                'data' => $data,
                'search' => $request->search,
                'since' => $request->since,
                'until' => $request->until,
                'filter' => $request->filter,
                'query' => $request->getQueryString(),
            ]);
        }
    }

    /**
     * @param Request $request
     * @return View
     */
    public function print(Request $request): View
    {
        $agenda = __('menu.agenda.menu');
        $letter = __('menu.agenda.incoming_letter');
        $title = App::getLocale() == 'id' ? "$agenda $letter" : "$letter $agenda";

      	$data = Letter::with(['allDispositions.disposition_to_name'])->incoming()->agenda($request->since, $request->until, $request->filter)->orderBy('disposition_number')->get();
      	//dd($data->first()->allDispositions->first()->content);
        return view('pages.transaction.incoming.print', [
            'data' => $data,
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
      	if(Letter::incoming()->count() != 0){
            return view('pages.transaction.incoming.create', [
                'classifications' => Classification::orderBy('code')->get(),
                'statuses' => LetterStatus::all(),
                'users' => User::all(),
                'numLetter' => Letter::incoming()->orderBy('number', 'DESC')->first()->number,
            ]);
        } else {
        	return view('pages.transaction.incoming.create', [
                'classifications' => Classification::all(),
                'statuses' => LetterStatus::all(),
                'users' => User::all(),
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreLetterRequest $request
     * @return RedirectResponse
     */
    public function store(StoreLetterRequest $request): RedirectResponse
    {
        try {
            $user = auth()->user();

            if ($request->type != LetterType::INCOMING->type())
                throw new \Exception(__('menu.transaction.incoming_letter'));
            $newLetter = $request->validated();

            $newLetter['user_id'] = $user->id;

            $letter = Letter::create($newLetter);

            if ($request->hasFile('attachments')) {
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
                        'user_id' => $user->id,
                        'letter_id' => $letter->id,
                    ]);
                }
            }

            // if (isset($newLetter['cc'])) {
            //     $target = '6281310354407';
            //     $no_surat = $newLetter['reference_number'];
            //     $pengirim = $newLetter['from'];
            //     $tanggal_surat = $newLetter['letter_date'];
            //     $kode_klasifikasi = $newLetter['classification_code'];
            //     $link_surat = asset('storage/attachments/' . $filename);

            //     $header = "*[SURAT MASUK - {$kode_klasifikasi}]*";


            //     $data = array(
            //         "token" => "9l8cHixxXXMxLBcULmETeo9tBPHylx3H",
            //         "number" => $target,
            //         "message" => $header .
            //             "\r\n\r\nNomor Surat : {$no_surat}" .
            //             "\r\nPengirim : {$pengirim}" .
            //             "\r\nTanggal Surat : {$tanggal_surat}" .
            //             "\r\n\r\n_link :_ {$link_surat}",

            //     );

            //     $url = "http://103.121.197.184:3000/api/send";
            //     $content = json_encode($data);

            //     $curl = curl_init($url);
            //     curl_setopt($curl, CURLOPT_HEADER, false);
            //     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            //     curl_setopt(
            //         $curl,
            //         CURLOPT_HTTPHEADER,
            //         array("Content-type: application/json")
            //     );
            //     curl_setopt($curl, CURLOPT_POST, true);
            //     curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

            //     $json_response = curl_exec($curl);
            // }

            return redirect()
                ->route('transaction.incoming.index')
                ->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            dd($exception);
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Letter $incoming
     * @return View
     */
    public function show(Letter $incoming): View
    {
        if (auth()->user()->role == 'staff') {
            $data = Letter::join('dispositions', 'letters.id', '=', 'dispositions.letter_id')
                // ->select('letters.id as id', 'reference_number', 'agenda_number', 'from', 'letter_date', 'received_date', 'description', 'letters.note as note', 'type', 'classification_code', 'letters.user_id as user_id', '')
                ->where('dispositions.to', '=', auth()->user()->id)
                ->where('letters.id', '=', $incoming->id)
                ->select('letters.*')
                ->addSelect('dispositions.to as disposition_to', 'dispositions.due_date', 'dispositions.content', 'dispositions.note as disposition_note', 'dispositions.letter_status', 'dispositions.user_id as disposition_from')
                ->latest('letters.created_at')
                ->first();
        } else {
            $data = $incoming->load(['classification', 'user', 'attachments']);
        }

        return view('pages.transaction.incoming.show', [
            'data' => $data,
            // 'data' => $incoming->load(['classification', 'user', 'attachments']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Letter $incoming
     * @return View
     */
    public function edit(Letter $incoming): View
    {
        return view('pages.transaction.incoming.edit', [
            'data' => $incoming,
            'classifications' => Classification::orderBy('code')->get(),
            'statuses' => LetterStatus::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateLetterRequest $request
     * @param Letter $incoming
     * @return RedirectResponse
     */
    public function update(UpdateLetterRequest $request, Letter $incoming): RedirectResponse
    {
        try {
            $incoming->update($request->validated());
            if ($request->hasFile('attachments')) {
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
                        'letter_id' => $incoming->id,
                    ]);
                }
            }
            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Letter $incoming
     * @return RedirectResponse
     */
    public function destroy(Letter $incoming): RedirectResponse
    {
        try {
            $incoming->delete();
            return redirect()
                ->route('transaction.incoming.index')
                ->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }
}
