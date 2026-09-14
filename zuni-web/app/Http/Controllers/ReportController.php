<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{

    public function index()
    {
        return view('pages.report.index');
    }

    public function create()
    {
        return view('pages.report.register');
    }

    public function store(Request $request)
    {
        return redirect()
            ->route('pages.report.index')
            ->with('success', 'Relatório enviado com sucesso!');
    }

    public function show(Report $report)
    {
        return view('pages.report.show');
    }

    public function edit(Report $report)
    {
        return view('pages.report.edit');
    }

    public function update(Request $request, Report $report)
    {
        return redirect()
            ->route('pages.report.index')
            ->with('success', 'Relatório alterado com sucesso!');
    }

    public function destroy(Report $report)
    {
        return redirect()
            ->route('pages.report.index')
            ->with('success', 'Relatório apagado com sucesso!');
    }
}
