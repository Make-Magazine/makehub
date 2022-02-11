<?php

class csmm_stats
{
    static function get_stats($ndays = 30)
    {
        $stats = get_option(CSMM_STATS);
        $total = 0;
        
        $days = array();
        for ($i = $ndays; $i >= 0; $i--){
            $days[date("Y-m-d", strtotime('-' . $i . ' days'))] = 0;
        }

        if(false !== $stats){
            for ($i = $ndays; $i >= 0; $i--){
                $today = date("Y-m-d", strtotime('-' . $i . ' days'));
                if(array_key_exists($today, $stats['visits'])){
                    $days[$today] = $stats['visits'][$today];
                    $total += $days[$today];
                } else {
                    $days[$today] = 0;
                }
            }
        }

        if ($total < 1) {
            $chart_stats['days'] = array_keys($days);
            $chart_stats['count'] = array(3, 4, 67, 76, 45, 32, 134, 6, 65, 65, 56, 123, 156, 156, 123, 156, 67, 88, 54, 178,3, 4, 67, 76, 45, 32, 134, 6, 65, 65, 56, 123, 156, 156, 123, 156, 67, 88, 54, 178);
            $chart_stats['total'] = $total;

            return $chart_stats;
        }

        $chart_stats = array('days' => array(), 'count' => array(), 'total' => 0);
        foreach ($days as $day => $count) {
            $chart_stats['days'][] = $day;
            $chart_stats['count'][] = $count;
            $chart_stats['total'] += $count;
        }
        $chart_stats['period'] = $ndays;
        return $chart_stats;
    } // get_stats

    static function get_stats_count(){
        $stats = get_option(CSMM_STATS);
        if(false === $stats){
            return 0;
        }
        
        $total = 0;
        foreach ($stats['visits'] as $day => $count) {
            $total += $count;
        }
        return $total;
    }


    static function prepare_stats($ndays = 20)
    {
        $stats = get_option(CSMM_STATS);

        if(false === $stats){
            return $stats;
        }
        
        $days = array();
        for ($i = $ndays; $i >= 0; $i--){
            $today = date("Y-m-d", strtotime('-' . $i . ' days'));
            if(array_key_exists($today, $stats['visits'])){
                $days[$today] = $stats['visits'][$today];
            } else {
                $days[$today] = 0;
            }
        }
        $stats['general']['countries'] = array_slice($stats['general']['countries'],0,10);
        $countries = [];
        $other = 0;
        $ccount = 0;
        foreach($stats['general']['countries'] as $country => $visits){
            if($ccount<10){
                $stats['general']['countries'][$country] = $visits;
            } else {
                $other += $visits;
            }
            $ccount++;
            
        }
        $stats['general']['countries']['Other'] = $other;
        $stats['visits'] = $days;
        
        return $stats;
    } // prepare_stats


    static function get_top_countries($limit = 10)
    {
        $stats = get_option(CSMM_STATS);
        $total = 0;
            
        if(false !== $stats){
            $countries = array();
            $countries_percent = array();
            $other = 0;
            foreach ($stats['general']['countries'] as $country => $count) {
                $total += $count;
                if (empty($country) || $country == 'unknown' ||  $limit == 1) {
                    $other += $count;
                } else {
                    $countries[$country] = $count;
                    $limit--;
                }
            }
        }

        if ($total < 1) {
            $countries_percent = array(
                'France' => '15',
                'United States' => '8',
                'China' => '6',
                'Germany' => '3',
                'Russia' => '1'
            );
            return $countries_percent;
        }

        if ($other > 0) {
            $countries['Other'] = $other;
        }

        foreach ($countries as $country => $count) {
            $countries_percent[$country] = round($count / $total * 1000) / 10;
        }

        return $countries_percent;
    } // get_top_countries

