<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDispositionRequest;
use App\Http\Requests\UpdateDispositionRequest;
use App\Models\Disposition;
use App\Models\Letter;
use App\Models\LetterStatus;
use App\Models\Classification;
use App\Models\User;
use App\Mail\DispositionMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DispositionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param Letter $letter
     * @return View
     */
    public function index(Request $request, Letter $letter): View
    {
        switch (auth()->user()->role) {
            case 'admin':
                return view('pages.transaction.disposition.index', [
                    'data' => Disposition::render($letter, $request->search),
                    'letter' => $letter,
                    'search' => $request->search,
                ]);
                break;

            case 'kapus':
                return view('pages.transaction.disposition.index', [
                    'data' => Disposition::render($letter, $request->search),
                    'letter' => $letter,
                    'search' => $request->search,
                ]);
                break;

            case 'kabagum':
                return view('pages.transaction.disposition.index', [
                    'data' => Disposition::render($letter, $request->search),
                    'letter' => $letter,
                    'search' => $request->search,
                ]);
                break;

            case 'staff':
                return view('pages.transaction.disposition.index', [
                    'data' => Disposition::where('user_id', '=', auth()->user()->id)->where('letter_id', '=', $letter->id)->render($letter, $request->search),
                    'letter' => $letter,
                    'search' => $request->search,
                ]);
                break;

            default:
                # code...
                break;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Letter $letter
     * @return View
     */
    public function create(Letter $letter): View
    {
        return view('pages.transaction.disposition.create', [
            'letter' => $letter,
            'statuses' => LetterStatus::all(),
            'users' => User::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Letter $letter
     * @param StoreDispositionRequest $request
     * @return RedirectResponse
     */
    public function store(StoreDispositionRequest $request, Letter $letter): RedirectResponse
    {
        try {
            $variab = array();
            foreach ($request->input('to') as $key => $to) {
                $user = User::where('id', '=', $to)->first();
                array_push($variab, $user);

                $newDisposition = $request->validated();
                $newDisposition['user_id'] = auth()->user()->id;
                $newDisposition['letter_id'] = $letter->id;
                $newDisposition['to'] = $to;
                Disposition::create($newDisposition);
            }

            // $getTo = User::where('id', '=', $to)->first();
            $getFrom = User::where('id', '=', auth()->user()->id)->first();
            $emailTo = array_column($variab, 'email');
            $allTo = array_column($variab, 'name');
            $allTo = implode(', ', $allTo);
            $letter_status = LetterStatus::find($request->letter_status)->status;

            if (auth()->user()->role == 'admin')
                $from = 'Kepala BPS Kab. Aceh Utara';
            else
                $from = $getFrom->name;

            $klasifikasi = Classification::where('code', $letter->classification_code)->first()->type;
            $perihal = $letter->note;
            $isi_disposisi = $request->content;
            $link_surat = route('transaction.incoming.show', $letter);
          	// $link_surat = $letter->attachments->first()->path_url;
            $tenggat_waktu = date('d F Y', strtotime($request->due_date));

            $list = array(
                'from' => $getFrom,
                'letter_status' => $letter_status,
                'allTo' => $allTo,
                'fromName' => $from,
                'classification' => $klasifikasi,
                'perihal' => $perihal,
                'disposisi' => $isi_disposisi,
                'link' => $link_surat,
                'due_date' => $tenggat_waktu,
            );

            Mail::to($emailTo)->send(new DispositionMail($list));

            return redirect()
                ->route('transaction.disposition.index', $letter)
                ->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            dd([$exception->getMessage(), $request->all()]);
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Letter $letter
     * @param Disposition $disposition
     * @return View
     */
    public function edit(Letter $letter, Disposition $disposition): View
    {
        return view('pages.transaction.disposition.edit', [
            'data' => $disposition,
            'letter' => $letter,
            'statuses' => LetterStatus::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateDispositionRequest $request
     * @param Letter $letter
     * @param Disposition $disposition
     * @return RedirectResponse
     */
    public function update(UpdateDispositionRequest $request, Letter $letter, Disposition $disposition): RedirectResponse
    {
        try {
            $disposition->update($request->validated());
            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Letter $letter
     * @param Disposition $disposition
     * @return RedirectResponse
     */
    public function destroy(Letter $letter, Disposition $disposition): RedirectResponse
    {
        try {
            $disposition->delete();
            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }
}
