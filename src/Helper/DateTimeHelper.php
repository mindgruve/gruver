<?php

namespace Mindgruve\Gruver\Helper;


class DateTimeHelper
{


    const HOUR_IN_SECONDS = 3600;
    const MINUTE_IN_SECONDS = 60;
    const DAY_IN_SECONDS = 86400;
    const WEEK_IN_SECONDS = 604800;
    const MONTH_IN_SECONDS = 2592000;
    const YEAR_IN_SECONDS = 31557600;

    public static function humanTimeDiff($from, $to = '')
    {
        if (empty($to)) {
            $to = time();
        }

        $diff = (int)abs($to - $from);

        $since = 'n/a';
        if ($diff < self::HOUR_IN_SECONDS) {
            $mins = round($diff / self::MINUTE_IN_SECONDS);
            if ($mins <= 1) {
                $mins = 1;
            }
            /* translators: min=minute */
            $since = sprintf('%s min', $mins);
        } elseif ($diff < self::DAY_IN_SECONDS && $diff >= self::HOUR_IN_SECONDS) {
            $hours = round($diff / self::HOUR_IN_SECONDS);
            if ($hours <= 1) {
                $hours = 1;
            }
            $since = sprintf('%s hour', $hours);
        } elseif ($diff < self::WEEK_IN_SECONDS && $diff >= self::DAY_IN_SECONDS) {
            $days = round($diff / self::DAY_IN_SECONDS);
            if ($days <= 1) {
                $days = 1;
            }
            $since = sprintf('%s day', $days);
        } elseif ($diff < self::MONTH_IN_SECONDS && $diff >= self::WEEK_IN_SECONDS) {
            $weeks = round($diff / self::WEEK_IN_SECONDS);
            if ($weeks <= 1) {
                $weeks = 1;
            }
            $since = sprintf('%s week', $weeks);
        } elseif ($diff < self::YEAR_IN_SECONDS && $diff >= self::MONTH_IN_SECONDS) {
            $months = round($diff / self::MONTH_IN_SECONDS);
            if ($months <= 1) {
                $months = 1;
            }
            $since = sprintf('%s month', $months);
        } elseif ($diff >= self::YEAR_IN_SECONDS) {
            $years = round($diff / self::YEAR_IN_SECONDS);
            if ($years <= 1) {
                $years = 1;
            }
            $since = sprintf('%s year', $years);
        }

        return $since.' ago';
    }
}