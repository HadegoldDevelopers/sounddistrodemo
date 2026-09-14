<?php

namespace App\Helpers;

class CountryHelper
{
    /**
     * Convert a country code (2-letter or 3-letter ISO) or name to full country name.
     *
     * @param string $code
     * @return string
     */
     
    protected static array $countries = [
            'AF' => 'Afghanistan', 'AFG' => 'Afghanistan',
            'AL' => 'Albania', 'ALB' => 'Albania',
            'DZ' => 'Algeria', 'DZA' => 'Algeria',
            'AS' => 'American Samoa', 'ASM' => 'American Samoa',
            'AD' => 'Andorra', 'AND' => 'Andorra',
            'AO' => 'Angola', 'AGO' => 'Angola',
            'AI' => 'Anguilla', 'AIA' => 'Anguilla',
            'AQ' => 'Antarctica', 'ATA' => 'Antarctica',
            'AG' => 'Antigua and Barbuda', 'ATG' => 'Antigua and Barbuda',
            'AR' => 'Argentina', 'ARG' => 'Argentina',
            'AM' => 'Armenia', 'ARM' => 'Armenia',
            'AW' => 'Aruba', 'ABW' => 'Aruba',
            'AU' => 'Australia', 'AUS' => 'Australia',
            'AT' => 'Austria', 'AUT' => 'Austria',
            'AZ' => 'Azerbaijan', 'AZE' => 'Azerbaijan',
            'BS' => 'Bahamas', 'BHS' => 'Bahamas',
            'BH' => 'Bahrain', 'BHR' => 'Bahrain',
            'BD' => 'Bangladesh', 'BGD' => 'Bangladesh',
            'BB' => 'Barbados', 'BRB' => 'Barbados',
            'BY' => 'Belarus', 'BLR' => 'Belarus',
            'BE' => 'Belgium', 'BEL' => 'Belgium',
            'BZ' => 'Belize', 'BLZ' => 'Belize',
            'BJ' => 'Benin', 'BEN' => 'Benin',
            'BM' => 'Bermuda', 'BMU' => 'Bermuda',
            'BT' => 'Bhutan', 'BTN' => 'Bhutan',
            'BO' => 'Bolivia', 'BOL' => 'Bolivia',
            'BA' => 'Bosnia and Herzegovina', 'BIH' => 'Bosnia and Herzegovina',
            'BW' => 'Botswana', 'BWA' => 'Botswana',
            'BR' => 'Brazil', 'BRA' => 'Brazil',
            'IO' => 'British Indian Ocean Territory', 'IOT' => 'British Indian Ocean Territory',
            'BN' => 'Brunei', 'BRN' => 'Brunei',
            'BG' => 'Bulgaria', 'BGR' => 'Bulgaria',
            'BF' => 'Burkina Faso', 'BFA' => 'Burkina Faso',
            'BI' => 'Burundi', 'BDI' => 'Burundi',
            'CV' => 'Cabo Verde', 'CPV' => 'Cabo Verde',
            'KH' => 'Cambodia', 'KHM' => 'Cambodia',
            'CM' => 'Cameroon', 'CMR' => 'Cameroon',
            'CA' => 'Canada', 'CAN' => 'Canada',
            'KY' => 'Cayman Islands', 'CYM' => 'Cayman Islands',
            'CF' => 'Central African Republic', 'CAF' => 'Central African Republic',
            'TD' => 'Chad', 'TCD' => 'Chad',
            'CL' => 'Chile', 'CHL' => 'Chile',
            'CN' => 'China', 'CHN' => 'China',
            'CX' => 'Christmas Island', 'CXR' => 'Christmas Island',
            'CC' => 'Cocos (Keeling) Islands', 'CCK' => 'Cocos (Keeling) Islands',
            'CO' => 'Colombia', 'COL' => 'Colombia',
            'KM' => 'Comoros', 'COM' => 'Comoros',
            'CG' => 'Congo', 'COG' => 'Congo',
            'CD' => 'Democratic Republic of the Congo', 'COD' => 'Democratic Republic of the Congo',
            'CK' => 'Cook Islands', 'COK' => 'Cook Islands',
            'CR' => 'Costa Rica', 'CRI' => 'Costa Rica',
            'CI' => 'Côte d’Ivoire', 'CIV' => 'Côte d’Ivoire',
            'HR' => 'Croatia', 'HRV' => 'Croatia',
            'CU' => 'Cuba', 'CUB' => 'Cuba',
            'CW' => 'Curaçao', 'CUW' => 'Curaçao',
            'CY' => 'Cyprus', 'CYP' => 'Cyprus',
            'CZ' => 'Czech Republic', 'CZE' => 'Czech Republic',
            'DK' => 'Denmark', 'DNK' => 'Denmark',
            'DJ' => 'Djibouti', 'DJI' => 'Djibouti',
            'DM' => 'Dominica', 'DMA' => 'Dominica',
            'DO' => 'Dominican Republic', 'DOM' => 'Dominican Republic',
            'EC' => 'Ecuador', 'ECU' => 'Ecuador',
            'EG' => 'Egypt', 'EGY' => 'Egypt',
            'SV' => 'El Salvador', 'SLV' => 'El Salvador',
            'GQ' => 'Equatorial Guinea', 'GNQ' => 'Equatorial Guinea',
            'ER' => 'Eritrea', 'ERI' => 'Eritrea',
            'EE' => 'Estonia', 'EST' => 'Estonia',
            'SZ' => 'Eswatini', 'SWZ' => 'Eswatini',
            'ET' => 'Ethiopia', 'ETH' => 'Ethiopia',
            'FK' => 'Falkland Islands', 'FLK' => 'Falkland Islands',
            'FO' => 'Faroe Islands', 'FRO' => 'Faroe Islands',
            'FJ' => 'Fiji', 'FJI' => 'Fiji',
            'FI' => 'Finland', 'FIN' => 'Finland',
            'FR' => 'France', 'FRA' => 'France',
            'GF' => 'French Guiana', 'GUF' => 'French Guiana',
            'PF' => 'French Polynesia', 'PYF' => 'French Polynesia',
            'TF' => 'French Southern Territories', 'ATF' => 'French Southern Territories',
            'GA' => 'Gabon', 'GAB' => 'Gabon',
            'GM' => 'Gambia', 'GMB' => 'Gambia',
            'GE' => 'Georgia', 'GEO' => 'Georgia',
            'DE' => 'Germany', 'DEU' => 'Germany',
            'GH' => 'Ghana', 'GHA' => 'Ghana',
            'GI' => 'Gibraltar', 'GIB' => 'Gibraltar',
            'GR' => 'Greece', 'GRC' => 'Greece',
            'GL' => 'Greenland', 'GRL' => 'Greenland',
            'GD' => 'Grenada', 'GRD' => 'Grenada',
            'GP' => 'Guadeloupe', 'GLP' => 'Guadeloupe',
            'GU' => 'Guam', 'GUM' => 'Guam',
            'GT' => 'Guatemala', 'GTM' => 'Guatemala',
            'GG' => 'Guernsey', 'GGY' => 'Guernsey',
            'GN' => 'Guinea', 'GIN' => 'Guinea',
            'GW' => 'Guinea-Bissau', 'GNB' => 'Guinea-Bissau',
            'GY' => 'Guyana', 'GUY' => 'Guyana',
            'HT' => 'Haiti', 'HTI' => 'Haiti',
            'HN' => 'Honduras', 'HND' => 'Honduras',
            'HK' => 'Hong Kong', 'HKG' => 'Hong Kong',
            'HU' => 'Hungary', 'HUN' => 'Hungary',
            'IS' => 'Iceland', 'ISL' => 'Iceland',
            'IN' => 'India', 'IND' => 'India',
            'ID' => 'Indonesia', 'IDN' => 'Indonesia',
            'IR' => 'Iran', 'IRN' => 'Iran',
            'IQ' => 'Iraq', 'IRQ' => 'Iraq',
            'IE' => 'Ireland', 'IRL' => 'Ireland',
            'IM' => 'Isle of Man', 'IMN' => 'Isle of Man',
            'IL' => 'Israel', 'ISR' => 'Israel',
            'IT' => 'Italy', 'ITA' => 'Italy',
            'JM' => 'Jamaica', 'JAM' => 'Jamaica',
            'JP' => 'Japan', 'JPN' => 'Japan',
            'JE' => 'Jersey', 'JEY' => 'Jersey',
            'JO' => 'Jordan', 'JOR' => 'Jordan',
            'KZ' => 'Kazakhstan', 'KAZ' => 'Kazakhstan',
            'KE' => 'Kenya', 'KEN' => 'Kenya',
            'KI' => 'Kiribati', 'KIR' => 'Kiribati',
            'KW' => 'Kuwait', 'KWT' => 'Kuwait',
            'KG' => 'Kyrgyzstan', 'KGZ' => 'Kyrgyzstan',
            'LA' => 'Laos', 'LAO' => 'Laos',
            'LV' => 'Latvia', 'LVA' => 'Latvia',
            'LB' => 'Lebanon', 'LBN' => 'Lebanon',
            'LS' => 'Lesotho', 'LSO' => 'Lesotho',
            'LR' => 'Liberia', 'LBR' => 'Liberia',
            'LY' => 'Libya', 'LBY' => 'Libya',
            'LI' => 'Liechtenstein', 'LIE' => 'Liechtenstein',
            'LT' => 'Lithuania', 'LTU' => 'Lithuania',
            'LU' => 'Luxembourg', 'LUX' => 'Luxembourg',
            'MO' => 'Macao', 'MAC' => 'Macao',
            'MG' => 'Madagascar', 'MDG' => 'Madagascar',
            'MW' => 'Malawi', 'MWI' => 'Malawi',
            'MY' => 'Malaysia', 'MYS' => 'Malaysia',
            'MV' => 'Maldives', 'MDV' => 'Maldives',
            'ML' => 'Mali', 'MLI' => 'Mali',
            'MT' => 'Malta', 'MLT' => 'Malta',
            'MH' => 'Marshall Islands', 'MHL' => 'Marshall Islands',
            'MQ' => 'Martinique', 'MTQ' => 'Martinique',
            'MR' => 'Mauritania', 'MRT' => 'Mauritania',
            'MU' => 'Mauritius', 'MUS' => 'Mauritius',
            'YT' => 'Mayotte', 'MYT' => 'Mayotte',
            'MX' => 'Mexico', 'MEX' => 'Mexico',
            'FM' => 'Micronesia', 'FSM' => 'Micronesia',
            'MD' => 'Moldova', 'MDA' => 'Moldova',
            'MC' => 'Monaco', 'MCO' => 'Monaco',
            'MN' => 'Mongolia', 'MNG' => 'Mongolia',
            'ME' => 'Montenegro', 'MNE' => 'Montenegro',
            'MS' => 'Montserrat', 'MSR' => 'Montserrat',
            'MA' => 'Morocco', 'MAR' => 'Morocco',
            'MZ' => 'Mozambique', 'MOZ' => 'Mozambique',
            'MM' => 'Myanmar', 'MMR' => 'Myanmar',
            'NA' => 'Namibia', 'NAM' => 'Namibia',
            'NR' => 'Nauru', 'NRU' => 'Nauru',
            'NP' => 'Nepal', 'NPL' => 'Nepal',
            'NL' => 'Netherlands', 'NLD' => 'Netherlands',
            'NC' => 'New Caledonia', 'NCL' => 'New Caledonia',
            'NZ' => 'New Zealand', 'NZL' => 'New Zealand',
            'NI' => 'Nicaragua', 'NIC' => 'Nicaragua',
            'NE' => 'Niger', 'NER' => 'Niger',
            'NG' => 'Nigeria', 'NGA' => 'Nigeria',
            'NU' => 'Niue', 'NIU' => 'Niue',
            'NF' => 'Norfolk Island', 'NFK' => 'Norfolk Island',
            'KP' => 'North Korea', 'PRK' => 'North Korea',
            'MK' => 'North Macedonia', 'MKD' => 'North Macedonia',
            'MP' => 'Northern Mariana Islands', 'MNP' => 'Northern Mariana Islands',
            'NO' => 'Norway', 'NOR' => 'Norway',
            'OM' => 'Oman', 'OMN' => 'Oman',
            'PK' => 'Pakistan', 'PAK' => 'Pakistan',
            'PW' => 'Palau', 'PLW' => 'Palau',
            'PS' => 'Palestine', 'PSE' => 'Palestine',
            'PA' => 'Panama', 'PAN' => 'Panama',
            'PG' => 'Papua New Guinea', 'PNG' => 'Papua New Guinea',
            'PY' => 'Paraguay', 'PRY' => 'Paraguay',
            'PE' => 'Peru', 'PER' => 'Peru',
            'PH' => 'Philippines', 'PHL' => 'Philippines',
            'PL' => 'Poland', 'POL' => 'Poland',
            'PT' => 'Portugal', 'PRT' => 'Portugal',
            'PR' => 'Puerto Rico', 'PRI' => 'Puerto Rico',
            'QA' => 'Qatar', 'QAT' => 'Qatar',
            'RO' => 'Romania', 'ROU' => 'Romania',
            'RU' => 'Russia', 'RUS' => 'Russia',
            'RW' => 'Rwanda', 'RWA' => 'Rwanda',
            'RE' => 'Réunion', 'REU' => 'Réunion',
            'BL' => 'Saint Barthélemy', 'BLM' => 'Saint Barthélemy',
            'SH' => 'Saint Helena', 'SHN' => 'Saint Helena',
            'KN' => 'Saint Kitts and Nevis', 'KNA' => 'Saint Kitts and Nevis',
            'LC' => 'Saint Lucia', 'LCA' => 'Saint Lucia',
            'MF' => 'Saint Martin', 'MAF' => 'Saint Martin',
            'PM' => 'Saint Pierre and Miquelon', 'SPM' => 'Saint Pierre and Miquelon',
            'VC' => 'Saint Vincent and the Grenadines', 'VCT' => 'Saint Vincent and the Grenadines',
            'WS' => 'Samoa', 'WSM' => 'Samoa',
            'SM' => 'San Marino', 'SMR' => 'San Marino',
            'ST' => 'Sao Tome and Principe', 'STP' => 'Sao Tome and Principe',
            'SA' => 'Saudi Arabia', 'SAU' => 'Saudi Arabia',
            'SN' => 'Senegal', 'SEN' => 'Senegal',
            'RS' => 'Serbia', 'SRB' => 'Serbia',
            'SC' => 'Seychelles', 'SYC' => 'Seychelles',
            'SL' => 'Sierra Leone', 'SLE' => 'Sierra Leone',
            'SG' => 'Singapore', 'SGP' => 'Singapore',
            'SX' => 'Sint Maarten', 'SXM' => 'Sint Maarten',
            'SK' => 'Slovakia', 'SVK' => 'Slovakia',
            'SI' => 'Slovenia', 'SVN' => 'Slovenia',
            'SB' => 'Solomon Islands', 'SLB' => 'Solomon Islands',
            'SO' => 'Somalia', 'SOM' => 'Somalia',
            'ZA' => 'South Africa', 'ZAF' => 'South Africa',
            'KR' => 'South Korea', 'KOR' => 'South Korea',
            'SS' => 'South Sudan', 'SSD' => 'South Sudan',
            'ES' => 'Spain', 'ESP' => 'Spain',
            'LK' => 'Sri Lanka', 'LKA' => 'Sri Lanka',
            'SD' => 'Sudan', 'SDN' => 'Sudan',
            'SR' => 'Suriname', 'SUR' => 'Suriname',
            'SE' => 'Sweden', 'SWE' => 'Sweden',
            'CH' => 'Switzerland', 'CHE' => 'Switzerland',
            'SY' => 'Syria', 'SYR' => 'Syria',
            'TW' => 'Taiwan', 'TWN' => 'Taiwan',
            'TJ' => 'Tajikistan', 'TJK' => 'Tajikistan',
            'TZ' => 'Tanzania', 'TZA' => 'Tanzania',
            'TH' => 'Thailand', 'THA' => 'Thailand',
            'TL' => 'Timor-Leste', 'TLS' => 'Timor-Leste',
            'TG' => 'Togo', 'TGO' => 'Togo',
            'TO' => 'Tonga', 'TON' => 'Tonga',
            'TT' => 'Trinidad and Tobago', 'TTO' => 'Trinidad and Tobago',
            'TN' => 'Tunisia', 'TUN' => 'Tunisia',
            'TR' => 'Turkey', 'TUR' => 'Turkey',
            'TM' => 'Turkmenistan', 'TKM' => 'Turkmenistan',
            'TV' => 'Tuvalu', 'TUV' => 'Tuvalu',
            'UG' => 'Uganda', 'UGA' => 'Uganda',
            'UA' => 'Ukraine', 'UKR' => 'Ukraine',
            'AE' => 'United Arab Emirates', 'ARE' => 'United Arab Emirates',
            'GB' => 'United Kingdom', 'GBR' => 'United Kingdom',
            'US' => 'United States', 'USA' => 'United States',
            'UY' => 'Uruguay', 'URY' => 'Uruguay',
            'UZ' => 'Uzbekistan', 'UZB' => 'Uzbekistan',
            'VU' => 'Vanuatu', 'VUT' => 'Vanuatu',
            'VA' => 'Vatican City', 'VAT' => 'Vatican City',
            'VE' => 'Venezuela', 'VEN' => 'Venezuela',
            'VN' => 'Vietnam', 'VNM' => 'Vietnam',
            'WF' => 'Wallis and Futuna', 'WLF' => 'Wallis and Futuna',
            'EH' => 'Western Sahara', 'ESH' => 'Western Sahara',
            'YE' => 'Yemen', 'YEM' => 'Yemen',
            'ZM' => 'Zambia', 'ZMB' => 'Zambia',
            'ZW' => 'Zimbabwe', 'ZWE' => 'Zimbabwe',
        ];
        
/**
     * Convert a country code (2-letter or 3-letter ISO) or name to full country name.
     */
    public static function getCountryName(string $code): string
    {
        $codeUpper = strtoupper($code);

        // Return full country name if code exists, otherwise return original input
        return self::$countries[$codeUpper] ?? $code;
    }

    /**
     * Get all keys (codes) that match a country name (case-insensitive)
     */
    public static function getCountryKeys(string $countryName): array
    {
        $countryLower = strtolower($countryName);
        $keys = [];

        foreach (self::$countries as $code => $name) {
            if (strtolower($name) === $countryLower) {
                $keys[] = $code;
            }
        }

        return array_unique($keys);
    }
    
public static function normalizeCountryCode(string $codeOrName): ?string
{
    $input = strtoupper(trim($codeOrName));

    // If input is a code (2-letter or 3-letter), return country name
    if (isset(self::$countries[$input])) {
        return self::$countries[$input];
    }

    // If input is already a full country name, check against map values
    $found = array_search(ucwords(strtolower($codeOrName)), self::$countries, true);
    if ($found !== false) {
        return self::$countries[$found];
    }

    // Not found
    return null;
}

}