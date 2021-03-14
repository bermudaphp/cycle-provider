<?php

namespace Bermuda\Cycle;

use Cycle\ORM\Select;
use Bermuda\Utils\Date;
use Bermuda\String\Str;
use Spiral\Database\Injection\Fragment;
use Spiral\Database\Injection\Parameter;
use Bermuda\HTTP\Exception\BadRequestException;

/**
 * Class DatesRepository
 * @package Bermuda\Cycle
 */
abstract class DatesRepository extends Repository
{
    /**
     * @return string
     */
    protected function dateCol(): string
    {
        return 'created_at';
    }

    /**
     * @param Select $select
     * @param \DateTimeInterface[] $dates
     * @return Select
     */
    protected function dates(Select $select, array $dates): Select
    {
        if (($count = count($dates)) == 2)
        {
            return $select->where(new Fragment('Date('.$this->dateCol().')'), 'between', $dates[0]->format('Y-m-d'), $dates[1]->format('Y-m-d'));
        }

        elseif ($count == 1)
        {
            return $select->where(new Fragment('Date('.$this->dateCol().')'), $dates[0]->format('Y-m-d'));
        }

        elseif ($count > 2)
        {
            $params = [];

            foreach ($dates as $date)
            {
                $params[] = $date->format('Y-m-d');
            }

            return $select->where(new Fragment('Date('.$this->dateCol().')'), 'in', new Parameter($params));
        }

        return $select;
    }

    /**
     * @param string $dates
     * @return \DateTimeInterface[]
     */
    protected function parseDates(string $dates): array
    {
        $parsed = [];

        if (Str::contains($dates, ':'))
        {
            foreach (explode(':', $dates, 2) as $date)
            {
                if (!Date::isDate($date))
                {
                    throw new BadRequestException('Unable to parse dates.');
                }

                $parsed[] = new \DateTimeImmutable($date);
            }
        }

        else {
            foreach (explode(',', $dates) as $date)
            {
                if (!Date::isDate($date))
                {
                    throw new BadRequestException('Unable to parse dates.');
                }

                $parsed[] = new \DateTimeImmutable($date);
            }
        }

        return $parsed;
    }

    /**
     * @param array $queryParams
     * @return Select
     */
    protected function buildSelectQuery(array $queryParams): Select
    {
        $select = parent::buildSelectQuery($queryParams);

        if (isset($queryParams['dates']))
        {
            $dates = $this->parseDates($queryParams['dates']);
            $select = $this->dates($select, $dates);
        }

        if (isset($queryParams['order']))
        {
            $order = strtoupper($queryParams['order']);

            if ($order != 'ASC' && $order != 'DESC')
            {
                unset($order);
            }
        }

        return $select->orderBy($this->dateCol(), $order ?? 'ASC');
    }
}
