<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resident extends Model
{
    use HasFactory;

    protected $fillable = [
        'block',
        'house_number',
        'family_head_name',
        'upload_token',
        'status',
    ];

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVE');
    }

    public function scopeByBlock($query, string $block)
    {
        return $query->where('block', strtoupper($block));
    }

    /**
     * Get a human-readable display title for the resident unit.
     * e.g. "Blok A1 No.07" or "Kavling Bapak Ahmad"
     */
    public function getDisplayLabelAttribute(): string
    {
        if ($this->block === 'KAVLING') {
            return 'Kavling ' . ($this->family_head_name ?? 'Warga');
        }

        $label = $this->block . ' No.' . str_pad($this->house_number, 2, '0', STR_PAD_LEFT);
        if (!empty($this->family_head_name)) {
            $label .= ' (' . $this->family_head_name . ')';
        }

        return $label;
    }
}
