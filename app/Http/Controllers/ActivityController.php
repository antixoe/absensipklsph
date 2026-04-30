<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        if (!$student) {
            abort(403, 'Student profile not found');
        }

        $absensis = Absensi::where('student_id', $student->id)
            ->orderBy('tanggal_absensi', 'desc')
            ->paginate(10);
        
        return view('absensis.index', compact('absensis'));
    }

    public function create()
    {
        return view('absensis.create');
    }

    public function store(Request $request)
    {
        $student = Auth::user()->student;
        if (!$student) {
            abort(403, 'Student profile not found');
        }

        $validated = $request->validate([
            'tanggal_absensi' => ['required', 'date'],
            'jam_masuk' => ['nullable', 'date_format:H:i'],
            'jam_keluar' => ['nullable', 'date_format:H:i'],
            'status' => ['required', 'string', 'in:hadir,alpa,izin,sakit'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ]);

        Absensi::create([
            'student_id' => $student->id,
            'tanggal_absensi' => $validated['tanggal_absensi'],
            'jam_masuk' => $validated['jam_masuk'],
            'jam_keluar' => $validated['jam_keluar'],
            'status' => $validated['status'],
            'keterangan' => $validated['keterangan'],
        ]);

        return redirect('/absensis')->with('success', 'Absensi berhasil dibuat!');
    }

    public function show(Absensi $absensi)
    {
        $student = Auth::user()->student;
        if ($absensi->student_id !== $student->id) {
            abort(403);
        }
        return view('absensis.show', compact('absensi'));
    }

    public function edit(Absensi $absensi)
    {
        $student = Auth::user()->student;
        if ($absensi->student_id !== $student->id) {
            abort(403);
        }
        return view('absensis.edit', compact('absensi'));
    }

    public function update(Request $request, Absensi $absensi)
    {
        $student = Auth::user()->student;
        if ($absensi->student_id !== $student->id) {
            abort(403);
        }

        $validated = $request->validate([
            'tanggal_absensi' => ['required', 'date'],
            'jam_masuk' => ['nullable', 'date_format:H:i'],
            'jam_keluar' => ['nullable', 'date_format:H:i'],
            'status' => ['required', 'string', 'in:hadir,alpa,izin,sakit'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ]);

        $absensi->update([
            'tanggal_absensi' => $validated['tanggal_absensi'],
            'jam_masuk' => $validated['jam_masuk'],
            'jam_keluar' => $validated['jam_keluar'],
            'status' => $validated['status'],
            'keterangan' => $validated['keterangan'],
        ]);

        return redirect('/absensis')->with('success', 'Absensi berhasil diperbarui!');
    }

    public function destroy(Absensi $absensi)
    {
        $student = Auth::user()->student;
        if ($absensi->student_id !== $student->id) {
            abort(403);
        }

        $absensi->delete();
        return redirect('/absensis')->with('success', 'Absensi berhasil dihapus!');
    }
}
