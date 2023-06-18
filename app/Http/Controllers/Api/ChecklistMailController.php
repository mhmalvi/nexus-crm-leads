<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\ChecklistMail;
use Illuminate\Support\Facades\Mail;

class ChecklistMailController extends Controller
{
    public function leadMail(Request $request)   ///send mail with checklist attachment
    {
        // dd("fghbfg");
        $template = $request->template;
        if ($request->checklist) {

            $fileName = time() . '.' . $request->checklist->getClientOriginalExtension();
            $request->checklist->move(public_path('assets/checklist_pdf'), $fileName);
            // dd($fileName);
            $file_path = "assets/checklist_pdf/" . $fileName;
            if ($template) {
                return response()->json([
                    'message' => 'Mail sent',
                    'status' => 200
                ]);
                Mail::to("megatanjib@gmail.com")->queue(new ChecklistMail($file_path, $template));
            } else {
                return response()->json([
                    'message' => 'please provide mail template',
                    'status' => 400
                ]);
            }
        } else {
            return response()->json([
                'message' => 'please upload checklist',
                'status' => 400
            ]);
        }
    }

    public function save_mail_template(Request $request){   /////  save mail template
        dd($request->all());
    }
}