    static function get_top_browsers($limit = 10)
    {
        $stats = get_option(CSMM_STATS);
        $total = 0;
        
        if(false !== $stats){
            $browsers = array();
            $browsers_percent = array();
            $other = 0;
            foreach ($stats['general']['browsers'] as $browser => $count) {
                $total += $count;
                if (empty($browser) || $browser == 'unknown' || $limit == 1) {
                    $other += $count;
                } else {
                    $browsers[$browser] = $count;
                    $limit--;
                }
            }
        }

        if ($total < 1) {
            $browsers_percent = array(
                'Chrome' => '35',
                'Internet Explorer' => '34',
                'Firefox' => '24',
                'Safari' => '2',
                'Opera' => '1'
            );
            return $browsers_percent;
        }

        if ($other > 0) {
            $browsers['Other'] = $other;
        }

        foreach ($browsers as $browser => $count) {
            $browsers_percent[$browser] = round($count / $total * 1000) / 10;
        }

        return $browsers_percent;
    } // get_top_browsers

    static function get_top_devices($limit = 10)
    {
        $stats = get_option(CSMM_STATS);
        $total = 0;
        
        if(false !== $stats){
            $devices = array();
            $devices_percent = array();
            $other = 0;
            foreach ($stats['general']['devices'] as $device => $count) {
                $total += $count;
                if (empty($device) || $device == 'unknown' || $limit == 1) {
                    $other += $count;
                } else {
                    switch($device){
                        case 'desktop':
                        $device_label = '<i data-fip-value="57426" data-icomoon=""></i> Desktop';
                        break;
                        case 'tablet':
                        $device_label = '<i data-fip-value="57430" data-icomoon=""></i> Tablet';
                        break;
                        case 'mobile':
                        $device_label = '<i data-fip-value="57428" data-icomoon=""></i> Mobile';
                        break;
                    }
                    $devices[$device_label] = $count;
                    $limit--;
                }
            }
        }

        if ($total < 1) {
            $devices_percent = array(
                '<i data-fip-value="57426" data-icomoon=""></i> Desktop' => 60,
                '<i data-fip-value="57430" data-icomoon=""></i> Tablet' => 26,
                '<i data-fip-value="57428" data-icomoon=""></i> Mobile' => 14
            );
            return $devices_percent;
        }

        if ($other > 0) {
            $devices['Other'] = $other;
        }

        foreach ($devices as $device => $count) {
            $devices_percent[$device] = round($count / $total * 1000) / 10;
        }

        return $devices_percent;
    } // get_top_devices

    static function get_device_stats()
    {
        $devices = self::get_top_devices();
        $device_stats = array('labels' => array(), 'percent' => array());
        foreach ($devices as $device => $percent) {
            $device_stats['labels'][] = ucfirst($device);
            $device_stats['percent'][] = $percent;
        }

        return $device_stats;
    } // get_device_stats

    static function get_top_bots($limit = 10)
    {
        $stats = get_option(CSMM_STATS);
        $total = 0;
        
        if(false !== $stats){
            $bots = array();
            $bots_percent = array();
            $other = 0;
            $human = 0;

            foreach ($stats['general']['traffic'] as $bot => $count) {
                $total += $count;
                if ($bot == 'human' || $limit == 1) {
                    $human += $count;
                } else if ($limit == 1 || $bot == 'unknown') {
                    $other += $count;
                } else {
                    $bots[$bot] = $count;
                    $limit--;
                }
            }
        }

        if ($total < 1) {
            $bots_percent = array(
                'Human' => '35',
                'Google' => '34',
                'Bing' => '24',
                'Archive' => '2',
                'Other' => '1'
            );
            return $bots_percent;
        }

        if ($human > 0) {
            $bots['Human'] = $human;
        }

        arsort($bots);

        if ($other > 0) {
            $bots['Other'] = $other;
        }

        foreach ($bots as $bot => $count) {
            $bots_percent[$bot] = round($count / $total * 1000) / 10;
        }

        return $bots_percent;
    }

