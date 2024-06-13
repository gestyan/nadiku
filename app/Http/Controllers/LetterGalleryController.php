<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Letter;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LetterGalleryController extends Controller
{
    public function incoming(Request $request): View
    {
      	if(auth()->user()->role == 'staff'){
        	$data = Attachment::join('letters', 'attachments.letter_id', '=', 'letters.id')
          							->join('dispositions', 'dispositions.letter_id', '=', 'letters.id')
          							->select('attachments.*')
          							->where('letters.type', 'incoming')
          							->where('dispositions.to', auth()->user()->id)->render($request->search);
        } else {
        	$data = Attachment::incoming()->render($request->search);
        }
        return view('pages.gallery.incoming', [
            'data' => $data,
            'search' => $request->search,
        ]);
    }

    public function outgoing(Request $request): View
    {
      	if(auth()->user()->role == 'staff'){
        	$data = Attachment::outgoing()->whereHas('letter', function($q) {
            			$q->where('user_id', auth()->user()->id);
            		})->render($request->search);
        } else {
        	$data = Attachment::outgoing()->render($request->search);
        }
        return view('pages.gallery.outgoing', [
            'data' => $data,
            'search' => $request->search,
        ]);
    }
}
