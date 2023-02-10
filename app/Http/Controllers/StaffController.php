<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Log;
use Illuminate\Http\Request;
use App\Http\Resources\LogsResource;
use App\Http\Resources\StaffResource;
use App\Http\Requests\StaffRequest;

class StaffController extends Controller
{
    public function index(Request $request){
        if($request->search){
            $data = StaffResource::collection(
                User::query()
                ->with('profile')
                ->when($request->keyword, function ($query, $keyword) {
                    $query->whereHas('profile',function ($query) use ($keyword) {
                        $query->where(\DB::raw('concat(firstname," ",lastname)'), 'LIKE', "%{$keyword}%")
                        ->orWhere(\DB::raw('concat(lastname," ",firstname)'), 'LIKE', "%{$keyword}%");
                    })->orWhere(function ($query) use ($keyword) {
                        $query->where('username', 'LIKE', "%{$keyword}%")->whereNotIn('role',['Scholar','Administrator']);
                    });
                })
                ->whereNotIn('role',['Scholar','Administrator'])
                ->paginate(10)
                ->withQueryString()
            );
            return $data;
        }else{
            return inertia('Modules/Staffs/Index');
        }
    }

    public function store(StaffRequest $request){
        $data = \DB::transaction(function () use ($request){
            $data = User::create(array_merge($request->all(), ['password' => bcrypt('123456789')]));
            $data->profile()->create($request->all());
            return $data;
        });

        return back()->with([
            'message' => 'Staff created successfully. Thanks',
            'data' => new StaffResource($data),
            'type' => 'bxs-check-circle'
        ]); 
    }

    public function show(Request $request){
        if($request->staff == 'logs'){
            $data = Log::lists();
            return LogsResource::collection($data);
        }
        // $data = new StaffResource(User::findOrFail($request->staff));
        // return inertia('Modules/Staffs/Profile',['user' => $data]);
    }
}
