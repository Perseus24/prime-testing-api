<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportTestCasesRequest;
use App\Services\TestCaseImporter;
use Illuminate\Http\Request;

class TestCaseImportController extends Controller
{
    public function preview(ImportTestCasesRequest $request, TestCaseImporter $importer)
    {
        return response()->json([
            'data' => $importer->preview($request->file('file'), $request->boolean('has_header', true)),
        ]);
    }

    public function store(ImportTestCasesRequest $request, TestCaseImporter $importer)
    {
        return response()->json(
            $importer->import($request->file('file'), $request->input('on_duplicate', 'skip'), $request->boolean('has_header', true))
        );
    }
}
