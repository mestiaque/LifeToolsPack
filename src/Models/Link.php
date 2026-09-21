<?php

namespace ME\EmCore\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $fillable = [
        'title', 'description', 'link'
    ];

    /**
     * Prefix used to mark a stored value as Base64-encoded, so legacy
     * plain-text rows (saved before encoding was introduced) can always
     * be told apart from newly encoded ones — no guessing required.
     */
    private const ENCODED_PREFIX = 'b64:';

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $this->encodeValue($value);
    }

    public function setLinkAttribute($value)
    {
        $this->attributes['link'] = $this->encodeValue($value);
    }

    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = $this->encodeValue($value);
    }

    public function getTitleAttribute($value)
    {
        return $this->encodedPayload($value);
    }

    public function getLinkAttribute($value)
    {
        return $this->encodedPayload($value);
    }

    public function getDescriptionAttribute($value)
    {
        return $this->encodedPayload($value);
    }

    public function getDecodedTitleAttribute()
    {
        return $this->decodeValue($this->attributes['title'] ?? null);
    }

    public function getDecodedLinkAttribute()
    {
        return $this->decodeValue($this->attributes['link'] ?? null);
    }

    public function getDecodedDescriptionAttribute()
    {
        return $this->decodeValue($this->attributes['description'] ?? null);
    }

    private function encodeValue($value)
    {
        return is_null($value) ? $value : self::ENCODED_PREFIX . base64_encode($value);
    }

    /**
     * Plain Base64 (no prefix) for display, whether the row is already
     * encoded or is still legacy plain text.
     */
    private function encodedPayload($value)
    {
        if (is_null($value)) {
            return $value;
        }

        return str_starts_with($value, self::ENCODED_PREFIX)
            ? substr($value, strlen(self::ENCODED_PREFIX))
            : base64_encode($value);
    }

    /**
     * Plain text for editing, whether the row is already encoded or is
     * still legacy plain text.
     */
    private function decodeValue($value)
    {
        if (is_null($value)) {
            return $value;
        }

        return str_starts_with($value, self::ENCODED_PREFIX)
            ? base64_decode(substr($value, strlen(self::ENCODED_PREFIX)))
            : $value;
    }
}
