<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Auth;
use Smalot\PdfParser\Parser;


class PDFController extends Controller
{
    
    public function uploadAndPrint(Request $request)
    {
        $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:10240', 
            'copies' => 'required|integer|min:1',
            'is_color' => 'required|in:0,1',
        ]);
        // dd($request->file('pdf'));
        $pdfFile = $request->file('pdf');
        $filename = time() . '_' . $pdfFile->getClientOriginalName();
        $path = $pdfFile->storeAs('pdfs', $filename, 'public');
    
        $fullPath = storage_path("app/public/{$path}");
    
        $pageCount = $this->getPdfPageCount($fullPath);

        $pricePerPage = ($request->input('is_color')) ? 1 : 0.90;
        $copies = (int)$request->input('copies');
        $totalCost = $pageCount * $copies * $pricePerPage;
    
        $user = Auth::user();
        if ($user->wallet_balance < $totalCost) {
            return back()->with('error', 'الرصيد غير كافٍ لإتمام العملية.');
        }
    
        $user->wallet_balance -= $totalCost;
        $user->save();
    
        $user->walletTransactions()->create([
            'type' => 'withdrawal',
            'amount' => $totalCost,
            'description' => "طباعة {$copies} نسخة من ملف PDF ({$pageCount} صفحة لكل نسخة)."
        ]);
    
        $this->printPDF($fullPath, $copies,$request->input('is_color'));
    
        return back()->with('success', "تمت العملية بنجاح. تم خصم {$totalCost} جنيه من رصيدك.");
    }
    

    private function getPdfPageCount($filePath)
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);
        return count($pdf->getPages());
    }
    
    // private function getPdfPageCount($filePath)
    // {
    //     $pdf = new Fpdi();
    //     return $pdf->setSourceFile($filePath);
    // }
    public function printPDF($filename, $copies = 1, $isColor = false)
    {
        $path = $filename;
    
        if (!file_exists($path)) {
            abort(404, 'الملف غير موجود');
        }
    
        $copies = intval($copies);
        $copies = max(1, $copies);
    
        // مسار SumatraPDF
        $sumatraPath = '"C:\\laragon\\www\\print-pdf\\SumatraPDF\\SumatraPDF.exe"';
    
        // تحديد اسم الطابعة بناءً على اختيار اللون
        $printerName = $isColor ? 'XP80C' : 'XP80C';//Printer-BW
    
        for ($i = 0; $i < $copies; $i++) {
            $cmd = "$sumatraPath -print-to \"$printerName\" -silent " . escapeshellarg($path);
            exec($cmd);
        }
    }
    
    
    
    // public function printPDF($filename, $copies = 1)
    // {
    //     $path = public_path('pdfs/' . $filename);
    
    //     if (!file_exists($path)) {
    //         abort(404, 'الملف غير موجود');
    //     }
    
    //     // عدد النسخ باستخدام -n
    //     $copies = intval($copies);
    //     $copies = max(1, $copies); 
    
    //     exec("lp -n {$copies} " . escapeshellarg($path));
    // }
    



}