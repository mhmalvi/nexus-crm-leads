<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\ChecklistMail;
use App\Models\MailTemplate;
use App\Models\Checklist;
use Illuminate\Support\Facades\Mail;

class ChecklistMailController extends Controller
{
    public function save_checklist(Request $request)
    {       //////////// insert checklist to database //////////////
        // dd(json_encode($request->checklist_file));
        $request->validate([
            'checklist_file' => 'required|mimes:pdf'
        ]);
        $checklists = new Checklist();
        if ($request->checklist_file) {
            $data = array();
            foreach ($request->checklist_file as $files) {
                $fileName = $files->getClientOriginalName();
                $checklist_exist = Checklist::where('file_name', $fileName)->exists();
                if (!$checklist_exist) {
                    $path = $files->store('assets/checklists', ['disk' =>   'checklist_files']);
                    $save = Checklist::create([
                        'file_path' => $path,
                        'file_name' => $fileName,
                        'uploaded_by' => $request->uploaded_by,
                        'company_id' => $request->company_id
                    ]);
                    $path = "";
                } else {
                    return response()->json([
                        'message' => 'File already exists',
                        'status' => 409
                    ], 409);
                }
            }
            if ($save) {
                return response()->json([
                    'message'    => 'Uploaded successfully',
                    'status' => 201,
                    'data' => $save
                ], 201);
            }
        } else {
            return response()->json([
                'message' => 'Please select a pdf file',
                'status' => 404
            ], 404);
        }
    }

    public function fetch_checklist(Request $request, $id)
    {
        // dd($id);
        $checklists = Checklist::where('company_id', $id)->get();
        if ($checklists) {
            return response()->json([
                'message' => 'success',
                'status' => 200,
                'data' => $checklists
            ], 200);
        } else {
            return response()->json([
                'message' => 'failed',
                'status' => 500
            ], 500);
        }
    }

    public function delete_checklist(Request $request, $id)
    {
        // dd($id);
        // dd($request);
        if ($request->file_id !== 0) {
            $checklist = array();
            // foreach($request->id as $file_id){
            $checklist = Checklist::where('company_id', $id)->where('id', $request->file_id)->first();
            $file_path = public_path($checklist->file_path);
            // dd($file_path);
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            $delete = $checklist->delete();
            if ($delete) {
                return response()->json([
                    'message' => "Deleted successfully",
                    'status' => 201
                ], 201);
            } else {
                return response()->json([
                    'message' => "Failed",
                    'status' => 500
                ], 500);
            }
        } else if ($request->attach_list) {
            foreach ($request->attach_list as $file) {
                // dd($file['id']);
                $checklist = Checklist::where('company_id', $id)->where('id', $file['id'])->first();
                $file_path = public_path('products/images/' . $checklist->file_name);
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
                $delete = $checklist->delete();
            }
            if ($delete) {
                return response()->json([
                    'message' => "Deleted successfully",
                    'status' => 201
                ], 201);
            } else {
                return response()->json([
                    'message' => "Failed",
                    'status' => 500
                ], 500);
            }
        } else {
            return response()->json([
                'message'    => "No data selected"
            ]);
        }


        // }
        // dd($checklist);

        if ($delete) {
            return response()->json([
                'message' => 'Deleted successfully',
                'status' => 200
            ], 200);
        } else {
            return response()->json([
                'message'    => 'Failed',
                'status' => 500
            ], 500);
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
                    'message' => 'Template inserted successfully',
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

    public function leadMail(Request $request)
    {
        // dd("fghbfg");
        $template = $request->template;
        $subject = $request->mail_subject;
        if ($request->checklist) {

            $fileName = time() . '.' . $request->checklist->getClientOriginalExtension();
            // $request->checklist->move(public_path('assets/checklist_pdf'), $fileName);
            // dd($fileName);
            $file_path = "assets/checklist_pdf/" . $fileName;
            if ($template) {

                Mail::to($request->student_email)->cc($request->sender)->bcc($request->sender)->queue(new ChecklistMail($file_path, $template, $subject));
                return response()->json([
                    'message' => 'Mail sent',
                    'status' => 200
                ]);
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

    public function fetch_mail_templates_by_id($id)
    {
        $template = MailTemplate::find($id);
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

    public function delete_template(Request $request, $id)
    {

        $template = MailTemplate::find($id);
        if ($template) {
            $delete = $template->delete();
            if ($delete) {
                return response()->json([
                    'message' => 'deleted',
                    'data' => $template,
                    'status' => 200
                ], 200);
            } else {
                return response()->json([
                    'message' => 'failed',
                    'status' => 500
                ], 500);
            }
        } else {
            return response()->json([
                'message' => 'not found',
                'status' => 404
            ], 404);
        }
    }
}
