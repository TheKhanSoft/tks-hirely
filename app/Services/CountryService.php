<?php

namespace App\Services;

class CountryService
{
    /**
     * Prioritized common countries for university academia & international candidates.
     */
    protected static array $priorityCountries = [
        'Pakistan',
        'United Kingdom',
        'United States',
        'Canada',
        'Australia',
        'Saudi Arabia',
        'United Arab Emirates',
        'Germany',
        'China',
        'Malaysia',
        'Qatar',
        'Turkey',
        'France',
        'Italy',
        'Netherlands',
        'Sweden',
        'Singapore',
        'Japan',
        'South Korea',
        'Oman',
        'Bahrain',
        'Kuwait',
    ];

    /**
     * Full list of world countries (ISO 3166-1 standard).
     */
    protected static array $allCountries = [
        'Afghanistan', 'Albania', 'Algeria', 'Andorra', 'Angola', 'Antigua and Barbuda',
        'Argentina', 'Armenia', 'Australia', 'Austria', 'Azerbaijan', 'Bahamas',
        'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize',
        'Benin', 'Bhutan', 'Bolivia', 'Bosnia and Herzegovina', 'Botswana', 'Brazil',
        'Brunei', 'Bulgaria', 'Burkina Faso', 'Burundi', 'Cabo Verde', 'Cambodia',
        'Cameroon', 'Canada', 'Central African Republic', 'Chad', 'Chile', 'China',
        'Colombia', 'Comoros', 'Congo (Congo-Brazzaville)', 'Costa Rica', 'Croatia', 'Cuba',
        'Cyprus', 'Czechia (Czech Republic)', 'Democratic Republic of the Congo', 'Denmark', 'Djibouti', 'Dominica',
        'Dominican Republic', 'Ecuador', 'Egypt', 'El Salvador', 'Equatorial Guinea', 'Eritrea',
        'Estonia', 'Eswatini', 'Ethiopia', 'Fiji', 'Finland', 'France',
        'Gabon', 'Gambia', 'Georgia', 'Germany', 'Ghana', 'Greece',
        'Grenada', 'Guatemala', 'Guinea', 'Guinea-Bissau', 'Guyana', 'Haiti',
        'Holy See', 'Honduras', 'Hungary', 'Iceland', 'India', 'Indonesia',
        'Iran', 'Iraq', 'Ireland', 'Italy', 'Jamaica', 'Japan',
        'Jordan', 'Kazakhstan', 'Kenya', 'Kiribati', 'Kuwait', 'Kyrgyzstan',
        'Laos', 'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Libya',
        'Liechtenstein', 'Lithuania', 'Luxembourg', 'Madagascar', 'Malawi', 'Malaysia',
        'Maldives', 'Mali', 'Malta', 'Marshall Islands', 'Mauritania', 'Mauritius',
        'Mexico', 'Micronesia', 'Moldova', 'Monaco', 'Mongolia', 'Montenegro',
        'Morocco', 'Mozambique', 'Myanmar (formerly Burma)', 'Namibia', 'Nauru', 'Nepal',
        'Netherlands', 'New Zealand', 'Nicaragua', 'Niger', 'Nigeria', 'North Korea',
        'North Macedonia', 'Norway', 'Oman', 'Pakistan', 'Palau', 'Palestine State',
        'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines', 'Poland',
        'Portugal', 'Qatar', 'Romania', 'Russia', 'Rwanda', 'Saint Kitts and Nevis',
        'Saint Lucia', 'Saint Vincent and the Grenadines', 'Samoa', 'San Marino', 'Sao Tome and Principe', 'Saudi Arabia',
        'Senegal', 'Serbia', 'Seychelles', 'Sierra Leone', 'Singapore', 'Slovakia',
        'Slovenia', 'Solomon Islands', 'Somalia', 'South Africa', 'South Korea', 'South Sudan',
        'Spain', 'Sri Lanka', 'Sudan', 'Suriname', 'Sweden', 'Switzerland',
        'Syria', 'Tajikistan', 'Tanzania', 'Thailand', 'Timor-Leste', 'Togo',
        'Tonga', 'Trinidad and Tobago', 'Tunisia', 'Turkey', 'Turkmenistan', 'Tuvalu',
        'Uganda', 'Ukraine', 'United Arab Emirates', 'United Kingdom', 'United States', 'Uruguay',
        'Uzbekistan', 'Vanuatu', 'Venezuela', 'Vietnam', 'Yemen', 'Zambia', 'Zimbabwe'
    ];

    /**
     * Get all countries with priority countries at the top.
     */
    public static function all(): array
    {
        $remaining = array_values(array_diff(self::$allCountries, self::$priorityCountries));
        sort($remaining);
        
        return array_values(array_unique(array_merge(self::$priorityCountries, $remaining)));
    }

    /**
     * Get list of prioritized countries.
     */
    public static function priority(): array
    {
        return self::$priorityCountries;
    }

    /**
     * Search countries by query string.
     */
    public static function search(string $query): array
    {
        $query = trim(strtolower($query));
        if ($query === '') {
            return self::all();
        }

        return array_values(array_filter(self::all(), function ($country) use ($query) {
            return str_contains(strtolower($country), $query);
        }));
    }
}
