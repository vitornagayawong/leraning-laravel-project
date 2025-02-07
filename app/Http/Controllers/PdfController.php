<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function geraPdf() {
        $clientes = Cliente::all();
        //dd($clientes);
        $pdf = FacadePdf::loadView('pdf', compact('clientes'));

        return $pdf->setPaper('a4')->stream('Todos_clientes.pdf');

    }
}
