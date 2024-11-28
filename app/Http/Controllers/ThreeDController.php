<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class ThreeDController extends Controller
{
    public function getFreeBoxes(Request $request)
    {
        try {
            $start = $request->input('start');
            $finish = $request->input('finish');

            if (!$start || !$finish) {
                return response()->json(['error' => 'Missing parameters'], 400);
            }

            $cacheKey = 'free_boxes_reallogistic_' . md5($start . $finish);

            $freeBoxes = Cache::remember($cacheKey, 86400, function () use ($start, $finish) {
                $boxes = DB::table('reallogistic_boxes')->orderBy('title')->get();
                $boxPrices = DB::table('reallogistic_boxes_price')->pluck('price', 'id');

                $freeBoxes = [];
                $amount = 1; // Ensure amount is initialized here


                foreach ($boxes as $box) {
                    for ($i = 1; $i <= $box->amount; $i++) {
                        for ($e = 0; $e <= 3; $e++) {
                            for ($c = 1; $c <= 3; $c++) {
                                $boxId = $box->title . str_pad($amount, 4, '0', STR_PAD_LEFT) . "E$e" . "C$c";
                                $reserved = DB::table('reallogistic_reserve')
                                    ->where('box_id', $boxId)
                                    ->where('start', '<=', strtotime($finish))
                                    ->where('finish', '>=', strtotime($start))
                                    ->exists();

                                if (!$reserved) {
                                    $price = $boxPrices[$boxId] ?? 0;
                                    $freeBoxes[] = ['box_id' => $boxId, 'price' => $price];
                                }
                                $amount++; // Increment amount here
                            }
                        }
                    }
                }


                return $freeBoxes;
            });

            return response()->json($freeBoxes);
        } catch (Exception $e) {
            return $e;
        }
    }

    public function reserveBoxesWithData(Request $request)
    {


        try {
            $boxIds = $request->input('box_id');
            $start = $request->input('start');
            $finish = $request->input('finish');

            if (!$boxIds || !$start || !$finish) {
                return response()->json(['error' => 'Missing parameters'], 400);
            }

            $boxIds = explode(',', $boxIds);
            $reserveIds = [];

            foreach ($boxIds as $boxId) {
                $reserved = DB::table('reallogistic_reserve')
                    ->where('box_id', $boxId)
                    ->where('start', strtotime($start))
                    ->where('finish', strtotime($finish))
                    ->exists();

                if (!$reserved) {
                    $reserveId = DB::table('reallogistic_reserve')->insertGetId([
                        'box_id' => $boxId,
                        'start' => strtotime($start),
                        'finish' => strtotime($finish),
                        //'price' => 200
                    ]);
                    $reserveIds[] = $reserveId;
                }
            }

            $file = $request->file('file');
            $fileName = $file ? md5(time()) . '.' . $file->getClientOriginalExtension() : '';
            if ($file) {
                Storage::putFileAs('uploads', $file, $fileName);
            }

            DB::table('reallogistic_reserve_data')->insert([
                'reserve_ids' => implode(',', $reserveIds),
                'category' => $request->input('category', ''),
                'file' => $fileName,
                'comment' => $request->input('comment', ''),
                'first_name' => $request->input('first_name', ''),
                'last_name' => $request->input('last_name', ''),
                'email' => $request->input('email', ''),
                'phone' => $request->input('phone', '')
            ]);

            // Clear cache for free boxes
            $cacheKey = 'free_boxes_reallogistic_' . md5($start . $finish);
            Cache::forget($cacheKey);

            // $this->sendEmailNotification($request, $boxIds, $fileName);

            return response()->json(['status' => 'done']);
        } catch (Exception $e) {
            return $e;
        }
    }

    private function sendEmailNotification(Request $request, $boxIds, $fileName)
    {
        $subject = "Allées " . date("d-m-Y H:i", time());
        $data = [
            'subject' => $subject,
            'box_id' => implode(',', $boxIds),
            'category' => $request->input('category', ''),
            'file_name' => $fileName,
            'comment' => $request->input('comment', ''),
            'first_name' => $request->input('first_name', ''),
            'last_name' => $request->input('last_name', ''),
            'email' => $request->input('email', ''),
            'phone' => $request->input('phone', '')
        ];

        Mail::send('emails.reservation', $data, function ($message) use ($subject) {
            $message->to('zazurka@mail.ru', 'Recipient Name')
                ->cc('maariyah@dataconsulting.tech', 'CC Recipient Name')
                ->subject($subject);
        });
    }

    public function geReservedBoxes(Request $request)
    {
        try {
            $boxesReserved = DB::table('reallogistic_reserve')->pluck('box_id')->toArray();
            return response()->json($boxesReserved);
        } catch (Exception $e) {
            dd('erreur ', $e);
            //return $e;
        }
    }
}
