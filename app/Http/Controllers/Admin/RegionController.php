<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRegionRequest;
use App\Http\Requests\Admin\UpdateRegionRequest;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class RegionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Region::class, 'region');
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->trim();
        $level = $request->string('level')->trim();
        $parentId = $request->integer('parent_id') ?: null;

        $regions = Region::query()
            ->with('parent')
            ->when($search->isNotEmpty(), function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder->where('name', 'like', '%'.$search.'%')
                        ->orWhere('code', 'like', '%'.$search.'%');
                });
            })
            ->when($level->isNotEmpty(), fn ($query) => $query->where('level', $level))
            ->when($parentId, fn ($query) => $query->where('parent_id', $parentId))
            ->orderByRaw("CASE level WHEN 'province' THEN 1 WHEN 'city' THEN 2 WHEN 'district' THEN 3 ELSE 4 END")
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $regionsData = $this->regionsForDropdown();

        return view('admin.regions.index', [
            'regions' => $regions,
            'search' => $search->toString(),
            'levelFilter' => $level->toString(),
            'parentFilter' => $parentId,
            'regionsData' => $regionsData,
            'levelLabels' => Region::LEVEL_LABELS,
        ]);
    }

    public function create(): View
    {
        return view('admin.regions.create', [
            'regionsData' => $this->regionsForDropdown(),
            'levelLabels' => Region::LEVEL_LABELS,
        ]);
    }

    public function store(StoreRegionRequest $request): RedirectResponse
    {
        Region::create($request->validated());

        return redirect()
            ->route('admin.regions.index')
            ->with('status', 'Wilayah berhasil ditambahkan.');
    }

    public function edit(Region $region): View
    {
        $region->load('parent.parent.parent');

        return view('admin.regions.edit', [
            'region' => $region,
            'regionsData' => $this->regionsForDropdown(),
            'levelLabels' => Region::LEVEL_LABELS,
        ]);
    }

    public function update(UpdateRegionRequest $request, Region $region): RedirectResponse
    {
        $region->update($request->validated());

        return redirect()
            ->route('admin.regions.index')
            ->with('status', 'Wilayah berhasil diperbarui.');
    }

    public function destroy(Region $region): RedirectResponse
    {
        if ($region->children()->exists()) {
            return redirect()
                ->route('admin.regions.index')
                ->with('error', 'Wilayah masih memiliki turunan, hapus turunan terlebih dahulu.');
        }

        $region->delete();

        return redirect()
            ->route('admin.regions.index')
            ->with('status', 'Wilayah berhasil dihapus.');
    }

    protected function regionsForDropdown(): Collection
    {
        return Region::query()
            ->orderBy('name')
            ->get(['id', 'name', 'level', 'parent_id']);
    }
}
