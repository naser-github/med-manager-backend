<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function test()
    {


//        $user = DB::table('users')->where('email', 'hbj')->first();
//        if ($user){
//            dd($user);
//        }else{
//            dd('nothing');
//        }

//        $results = DB::table('test_codes')->orderBy('TESTCODE')->get();
//
//        $test_codes = $results->unique('TESTCODE')->sortBy('TESTCODE')->pluck('TESTCODE');
//
//        $padids = $results->unique('PATID');
//
//        $array = array();
//
//        foreach ($padids as $padid){
//            foreach ($test_codes as $test_code){
//                $result = $results->where('TESTCODE', $test_code)->where('PATID', $padid->PATID)->first();
//
//                if ($result){
//                   $array[] =  $result->RESULT;
//                }else{
//                    $array[] =  '';
//                }
//            }
//            $padid->array = $array;
//            $array = [];
//
//        }
//
//        return view('data', compact('padids', 'test_codes'));

//        $string = 'PATID,AGEY,AGEM,AGED,SEX,';
//
//        $testcode = DB::table('test_codes')
//            ->select('TESTCODE')
//            ->groupBy('TESTCODE')
//            ->get();
//
//        foreach ($testcode as $code) {
//            $string = $string . 'SUM(Case WHEN TESTCODE = "' . $code->TESTCODE . '" then RESULT end) as "' . 'TESTCODE' . $code->TESTCODE . '",';
//        }
//
//        $string .= 'created_at';
//
//        $query = DB::table('test_codes')
//            ->select(DB::raw($string), 'created_at')
//            ->groupBy('PATID')
//            ->get();
//
//        return view('data', compact('query', 'testcode'));

    }
}
