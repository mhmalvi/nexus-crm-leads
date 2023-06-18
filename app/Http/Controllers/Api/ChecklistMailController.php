<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\ChecklistMail;
use App\Models\MailTemplate;
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

    public function save_mail_template(Request $request)
    {   /////  save mail template
        $template_exist = MailTemplate::where('template_name', $request->template_name)->exists();
        if (!$template_exist) {
            $template = MailTemplate::create([
                'template_name' => $request->template_name,
                'template_description' => $request->template_description,
            ]);

            if ($template) {
                return response()->json([
                    'message' => 'inserted',
                    'status' => 201
                ], 201);
            } else {
                return response()->json([
                    'message' => 'failed',
                    'status' => 500
                ], 500);
            }
        } else {
            return response()->json([
                'message' => 'Template name already exists',
            ]);
        }
    }

    public function fetch_mail_templates(Request $request)
    {
        $template = MailTemplate::all();
        if ($template) {
            return response()->json([
                'message' => 'success',
                'data' => $template,
                'status' => 200
            ], 200);
        } else {
            return response()->json([
                'message' => 'not found',
                'status' => 404
            ], 404);
        }
    }
}
