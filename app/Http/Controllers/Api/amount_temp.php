public function leadAddAmount(Request $request)
    {

        if (!isset($request->lead_id) || !isset($request->amount)) {
            return response()->json([
                'status' => false,
                'message' => 'Lead id and Lead Amount required'
            ], 406);
        }


        try {
            LeadAmountHistory::updateOrcreate([
                'lead_id' => $request->lead_id,
                'amount' => $request->amount
            ])->toArray();
            $leadAmountHistory = LeadAmountHistory::where('lead_id', '=', $request->lead_id)->get()->toArray();
            return response()->json([
                'status' => true,
                'message' => 'Lead amount added successfully',
                'data'   => $leadAmountHistory

            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }