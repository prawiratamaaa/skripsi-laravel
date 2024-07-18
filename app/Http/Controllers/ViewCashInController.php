<?php

namespace App\Http\Controllers;

use App\Models\DetailJournalEntry;
use App\Models\JournalEntry;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ViewCashInController extends Controller
{
    public function index($id)
    {
        Log::debug('Id Journal Entry:', ['id' => $id]);

        $journalData = JournalEntry::joinDetailAndUsers($id);
        $detailJournal = DetailJournalEntry::where('id',$id)->first();
        
        $journalEntry = $journalData->journalEntry;
        $id = $journalEntry ? $journalEntry->id : null;

        Log::debug('evidence img:' .json_encode($journalData->details));
        Log::debug('evidence img:' .json_encode($detailJournal));
        Log::debug('evidence code:' .json_encode($id));

        return view('user-accounting.view-cash-in', [
            'journalEntry' => $journalData->journalEntry,
            'details' => $journalData->details,
            'detailJournal' => $detailJournal,
            'id' => $id
        ]);
    }

    public function cancel($id)
    {
        try {
            $journalEntry = JournalEntry::findOrFail($id);

            // Update entri 
            $journalEntry->is_reversed = 1;
            $journalEntry->reversed_by = Auth::user()->name;
            $journalEntry->reversed_at = now();
            $journalEntry->save();

            return redirect()->route('view-cash-in.index', ['id' => $id])->with('berhasil', 'Entri jurnal berhasil dibatalkan.');
        } catch (Exception $e) {
            return redirect()->route('view-cash-in.index', ['id' => $id])->with('gagal', 'Entru gagal dibatalkan.' . $e->getMessage());
        }
    }
}
