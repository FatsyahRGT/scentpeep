<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    public const TYPE_WAREHOUSE = 'warehouse';
    public const TYPE_RESELLER = 'reseller';

    /**
     * Get auto-generated high-contrast HSL color palette for this location instance.
     */
    public function getColorPalette(): array
    {
        return self::paletteForName($this->name, $this->id);
    }

    /**
     * Generate an accessible, high-contrast HSL color palette based on name and optional ID.
     * Uses Golden Angle (137.507764 deg) for mathematically optimal chromatic distribution
     * while preserving recognizable anchor hues for key team members (IWAN, RIZKY, VANNY).
     */
    public static function paletteForName(string $name, ?int $id = null): array
    {
        $nameUpper = strtoupper(trim($name));

        if ($nameUpper === 'IWAN') {
            $hue = 145.0; // Emerald Green
        } elseif ($nameUpper === 'RIZKY') {
            $hue = 215.0; // Sapphire Blue
        } elseif ($nameUpper === 'VANNY') {
            $hue = 345.0; // Ruby / Crimson
        } elseif ($id && $id > 0) {
            // Golden Angle distribution for sequentially distinct hues
            $hue = fmod((($id - 1) * 137.507764 + 45.0), 360.0);
        } else {
            $seed = abs(crc32($nameUpper)) % 360;
            $hue = fmod(($seed * 137.507764 + 45.0), 360.0);
        }

        if ($hue < 0) {
            $hue += 360.0;
        }

        // Adjust text lightness for inherently luminous hues (e.g. amber/yellow zone) to preserve WCAG AAA contrast
        $isYellowZone = ($hue >= 35.0 && $hue <= 65.0);
        $textL = $isYellowZone ? 0.16 : 0.22;
        $badgeTextL = $isYellowZone ? 0.20 : 0.26;

        return [
            'name' => self::hueToColorName($hue),
            'hue' => round($hue, 1),
            'text' => self::hslToHex($hue, 0.75, $textL),
            'bg' => self::hslToHex($hue, 0.65, 0.97),
            'border' => self::hslToHex($hue, 0.55, 0.82),
            'badge_bg' => self::hslToHex($hue, 0.70, 0.92),
            'badge_text' => self::hslToHex($hue, 0.80, $badgeTextL),
            'pill_bg' => self::hslToHex($hue, 0.70, 0.48),
        ];
    }

    /**
     * Map hue angle to a human-friendly descriptive color name.
     */
    public static function hueToColorName(float $hue): string
    {
        $h = fmod($hue, 360.0);
        if ($h < 0) {
            $h += 360.0;
        }

        return match (true) {
            $h < 15.0 => 'ruby',
            $h < 45.0 => 'coral',
            $h < 75.0 => 'amber',
            $h < 165.0 => 'emerald',
            $h < 195.0 => 'teal',
            $h < 255.0 => 'sapphire',
            $h < 290.0 => 'indigo',
            $h < 330.0 => 'amethyst',
            default => 'ruby',
        };
    }

    /**
     * Convert HSL color values to hexadecimal string (#RRGGBB).
     */
    public static function hslToHex(float $h, float $s, float $l): string
    {
        $hNorm = fmod($h, 360.0);
        if ($hNorm < 0) {
            $hNorm += 360.0;
        }

        $c = (1 - abs(2 * $l - 1)) * $s;
        $x = $c * (1 - abs(fmod($hNorm / 60.0, 2) - 1));
        $m = $l - $c / 2;

        [$rPrime, $gPrime, $bPrime] = match (true) {
            $hNorm < 60.0 => [$c, $x, 0],
            $hNorm < 120.0 => [$x, $c, 0],
            $hNorm < 180.0 => [0, $c, $x],
            $hNorm < 240.0 => [0, $x, $c],
            $hNorm < 300.0 => [$x, 0, $c],
            default => [$c, 0, $x],
        };

        $r = (int) round(($rPrime + $m) * 255);
        $g = (int) round(($gPrime + $m) * 255);
        $b = (int) round(($bPrime + $m) * 255);

        return sprintf('#%02x%02x%02x', max(0, min(255, $r)), max(0, min(255, $g)), max(0, min(255, $b)));
    }

    protected $fillable = [
        'name',
        'type',
        'user_id',
    ];

    public function isWarehouse(): bool
    {
        return $this->type === self::TYPE_WAREHOUSE;
    }

    public function isReseller(): bool
    {
        return $this->type === self::TYPE_RESELLER;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function outgoingTransfers(): HasMany
    {
        return $this->hasMany(StockTransfer::class, 'from_location_id');
    }

    public function incomingTransfers(): HasMany
    {
        return $this->hasMany(StockTransfer::class, 'to_location_id');
    }
}