    static function country_name_to_code($country)
    {
        $countrycodes = array(
            'other' => 'Other',
            'AF' => 'Afghanistan',
            'AX' => 'Åland Islands',
            'AL' => 'Albania',
            'DZ' => 'Algeria',
            'AS' => 'American Samoa',
            'AD' => 'Andorra',
            'AO' => 'Angola',
            'AI' => 'Anguilla',
            'AQ' => 'Antarctica',
            'AG' => 'Antigua and Barbuda',
            'AR' => 'Argentina',
            'AU' => 'Australia',
            'AT' => 'Austria',
            'AZ' => 'Azerbaijan',
            'BS' => 'Bahamas',
            'BH' => 'Bahrain',
            'BD' => 'Bangladesh',
            'BB' => 'Barbados',
            'BY' => 'Belarus',
            'BE' => 'Belgium',
            'BZ' => 'Belize',
            'BJ' => 'Benin',
            'BM' => 'Bermuda',
            'BT' => 'Bhutan',
            'BO' => 'Bolivia',
            'BA' => 'Bosnia and Herzegovina',
            'BW' => 'Botswana',
            'BV' => 'Bouvet Island',
            'BR' => 'Brazil',
            'IO' => 'British Indian Ocean Territory',
            'BN' => 'Brunei Darussalam',
            'BG' => 'Bulgaria',
            'BF' => 'Burkina Faso',
            'BI' => 'Burundi',
            'KH' => 'Cambodia',
            'CM' => 'Cameroon',
            'CAN' => 'Canada',
            'CV' => 'Cape Verde',
            'KY' => 'Cayman Islands',
            'CF' => 'Central African Republic',
            'TD' => 'Chad',
            'CL' => 'Chile',
            'CN' => 'China',
            'CX' => 'Christmas Island',
            'CC' => 'Cocos (Keeling) Islands',
            'CO' => 'Colombia',
            'KM' => 'Comoros',
            'CG' => 'Congo',
            'CD' => 'Zaire',
            'CK' => 'Cook Islands',
            'CR' => 'Costa Rica',
            'CI' => 'Côte D\'Ivoire',
            'HR' => 'Croatia',
            'CU' => 'Cuba',
            'CY' => 'Cyprus',
            'CZ' => 'Czech Republic',
            'DK' => 'Denmark',
            'DJ' => 'Djibouti',
            'DM' => 'Dominica',
            'DO' => 'Dominican Republic',
            'EC' => 'Ecuador',
            'EG' => 'Egypt',
            'SV' => 'El Salvador',
            'GQ' => 'Equatorial Guinea',
            'ER' => 'Eritrea',
            'EE' => 'Estonia',
            'ET' => 'Ethiopia',
            'FK' => 'Falkland Islands (Malvinas)',
            'FO' => 'Faroe Islands',
            'FJ' => 'Fiji',
            'FI' => 'Finland',
            'FR' => 'France',
            'GF' => 'French Guiana',
            'PF' => 'French Polynesia',
            'TF' => 'French Southern Territories',
            'GA' => 'Gabon',
            'GM' => 'Gambia',
            'GE' => 'Georgia',
            'DE' => 'Germany',
            'GH' => 'Ghana',
            'GI' => 'Gibraltar',
            'GR' => 'Greece',
            'GL' => 'Greenland',
            'GD' => 'Grenada',
            'GP' => 'Guadeloupe',
            'GU' => 'Guam',
            'GT' => 'Guatemala',
            'GG' => 'Guernsey',
            'GN' => 'Guinea',
            'GW' => 'Guinea-Bissau',
            'GY' => 'Guyana',
            'HT' => 'Haiti',
            'HM' => 'Heard Island and Mcdonald Islands',
            'VA' => 'Vatican City State',
            'HN' => 'Honduras',
            'HK' => 'Hong Kong',
            'HU' => 'Hungary',
            'IS' => 'Iceland',
            'IN' => 'India',
            'ID' => 'Indonesia',
            'IR' => 'Iran, Islamic Republic of',
            'IQ' => 'Iraq',
            'IE' => 'Ireland',
            'IM' => 'Isle of Man',
            'IL' => 'Israel',
            'IT' => 'Italy',
            'JM' => 'Jamaica',
            'JP' => 'Japan',
            'JE' => 'Jersey',
            'JO' => 'Jordan',
            'KZ' => 'Kazakhstan',
            'KE' => 'KENYA',
            'KI' => 'Kiribati',
            'KP' => 'Korea, Democratic People\'s Republic of',
            'KR' => 'Korea, Republic of',
            'KW' => 'Kuwait',
            'KG' => 'Kyrgyzstan',
            'LA' => 'Lao People\'s Democratic Republic',
            'LV' => 'Latvia',
            'LB' => 'Lebanon',
            'LS' => 'Lesotho',
            'LR' => 'Liberia',
            'LY' => 'Libyan Arab Jamahiriya',
            'LI' => 'Liechtenstein',
            'LT' => 'Lithuania',
            'LU' => 'Luxembourg',
            'MO' => 'Macao',
            'MK' => 'Macedonia, the Former Yugoslav Republic of',
            'MG' => 'Madagascar',
            'MW' => 'Malawi',
            'MY' => 'Malaysia',
            'MV' => 'Maldives',
            'ML' => 'Mali',
            'MT' => 'Malta',
            'MH' => 'Marshall Islands',
            'MQ' => 'Martinique',
            'MR' => 'Mauritania',
            'MU' => 'Mauritius',
            'YT' => 'Mayotte',
            'MX' => 'Mexico',
            'FM' => 'Micronesia, Federated States of',
            'MD' => 'Republic of Moldova',
            'MC' => 'Monaco',
            'MN' => 'Mongolia',
            'ME' => 'Montenegro',
            'MS' => 'Montserrat',
            'MA' => 'Morocco',
            'MZ' => 'Mozambique',
            'MM' => 'Myanmar',
            'NA' => 'Namibia',
            'NR' => 'Nauru',
            'NP' => 'Nepal',
            'NL' => 'Netherlands',
            'AN' => 'Netherlands Antilles',
            'NC' => 'New Caledonia',
            'NZ' => 'New Zealand',
            'NI' => 'Nicaragua',
            'NE' => 'Niger',
            'NG' => 'Nigeria',
            'NU' => 'Niue',
            'NF' => 'Norfolk Island',
            'MP' => 'Northern Mariana Islands',
            'NO' => 'Norway',
            'OM' => 'Oman',
            'PK' => 'Pakistan',
            'PW' => 'Palau',
            'PS' => 'Palestinian Territory, Occupied',
            'PA' => 'Panama',
            'PG' => 'Papua New Guinea',
            'PY' => 'Paraguay',
            'PE' => 'Peru',
            'PH' => 'Philippines',
            'PN' => 'Pitcairn',
            'PL' => 'Poland',
            'PT' => 'Portugal',
            'PR' => 'Puerto Rico',
            'QA' => 'Qatar',
            'RE' => 'Réunion',
            'RO' => 'Romania',
            'RU' => 'Russia',
            'RW' => 'Rwanda',
            'SH' => 'Saint Helena',
            'KN' => 'Saint Kitts and Nevis',
            'LC' => 'Saint Lucia',
            'PM' => 'Saint Pierre and Miquelon',
            'VC' => 'Saint Vincent and the Grenadines',
            'WS' => 'Samoa',
            'SM' => 'San Marino',
            'ST' => 'Sao Tome and Principe',
            'SA' => 'Saudi Arabia',
            'SN' => 'Senegal',
            'RS' => 'Serbia',
            'SC' => 'Seychelles',
            'SL' => 'Sierra Leone',
            'SG' => 'Singapore',
            'SK' => 'Slovakia',
            'SI' => 'Slovenia',
            'SB' => 'Solomon Islands',
            'SO' => 'Somalia',
            'ZA' => 'South Africa',
            'GS' => 'South Georgia and the South Sandwich Islands',
            'ES' => 'Spain',
            'LK' => 'Sri Lanka',
            'SD' => 'Sudan',
            'SR' => 'Suriname',
            'SJ' => 'Svalbard and Jan Mayen',
            'SZ' => 'Swaziland',
            'SE' => 'Sweden',
            'CH' => 'Switzerland',
            'SY' => 'Syrian Arab Republic',
            'TW' => 'Taiwan, Province of China',
            'TJ' => 'Tajikistan',
            'TZ' => 'Tanzania, United Republic of',
            'TH' => 'Thailand',
            'TL' => 'Timor-Leste',
            'TG' => 'Togo',
            'TK' => 'Tokelau',
            'TO' => 'Tonga',
            'TT' => 'Trinidad and Tobago',
            'TN' => 'Tunisia',
            'TR' => 'Turkey',
            'TM' => 'Turkmenistan',
            'TC' => 'Turks and Caicos Islands',
            'TV' => 'Tuvalu',
            'UG' => 'Uganda',
            'UA' => 'Ukraine',
            'AE' => 'United Arab Emirates',
            'GB' => 'United Kingdom',
            'US' => 'United States',
            'UM' => 'United States Minor Outlying Islands',
            'UY' => 'Uruguay',
            'UZ' => 'Uzbekistan',
            'VU' => 'Vanuatu',
            'VE' => 'Venezuela',
            'VN' => 'Viet Nam',
            'VG' => 'Virgin Islands, British',
            'VI' => 'Virgin Islands, U.S.',
            'WF' => 'Wallis and Futuna',
            'EH' => 'Western Sahara',
            'YE' => 'Yemen',
            'ZM' => 'Zambia',
            'ZW' => 'Zimbabwe',
        );

        return array_search($country, $countrycodes);
    } // country_name_to_code

