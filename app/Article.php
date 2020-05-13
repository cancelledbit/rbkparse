<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Article extends Model
{
    public const SHORT_TEXT_LENGTH = 200;
    /** @var bool  */
    public $incrementing = false;
    /** @var string  */
    protected $keyType = 'string';
    /** @var string[]  */
    protected $fillable = ['id'];

    public function getDateCreated(): string {
        return $this->created_at;
    }

    public function getShortText(): string {
        if (!$this->summary) {
            $textShort = mb_substr($this->content, 0, static::SHORT_TEXT_LENGTH);
            return implode('.', explode('.', $textShort, -1));
        }
        return $this->summary;
    }
}
