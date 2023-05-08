<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CampaignDetails;
use App\Models\CoursesInfo;
use App\Models\LeadDetails;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class LeadScraper extends Controller
{
    //
    /**
     * Lead Scrap From Facebook
     * @param Request $request
     * @return
     */
    public function dataScraper(Request $request)
    {
        if (!isset($request->client_id) || !isset($request->ac_k)) {
            return response()->json([
                'status' => false,
                'message' => 'Client id required '
            ], 406);
        }

        // dd($request->ac_k, $request->client_id);
        // Your code here!
        //$client_id = 2; // Set Client ID
        $accessToken = $request->ac_k;
        $client_id = $request->client_id;
        //dd($accessToken);
        // try {

        //$accessToken  =  "EAAKu5ZBYpE2cBAMOvGwtJwDv96WezHbxpdBbcFnBaqgmiMUNqMCNPAZApA0YuInf5AxHSiThsAIWETluZCPM7Bk4aLnBnAlrZBUChP9Lhoy9IFMbi9xSce8qCaJcaxUOln0lrKZAsjitJZAPTzJs7ay2T3XYhubnb7GNL0geO6DHkWSaZCIwE5EJZB54VxFruxgTr0KbFIGf7AZDZD";

        //$accessToken  = "EAAIfArqorGcBAMU8HzLfJ0KNrdHNWqOUZC1hSc2gcTd7ELcnDagLSlBuAInAtUFgPw5VB3PIMSZBIbPTswY9lQQ0K84XaJ5h2Pod01Y2lp1rJmXNamaQW3wFR006U3dGDRB31GfZCWZBxKZATGWVWVZAblnLPpY3XIZCw3EUoLmXbAmDlB9TGfmNXCZBO7z9HVIlzR4G7l0RLQZDZD";
        $url = "https://graph.facebook.com/v15.0/me?fields=id,name,adaccounts{campaigns.limit(10000){name,start_time,stop_time,status,ads{leads.limit(10000)}},business_name}&access_token=" . $accessToken;
        $dataArray = json_decode(file_get_contents($url), true);
        //dd($dataArray);
        $campaignData = [];
        $leads = [];
        $leadFrom = 'fb';
        if ($client_id != "" && $dataArray != "" && count($dataArray) > 0 && isset($dataArray['id'])) {
            if (isset($dataArray['adaccounts']['data']) && count($dataArray['adaccounts']['data']) > 0) {
                //dd($dataArray['adaccounts']['data']);
                $leadDetails = [];
                foreach ($dataArray['adaccounts']['data'] as $dataCampaign) {
                    if (isset($dataCampaign['campaigns']) && count($dataCampaign['campaigns']) > 0) { //Start of data scraping
                        $campaignData['account'][$dataCampaign['id']]['business_id'] = $dataCampaign['id'];
                        $campaignData['account'][$dataCampaign['id']]['business_name'] = $dataCampaign['business_name'];
                        if (isset($dataCampaign['campaigns']['data']) && count($dataCampaign['campaigns']['data']) > 0) {
                            $campaignDetails = [];
                            foreach ($dataCampaign['campaigns']['data'] as $campaign) {
                                //dd($campaign);
                                if (isset($campaign['ads']['data']) && count($campaign['ads']['data']) > 0) {
                                    foreach ($campaign['ads']['data'] as $adsData) {
                                        if (isset($adsData['leads']['data']) && count($adsData['leads']['data']) > 0) {
                                            $leadDetailsInfo = [];
                                            $tempArray = [];
                                            //dd($adsData['leads']['data']);
                                            foreach ($adsData['leads']['data'] as $lead) {
                                                //dd($lead);
                                                if (isset($lead['field_data']) && count($lead['field_data']) > 0) {
                                                    //dd($lead['field_data']);
                                                    if ($this->_checkFBInboxData($lead['field_data'])) {
                                                        // dd('here');
                                                        foreach ($lead['field_data'] as $fieldData) {
                                                            $tempArray['lead'][$lead['id']]['name'][] = $fieldData['name'];
                                                            $tempArray['lead'][$lead['id']]['data'][] = $fieldData;
                                                        }
                                                        //dd($tempArray);

                                                        if (count($tempArray['lead'][$lead['id']]['data']) > 0) {
                                                            foreach ($tempArray['lead'][$lead['id']]['data'] as $fieldValue) {
                                                                if (strlen(stristr($fieldValue['name'], "live_in")) > 0) {

                                                                    $leadDetailsInfo['lead'][$lead['id']]['work_location'] = $fieldValue['values'][0];
                                                                }
                                                                if (strlen(stristr($fieldValue['name'], "qualification_are_you")) > 0) {

                                                                    $courseCodeArray = explode('_', iconv('utf-8', 'ascii//TRANSLIT', $this->_cleanString($fieldValue['values'][0])));
                                                                    $leadDetailsInfo['lead'][$lead['id']]['course_title'] = $this->_cleanString(str_replace("_", " ", iconv('utf-8', 'ascii//TRANSLIT', $fieldValue['values'][0])));
                                                                    foreach ($courseCodeArray as $courseCodeString) {
                                                                        $isThereNumber = false;
                                                                        $courseCodeString = iconv('utf-8', 'ascii//TRANSLIT', $courseCodeString);
                                                                        for ($i = 0; $i < strlen($courseCodeString); $i++) {
                                                                            if (ctype_digit($courseCodeString[$i])) {
                                                                                $isThereNumber = true;
                                                                                break;
                                                                            }
                                                                        }
                                                                        if ($isThereNumber) {
                                                                            $leadDetailsInfo['lead'][$lead['id']]['course_code'] = trim(rtrim($courseCodeString, '-'));
                                                                        }
                                                                    }
                                                                    $courseCode = isset($leadDetailsInfo['lead'][$lead['id']]['course_code']) ? $leadDetailsInfo['lead'][$lead['id']]['course_code'] : '';
                                                                    if ($courseCode == "") {
                                                                        $courseCode = isset($leadDetailsInfo['lead'][$lead['id']]['course_title']) ? $leadDetailsInfo['lead'][$lead['id']]['course_title'] : '';
                                                                    }

                                                                    $courseData = CoursesInfo::where('course_code', '=', trim($courseCode))->first();
                                                                    if ($courseData === null) {

                                                                        $courseData = CoursesInfo::create([
                                                                            'course_code' => $courseCode,
                                                                            'course_title' => isset($leadDetailsInfo['lead'][$lead['id']]['course_title']) ? trim($leadDetailsInfo['lead'][$lead['id']]['course_title']) : '',
                                                                            'course_description' => isset($leadDetailsInfo['lead'][$lead['id']]['course_title']) ? $leadDetailsInfo['lead'][$lead['id']]['course_title'] : '',
                                                                            'status' => 1
                                                                        ]);
                                                                    }
                                                                } // EOF Course
                                                                // User Info
                                                                if (strlen(stristr($fieldValue['name'], "full_name")) > 0) {
                                                                    //dd('here');
                                                                    $leadDetailsInfo['lead'][$lead['id']]['full_name'] = $fieldValue['values'][0];
                                                                }
                                                                if (strlen(stristr($fieldValue['name'], "phone_number")) > 0) {
                                                                    $leadDetailsInfo['lead'][$lead['id']]['phone_number'] = $fieldValue['values'][0];
                                                                }
                                                                if (strlen(stristr($fieldValue['name'], "email")) > 0) {
                                                                    $leadDetailsInfo['lead'][$lead['id']]['email'] = $fieldValue['values'][0];
                                                                }
                                                            }
                                                        }
                                                        $leadDetailsInfo['lead'][$lead['id']]['lead_id'] = $lead['id'];
                                                        $leadDetailsInfo['lead'][$lead['id']]['lead_apply_date'] = $lead['created_time'];
                                                        $leadDetailsInfo['lead'][$lead['id']]['form_data'] = $lead['field_data'];
                                                        //
                                                        //dd($leadDetailsInfo);
                                                        $leadData = LeadDetails::where('lead_id', '=', $lead['id'])->first();
                                                        //dd($leadData);

                                                        $lead_apply_date = Carbon::parse($lead['created_time'])->toDateTime();
                                                        //dd($start_time); // 2020-11-23 13:26:02
                                                        if ($leadData === null) {
                                                            DB::table('lead_details')->insert([
                                                                'lead_id' => $lead['id'],
                                                                'student_id' => '0',
                                                                'full_name' => isset($leadDetailsInfo['lead'][$lead['id']]['full_name']) ? $leadDetailsInfo['lead'][$lead['id']]['full_name'] : '',
                                                                'phone_number' => isset($leadDetailsInfo['lead'][$lead['id']]['phone_number']) ? $leadDetailsInfo['lead'][$lead['id']]['phone_number'] : '',
                                                                'student_email' => isset($leadDetailsInfo['lead'][$lead['id']]['email']) ? $leadDetailsInfo['lead'][$lead['id']]['email'] : '',
                                                                'client_id' => isset($client_id) ? $client_id : '1',
                                                                'campaign_id' => isset($campaign['id']) ? $campaign['id'] : '',
                                                                'sales_user_id' => '0',
                                                                'document_certificate_id' => '0',
                                                                'course_id' => isset($courseData->id) ? $courseData->id : '0',
                                                                'work_location' => isset($leadDetailsInfo['lead'][$lead['id']]['work_location']) ? $leadDetailsInfo['lead'][$lead['id']]['work_location'] : '',
                                                                'lead_from' => $leadFrom,
                                                                'form_data' => json_encode($lead['field_data']),
                                                                'star_review' => '0',
                                                                'lead_apply_date' => isset($lead_apply_date) ? $lead_apply_date : '',
                                                                'lead_details_status' => 1 // Default New Lead
                                                            ]);

                                                            //

                                                        }
                                                    }
                                                }
                                            }

                                            $campaignDetails['campaign_name'] = $campaign['name'];
                                            $campaignDetails['campaign_id'] = $campaign['id'];
                                            $campaignDetails['start_time'] = $campaign['start_time'];
                                            $campaignDetails['stop_time'] = isset($campaign['stop_time']) ? $campaign['stop_time'] : $campaign['start_time'];
                                            $campaignDetails['campaign_status'] = $campaign['status'];

                                            ////////////Insert Campaign//////////////////////////

                                            $campaignData = CampaignDetails::where('campaign_id', '=', $campaign['id'])->first();
                                            $start_time = Carbon::parse($campaign['start_time'])->toDateTime();

                                            $stop_time = Carbon::parse($campaignDetails['stop_time'])->toDateTime();

                                            //dd($start_time); // 2020-11-23 13:26:02


                                            if ($campaignData === null) {
                                                $campaign = CampaignDetails::create([
                                                    'campaign_name' => $campaign['name'],
                                                    'campaign_id' => $campaign['id'],
                                                    'client_id' => $client_id,
                                                    'business_id' => $dataCampaign['id'],
                                                    'business_name' => $dataCampaign['business_name'],
                                                    'start_time' => $start_time,
                                                    'stop_time' => $stop_time,
                                                    'campaign_status' => $campaign['status']
                                                ]);
                                            }

                                            ////////////////////////////////
                                            $leadDetails['campaign'][$campaign['id']]['campaign_details'] = $campaignDetails;
                                            $leadDetails['campaign'][$campaign['id']]['campaign_leads'] = $leadDetailsInfo;
                                            $leadDetails['lead_from'] = $leadFrom;
                                        }
                                    }
                                }
                                $leads['account'][$dataCampaign['id']] = $leadDetails;
                            }
                        }
                    } // EOF data scraping
                }

                //                    $response = Http::post('http://leadapp.crm.com/api/lead/create', [
                //                        'data' => $leads,
                //                        'client_id' =>$client_id
                //                    ]);
                //
                //                    if($response->status() == 406){
                //                        return response()->json([
                //                            'status' => false,
                //                            'message' => 'While inserting the data, there was an issue. Please Try again. ',
                //                            'data' => json_decode($response->body(), true)
                //                        ], 406);
                //                    }else{
                //                        return response()->json([
                //                            'status' => true,
                //                            'message' => 'Data Scrap Successfully',
                //                            'data' => json_decode($response->body(), true)
                //                        ], 201);
                //                    }

            }
        } // EOF AdAccount
        //dd($tempArray);
        //dd($leads);

        // dd($leads);
        return response()->json([
            'status' => true,
            'message' => 'Data Scrap Successfully',
            //'data'   =>$leads
        ], 201);
        ///////////////////////////////
        //        } catch (\Throwable $th) {
        //            return response()->json([
        //                'status' => false,
        //                'message' => $th->getMessage()
        //            ], 500);
        //        }
        //////////////////////////////////////////
    }

    private function _checkFBInboxData($array)
    {
        //dd($array);
        $noInboxURL = true;
        foreach ($array as $fieldData) {
            if ($fieldData['name'] == 'inbox_url') {
                $noInboxURL = false;
            }
        }
        return $noInboxURL;
    }

    private function _cleanString($text)
    {
        $utf8 = array(
            '/[áàâãªä]/u'   =>   'a',
            '/[ÁÀÂÃÄ]/u'    =>   'A',
            '/[ÍÌÎÏ]/u'     =>   'I',
            '/[íìîï]/u'     =>   'i',
            '/[éèêë]/u'     =>   'e',
            '/[ÉÈÊË]/u'     =>   'E',
            '/[óòôõºö]/u'   =>   'o',
            '/[ÓÒÔÕÖ]/u'    =>   'O',
            '/[úùûü]/u'     =>   'u',
            '/[ÚÙÛÜ]/u'     =>   'U',
            '/ç/'           =>   'c',
            '/Ç/'           =>   'C',
            '/ñ/'           =>   'n',
            '/Ñ/'           =>   'N',
            '/–/'           =>   '-', // UTF-8 hyphen to "normal" hyphen
            '/[’‘‹›‚]/u'    =>   ' ', // Literally a single quote
            '/[“”«»„]/u'    =>   ' ', // Double quote
            '/ /'           =>   ' ', // nonbreaking space (equiv. to 0x160)
            '/¬/'             => ''
        );
        return preg_replace(array_keys($utf8), array_values($utf8), $text);
    }
}