    static function print_stats()
    {
        $stats = get_option(CSMM_STATS);
        
        $countries = self::get_top_countries();
        echo '<div class="csmm-stats-column">';
        echo '<h3>Top Countries</h3>';
        echo '<table class="csmm-stats-table">';
        foreach ($countries as $country => $count) {
            echo '<tr><td>' . ($country != 'unknown' ? '<img style="filter: saturate(0.75);" src="' . CSMM_URL . '/framework/admin/img/flags/' . strtolower(self::country_name_to_code($country)) . '.png" /> ' : '<img style="filter: saturate(0.75);" src="' . CSMM_URL . '/framework/admin/img/flags/other.png" /> ') . $country . '</td><td>' . $count . '%</td></tr>';
        }
        echo '</table>';
        echo '</div>';

        $browsers = self::get_top_browsers();
        echo '<div class="csmm-stats-column">';
        echo '<h3>Top Browsers</h3>';
        echo '<table class="csmm-stats-table">';
        foreach ($browsers as $browser => $count) {
            echo '<tr><td>' . $browser . '</td><td>' . $count . '%</td></tr>';
        }
        echo '</table>';
        echo '</div>';

        $devices = self::get_top_devices();
        echo '<div class="csmm-stats-column">';
        echo '<h3>Top Devices</h3>';
        echo '<table class="csmm-stats-table">';
        foreach ($devices as $device => $count) {
            echo '<tr><td>' . $device . '</td><td>' . $count . '%</td></tr>';
        }
        echo '</table>';
        echo '</div>';



        $bots = self::get_top_bots();
        echo '<div class="csmm-stats-column">';
        echo '<h3>Traffic Type</h3>';
        echo '<table class="csmm-stats-table">';
        foreach ($bots as $bot => $count) {
            echo '<tr><td>' . ($bot == 'Human' ? '<span class="human">Human</span>' : $bot) . '</td><td>' . $count . '%</td></tr>';
        }
        echo '</table>';
        echo '</div>';
    }
} // class
