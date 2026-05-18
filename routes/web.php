<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExtractionController;
use App\Http\Controllers\ExtractionRalanController;
use App\Http\Controllers\PemberianObatController;
use App\Http\Controllers\PenerimaanObatFarmasiController;
use App\Http\Controllers\LaboratoriumController;
use App\Http\Controllers\FarmasiController;
use App\Http\Controllers\HargaBarangController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rawat Inap Extraction
    Route::get('/extraction', [ExtractionController::class, 'index'])->name('extraction.index');
    Route::get('/extraction/tarik', [ExtractionController::class, 'tarik'])->name('extraction.tarik');
    Route::get('/extraction/export/excel', [ExtractionController::class, 'exportExcel'])->name('extraction.export.excel');
    Route::get('/extraction/export/pdf', [ExtractionController::class, 'exportPdf'])->name('extraction.export.pdf');

    // Rawat Jalan Extraction
    Route::get('/extraction-ralan', [ExtractionRalanController::class, 'index'])->name('extraction_ralan.index');
    Route::get('/extraction-ralan/tarik', [ExtractionRalanController::class, 'tarik'])->name('extraction_ralan.tarik');
    Route::get('/extraction-ralan/export/excel', [ExtractionRalanController::class, 'exportExcel'])->name('extraction_ralan.export.excel');
    Route::get('/extraction-ralan/export/pdf', [ExtractionRalanController::class, 'exportPdf'])->name('extraction_ralan.export.pdf');

    // Pemberian Obat dan BHP
    Route::get('/pemberian-obat', [PemberianObatController::class, 'index'])->name('pemberian_obat.index');
    Route::get('/pemberian-obat/export/excel', [PemberianObatController::class, 'exportExcel'])->name('pemberian_obat.export.excel');
    Route::get('/pemberian-obat/export/pdf', [PemberianObatController::class, 'exportPdf'])->name('pemberian_obat.export.pdf');

    // Penerimaan Obat dan BHP Farmasi
    Route::get('/penerimaan-obat-farmasi', [PenerimaanObatFarmasiController::class, 'index'])->name('penerimaan_obat_farmasi.index');
    Route::get('/penerimaan-obat-farmasi/export/excel', [PenerimaanObatFarmasiController::class, 'exportExcel'])->name('penerimaan_obat_farmasi.export.excel');
    Route::get('/penerimaan-obat-farmasi/export/pdf', [PenerimaanObatFarmasiController::class, 'exportPdf'])->name('penerimaan_obat_farmasi.export.pdf');

    // Waktu Tunggu Rawat Jalan
    Route::get('/waktu-tunggu-ralan', [FarmasiController::class, 'index'])->name('farmasi.waktu_tunggu_ralan.index');
    Route::get('/waktu-tunggu-ralan/export/excel', [FarmasiController::class, 'exportExcel'])->name('farmasi.waktu_tunggu_ralan.export.excel');
    Route::get('/waktu-tunggu-ralan/export/pdf', [FarmasiController::class, 'exportPdf'])->name('farmasi.waktu_tunggu_ralan.export.pdf');

    // Harga Barang
    Route::get('/harga-barang', [HargaBarangController::class, 'index'])->name('farmasi.harga_barang.index');
    Route::get('/harga-barang/export/excel', [HargaBarangController::class, 'exportExcel'])->name('farmasi.harga_barang.export.excel');
    Route::get('/harga-barang/export/pdf', [HargaBarangController::class, 'exportPdf'])->name('farmasi.harga_barang.export.pdf');

    // Waktu Tunggu Rawat Jalan BPJS
    Route::get('/waktu-tunggu-bpjs', [FarmasiController::class, 'waktuTungguBpjs'])->name('farmasi.waktu_tunggu_bpjs.index');
    Route::get('/waktu-tunggu-bpjs/export/excel', [FarmasiController::class, 'exportBpjsExcel'])->name('farmasi.waktu_tunggu_bpjs.export.excel');
    Route::get('/waktu-tunggu-bpjs/export/pdf', [FarmasiController::class, 'exportBpjsPdf'])->name('farmasi.waktu_tunggu_bpjs.export.pdf');

    // Sirkulasi Obat Farmasi
    Route::get('/sirkulasi-obat', [FarmasiController::class, 'sirkulasiIndex'])->name('farmasi.sirkulasi.index');
    Route::get('/sirkulasi-obat/export/excel', [FarmasiController::class, 'sirkulasiExportExcel'])->name('farmasi.sirkulasi.export.excel');
    Route::get('/sirkulasi-obat/export/pdf', [FarmasiController::class, 'sirkulasiExportPdf'])->name('farmasi.sirkulasi.export.pdf');

    // Laboratorium
    Route::get('/laboratorium-ralan', [LaboratoriumController::class, 'index'])->name('laboratorium.index');
    Route::get('/laboratorium-ranap', [LaboratoriumController::class, 'index_ranap'])->name('laboratorium.index_ranap');
    Route::get('/laboratorium-gabungan', [LaboratoriumController::class, 'index_gabungan'])->name('laboratorium.index_gabungan');
    Route::get('/laboratorium/export/excel', [LaboratoriumController::class, 'exportExcel'])->name('laboratorium.export.excel');
    Route::get('/laboratorium/export/pdf', [LaboratoriumController::class, 'exportPdf'])->name('laboratorium.export.pdf');
});
