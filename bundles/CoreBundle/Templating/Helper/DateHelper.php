<?php

namespace Milex\CoreBundle\Templating\Helper;

use Milex\CoreBundle\Helper\CoreParametersHelper;
use Milex\CoreBundle\Helper\DateTimeHelper;
use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\Translation\TranslatorInterface;

class DateHelper extends Helper
{
    /**
     * @var string[]
     */
    protected $formats;

    /**
     * @var DateTimeHelper
     */
    protected $helper;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var CoreParametersHelper
     */
    private $coreParametersHelper;

    /**
     * @param string $dateFullFormat
     * @param string $dateShortFormat
     * @param string $dateOnlyFormat
     * @param string $timeOnlyFormat
     */
    public function __construct(
        $dateFullFormat,
        $dateShortFormat,
        $dateOnlyFormat,
        $timeOnlyFormat,
        TranslatorInterface $translator,
        CoreParametersHelper $coreParametersHelper
    ) {
        $this->formats = [
            'datetime' => $dateFullFormat,
            'short'    => $dateShortFormat,
            'date'     => $dateOnlyFormat,
            'time'     => $timeOnlyFormat,
        ];

        $this->helper               = new DateTimeHelper(null, null, 'local');
        $this->translator           = $translator;
        $this->coreParametersHelper = $coreParametersHelper;
    }

    /**
     * @param string           $type
     * @param \DateTime|string $datetime
     * @param string           $timezone
     * @param string           $fromFormat
     *
     * @return string
     */
    protected function format($type, $datetime, $timezone, $fromFormat)
    {
        if (empty($datetime)) {
            return '';
        } else {
            $this->helper->setDateTime($datetime, $fromFormat, $timezone);

            return $this->helper->toLocalString(
                $this->formats[$type]
            );
        }
    }

    /**
     * Returns full date. eg. October 8, 2014 21:19.
     *
     * @param \DateTime|string $datetime
     * @param string           $timezone
     * @param string           $fromFormat
     *
     * @return string
     */
    public function toFull($datetime, $timezone = 'local', $fromFormat = 'Y-m-d H:i:s')
    {
        return $this->format('datetime', $datetime, $timezone, $fromFormat);
    }

    /**
     * Returns date and time concat eg 2014-08-02 5:00am.
     *
     * @param \DateTime|string $datetime
     * @param string           $timezone
     * @param string           $fromFormat
     *
     * @return string
     */
    public function toFullConcat($datetime, $timezone = 'local', $fromFormat = 'Y-m-d H:i:s')
    {
        $this->helper->setDateTime($datetime, $fromFormat, $timezone);

        return $this->helper->toLocalString(
            $this->formats['date'].' '.$this->formats['time']
        );
    }

    /**
     * Returns short date format eg Sun, Oct 8.
     *
     * @param \DateTime|string $datetime
     * @param string           $timezone
     * @param string           $fromFormat
     *
     * @return string
     */
    public function toShort($datetime, $timezone = 'local', $fromFormat = 'Y-m-d H:i:s')
    {
        return $this->format('short', $datetime, $timezone, $fromFormat);
    }

    /**
     * Returns date only e.g. 2014-08-09.
     *
     * @param \DateTime|string $datetime
     * @param string           $timezone
     * @param string           $fromFormat
     *
     * @return string
     */
    public function toDate($datetime, $timezone = 'local', $fromFormat = 'Y-m-d H:i:s')
    {
        return $this->format('date', $datetime, $timezone, $fromFormat);
    }

    /**
     * Returns time only e.g. 21:19.
     *
     * @param \DateTime|string $datetime
     * @param string           $timezone
     * @param string           $fromFormat
     *
     * @return string
     */
    public function toTime($datetime, $timezone = 'local', $fromFormat = 'Y-m-d H:i:s')
    {
        return $this->format('time', $datetime, $timezone, $fromFormat);
    }

    /**
     * Returns date/time like Today, 10:00 AM.
     *
     * @param        $datetime
     * @param string $timezone
     * @param string $fromFormat
     * @param bool   $forceDateForNonText If true, return as full date/time rather than "29 days ago"
     *
     * @return string
     */
    public function toText($datetime, $timezone = 'local', $fromFormat = 'Y-m-d H:i:s', $forceDateForNonText = false)
    {
        if (empty($datetime)) {
            return '';
        }

        $this->helper->setDateTime($datetime, $fromFormat, $timezone);

        $textDate = $this->helper->getTextDate();
        $dt       = $this->helper->getLocalDateTime();

        if ($textDate) {
            return $this->translator->trans('milex.core.date.'.$textDate, ['%time%' => $dt->format($this->coreParametersHelper->get('date_format_timeonly'))]);
        } else {
            $interval = $this->helper->getDiff('now', null, true);

            if ($interval->invert && !$forceDateForNonText) {
                // In the past
                return $this->translator->trans('milex.core.date.ago', ['%days%' => $interval->days]);
            } else {
                // In the future
                return $this->toFullConcat($datetime, $timezone, $fromFormat);
            }
        }
    }

    /**
     * Format DateInterval into humanly readable format.
     * Example: 55 minutes 49 seconds.
     * It doesn't return zero values like 0 years.
     *
     * @param DateInterval $range
     * @param string       $format
     *
     * @return string $formatedRange
     */
    public function formatRange($range, $format = null)
    {
        if ($range instanceof \DateInterval) {
            $formated  = [];
            $timeUnits = ['y' => 'year', 'm' => 'month', 'd' => 'day', 'h' => 'hour', 'i' => 'minute', 's' => 'second'];

            foreach ($timeUnits as $key => $unit) {
                if ($range->{$key}) {
                    $formated[] = $this->translator->trans(
                        'milex.core.date.'.$unit,
                        ['%count%' => $range->{$key}]
                    );
                }
            }

            if (empty($formated)) {
                return $this->translator->trans('milex.core.date.less.than.second');
            }

            return implode(' ', $formated);
        }

        return '';
    }

    /**
     * @return string
     */
    public function getFullFormat()
    {
        return $this->formats['datetime'];
    }

    /**
     * @return string
     */
    public function getDateFormat()
    {
        return $this->formats['date'];
    }

    /**
     * @return string
     */
    public function getTimeFormat()
    {
        return $this->formats['time'];
    }

    /**
     * @return string
     */
    public function getShortFormat()
    {
        return $this->formats['short'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'date';
    }
}
