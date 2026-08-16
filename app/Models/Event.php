<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'type',
        'name',
        'event_date',
        'description',
        'allow_resident_upload',
        'status',
        'thumbnail_path',
    ];

    protected $casts = [
        'event_date' => 'date',
        'allow_resident_upload' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->uuid)) {
                $event->uuid = (string) Str::uuid();
            }
        });
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    public function adminPhotos(): HasMany
    {
        return $this->hasMany(Photo::class)->where('uploader_type', 'ADMIN');
    }

    public function residentPhotos(): HasMany
    {
        return $this->hasMany(Photo::class)->where('uploader_type', 'RESIDENT');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'PUBLISHED');
    }

    public function scopeNotDraft($query)
    {
        return $query->whereIn('status', ['PUBLISHED', 'ARCHIVED']);
    }

    public function scopeAcara($query)
    {
        return $query->where('type', 'ACARA');
    }

    public function scopeKegiatan($query)
    {
        return $query->where('type', 'KEGIATAN');
    }

    /**
     * Check if resident uploads are permitted for this event.
     */
    public function canAcceptResidentUpload(): bool
    {
        if ($this->status !== 'PUBLISHED') {
            return false;
        }

        if ($this->type === 'KEGIATAN' && !$this->allow_resident_upload) {
            return false;
        }

        return true;
    }
}
