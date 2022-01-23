<?php

namespace Bermuda\Cycle;

use Cycle\ORM\Select;
use Bermuda\Utils\Date;
use Bermuda\String\Str;
use Spiral\Database\Injection\Fragment;
use Spiral\Database\Injection\Parameter;

trait Dates
{
    protected string $dateTimeFormat = 'Y-m-d';
    protected string $dateTimeColumn = 'created_at';
    protected string $dateTimeStringDelimiter = ',';
    protected string $dateTimeStringRangeDelimiter = ':';
    
    /**
     * @param Select $select
     * @param \DateTimeInterface[] $dates
     * @return Select
     */
    protected function dates(Select $select, array $dates): Select
    {
        if (($count = count($dates)) == 2) {
            return $select->where(new Fragment('Date('.$this->dateTimeColumn.')'), 'between', $dates[0]->format($this->dateTimeFormat), $dates[1]->format($this->dateTimeFormat));
        } elseif ($count == 1) {
            return $select->where(new Fragment('Date('.$this->dateTimeColumn.')'), $dates[0]->format($this->dateTimeFormat));
        } elseif ($count > 2) {
            foreach ($dates as $date) {
                $params[] = $date->format($this->dateTimeFormat);
            }

            return $select->where(new Fragment('Date('.$this->dateTimeColumn.')'), 'in', new Parameter($params ?? []));
        }

        return $select;
    }

    /**
     * @param string $dates
     * @return \DateTimeInterface[]
     */
    protected function parseDatesString(string $dates): array
    {
        if (Str::contains($dates, $this->dateTimeStringRangeDelimiter)) {
            foreach (explode($this->dateTimeStringRangeDelimiter, $dates, 2) as $date) {
                if (!Date::isDate($date)) {
                    throw new \RuntimeException('Unable to parse dates string.');
                }

                $parsed[] = Clock::fromString($date);
            }
        } else {
            foreach (explode($this->dateTimeStringDelimiter, $dates) as $date) {
                if (!Date::isDate($date)) {
                    throw new \RuntimeException('Unable to parse dates string.');
                }

                $parsed[] = Clock::fromString($date);
            }
        }

        return $parsed ?? [];
    }

    /**
     * @param array $queryParams
     * @return Select
     */
    protected function buildSelectQuery(array $queryParams): Select
    {
        $select = parent::buildSelectQuery($queryParams);

        if (isset($queryParams['dates'])) {
            $dates = $this->parseDates($queryParams['dates']);
            $select = $this->dates($select, $dates);
        }

        if (isset($queryParams['order'])) {
            $order = strtoupper($queryParams['order']);

            if ($order != 'ASC' && $order != 'DESC') {
                unset($order);
            }
        }

        return $select->orderBy($this->dateTimeColumn, $order ?? 'ASC');
    }
}
