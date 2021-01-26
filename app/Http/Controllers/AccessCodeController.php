<?php

namespace App\Http\Controllers;

use App\AccessCode;
use App\AppConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cookie;

class AccessCodeController extends Controller
{
    public function view()
    {
        return view('auth.access-code');
    }

    public function index()
    {
        $entities = AccessCode::all();

        $response = [
            'success' => !!count($entities),
            'entities' => $entities,
        ];

        return response()->json($response);
    }

    public function access(Request $request)
    {
        $request->validate([
            'access_code' => 'required|string',
        ]);

        $code = $request->input('access_code');

        $accessCode = AccessCode::query()
            ->where('code', $code)
            ->first();

        if (! $accessCode) {
            return redirect()->back();
        }

        $now = Carbon::now()->format('Y-m-d H:m:s');
        $date = $accessCode->expire_date;


        $duration = 60 * 60 * 24 * 5;
        if (! $accessCode->forever) {
            if ($now > $date) {
                return redirect()->back();
            }

            $duration = Carbon::parse($now)->diffInSeconds(Carbon::parse($date));
        }

        $hash = md5($accessCode->code);

        Cookie::queue('ACCESS', $hash,
            $duration
        );

        return redirect()
            ->route('app.home', AppConfig::getLocale());
    }

    public function store(Request $request, $id = 0)
    {
        $request->validate([
            'code' => 'string|required',
            'expire_date' => 'string|required',
            'forever' => 'boolean',
        ]);

        $forever = $request->input('forever');
        $expireDate = $request->input('expire_date');

        if ($forever) {
            $expireDate = now();
        }

        $entity = AccessCode::query()
            ->updateOrCreate([
                'id' => $id,
            ], [
                'code' => $request->input('code'),
                'expire_date' => $expireDate,
                'forever' => $forever,
            ]);

        $response = [
            'success' => !!$entity,
            'entity' => $entity,
        ];

        return response()->json($response);
    }

    public function delete($id)
    {
        $success = AccessCode::query()
            ->where('id', $id)
            ->delete();

        $response = [
            'success' => !!$success,
        ];

        return response()->json($response);
    }
}
