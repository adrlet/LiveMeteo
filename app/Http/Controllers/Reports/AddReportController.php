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

class AddReportController extends Controller
{
    public function create(Request $request)
    {
        $date = Carbon::now()->subHours(6)->format('Y-m-d H:i:s');
        $selectAtri = 'reports.id,
        ST_X(reports.coordinates) as x,
        ST_Y(reports.coordinates) as y,
        reports.category,
        reports.temperature,
        reports.wind,
        time(reports.created_at) as created_time,
        users.login,
        users.name';

        $reports = Report::join('users', 'users.id', '=', 'reports.user_id')
        ->select(DB::raw($selectAtri))
        ->where('reports.created_at', '>', $date)
        ->get();

        $user = $request->user('sanctum');
        $auth = $user && $user->email_verified_at != null;
        foreach($reports as $report)
        {
            $report->category = trans('report.'.$report->category);
            $report->wind = trans('report.'.$report->wind);

            if($report->login == null)
                $report->name == 'Usunięty';
            else if($report->name == null)
                $report->name = $report->login;
            unset($report->login);

            $voted = TRUE;
            if($auth)
                if(Reportsrating::where('user_id', $user->id)->where('report_id', $report->id)->first() == null)
                    $voted = FALSE;
            $report->voted = $voted;
        }

        return response()->json(compact('reports'));
    }

    public function store(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'coordinatesX' => ['numeric', 'required'],
            'coordinatesY' => ['numeric', 'required'],
            'category' => ['string', 'required', 'in:SUNNY,CLOUDY,STORM,RAINFALL,SNOWING,HAILING'],
            'temperature' => ['numeric', 'required'],
            'wind' => ['string', 'required', 'in:STRONG,WEAK,NO'],
        ]);

        if($valid->fails())
            return response()->json(['errors' => $valid->errors()]);

        $report = new Report;
        $report->fill([
            'coordinates' => DB::raw("GeomFromText('POINT(".$request->coordinatesX." ".$request->coordinatesY.")')"),
            'category' => $request->category,
            'temperature' => $request->temperature,
            'wind' => $request->wind,
            'user_id' => Auth::user()->id,
        ])->save();

        return response()->json('True');
    }
}
