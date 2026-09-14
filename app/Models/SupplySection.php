<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class SupplySection extends Model
{
    protected $fillable = ['name', 'classification'];

    /**
     * Section => [classifications] map built from quick-added rows.
     */
    public static function sectionMap(): Collection
    {
        return self::query()
            ->orderBy('name')
            ->orderBy('classification')
            ->get(['name', 'classification'])
            ->groupBy('name')
            ->map(fn ($rows) => $rows->pluck('classification')->filter()->unique()->values());
    }

    /**
     * Merge the quick-added sections into a section => [classifications] map
     * built from actual supply rows, so the add/edit form dropdowns always
     * offer them even before any supply has been recorded under them.
     *
     * @param  Collection|null  $sections  article => Collection of classifications
     */
    public static function mergeWithExisting(?Collection $sections): Collection
    {
        $sections = $sections ?? collect();

        foreach (self::sectionMap() as $name => $classifications) {
            $existing = $sections->get($name, collect());

            $sections[$name] = collect($classifications)
                ->merge($existing)
                ->filter()
                ->unique()
                ->sort()
                ->values();
        }

        return $sections->sortKeys();
    }
}
