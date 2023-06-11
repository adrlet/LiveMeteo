<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Reportsrating;
use Illuminate\Support\Facades\Validator;   

class ShowReportController extends Controller
{
    public function create(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:reports'],
        ]);

        if($valid->fails())
            return response()->json(['errors' => $valid->errors()]);

        $date = Carbon::now()->subHours(6)->format('Y-m-d H:i:s');
        $selectAtri = 'reports.id,
        ST_X(reports.coordinates) as x, 
        ST_Y(reports.coordinates) as y, 
        reports.category,
        reports.temperature,
        reports.wind,
        reports.created_at,
        users.login,
        users.name';
        
        $report = Report::join('users', 'users.id', '=', 'reports.user_id')
        ->select(DB::raw($selectAtri))
        ->where('reports.created_at', '>', $date)
        ->find($request->id);

        if($report == null)
            return response()->json(['errors' => [0 => [0 => trans('report.null')]]]);

        if($report->login == null)
            $report->name == 'Usunięty';
        else if($report->name = null)
            $report->name = $report->login;
        unset($report->login);

        $user = $request->user('sanctum');
        $voted = TRUE;
        if($user && $user->email_verified_at != null)
            if(Reportsrating::where('user_id', $user->id)->where('report_id', $request->id)->first() == null)
                $voted = FALSE;

        return response()->json(['report' => $report, 'voted' => $voted]);
    }

    public function store(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'rate' => ['required', 'string'],
            'id' => ['required', 'integer', 'exists:reports'],
        ]);

        if($valid->fails())
            return response()->json(['errors' => $valid->errors()]);

        $date = Carbon::now()->subHours(6)->format('Y-m-d H:i:s');
        $report = Report::where('created_at', '>', $date)->find($request->id);
        if($report == null)
            return response()->json(['errors' => [0 => [0 => trans('report.null')]]]);

        $user = $request->user('sanctum');
        $reportsrating = Reportsrating::firstOrNew(['user_id' => $user->id, 'report_id' => $request->id]);
        if($reportsrating->rate != null)
            return response()->json(['errors' => [0 => [0 => trans('report.voted')]]]);

        if($request->rate == 'up')
        {
            $report->rating = $report->rating+1;
            $reportsrating->rate = 'up';
        }
        elseif($request->rate == 'down')
        {
            $report->rating = $report->rating-1;
            $reportsrating->rate = 'down';
        }
        else
            return response()->json(['errors' => [0 => [0 => trans('report.wrongvote')]]]);
        
        $report->save();
        $reportsrating->save();

        if($report->rating < -5)
        {
            $report->delete();
            return response()->json(['errors' => [0 => [0 => trans('report.null')]]]);
        }

        return response()->json('True');
    }
}
