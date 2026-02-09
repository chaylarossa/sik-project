<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUnitRequest;
use App\Http\Requests\Admin\UpdateUnitRequest;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Unit::class, 'unit');
    }

    public function index(Request $request)
    {
        $search = $request->string('search')->trim();

        $units = Unit::query()
            ->when($search->isNotEmpty(), function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder->where('name', 'like', '%'.$search.'%')
                        ->orWhere('code', 'like', '%'.$search.'%')
                        ->orWhere('contact_phone', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.units.index', [
            'units' => $units,
            'search' => $search->toString(),
        ]);
    }

    public function create()
    {
        return view('admin.units.create');
    }

    public function store(StoreUnitRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        Unit::create($data);

        return redirect()
            ->route('admin.units.index')
            ->with('status', 'Unit berhasil ditambahkan.');
    }

    public function edit(Unit $unit)
    {
        return view('admin.units.edit', [
            'unit' => $unit,
        ]);
    }

    public function update(UpdateUnitRequest $request, Unit $unit)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $unit->update($data);

        return redirect()
            ->route('admin.units.index')
            ->with('status', 'Unit berhasil diperbarui.');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();

        return redirect()
            ->route('admin.units.index')
            ->with('status', 'Unit berhasil dihapus.');
    }
}
