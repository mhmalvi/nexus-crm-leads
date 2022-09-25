<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LeadScraper extends Controller
{
    //
    /**
     * Lead Scrap From Facebook
     * @param Request $request
     * @return
     */
    public function dataScraper(Request $request){

        try {
//            $decodeUrl = urldecode("https://graph.facebook.com/v15.0/me?fields=id%2Cname%2Cadaccounts%7Bcampaigns%7Bname%2Cstart_time%2Cstop_time%2Cstatus%2Cads%7Bleads%7D%7D%7D&access_token=EAAKu5ZBYpE2cBAMOvGwtJwDv96WezHbxpdBbcFnBaqgmiMUNqMCNPAZApA0YuInf5AxHSiThsAIWETluZCPM7Bk4aLnBnAlrZBUChP9Lhoy9IFMbi9xSce8qCaJcaxUOln0lrKZAsjitJZAPTzJs7ay2T3XYhubnb7GNL0geO6DHkWSaZCIwE5EJZB54VxFruxgTr0KbFIGf7AZDZD"). "\n";

            $accessToken  =  "EAAKu5ZBYpE2cBAMOvGwtJwDv96WezHbxpdBbcFnBaqgmiMUNqMCNPAZApA0YuInf5AxHSiThsAIWETluZCPM7Bk4aLnBnAlrZBUChP9Lhoy9IFMbi9xSce8qCaJcaxUOln0lrKZAsjitJZAPTzJs7ay2T3XYhubnb7GNL0geO6DHkWSaZCIwE5EJZB54VxFruxgTr0KbFIGf7AZDZD";

            //$accessToken  = "EAAIfArqorGcBAK9ZBR7IjJDk4KPkTtm3DOjBtJiBpnO0WUnDb1Vhk4xMVZACdL6h0NZAxAY80QpSzxyZBQECIOePPCZBzmvxX7iztei5l14nYQyID4CiTG2o9d9HxWqxm4idVjIFtTxbEsOIFyO5pVjWDIln3dvLnwPN8Uh3a4h0qJgwBgPdYdyjPbZCbJJU9bcXdLteNOZBgZDZD";

            $url = "https://graph.facebook.com/v15.0/me?fields=id,name,adaccounts{campaigns.limit(10000){name,start_time,stop_time,status,ads{leads.limit(10000)}},business_name}&access_token=".$accessToken;

            $dataArray = json_decode(file_get_contents($url), true);
            //dd($dataArray);

            $campaignData = [];
            $leads = [];
            $courseInfo = [];
            $courseQueries = [];
            $queriesAnswer = [];
            $user = [];
            $leadFrom = 'fb';

            if($dataArray!="" && count($dataArray)>0){
                if(isset($dataArray['adaccounts']['data']) && count($dataArray['adaccounts']['data'])>0){
                    //dd($dataArray['adaccounts']['data']);
                    foreach ($dataArray['adaccounts']['data'] as $dataCampaign){
                        if(isset($dataCampaign['campaigns']) && count($dataCampaign['campaigns'])>0){ //Start of data scraping
                            //dd($dataCampaign['business_name']);
                            $campaignData['business_id'] = $dataCampaign['id'];
                            $campaignData['business_name'] = $dataCampaign['business_name'];

                            if(isset($dataCampaign['campaigns']['data']) && count($dataCampaign['campaigns']['data'])>0){
                                //dd($dataCampaign['campaigns']['data']);
                                $campaignDetails = [];

                                foreach ($dataCampaign['campaigns']['data'] as $campaign){
                                    $campaignDetails['campaign_name'] = $campaign['name'];
                                    $campaignDetails['campaign_id'] = $campaign['name'];
                                    $campaignDetails['start_time'] = $campaign['start_time'];
                                    $campaignDetails['stop_time'] = isset($campaign['stop_time'])?$campaign['stop_time']:$campaign['start_time'];
                                    $campaignDetails['campaign_status'] = $campaign['status'];
                                    $campaignData['data'][]=$campaignDetails;

                                    if(isset($campaign['ads']['data']) && count($campaign['ads']['data'])>0){
                                        foreach ($campaign['ads']['data'] as $adsData){
                                            if(isset($adsData['leads']['data']) && count($adsData['leads']['data'])>0){
                                               $leadDetails = [];
                                                $courseDetails = [];
                                               foreach ($adsData['leads']['data'] as $lead){

                                                   if(isset($lead['field_data']) && count($lead['field_data'])>0){
                                                       //dd($lead['field_data']);
                                                       $tempArray = [];
                                                       foreach ($lead['field_data'] as $fieldData){
                                                           $tempArray['name'][]=$fieldData['name'];
                                                           $tempArray['data'][]=$fieldData;
//
                                                       }
                                                       //dd($tempArray);
                                                       if(!in_array('inbox_url', $tempArray['name'])){
                                                           //dd($tempArray);
                                                           if(count($tempArray['data'])>0){
                                                               foreach ($tempArray['data'] as $fieldValue){
                                                                   if (strlen(stristr($fieldValue['name'],"live_in"))>0) {
                                                                       //echo "true";die(); // are Found
                                                                       $leadDetails['work_location'] = $fieldValue['values'][0];
                                                                       //dd($leadDetails);
                                                                   }
                                                                   if (strlen(stristr($fieldValue['name'],"qualification_are_you"))>0) {
                                                                       //echo "true";die(); // are Found
                                                                       $courseDetails['title'] = $fieldValue['values'][0];
                                                                       $courseCodeArray = explode('_', $fieldValue['values'][0]);
                                                                       //dd($courseCodeArray);
                                                                       foreach ($courseCodeArray as $courseCodeString){
                                                                           if (preg_match('~[0-9]+~', $courseCodeString)) {
                                                                               $courseDetails['course_code'] = trim(rtrim($courseCodeString, '-'));
                                                                               break;
                                                                           }
//                                                                           if(ctype_alnum($courseCodeString)===true){
//                                                                               $courseDetails['course_code'] = $courseCodeString;
//                                                                               break;
//                                                                               //dd($courseCodeString);
//                                                                           }
                                                                       }
                                                                      // dd($courseDetails);

                                                                       //dd($leadDetails);
                                                                   }

                                                               }
                                                           }
                                                       }else{
                                                           $tempArray = [];
                                                       }
                                                   }
                                                   if(isset($leadDetails['work_location'])){
                                                       $leadDetails['lead_id'] = $lead['id'];
                                                       $leadDetails['lead_apply_date'] = $lead['created_time'];
                                                       $leadDetails['lead_from'] = $leadFrom;
                                                       $leads[]=$leadDetails;
                                                       $courseInfo[] = $courseDetails;
                                                   }

                                               }
                                            }
                                        }
                                    }
                                }
                            }
                        } // EOF data scraping
                    }
                }
            }

            dd($courseInfo);
            dd($leads);
            dd($campaignData);
            return response()->json([
                'status' => true,
                'message' => 'Data Scrap Successfully',
                'data' => []
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }

    }
}
