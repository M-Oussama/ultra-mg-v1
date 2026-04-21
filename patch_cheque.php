<?php
$content = file_get_contents('app/Http/Controllers/ChequeController.php');

$storeFind = "'client_id' => 'required|exists:certify_clients,id',\n            'file' => 'nullable|mimes:pdf|max:10240', // Max 10MB PDF\n        ]);";
$storeReplace = "'client_id' => 'required|exists:certify_clients,id',\n            'amount' => 'required|numeric|min:0',\n            'file' => 'nullable|mimes:pdf|max:10240',\n        ]);";
$content = str_replace($storeFind, $storeReplace, $content);

$dataFind = "\$data = \$request->only(['cheque_date', 'cheque_number', 'client_id']);";
$dataReplace = "\$data = \$request->only(['cheque_date', 'cheque_number', 'client_id', 'amount']);";
$content = str_replace($dataFind, $dataReplace, $content);

$updateFind = "'client_id' => 'sometimes|exists:certify_clients,id',\n            'file' => 'nullable|mimes:pdf|max:10240',\n        ]);";
$updateReplace = "'client_id' => 'sometimes|exists:certify_clients,id',\n            'amount' => 'sometimes|numeric|min:0',\n            'file' => 'nullable|mimes:pdf|max:10240',\n        ]);";
$content = str_replace($updateFind, $updateReplace, $content);

$deleteFind = "public function delete(\$id)
    {
        \$cheque = Cheque::find(\$id);
        if (!\$cheque) {
            return response()->json(['message' => 'Cheque not found'], 404);
        }

        \$cheque->delete();";
$deleteReplace = "public function delete(\$id)
    {
        \$cheque = Cheque::find(\$id);
        if (!\$cheque) {
            return response()->json(['message' => 'Cheque not found'], 404);
        }

        \$usedInCertify = \App\Models\CertifyInvoices::where('cheque_number', \$cheque->cheque_number)->exists();
        \$usedInSubCertify = \App\Models\SubCertifyInvoices::where('cheque_number', \$cheque->cheque_number)->exists();
        if (\$usedInCertify || \$usedInSubCertify) {
            return response()->json(['message' => 'Cannot delete this cheque. It is used by one or more invoices.'], 422);
        }

        \$cheque->delete();";
$content = str_replace($deleteFind, $deleteReplace, $content);

$newMethods = "
    public function getStatus(Request \$request, \$chequeId)
    {
        return \$this->getStatusExcluding(\$request, \$chequeId, null);
    }

    public function getStatusExcluding(Request \$request, \$chequeId, \$excludeCommandId = null)
    {
        \$cheque = Cheque::find(\$chequeId);
        if (!\$cheque) return response()->json(['message' => 'Cheque not found'], 404);

        \$usedCertify = \\App\\Models\\CertifyInvoices::where('cheque_number', \$cheque->cheque_number);
        \$usedSub = \\App\\Models\\SubCertifyInvoices::where('cheque_number', \$cheque->cheque_number);

        if (\$excludeCommandId) {
            \$type = \$request->query('type');
            if (\$type === 'sub') {
                \$usedSub->where('id', '!=', \$excludeCommandId);
            } elseif (\$type === 'certify') {
                \$usedCertify->where('id', '!=', \$excludeCommandId);
            } else {
                \$usedCertify->where('id', '!=', \$excludeCommandId);
                \$usedSub->where('id', '!=', \$excludeCommandId);
            }
        }

        \$usedAmount = \$usedCertify->sum('amount') + \$usedSub->sum('amount');
        \$remaining = max(0, \$cheque->amount - \$usedAmount);

        return response()->json([
            'used_amount' => (float)\$usedAmount,
            'remaining_balance' => (float)\$remaining,
            'is_available' => ((float)\$remaining > 0),
            'cheque_amount' => (float)\$cheque->amount,
        ]);
    }

    public function getAvailable()
    {
        \$cheques = Cheque::with('client')->get()->map(function(\$cheque) {
            \$usedCertify = \\App\\Models\\CertifyInvoices::where('cheque_number', \$cheque->cheque_number)->sum('amount');
            \$usedSub = \\App\\Models\\SubCertifyInvoices::where('cheque_number', \$cheque->cheque_number)->sum('amount');
            \$used = \$usedCertify + \$usedSub;
            \$cheque->used_amount = \$used;
            \$cheque->remaining_balance = max(0, \$cheque->amount - \$used);
            return \$cheque;
        })->filter(function(\$cheque) {
            return \$cheque->remaining_balance > 0;
        })->values();

        return response()->json(\$cheques);
    }
}
";

$content = preg_replace('/\}\s*$/', $newMethods, $content);

file_put_contents('app/Http/Controllers/ChequeController.php', $content);
echo "success";
