<?php
/**
 * Liste des indicatifs pays supportés (ordre : Cameroun en premier).
 */
function welcomy_phone_countries(): array
{
    return [
        ['code' => 'CM', 'name' => 'Cameroun', 'dial' => '+237', 'min' => 9, 'max' => 9, 'pattern' => '/^6\d{8}$/'],
        ['code' => 'FR', 'name' => 'France', 'dial' => '+33', 'min' => 9, 'max' => 9, 'pattern' => '/^[1-9]\d{8}$/'],
        ['code' => 'CI', 'name' => 'Côte d\'Ivoire', 'dial' => '+225', 'min' => 10, 'max' => 10, 'pattern' => '/^\d{10}$/'],
        ['code' => 'SN', 'name' => 'Sénégal', 'dial' => '+221', 'min' => 9, 'max' => 9, 'pattern' => '/^[7]\d{8}$/'],
        ['code' => 'GA', 'name' => 'Gabon', 'dial' => '+241', 'min' => 8, 'max' => 8, 'pattern' => '/^\d{8}$/'],
        ['code' => 'CG', 'name' => 'Congo', 'dial' => '+242', 'min' => 9, 'max' => 9, 'pattern' => '/^\d{9}$/'],
        ['code' => 'CD', 'name' => 'RD Congo', 'dial' => '+243', 'min' => 9, 'max' => 9, 'pattern' => '/^\d{9}$/'],
        ['code' => 'NG', 'name' => 'Nigeria', 'dial' => '+234', 'min' => 10, 'max' => 10, 'pattern' => '/^\d{10}$/'],
        ['code' => 'BJ', 'name' => 'Bénin', 'dial' => '+229', 'min' => 8, 'max' => 10, 'pattern' => '/^\d{8,10}$/'],
        ['code' => 'TG', 'name' => 'Togo', 'dial' => '+228', 'min' => 8, 'max' => 8, 'pattern' => '/^\d{8}$/'],
        ['code' => 'BE', 'name' => 'Belgique', 'dial' => '+32', 'min' => 9, 'max' => 9, 'pattern' => '/^[1-9]\d{8}$/'],
        ['code' => 'CH', 'name' => 'Suisse', 'dial' => '+41', 'min' => 9, 'max' => 9, 'pattern' => '/^\d{9}$/'],
        ['code' => 'US', 'name' => 'États-Unis', 'dial' => '+1', 'min' => 10, 'max' => 10, 'pattern' => '/^\d{10}$/'],
    ];
}

function welcomy_find_country_by_dial(string $dial): ?array
{
    $dial = '+' . ltrim(preg_replace('/\D/', '', $dial), '+');
    foreach (welcomy_phone_countries() as $country) {
        if ($country['dial'] === $dial) {
            return $country;
        }
    }
    return null;
}

function welcomy_detect_country_from_phone(string $raw): ?array
{
    $digits = preg_replace('/\D/', '', $raw);
    if ($digits === '') {
        return null;
    }
    $countries = welcomy_phone_countries();
    usort($countries, fn($a, $b) => strlen($b['dial']) <=> strlen($a['dial']));
    foreach ($countries as $country) {
        $code = ltrim($country['dial'], '+');
        if (str_starts_with($digits, $code)) {
            return $country;
        }
    }
    return null;
}

/**
 * Normalise et valide un numéro. Retourne ex. +237652236142 ou null si invalide.
 */
function welcomy_normalize_phone(string $raw, ?string $dialCode = null): ?string
{
    $raw = trim($raw);
    if ($raw === '') {
        return null;
    }

    $country = null;
    $national = preg_replace('/\D/', '', $raw);

    if (str_starts_with($raw, '+') || (strlen($national) > 10 && welcomy_detect_country_from_phone($raw))) {
        $country = welcomy_detect_country_from_phone($raw);
        if ($country) {
            $national = substr($national, strlen(ltrim($country['dial'], '+')));
        }
    } elseif ($dialCode) {
        $country = welcomy_find_country_by_dial($dialCode);
    }

    if (!$country) {
        $country = welcomy_find_country_by_dial($dialCode ?? '+237') ?? welcomy_phone_countries()[0];
    }

    $national = ltrim($national, '0');

    if (!preg_match($country['pattern'], $national)) {
        return null;
    }

    return $country['dial'] . $national;
}

function welcomy_format_phone_display(string $phone): string
{
    $normalized = welcomy_normalize_phone($phone);
    if (!$normalized) {
        return $phone;
    }
    $country = welcomy_detect_country_from_phone($normalized);
    if (!$country) {
        return $normalized;
    }
    $national = substr($normalized, strlen($country['dial']));
    return $country['dial'] . ' ' . $national;
}
